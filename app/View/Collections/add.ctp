<?php
$edit = $this->request->params['action'] === 'edit' ? true : false;
$hasAttachTarget = !empty($attachElementType) && !empty($attachElementUuid);
$fields = [
    [
        'field' => 'name',
        'class' => 'span6'
    ],
    [
        'field' => 'type',
        'class' => 'input span6',
        'options' => $dropdownData['types'],
        'type' => 'dropdown'
    ],
    [
        'field' => 'description',
        'class' => 'span6',
        'type' => 'textarea'
    ],
    [
        'field' => 'distribution',
        'class' => 'input',
        'options' => $dropdownData['distributionLevels'],
        'default' => isset($data['Collection']['distribution']) ? $data['Collection']['distribution'] : $initialDistribution,
        'stayInLine' => 1,
        'type' => 'dropdown'
    ],
    [
        'field' => 'sharing_group_id',
        'class' => 'input',
        'options' => $dropdownData['sgs'],
        'label' => __("Sharing Group"),
        'type' => 'dropdown'
    ]
];

$metaFields = [];
if ($hasAttachTarget) {
    $metaFields[] = sprintf(
        '<input type="hidden" name="data[Collection][_attach_element_type]" value="%s">',
        h($attachElementType)
    );
    $metaFields[] = sprintf(
        '<input type="hidden" name="data[Collection][_attach_element_uuid]" value="%s">',
        h($attachElementUuid)
    );
}

$submitConfig = [
    'action' => $this->request->params['action']
];
if ($ajax && $hasAttachTarget) {
    $submitConfig['ajaxSubmit'] = 'return submitCollectionCreateAndReturnToEvent();';
}

echo $this->element('genericElements/Form/genericForm', [
    'data' => [
        'description' => __('Create collections to organise data shared by the community into buckets based on commonalities or as part of your research process. Collections are first class citizens and adhere to the same sharing rules as for example events do.'),
        'model' => 'Collection',
        'title' => $edit ? __('Edit collection') : __('Add new collection'),
        'fields' => $fields,
        'metaFields' => $metaFields,
        'submit' => $submitConfig
    ]
]);

if (!$ajax) {
    echo $this->element('/genericElements/SideMenu/side_menu', $menuData);
}

if ($ajax && $hasAttachTarget):
?>
<script>
function submitCollectionCreateAndReturnToEvent() {
    var closeModal = function() {
        $('#genericModal').modal('hide').remove();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
    };

    var $genericForm = $('#genericModal .genericForm');
    if (!$genericForm.length) {
        $genericForm = $('.genericForm').first();
    }
    var actionUrl = $genericForm.attr('action') || '';
    if (!/\.json($|\?)/.test(actionUrl)) {
        var qPos = actionUrl.indexOf('?');
        if (qPos === -1) {
            actionUrl += '.json';
        } else {
            actionUrl = actionUrl.slice(0, qPos) + '.json' + actionUrl.slice(qPos);
        }
    }
    $.ajax({
        type: 'POST',
        url: actionUrl,
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

            // Normalise response shape - some endpoints wrap payload as
            // {response: {saved: true, message: "..."}}
            var payload = response && response.response ? response.response : response;
            var isSuccess = false;

            // saveSuccessResponse style payload
            if (payload && payload.saved) {
                isSuccess = true;
            }

            // CRUDComponent::add REST payload for Collections/add returns the
            // created entity object instead of {saved: true, ...}
            if (!isSuccess && payload && payload.Collection && payload.Collection.id) {
                isSuccess = true;
            }

            if (isSuccess) {
                showMessage('success', payload.message || response.message || 'Collection created and element added.');
                closeModal();
                if (typeof window.loadEventCollections === 'function') {
                    window.loadEventCollections();
                }
                return;
            }

            if (payload) {
                var errorMessage = payload.errors || payload.error || payload.message;
                if (typeof errorMessage !== 'string') {
                    try {
                        errorMessage = Object.values(errorMessage || {}).flat().join(', ');
                    } catch (e) {
                        errorMessage = payload.message || 'Could not create collection.';
                    }
                }
                if (!errorMessage) {
                    errorMessage = 'Could not create collection.';
                }
                showMessage('fail', errorMessage);
                return;
            }
            showMessage('fail', 'Could not create collection.');
        },
        error: xhrFailCallback,
    });

    return false;
}
</script>
<?php
endif;
