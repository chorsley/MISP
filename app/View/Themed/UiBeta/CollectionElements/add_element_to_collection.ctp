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
?>

<?php
// Render the standard generic form which handles the POST correctly
$fields = [
        [
            'field' => 'collection_id',
            'class' => 'input span6',
            'options' => $dropdownData['collections'],
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
if ($this->Acl->canAccess('collections', 'add')) {
    $createCollectionUrl = sprintf(
        '%s/collections/add/attach_element_type:%s/attach_element_uuid:%s',
        h($baseurl),
        rawurlencode($currentElementType),
        rawurlencode($currentElementUuid)
    );
    $metaFields[] = '<div style="margin-top:8px; text-align:center;"><small>'
        . __('Don\'t have a collection yet?')
        . ' <a href="#" onclick="openGenericModal(\'' . $createCollectionUrl . '\'); return false;">'
        . '<i class="fa fa-plus"></i> ' . __('Create a new collection')
        . '</a></small></div>';
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
function submitAddElementToCollectionBeta() {
    var normalizeMessage = function(message, fallback) {
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
    };

    var $genericForm = $('#genericModal .genericForm');
    if (!$genericForm.length) {
        $genericForm = $('.genericForm').first();
    }

    $.ajax({
        type: 'POST',
        url: $genericForm.attr('action'),
        data: $genericForm.serialize(),
        headers: { Accept: 'application/json' },
        success: function(data) {
            var response = data;
            if (typeof data === 'string') {
                try {
                    response = JSON.parse(data);
                } catch (e) {
                    response = null;
                }
            }

            if (response && response.saved) {
                showMessage('success', normalizeMessage(response.success || response.message, 'Element added to the Collection.'));
                $('#genericModal').modal('hide').remove();
                if (typeof window.betaLoadEventCollections === 'function') {
                    window.betaLoadEventCollections();
                }
                return;
            }

            if (response) {
                showMessage('fail', normalizeMessage(response.errors || response.error || response.message, 'Element could not be added to the Collection.'));
                return;
            }

            showMessage('fail', 'Could not complete the requested action.');
        },
        error: xhrFailCallback
    });
}
</script>
