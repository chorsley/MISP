<?php
/**
 * Beta UI — Add element to Collection form
 *
 * Improved version of the default add_element_to_collection form:
 * - Shows what is being added at the top
 * - Collection dropdown with "create new collection" link
 * - Description textarea
 *
 * @since 2.5.x (beta)
 */
$currentElementType = !empty($this->request->params['pass'][0]) ? $this->request->params['pass'][0] : 'Event';
$currentElementUuid = !empty($this->request->params['pass'][1]) ? $this->request->params['pass'][1] : '';
$canCreateCollection = $this->Acl->canAccess('collections', 'add');
$createCollectionOptionValue = '__create_new_collection__';
$collectionOptions = $dropdownData['collections'];
asort($collectionOptions, SORT_NATURAL | SORT_FLAG_CASE);
if ($canCreateCollection) {
    $collectionOptions[$createCollectionOptionValue] = __('Create new collection...');
}
?>

<?php
// Render the standard generic form which handles the POST correctly
$fields = [
        [
            'field' => 'collection_id',
            'class' => 'input span6',
            'options' => $collectionOptions,
            'type' => 'dropdown',
            'label' => __('Collection')
        ],
        [
            'field' => 'description',
            'class' => 'span6',
            'type' => 'textarea',
            'label' => __('Analyst Note (optional)'),
            'placeholder' => __('Why is this event relevant to the collection?')
        ]
];

$description = sprintf(
    '<div class="beta-modal-header-info">'
    . '<i class="fa fa-folder-plus fa-2x" style="color:#428bca; margin-right:10px; vertical-align:middle;"></i>'
    . '<div style="display:inline-block; vertical-align:middle;">'
    . '<strong>%s</strong><br>'
    . '<small style="color:#888;">%s%s</small>'
    . '</div>'
    . '</div>'
    . '<hr style="margin: 12px 0;">',
    __('Add to Collection'),
    h($currentElementType),
    !empty($currentElementUuid) ? ' &mdash; <code style="font-size:11px;">' . h(substr($currentElementUuid, 0, 12)) . '…</code>' : ''
);

$metaFields = [];
if ($canCreateCollection) {
    $createCollectionUrl = sprintf(
        '%s/collections/add/attach_element_type:%s/attach_element_uuid:%s',
        h($baseurl),
        rawurlencode($currentElementType),
        rawurlencode($currentElementUuid)
    );
}

echo $this->element('genericElements/Form/genericForm', [
    'data' => [
        'description' => $description,
        'model' => 'CollectionElement',
        'title' => false,
        'fields' => $fields,
        'metaFields' => $metaFields,
        'submit' => [
            'action' => $this->request->params['action'],
            'ajaxSubmit' => 'submitAddElementToCollectionBeta();'
        ]
    ]
]);
?>

<script>
<?php if ($canCreateCollection): ?>
$(document)
    .off('focus.betaCollectionCreateOption', '#genericModal select[name="data[CollectionElement][collection_id]"]')
    .on('focus.betaCollectionCreateOption', '#genericModal select[name="data[CollectionElement][collection_id]"]', function() {
        $(this).data('betaPrevValue', $(this).val());
    })
    .off('change.betaCollectionCreateOption', '#genericModal select[name="data[CollectionElement][collection_id]"]')
    .on('change.betaCollectionCreateOption', '#genericModal select[name="data[CollectionElement][collection_id]"]', function() {
        var createValue = <?php echo json_encode($createCollectionOptionValue); ?>;
        if ($(this).val() !== createValue) {
            $(this).data('betaPrevValue', $(this).val());
            return;
        }

        var prevValue = $(this).data('betaPrevValue');
        if (prevValue && prevValue !== createValue) {
            $(this).val(prevValue);
        } else if (this.options.length > 0) {
            this.selectedIndex = 0;
        }

        openGenericModal(<?php echo json_encode($createCollectionUrl); ?>);
    });
<?php endif; ?>

function betaNormalizeCollectionModalMessage(message, fallback) {
    if (typeof message === 'string') {
        return message;
    }
    if (Array.isArray(message)) {
        return message.join(', ');
    }
    if (message && typeof message === 'object') {
        try {
            return Object.values(message).flat().join(', ');
        } catch (e) {
            return fallback;
        }
    }
    return fallback;
}

function betaGetCollectionModalForm() {
    var $genericForm = $('#genericModal .genericForm');
    return $genericForm.length ? $genericForm : $('.genericForm').first();
}

function betaParseCollectionModalResponse(data) {
    if (typeof data !== 'string') {
        return data;
    }
    try {
        return JSON.parse(data);
    } catch (e) {
        return null;
    }
}

function submitAddElementToCollectionBeta() {
    var $genericForm = betaGetCollectionModalForm();

    $.ajax({
        type: 'POST',
        url: $genericForm.attr('action'),
        data: $genericForm.serialize(),
        headers: { Accept: 'application/json' },
        success: function(data) {
            var response = betaParseCollectionModalResponse(data);

            if (response && response.saved) {
                showMessage('success', betaNormalizeCollectionModalMessage(response.success || response.message, 'Element added to the Collection.'));
                $('#genericModal').modal('hide').remove();
                if (typeof window.betaLoadEventCollections === 'function') {
                    window.betaLoadEventCollections();
                }
                return;
            }

            if (response) {
                showMessage('fail', betaNormalizeCollectionModalMessage(response.errors || response.error || response.message, 'Element could not be added to the Collection.'));
                return;
            }

            showMessage('fail', 'Could not complete the requested action.');
        },
        error: xhrFailCallback
    });
}
</script>
