<?php
    // Prepare items
    $items = [];
    if (!empty($event['objects'])) {
        $items = $event['objects'];
    } else {
        if (!empty($event['Attribute'])) {
            foreach ($event['Attribute'] as $attr) {
                $attr['objectType'] = 'attribute';
                $items[] = $attr;
            }
        }
        if (!empty($event['Object'])) {
            foreach ($event['Object'] as $obj) {
                $obj['objectType'] = 'object';
                $items[] = $obj;
            }
        }
    }
    // Sort desc by timestamp
    usort($items, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });
?>

<style>
    .beta-attr-table {
        width: 100%;
        border-collapse: separate; 
        border-spacing: 0;
        margin-top: 10px;
    }
    .beta-attr-table th {
        text-align: left;
        padding: 12px 10px;
        border-bottom: 2px solid #eee;
        color: #777;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .beta-attr-table td {
        padding: 10px;
        border-bottom: 1px solid #f9f9f9;
        vertical-align: middle;
        font-size: 13px;
    }
    .beta-attr-row:hover {
        background-color: #f5faff;
    }
    .beta-attr-row:hover .beta-row-menu-trigger {
        visibility: visible;
    }

    /* Row Actions (Checkbox + Dropdown) */
    .beta-row-actions {
        display: flex;
        align-items: center;
        width: 40px; 
    }
    .beta-row-menu-trigger {
        visibility: hidden;
        cursor: pointer;
        padding: 2px 5px;
        color: #777;
        margin-left: 5px;
    }
    .beta-row-menu-trigger:hover {
        color: #333;
        background: #e1e1e1;
        border-radius: 3px;
    }
    .beta-row-menu {
        display: none;
        position: absolute;
        top: 25px;
        left: 0;
        z-index: 1000;
        background: white;
        border: 1px solid #ccc;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-radius: 3px;
        min-width: 150px;
    }
    .beta-row-menu ul {
        list-style: none;
        padding: 5px 0;
        margin: 0;
    }
    .beta-row-menu li a {
        display: block;
        padding: 5px 15px;
        color: #333;
        text-decoration: none;
        font-size: 12px;
    }
    .beta-row-menu li a:hover {
        background-color: #f5f5f5;
        color: #000;
    }
    .beta-row-menu .divider {
        height: 1px;
        background: #eee;
        margin: 5px 0;
    }

    .attr-icon {
        width: 20px;
        text-align: center;
        color: #999;
        margin-right: 5px;
    }
    .attr-value {
        font-family: 'Consolas', 'Monaco', monospace;
        color: #333;
        word-break: break-all;
    }
    .attr-tag {
        display: inline-block;
        padding: 2px 6px;
        font-size: 10px;
        border-radius: 3px;
        margin-right: 4px;
        margin-bottom: 2px;
        border: 1px solid transparent;
        color: white; /* Ensure text is readable on colored tags */
    }
    .object-header-row {
        background-color: #f0f7fd;
        border-top: 2px solid #e1f0fa;
    }
    .object-title {
        font-weight: bold;
        color: #31708f;
    }
    .object-attr-row td:first-child {
        /* border-left handled inline for positioning */
    }
</style>

