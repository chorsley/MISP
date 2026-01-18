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
        padding: 3px 8px;
        font-size: 12px;
        border-radius: 3px;
        margin-right: 4px;
        margin-bottom: 2px;
        border: 1px solid transparent;
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
    .beta-sub-row {
        background-color: #fff;
    }
    .beta-sub-row td {
        padding: 4px 10px 8px 60px;
        border-bottom: 1px solid #f0f0f0;
    }
    .beta-tags-container, .beta-galaxies-container {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    .beta-galaxy-cluster {
        display: flex;
        align-items: center;
        background: #fcfcfc;
        border: 1px solid #eee;
        border-radius: 4px;
        padding: 4px 10px;
        font-size: 12px;
    }
    .beta-galaxy-cluster-label {
        font-weight: bold;
        color: #777;
        margin-right: 5px;
        text-transform: uppercase;
        font-size: 11px;
    }
    .beta-sighting-alert {
        color: #d9534f;
        font-weight: bold;
        font-size: 14px;
    }
    .beta-uuid-compact {
        font-size: 10px;
        color: #bbb;
        cursor: pointer;
        margin-left: 5px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .beta-attr-row:hover .beta-uuid-compact {
        opacity: 1;
    }
    .beta-tagging-links {
        display: none;
        margin-top: 4px;
        font-size: 0.9em;
        align-items: center;
        gap: 6px;
        color: #888;
    }
    .beta-attr-row:hover .beta-tagging-links,
    .object-attribute-row:hover .beta-tagging-links {
        display: flex;
    }
    .beta-tagging-label {
        font-weight: 600;
        margin-right: 2px;
        color: #666;
    }
    .beta-tag-link {
        color: #444;
        text-decoration: none;
        padding: 3px 6px;
        border-radius: 3px;
        background: #f0f0f0;
        border: 1px solid #ddd;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 11px;
    }
    .beta-tag-link:hover {
        background: #e0e0e0;
        color: #000;
        border-color: #ccc;
    }
    .beta-tag-link i {
        font-size: 0.9em;
    }
    .col-comment {
        font-style: italic;
        color: #555;
        font-size: 12px;
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .beta-attr-row:hover .col-comment {
        white-space: normal;
        overflow: visible;
    }
    .beta-columns-menu {
        min-width: 180px;
    }
</style>

<div class="beta-attributes-list">
    <!-- Toolbar -->
    <div class="beta-toolbar clearfix" style="margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between;">
        <div class="pull-left" style="display: flex; gap: 10px; align-items: center;">
             <?php if ($mayModify): ?>
                <a href="<?php echo $baseurl; ?>/attributes/add/<?php echo h($event['Event']['id']); ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?php echo __('Add Attribute'); ?></a>
                <a href="#" onclick="getPopup('<?php echo h($event['Event']['id']); ?>', 'objects', 'add'); return false;" class="btn btn-primary btn-sm"><i class="fa fa-cube"></i> <?php echo __('Add Object'); ?></a>
            <?php endif; ?>
            
            <button id="btn-toggle-all" class="btn btn-default btn-sm" onclick="toggleAllObjectsAttributes()"><i class="fa fa-expand"></i> <span id="label-toggle-all"><?php echo __('Expand All'); ?></span></button>
            
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-columns"></i> <?php echo __('Columns'); ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu beta-columns-menu">
                    <?php 
                        $cols = [
                            'category' => __('Category'),
                            'date' => __('Date'),
                            'sightings' => __('Sightings'),
                            'distribution' => __('Distribution'),
                            'correlation' => __('Correlation'),
                            'related' => __('Related Events'),
                            'comment' => __('Comment'),
                            'tags' => __('Tags'),
                            'galaxies' => __('Galaxies'),
                        ];
                        foreach ($cols as $id => $label):
                    ?>
                        <li>
                            <a href="#" onclick="toggleBetaColumn('<?php echo h($id); ?>'); return false;">
                                <i class="fa fa-check col-check-<?php echo h($id); ?>"></i> <?php echo h($label); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="pull-right">
             <input type="text" id="beta-attr-search" placeholder="Enter value to search..." class="form-control input-sm" style="display:inline-block; width: 250px;">
        </div>
    </div>

    <table class="beta-attr-table">
        <thead>
            <tr>
                <th style="width: 50px;"><input type="checkbox" class="select-all"></th>
                <th style="width: 40px;" title="<?php echo __('Recommend for blocking / alerting?'); ?>">IDS</th>
                <th class="col-category"><?php echo __('Category'); ?></th>
                <th><?php echo __('Type / Object'); ?></th>
                <th><?php echo __('Value / Attributes'); ?></th>
                <th class="col-comment"><?php echo __('Comment'); ?></th>
                <th class="col-correlation" style="width: 40px;" title="<?php echo __('Correlation'); ?>"><i class="fa fa-project-diagram"></i></th>
                <th class="col-related"><?php echo __('Related'); ?></th>
                <th class="col-sightings" style="width: 30px;" title="<?php echo __('Sightings'); ?>"><i class="fa fa-eye"></i></th>
                <th class="col-distribution" style="width: 30px;" title="<?php echo __('Distribution'); ?>"><i class="fa fa-share-alt"></i></th>
                <th class="col-date"><?php echo __('Date'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <?php 
                    $isObject = $item['objectType'] === 'object';
                    $dataType = $isObject ? 'object' : 'attribute';
                    $dataName = $isObject ? $item['name'] : $item['type'];
                    $rowClass = $isObject ? 'object-header-row' : '';
                    
                    $isSighted = isset($sightingsData['data'][$item['id']]);
                    if (!$isObject && !$isSighted && isset($item['Sighting']) && !empty($item['Sighting'])) {
                        $isSighted = true;
                    }
                    $distColor = '#999';
                    if (isset($distributionLevels[$item['distribution']])) {
                        // Dist color mapping based on shortDist or similar
                        $distColors = [0 => '#555', 1 => '#428bca', 2 => '#f0ad4e', 3 => '#5cb85c', 4 => '#d9534f', 5 => '#333'];
                        $distColor = $distColors[$item['distribution']] ?? '#999';
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
                                    
                                    <!-- Proposals -->
                                    <li><a href="#" onclick="simplePopup('<?php echo $baseurl; ?>/shadow_attributes/add/<?php echo h($item['id']); ?>');"><i class="fa fa-comment-dots"></i> Propose Edit</a></li>

                                    <!-- Context Specific Actions -->
                                    <?php if (!$isObject && ($item['type'] == 'malware-sample' || $item['type'] == 'attachment')): ?>
                                        <li><a href="<?php echo $baseurl; ?>/attributes/download/<?php echo h($item['id']); ?>"><i class="fa fa-download"></i> Download</a></li>
                                    <?php endif; ?>
                                    
                                    <!-- Sightings Actions -->
                                    <li class="divider"></li>
                                    <li><a href="#" onclick="simplePopup('<?php echo $baseurl; ?>/sightings/add/<?php echo h($item['id']); ?>');"><i class="fa fa-eye"></i> Add Sighting</a></li>
                                    <li><a href="#" onclick="simplePopup('<?php echo $baseurl; ?>/sightings/setFalsePositive/<?php echo h($item['id']); ?>');"><i class="fa fa-eye-slash"></i> False Positive</a></li>
                                    <li><a href="#" class="sightings_advanced_add" data-object-id="<?php echo h($item['id']); ?>" data-object-context="<?php echo $isObject ? 'object' : 'attribute'; ?>"><i class="fa fa-wrench"></i> Advanced Sightings</a></li>

                                    <!-- Enrichment -->
                                    <li class="divider"></li>
                                    <?php if (!$isObject): ?>
                                        <li><a href="#" onclick="simplePopup('<?php echo $baseurl;?>/events/queryEnrichment/<?php echo h($item['id']); ?>/0/Enrichment/Attribute');"><i class="fa fa-magic"></i> Enrich</a></li>
                                    <?php endif; ?>

                                    <!-- Utilities -->
                                    <li class="divider"></li>
                                    <li><a href="#" onclick="copyToClipboard('<?php echo h($item['uuid']); ?>'); showMessage('success', 'UUID copied');"><i class="fa fa-copy"></i> Copy UUID</a></li>

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

                    <!-- Category -->
                    <td class="col-category">
                        <?php if (!$isObject): ?>
                            <span style="font-size: 11px; color: #777;"><?php echo h($item['category']); ?></span>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Type / Object Name -->
                    <td>
                        <?php if ($isObject): ?>
                            <span class="object-title"><i class="fa fa-cubes"></i> <?php echo h($item['name']); ?></span>
                            <div style="font-size: 10px; color: #999;"><?php echo count($item['Attribute']); ?> attributes</div>
                        <?php else: ?>
                            <span class="attr-icon"><i class="fa fa-cube"></i></span> <?php echo h($item['type']); ?>
                            <i class="fa fa-fingerprint beta-uuid-compact" title="<?php echo h($item['uuid']); ?>" onclick="copyToClipboard('<?php echo h($item['uuid']); ?>'); showMessage('success', 'UUID copied');"></i>
                        <?php endif; ?>
                    </td>

                    <!-- Value -->
                    <td>
                         <?php if ($isObject): ?>
                              <span class="text-muted"><?php echo h($item['description']); ?></span>
                         <?php else: ?>
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <span class="attr-value"><?php echo h($item['value']); ?></span>
                                <?php if (isset($item['warnings'])): ?>
                                    <?php
                                        $temp = '';
                                        foreach ($item['warnings'] as $warning) {
                                            $temp .= '<span class="bold">' . h($warning['match']) . ':</span> <span class="red">' . h($warning['warninglist_name']) . '</span>';
                                            if (isset($warning['comment'])) {
                                                $temp .= ' (' . h($warning['comment']) . ')';
                                            }
                                            $temp .= '<br>';
                                        }
                                    ?>
                                    <span aria-label="<?= __('warning') ?>" role="img" tabindex="0" class="fa fa-exclamation-triangle" style="color: #f0ad4e;" data-placement="right" data-toggle="popover" data-content="<?= h($temp) ?>" data-trigger="hover">&nbsp;</span>
                                <?php endif; ?>
                                <?php if ($mayModify): ?>
                                     <div class="beta-tagging-links">
                                        <span class="beta-tagging-label"><?= __('Tags:') ?></span>
                                        <a href="#" class="beta-tag-link" onclick="getPopup('<?php echo h($item['id']); ?>/attribute', 'tags', 'selectTaxonomy'); return false;" title="<?= __('Add Global Tag') ?>"><i class="fa fa-plus"></i><i class="fa fa-globe"></i></a>
                                        <a href="#" class="beta-tag-link" onclick="getPopup('local:1/<?php echo h($item['id']); ?>/attribute', 'tags', 'selectTaxonomy'); return false;" title="<?= __('Add Local Tag') ?>"><i class="fa fa-plus"></i><i class="fa fa-user"></i></a>
                                        <span class="beta-tagging-label" style="margin-left: 5px;"><?= __('Galaxies:') ?></span>
                                        <a href="#" class="beta-tag-link" onclick="getPopup('<?php echo h($item['id']); ?>/attribute/local:0', 'galaxies', 'selectGalaxyNamespace'); return false;" title="<?= __('Add Galaxy Cluster') ?>"><i class="fa fa-plus"></i><i class="fa fa-globe"></i></a>
                                        <a href="#" class="beta-tag-link" onclick="getPopup('<?php echo h($item['id']); ?>/attribute/local:1', 'galaxies', 'selectGalaxyNamespace'); return false;" title="<?= __('Add Local Galaxy Cluster') ?>"><i class="fa fa-plus"></i><i class="fa fa-user"></i></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                         <?php endif; ?>
                    </td>

                    <!-- Comment -->
                    <td class="col-comment">
                        <?php echo h($item['comment'] ?? ''); ?>
                    </td>

                    <!-- Correlation Toggle -->
                    <td class="col-correlation" style="text-align: center;">
                        <?php if (!$isObject): ?>
                            <i class="fa fa-project-diagram beta-correlation-toggle" 
                               style="cursor: <?= ($mayModify ? 'pointer' : 'default') ?>; <?= ($item['disable_correlation'] ? 'opacity: 0.2;' : 'color: #428bca;') ?>"
                               data-id="<?= h($item['id']) ?>"
                               data-disable-correlation="<?= (int)$item['disable_correlation'] ?>"
                               title="<?= ($item['disable_correlation'] ? __('Correlation disabled') : __('Correlation enabled')) ?>"></i>
                        <?php endif; ?>
                    </td>

                    <!-- Related Events -->
                    <td class="col-related">
                        <?php 
                            $relatedCount = 0;
                            if (isset($item['RelatedAttribute'])) {
                                $relatedCount = count($item['RelatedAttribute']);
                            }
                        ?>
                        <?php if ($relatedCount > 0): ?>
                            <button class="btn btn-default btn-xs" onclick="showRelatedMenu(this, '<?php echo h($item['id']); ?>')"><?php echo $relatedCount; ?></button>
                        <?php endif; ?>
                    </td>

                    <!-- Sightings -->
                    <td class="col-sightings" style="text-align: center;">
                        <?php if ($isSighted): ?>
                            <span class="beta-sighting-alert" title="<?php echo __('Sighted'); ?>">!</span>
                        <?php endif; ?>
                    </td>

                    <!-- Distribution -->
                    <td class="col-distribution" style="text-align: center;">
                        <i class="fa fa-circle" style="color: <?php echo $distColor; ?>;" title="<?php echo h($distributionLevels[$item['distribution']] ?? ''); ?>"></i>
                    </td>

                    <!-- Date -->
                    <td class="col-date">
                        <?php 
                            $showDate = true;
                            if (isset($event['Event']['date']) && date('Y-m-d', $item['timestamp']) === $event['Event']['date']) {
                                $showDate = false;
                            }
                            $isNew = isset($event['Event']['publish_timestamp']) && $item['timestamp'] > $event['Event']['publish_timestamp'];
                        ?>
                        <div style="font-size: 11px; color:#555;">
                            <?php if ($showDate): ?>
                                <?php echo date('Y-m-d', $item['timestamp']); ?>
                            <?php else: ?>
                                <?php echo date('H:i', $item['timestamp']); ?>
                            <?php endif; ?>
                            <?php if ($isNew): ?>
                                <span class="bold red" title="<?= __('Element or modification to an existing element has not been published yet.') ?>">*</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>

                <!-- Tags Sub-row -->
                <?php if (!empty($item['AttributeTag'])): ?>
                <tr class="beta-sub-row col-tags-row">
                    <td colspan="11">
                        <div class="beta-tags-container">
                            <?php foreach ($item['AttributeTag'] as $tag): ?>
                                <span class="attr-tag" style="background-color:<?php echo h($tag['Tag']['colour']); ?>; color:<?php echo $this->TextColour->getTextColour($tag['Tag']['colour']); ?>">
                                    <?php echo h($tag['Tag']['name']); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- Galaxies Sub-row -->
                <?php if (!empty($item['Galaxy'])): ?>
                <tr class="beta-sub-row col-galaxies-row">
                    <td colspan="11">
                        <div class="beta-galaxies-container">
                            <?php 
                                $clusters = [];
                                foreach ($item['Galaxy'] as $galaxy) {
                                    foreach ($galaxy['GalaxyCluster'] as $cluster) {
                                        $clusters[$galaxy['name']][] = $cluster['tag_name'];
                                    }
                                }
                                foreach ($clusters as $galaxyName => $clusterTags):
                            ?>
                                <div class="beta-galaxy-cluster">
                                    <span class="beta-galaxy-cluster-label"><?php echo h($galaxyName); ?>:</span>
                                    <?php echo implode(', ', array_map('h', $clusterTags)); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                
                <!-- Expanded Object Attributes -->
                <?php if ($isObject && !empty($item['Attribute'])): ?>
                    <?php foreach ($item['Attribute'] as $subAttr): ?>
                        <?php 
                            $isSightedSub = isset($sightingsData['data'][$subAttr['id']]);
                            if (!$isSightedSub && isset($subAttr['Sighting']) && !empty($subAttr['Sighting'])) {
                                $isSightedSub = true;
                            }
                            $subDistColor = '#999';
                            if (isset($distributionLevels[$subAttr['distribution']])) {
                                $subDistColors = [0 => '#555', 1 => '#428bca', 2 => '#f0ad4e', 3 => '#5cb85c', 4 => '#d9534f', 5 => '#333'];
                                $subDistColor = $subDistColors[$subAttr['distribution']] ?? '#999';
                            }
                        ?>
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
                                                <li><a href="#" onclick="simplePopup('<?php echo $baseurl; ?>/shadow_attributes/add/<?php echo h($subAttr['id']); ?>');"><i class="fa fa-comment-dots"></i> Propose Edit</a></li>
                                                <li><a href="<?php echo $baseurl; ?>/attributes/download/<?php echo h($subAttr['id']); ?>"><i class="fa fa-download"></i> Download</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onclick="simplePopup('<?php echo $baseurl; ?>/sightings/add/<?php echo h($subAttr['id']); ?>');"><i class="fa fa-eye"></i> Add Sighting</a></li>
                                                <li><a href="#" onclick="simplePopup('<?php echo $baseurl; ?>/sightings/setFalsePositive/<?php echo h($subAttr['id']); ?>');"><i class="fa fa-eye-slash"></i> False Positive</a></li>
                                                <li><a href="#" class="sightings_advanced_add" data-object-id="<?php echo h($subAttr['id']); ?>" data-object-context="attribute"><i class="fa fa-wrench"></i> Advanced Sightings</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onclick="copyToClipboard('<?php echo h($subAttr['uuid']); ?>'); showMessage('success', 'UUID copied');"><i class="fa fa-copy"></i> Copy UUID</a></li>
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
                            <!-- Category -->
                            <td class="col-category"><span style="font-size: 11px; color: #777;"><?php echo h($subAttr['category']); ?></span></td>
                            <!-- Type -->
                            <td><span class="text-muted"><i class="fa fa-level-up fa-rotate-90"></i> <?php echo h($subAttr['type']); ?></span>
                                <i class="fa fa-fingerprint beta-uuid-compact" title="<?php echo h($subAttr['uuid']); ?>" onclick="copyToClipboard('<?php echo h($subAttr['uuid']); ?>'); showMessage('success', 'UUID copied');"></i>
                            </td>
                            <!-- Value -->
                            <td>
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <span class="attr-value"><?php echo h($subAttr['value']); ?></span>
                                    <?php if (isset($subAttr['warnings'])): ?>
                                        <?php
                                            $temp = '';
                                            foreach ($subAttr['warnings'] as $warning) {
                                                $temp .= '<span class="bold">' . h($warning['match']) . ':</span> <span class="red">' . h($warning['warninglist_name']) . '</span>';
                                                if (isset($warning['comment'])) {
                                                    $temp .= ' (' . h($warning['comment']) . ')';
                                                }
                                                $temp .= '<br>';
                                            }
                                        ?>
                                        <span aria-label="<?= __('warning') ?>" role="img" tabindex="0" class="fa fa-exclamation-triangle" style="color: #f0ad4e;" data-placement="right" data-toggle="popover" data-content="<?= h($temp) ?>" data-trigger="hover">&nbsp;</span>
                                    <?php endif; ?>
                                    <?php if ($mayModify): ?>
                                         <div class="beta-tagging-links">
                                            <span class="beta-tagging-label"><?= __('Tags:') ?></span>
                                            <a href="#" class="beta-tag-link" onclick="getPopup('<?php echo h($subAttr['id']); ?>/attribute', 'tags', 'selectTaxonomy'); return false;" title="<?= __('Add Global Tag') ?>"><i class="fa fa-plus"></i><i class="fa fa-globe"></i></a>
                                            <a href="#" class="beta-tag-link" onclick="getPopup('local:1/<?php echo h($subAttr['id']); ?>/attribute', 'tags', 'selectTaxonomy'); return false;" title="<?= __('Add Local Tag') ?>"><i class="fa fa-plus"></i><i class="fa fa-user"></i></a>
                                            <span class="beta-tagging-label" style="margin-left: 5px;"><?= __('Galaxies:') ?></span>
                                            <a href="#" class="beta-tag-link" onclick="getPopup('<?php echo h($subAttr['id']); ?>/attribute/local:0', 'galaxies', 'selectGalaxyNamespace'); return false;" title="<?= __('Add Galaxy Cluster') ?>"><i class="fa fa-plus"></i><i class="fa fa-globe"></i></a>
                                            <a href="#" class="beta-tag-link" onclick="getPopup('<?php echo h($subAttr['id']); ?>/attribute/local:1', 'galaxies', 'selectGalaxyNamespace'); return false;" title="<?= __('Add Local Galaxy Cluster') ?>"><i class="fa fa-plus"></i><i class="fa fa-user"></i></a>
                                          </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <!-- Comment -->
                            <td class="col-comment">
                                <?php echo h($subAttr['comment'] ?? ''); ?>
                            </td>
                            <!-- Correlation -->
                            <td class="col-correlation" style="text-align: center;">
                                <i class="fa fa-project-diagram beta-correlation-toggle" 
                                   style="cursor: <?= ($mayModify ? 'pointer' : 'default') ?>; <?= ($subAttr['disable_correlation'] ? 'opacity: 0.2;' : 'color: #428bca;') ?>"
                                   data-id="<?= h($subAttr['id']) ?>"
                                   data-disable-correlation="<?= (int)$subAttr['disable_correlation'] ?>"
                                   title="<?= ($subAttr['disable_correlation'] ? __('Correlation disabled') : __('Correlation enabled')) ?>"></i>
                            </td>
                            <!-- Related -->
                            <td class="col-related">
                                <?php 
                                    $subRelatedCount = 0;
                                    if (isset($subAttr['RelatedAttribute'])) {
                                        $subRelatedCount = count($subAttr['RelatedAttribute']);
                                    }
                                ?>
                                <?php if ($subRelatedCount > 0): ?>
                                    <button class="btn btn-default btn-xs" onclick="showRelatedMenu(this, '<?php echo h($subAttr['id']); ?>')"><?php echo $subRelatedCount; ?></button>
                                <?php endif; ?>
                            </td>
                            <!-- Sightings -->
                            <td class="col-sightings" style="text-align: center;">
                                <?php if ($isSightedSub): ?>
                                    <span class="beta-sighting-alert" title="<?php echo __('Sighted'); ?>">!</span>
                                <?php endif; ?>
                            </td>
                            <!-- Distribution -->
                            <td class="col-distribution" style="text-align: center;">
                                <i class="fa fa-circle" style="color: <?php echo $subDistColor; ?>;" title="<?php echo h($distributionLevels[$subAttr['distribution']] ?? ''); ?>"></i>
                            </td>
                            <!-- Date -->
                            <td class="col-date">
                                <?php 
                                    $showSubDate = true;
                                    if (isset($event['Event']['date']) && date('Y-m-d', $subAttr['timestamp']) === $event['Event']['date']) {
                                        $showSubDate = false;
                                    }
                                    $isSubNew = isset($event['Event']['publish_timestamp']) && $subAttr['timestamp'] > $event['Event']['publish_timestamp'];
                                ?>
                                <div style="font-size: 10px; color:#999;">
                                    <?php if ($showSubDate): ?>
                                        <?php echo date('Y-m-d', $subAttr['timestamp']); ?>
                                    <?php else: ?>
                                        <?php echo date('H:i', $subAttr['timestamp']); ?>
                                    <?php endif; ?>
                                    <?php if ($isSubNew): ?>
                                        <span class="bold red" title="<?= __('Element or modification to an existing element has not been published yet.') ?>">*</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>

                        <!-- Sub-Attr Tags -->
                        <?php if (!empty($subAttr['AttributeTag'])): ?>
                        <tr class="beta-sub-row col-tags-row">
                            <td style="border-left: 3px solid #e1f0fa;"></td>
                            <td colspan="10">
                                <div class="beta-tags-container">
                                    <?php foreach ($subAttr['AttributeTag'] as $tag): ?>
                                        <span class="attr-tag" style="background-color:<?php echo h($tag['Tag']['colour']); ?>; color:<?php echo $this->TextColour->getTextColour($tag['Tag']['colour']); ?>">
                                            <?php echo h($tag['Tag']['name']); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>

                        <!-- Sub-Attr Galaxies -->
                        <?php if (!empty($subAttr['Galaxy'])): ?>
                        <tr class="beta-sub-row col-galaxies-row">
                            <td style="border-left: 3px solid #e1f0fa;"></td>
                            <td colspan="10">
                                <div class="beta-galaxies-container">
                                    <?php 
                                        $subClusters = [];
                                        foreach ($subAttr['Galaxy'] as $galaxy) {
                                            foreach ($galaxy['GalaxyCluster'] as $cluster) {
                                                $subClusters[$galaxy['name']][] = $cluster['tag_name'];
                                            }
                                        }
                                        foreach ($subClusters as $galaxyName => $clusterTags):
                                    ?>
                                        <div class="beta-galaxy-cluster">
                                            <span class="beta-galaxy-cluster-label"><?php echo h($galaxyName); ?>:</span>
                                            <?php echo implode(', ', array_map('h', $clusterTags)); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>

                    <?php endforeach; ?>
                <?php endif; ?>

            <?php endforeach; ?>
        </tbody>
    </table>
</div>
    <script>
    var currentUri = "<?php echo isset($currentUri) ? h($currentUri) : $baseurl . '/events/viewEventAttributes/' . h($event['Event']['id']); ?>";
    
    // Column state
    var betaColumns = {
        category: true,
        date: true,
        sightings: true,
        distribution: true,
        correlation: true,
        related: true,
        comment: true,
        tags: true,
        galaxies: true
    };

    function toggleBetaColumn(col) {
        betaColumns[col] = !betaColumns[col];
        $('.col-' + col).toggle(betaColumns[col]);
        $('.col-' + col + '-row').toggle(betaColumns[col]);
        $('.col-check-' + col).toggleClass('fa-check fa-times');
        
        // Adjust sub-row colspan if needed (though 11 is static for now)
    }

    var allExpanded = false;
    function toggleAllObjectsAttributes() {
        allExpanded = !allExpanded;
        if (allExpanded) {
            $('.object-attr-row, .col-tags-row, .col-galaxies-row').show();
            $('#btn-toggle-all i').removeClass('fa-expand').addClass('fa-compress');
            $('#label-toggle-all').text('Collapse All');
        } else {
            $('.object-attr-row, .col-tags-row, .col-galaxies-row').hide();
            $('#btn-toggle-all i').removeClass('fa-compress').addClass('fa-expand');
            $('#label-toggle-all').text('Expand All');
        }
    }

    function showRelatedMenu(el, attributeId) {
        // Fetch related events via AJAX if not already present or show existing menu
        // For now, let's assume we can fetch them
        getPopup(attributeId, 'attributes', 'relatedAttributes', '', '#confirmation_box');
    }

    $(function() {
        // Initial column sync
        Object.keys(betaColumns).forEach(function(col) {
            $('.col-' + col).toggle(betaColumns[col]);
            if (betaColumns[col]) {
                $('.col-check-' + col).addClass('fa-check').removeClass('fa-times');
            } else {
                $('.col-check-' + col).addClass('fa-times').removeClass('fa-check');
            }
        });

        // Search filtering
        $('#beta-attr-search').on('keyup', function() {
            var val = $(this).val().toLowerCase();
            $('.beta-attr-row').each(function() {
                var text = $(this).find('.attr-value').text().toLowerCase();
                if (text === "") text = $(this).find('.object-title').text().toLowerCase();
                $(this).toggle(text.indexOf(val) > -1);
                // Also toggle sub-rows if parent is hidden?
                // For simplicity, let's just filter by value
            });
        });

        // Individual Object Toggle
        $(document).on('click', '.object-header-row', function() {
            var name = $(this).data('object-name');
            $('.object-attr-row[data-parent-object="' + name + '"]').toggle();
            // Also toggle tags/galaxies for the object if any
            // (Objects don't usually have tags/galaxies directly in the sub-row but just in case)
        });

        // Correlation Toggle handling
        $(document).on('click', '.beta-correlation-toggle', function() {
            <?php if (!$mayModify): ?>
                return false;
            <?php else: ?>
                var $this = $(this);
                var id = $this.data('id');
                var currentStatus = $this.data('disable-correlation');
                var newStatus = currentStatus === 1 ? 0 : 1;
                
                xhr({
                    url: "/attributes/editField/" + id,
                    type: "POST",
                    data: {
                        'Attribute': {
                            'disable_correlation': newStatus
                        }
                    },
                    success: function(data) {
                        if (data.saved) {
                            $this.data('disable-correlation', newStatus);
                            if (newStatus === 1) {
                                $this.css({ 'opacity': '0.2', 'color': '' });
                                $this.attr('title', '<?= __('Correlation disabled') ?>');
                            } else {
                                $this.css({ 'opacity': '1', 'color': '#428bca' });
                                $this.attr('title', '<?= __('Correlation enabled') ?>');
                            }
                            showMessage('success', 'Correlation flag updated.');
                        } else {
                            showMessage('fail', 'Failed to update correlation flag.');
                        }
                    }
                });
            <?php endif; ?>
        });

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
