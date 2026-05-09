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

<div class="beta-add-to-collection-modal">
    <div class="beta-modal-header-info">
        <i class="fa fa-folder-plus fa-2x" style="color:#428bca; margin-right:10px; vertical-align:middle;"></i>
        <div style="display:inline-block; vertical-align:middle;">
            <strong><?= __('Add to Collection') ?></strong><br>
            <small style="color:#888;">
                <?= h($currentElementType) ?>
                <?php if (!empty($currentElementUuid)): ?>
                    &mdash; <code style="font-size:11px;"><?= h(substr($currentElementUuid, 0, 12)) ?>…</code>
                <?php endif; ?>
            </small>
        </div>
    </div>

    <hr style="margin: 12px 0;">

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

    echo $this->element('genericElements/Form/genericForm', [
        'data' => [
            'description' => null,
            'model' => 'CollectionElement',
            'title' => false,
            'fields' => $fields,
            'submit' => [
                'action' => $this->request->params['action'],
                'ajaxSubmit' => 'submitGenericFormInPlace();'
            ]
        ]
    ]);
    ?>

    <?php if ($this->Acl->canAccess('collections', 'add')): ?>
        <div style="margin-top:8px; text-align:center;">
            <small>
                <?= __("Don't have a collection yet?") ?>
                <a href="#" onclick="openGenericModal('<?= $baseurl ?>/collections/add'); return false;">
                    <i class="fa fa-plus"></i> <?= __('Create a new collection') ?>
                </a>
            </small>
        </div>
    <?php endif; ?>
</div>