<div class="beta-attributes-list">
    <!-- Toolbar -->
    <div class="beta-toolbar clearfix" style="margin-bottom: 15px;">
        <div class="pull-left">
             <?php if ($mayModify): ?>
                <a href="<?php echo $baseurl; ?>/attributes/add/<?php echo $event['Event']['id']; ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?php echo __('Add Attribute'); ?></a>
            <?php endif; ?>
        </div>
        <div class="pull-right">
             <input type="text" placeholder="Filter..." class="form-control input-sm" style="display:inline-block; width: 200px;">
        </div>
    </div>

    <table class="beta-attr-table">
        <thead>
            <tr>
                <th style="width: 50px;"><input type="checkbox" class="select-all"></th>
                <th style="width: 40px;" title="<?php echo __('Recommend for blocking / alerting?'); ?>">IDS</th>
                <th>Type / Object</th>
                <th>Value / Attributes</th>
                <th>Context</th>
                <th>Tags</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): 
                $isObject = $item['objectType'] === 'object';
                $rowClass = $isObject ? 'object-header-row' : 'attr-row';
                if ($isObject) {
                    $dataType = 'object';
                    $dataName = h($item['name']);
                } else {
                    $dataType = 'attribute';
                    $dataName = h($item['type']);
                }
            ?>
                <tr class="beta-attr-row <?php echo $rowClass; ?>" 
                    data-object-type="<?php echo $dataType; ?>" 
                    <?php if ($isObject): ?>data-object-name="<?php echo $dataName; ?>"<?php else: ?>data-attribute-type="<?php echo $dataName; ?>"<?php endif; ?>>
                    
                    <!-- Checkbox & Actions Dropdown -->
                    <td style="position: relative;">
                        <div class="beta-row-actions">
                            <input type="checkbox" class="select-row" value="<?php echo h($item['id']); ?>">
                            <div class="beta-row-menu-trigger">
                                <i class="fa fa-caret-down"></i>
                            </div>
                            <div class="beta-row-menu">
                                <ul>
                                    <?php if ($mayModify): ?>
                                    <!-- Edit -->
                                    <li><a href="<?php echo $baseurl; ?>/<?php echo $isObject ? 'objects' : 'attributes'; ?>/edit/<?php echo h($item['id']); ?>"><i class="fa fa-edit"></i> Edit</a></li>
                                    
                                    <!-- Context Specific Actions -->
                                    <?php if (!$isObject && ($item['type'] == 'malware-sample' || $item['type'] == 'attachment')): ?>
                                        <li><a href="<?php echo $baseurl; ?>/attributes/download/<?php echo h($item['id']); ?>"><i class="fa fa-download"></i> Download</a></li>
                                    <?php endif; ?>
                                    
                                    <!-- Enrichment -->
                                    <li class="divider"></li>
                                    <?php if (!$isObject): ?>
                                        <li><a href="#" onclick="simplePopup('<?php echo $baseurl;?>/events/queryEnrichment/<?php echo h($item['id']); ?>/0/Enrichment/Attribute');"><i class="fa fa-magic"></i> Enrich</a></li>
                                    <?php endif; ?>

                                    <!-- Delete -->
                                    <li class="divider"></li>
                                    <li><a href="#" class="text-danger" onclick="deleteObject('<?php echo $isObject ? 'objects' : 'attributes'; ?>', 'delete', '<?php echo h($item['id']); ?>')"><i class="fa fa-trash"></i> Delete</a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </td>
                    
                    <!-- IDS Toggle -->
                    <td style="text-align: center;">
                        <?php if (!$isObject): ?>
                            <i class="fa fa-shield-alt beta-ids-toggle" 
                               style="font-size: 1.5em; cursor: <?= ($mayModify ? 'pointer' : 'default') ?>; <?= ($item['to_ids'] ? 'color: #ff8c00;' : 'opacity: 0.2;') ?>" 
                               data-id="<?= h($item['id']) ?>"
                               data-to-ids="<?= (int)$item['to_ids'] ?>"
                               title="<?= ($item['to_ids'] ? __('Recommended for blocking / alerting') : __('Not recommended for blocking / alerting')) ?>"></i>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Type / Object Name -->
                    <td>
                        <?php if ($isObject): ?>
                            <span class="object-title"><i class="fa fa-cubes"></i> <?php echo h($item['name']); ?></span>
                            <div style="font-size: 10px; color: #999;"><?php echo count($item['Attribute']); ?> attributes</div>
                        <?php else: ?>
                            <span class="attr-icon"><i class="fa fa-cube"></i></span> <?php echo h($item['type']); ?>
                        <?php endif; ?>
                    </td>

                    <!-- Value -->
                    <td>
                         <?php if ($isObject): ?>
                             <!-- Display first few attributes summary or description if available -->
                             <span class="text-muted"><?php echo h($item['description']); ?></span>
                         <?php else: ?>
                            <span class="attr-value"><?php echo h($item['value']); ?></span>
                         <?php endif; ?>
                    </td>

                    <!-- Context (Date) -->
                    <td>
                        <div style="font-size: 11px; color:#555;">
                            <?php echo $this->Time->time($item['timestamp']); ?>
                        </div>
                    </td>

                    <!-- Tags -->
                    <td>
                        <?php if (!empty($item['AttributeTag'])): ?>
                            <?php foreach ($item['AttributeTag'] as $tag): ?>
                                <span class="attr-tag" style="background-color:<?php echo h($tag['Tag']['colour']); ?>; color:<?php echo h($tag['Tag']['is_complex'] ? '#fff' : '#000'); ?>">
                                    <?php echo h($tag['Tag']['name']); ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                
                <!-- Expanded Object Attributes -->
                <?php if ($isObject && !empty($item['Attribute'])): ?>
                    <?php foreach ($item['Attribute'] as $subAttr): ?>
                        <tr class="beta-attr-row object-attr-row" data-object-type="attribute" data-attribute-type="<?php echo h($subAttr['type']); ?>" data-parent-object="<?php echo $dataName; ?>">
                            <td style="border-left: 3px solid #e1f0fa; padding-left: 20px; position: relative;">
                                 <!-- Checkbox & Actions for Sub-Attribute -->
                                 <div class="beta-row-actions">
                                    <input type="checkbox" class="select-row" value="<?php echo h($subAttr['id']); ?>">
                                    <div class="beta-row-menu-trigger">
                                        <i class="fa fa-caret-down"></i>
                                    </div>
                                    <div class="beta-row-menu">
                                        <ul>
                                             <?php if ($mayModify): ?>
                                                <li><a href="<?php echo $baseurl; ?>/attributes/edit/<?php echo h($subAttr['id']); ?>"><i class="fa fa-edit"></i> Edit</a></li>
                                                <li><a href="<?php echo $baseurl; ?>/attributes/download/<?php echo h($subAttr['id']); ?>"><i class="fa fa-download"></i> Download</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" class="text-danger" onclick="deleteObject('attributes', 'delete', '<?php echo h($subAttr['id']); ?>')"><i class="fa fa-trash"></i> Delete</a></li>
                                             <?php endif; ?>
                                        </ul>
                                    </div>
                                 </div>
                            </td>
                            <!-- IDS Toggle for Sub-Attribute -->
                            <td style="text-align: center; border-left: 3px solid #e1f0fa;">
                                <i class="fa fa-shield-alt beta-ids-toggle" 
                                   style="font-size: 1.5em; cursor: <?= ($mayModify ? 'pointer' : 'default') ?>; <?= ($subAttr['to_ids'] ? 'color: #ff8c00;' : 'opacity: 0.2;') ?>" 
                                   data-id="<?= h($subAttr['id']) ?>"
                                   data-to-ids="<?= (int)$subAttr['to_ids'] ?>"
                                   title="<?= ($subAttr['to_ids'] ? __('Recommended for blocking / alerting') : __('Not recommended for blocking / alerting')) ?>"></i>
                            </td>
                            <td><span class="text-muted"><i class="fa fa-level-up fa-rotate-90"></i> <?php echo h($subAttr['type']); ?></span></td>
                            <td><span class="attr-value"><?php echo h($subAttr['value']); ?></span></td>
                            <td><div style="font-size: 10px; color:#999;"><?php echo $this->Time->time($subAttr['timestamp']); ?></div></td>
                            <td>
                                 <?php if (!empty($subAttr['AttributeTag'])): ?>
                                    <?php foreach ($subAttr['AttributeTag'] as $tag): ?>
                                        <span class="attr-tag" style="background-color:<?php echo h($tag['Tag']['colour']); ?>; color:<?php echo h($tag['Tag']['is_complex'] ? '#fff' : '#000'); ?>">
                                            <?php echo h($tag['Tag']['name']); ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php endforeach; ?>
        </tbody>
    </table>
</div>
    <script>
    var currentUri = "<?php echo isset($currentUri) ? h($currentUri) : $baseurl . '/events/viewEventAttributes/' . h($event['Event']['id']); ?>";
    
    $(function() {
        // Dropdown handling
        $(document).on('click', '.beta-row-menu-trigger', function(e) {
            e.stopPropagation();
            var menu = $(this).next('.beta-row-menu');
            $('.beta-row-menu').not(menu).hide(); // Close others
            menu.toggle();
        });

        $(document).on('click', function() {
            $('.beta-row-menu').hide();
        });

        // Prevent closing when clicking inside the menu
        $(document).on('click', '.beta-row-menu', function(e) {
            e.stopPropagation();
        });
        
        // Select All checkboxes
        $('.select-all').change(function() {
            var checked = $(this).prop('checked');
            $('.select-row').prop('checked', checked);
        });

        // IDS Toggle handling
        $('.beta-ids-toggle').on('click', function() {
            <?php if (!$mayModify): ?>
                return false;
            <?php else: ?>
                var $this = $(this);
                var id = $this.data('id');
                var currentStatus = $this.data('to-ids');
                var newStatus = currentStatus === 1 ? 0 : 1;
                
                xhr({
                    url: "/attributes/editField/" + id,
                    type: "POST",
                    data: {
                        'Attribute': {
                            'to_ids': newStatus
                        }
                    },
                    success: function(data) {
                        if (typeof data === 'string') {
                            try {
                                data = JSON.parse(data);
                            } catch (e) {
                                showMessage('fail', 'Invalid response from server.');
                                return;
                            }
                        }
                        if (data.saved) {
                            $this.data('to-ids', newStatus);
                            if (newStatus === 1) {
                                $this.removeClass('text-muted');
                                $this.css({
                                    'color': '#ff8c00',
                                    'opacity': '1'
                                });
                                $this.attr('title', '<?= __('Recommended for blocking / alerting') ?>');
                            } else {
                                $this.addClass('text-muted');
                                $this.css({
                                    'color': '',
                                    'opacity': '0.2'
                                });
                                $this.attr('title', '<?= __('Not recommended for blocking / alerting') ?>');
                            }
                            showMessage('success', 'IDS flag updated.');
                            if (typeof eventUnpublish === 'function') {
                                eventUnpublish();
                            }
                        } else {
                            var errorMsg = 'Failed to update IDS flag.';
                            if (data.errors) {
                                if (typeof data.errors === 'string') errorMsg += ' ' + data.errors;
                                else if (typeof data.errors === 'object') {
                                    for (var key in data.errors) {
                                        errorMsg += ' ' + data.errors[key];
                                    }
                                }
                            }
                            showMessage('fail', errorMsg);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showMessage('fail', 'An error occurred while updating the IDS flag: ' + textStatus);
                    }
                });
            <?php endif; ?>
        });

        <?php
            if (isset($focus)):
        ?>
        focusObjectByUuid('<?= h($focus); ?>');
        <?php
            endif;
        ?>
        popoverStartup();
    });
    </script>
