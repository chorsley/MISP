<?php
    // Prepare items
    $items = [];
    
    // Workaround for missing RelatedAttribute in attributes
    $relatedMap = [];
    if (!empty($event['RelatedAttribute'])) {
        foreach ($event['RelatedAttribute'] as $attrId => $relations) {
            $relatedMap[$attrId] = $relations;
        }
    }

    if (!empty($event['objects'])) {
        $items = [];
        $objectAttributeIds = [];
        foreach ($event['objects'] as $item) {
            if ($item['objectType'] === 'object') {
                if (!empty($item['Attribute'])) {
                    foreach ($item['Attribute'] as $objAttr) {
                        $objectAttributeIds[$objAttr['id']] = true;
                    }
                }
            }
        }
        foreach ($event['objects'] as $item) {
            if ($item['objectType'] === 'attribute' && isset($objectAttributeIds[$item['id']])) {
                continue;
            }
            $items[] = $item;
        }
    } else {
        $objectAttributeIds = [];
        if (!empty($event['Object'])) {
            foreach ($event['Object'] as $obj) {
                if (!empty($obj['Attribute'])) {
                    foreach ($obj['Attribute'] as $objAttr) {
                        $objectAttributeIds[$objAttr['id']] = true;
                    }
                }
            }
        }

        if (!empty($event['Attribute'])) {
            foreach ($event['Attribute'] as $attr) {
                if (isset($objectAttributeIds[$attr['id']])) {
                    continue;
                }
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

    // Attach RelatedAttribute if missing
    if (!empty($relatedMap)) {
        foreach ($items as &$item) {
            if (isset($item['objectType']) && $item['objectType'] === 'attribute') {
                if (isset($relatedMap[$item['id']])) {
                    $item['RelatedAttribute'] = $relatedMap[$item['id']];
                }
            } elseif (isset($item['objectType']) && $item['objectType'] === 'object' && !empty($item['Attribute'])) {
                foreach ($item['Attribute'] as &$subAttr) {
                    if (isset($relatedMap[$subAttr['id']])) {
                        $subAttr['RelatedAttribute'] = $relatedMap[$subAttr['id']];
                    }
                }
                unset($subAttr);
            }
        }
        unset($item);
    }

    // Sort desc by timestamp
    usort($items, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });

    // Beta pagination config
    $betaPageSize = 50;
    $betaTotalItems = count($items);
    $betaTotalPages = max(1, ceil($betaTotalItems / $betaPageSize));
?>

<style>
    .beta-attributes-list {
        padding: 0;
        box-sizing: border-box;
    }
    .beta-toolbar {
        margin-left: -10px;
        margin-right: -10px;
        justify-content: flex-start;
    }
    .beta-toolbar .pull-right {
        margin-left: auto;
    }
    .beta-attr-row:hover .beta-row-menu-trigger {
        visibility: visible;
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
        background-color: #ebf5fb;
    }
    .object-attr-row, .object-attr-row + .beta-sub-row {
        background-color: #f8fbfe; /* Even more subtle pale blue for object members */
    }
    .object-header-row td {
        padding-top: 8px;
        padding-bottom: 8px;
        border-top: 1px solid #d1e9f5;
        border-bottom: 1px solid #d1e9f5;
    }
    .object-header-row td:first-child {
        border-left: 4px solid #31708f;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }
    .object-header-row td:last-child {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
        border-right: 1px solid #d1e9f5;
    }
    .object-title {
        font-weight: bold;
        color: #31708f;
        font-size: 14px;
    }
    .object-label {
        background: #31708f;
        color: white;
        font-size: 9px;
        padding: 1px 4px;
        border-radius: 3px;
        text-transform: uppercase;
        vertical-align: middle;
        margin-right: 5px;
        letter-spacing: 0.5px;
    }
    .object-header-meta {
        text-align: right;
    }
    .object-desc {
        font-size: 11px;
        color: #999;
    }
    .object-attr-row td:first-child {
        /* border-left handled inline for positioning */
    }
    .beta-sub-row {
        /* background-color inherited from preceding row logic where possible, otherwise white */
        background-color: #fff;
    }
    .beta-sub-row td {
        padding: 4px 10px 8px 10px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: top;
        border-left: 4px solid transparent; /* Keep aligned */
    }
    .beta-tags-container, .beta-galaxies-container {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
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
    .beta-columns-menu {
        min-width: 180px;
    }

    /* Beta Pagination Styles */
    .beta-pagination-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        margin-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    .beta-pagination-info {
        font-size: 13px;
        color: #666;
    }
    .beta-pagination-info .beta-page-badge {
        display: inline-block;
        background: #428bca;
        color: #fff;
        padding: 2px 10px;
        border-radius: 3px;
        font-weight: 600;
        font-size: 12px;
    }
    .beta-pagination-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .beta-pagination-controls .btn {
        min-width: 36px;
    }
    .beta-pagination-controls .beta-page-size-select {
        width: auto;
        display: inline-block;
        padding: 4px 8px;
        font-size: 12px;
        height: auto;
    }
    .beta-pagination-bottom {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #eee;
    }
    
    /* Tree Structure */
    .tree-cell {
        position: relative;
        padding-left: 10px !important;
        border-left: 4px solid transparent !important;
    }
    .tree-cell::before {
        /* Vertical line */
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: -3px;
        width: 2px;
        background-color: #999;
    }
    .tree-cell::after {
        /* Horizontal line - only for rows with a checkbox */
        content: '';
        position: absolute;
        top: 15px; /* Aligned with checkbox center in top-aligned layout */
        left: -3px;
        width: 13px;
        height: 2px;
        background-color: #999;
    }
    .tree-cell.no-tick::after {
        display: none;
    }
    .tree-cell.last-item::before {
        bottom: auto;
        height: 16px; /* Ends at the horizontal line */
    }

    .standalone-attr-row {
        background-color: #fff;
    }

    /* New Card-like styles */
    .beta-attr-meta-block {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .beta-attr-type-path {
        font-size: 11px;
        color: #888;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 2px;
    }
    .beta-category-label {
        font-size: 10px;
        color: #aaa;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .beta-type-insight {
        background: #f0f0f0;
        padding: 1px 6px;
        border-radius: 10px;
        font-weight: 600;
        color: #444;
        border: 1px solid #e0e0e0;
    }
    .beta-object-relation-insight {
        background: #e8f4fd;
        padding: 1px 6px;
        border-radius: 10px;
        font-weight: 600;
        color: #2f5a93;
        border: 1px solid #d1e9f5;
    }
    .beta-attr-tags-inline {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 2px;
        opacity: 0.4;
        transition: opacity 0.2s;
    }
    .beta-attr-row:hover .beta-attr-tags-inline {
        opacity: 1;
    }
    .beta-attr-value-container {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding-top: 2px;
    }
    .beta-attr-comment-inline {
        font-size: 11px;
        color: #777;
        font-style: italic;
    }
</style>

<div class="beta-attributes-list">
    <!-- Toolbar -->
    <div class="beta-toolbar clearfix" style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-start;">
        <div class="pull-left" style="display: flex; gap: 10px; align-items: center;">
             <?php if ($mayModify): ?>
                <a href="<?php echo $baseurl; ?>/attributes/add/<?php echo h($event['Event']['id']); ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?php echo __('Add Attribute'); ?></a>
                <a href="#" onclick="getPopup('<?php echo h($event['Event']['id']); ?>', 'objectTemplates', 'objectMetaChoice'); return false;" class="btn btn-primary btn-sm"><i class="fa fa-cube"></i> <?php echo __('Add Object'); ?></a>
            <?php endif; ?>
            
            <button id="btn-toggle-all" class="btn btn-default btn-sm" onclick="toggleAllObjectsAttributes()"><i class="fa fa-expand"></i> <span id="label-toggle-all"><?php echo __('Expand All'); ?></span></button>
            
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-columns"></i> <?php echo __('Columns'); ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu beta-columns-menu">
                    <?php 
                        $cols = [
                            'date' => __('Date'),
                            'sightings' => __('Sightings'),
                            'distribution' => __('Distribution'),
                            'correlation' => __('Correlation'),
                            'related' => __('Corr.'),
                            'comment' => __('Comment'),
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

    <!-- Beta Pagination Controls (Top) -->
    <div class="beta-pagination-container" id="beta-pagination-top">
        <div class="beta-pagination-info">
            <span class="beta-page-badge" id="beta-page-badge-top"><?php echo __('Page 1 of %s', $betaTotalPages); ?></span>
            <span id="beta-page-item-info-top">(<?php echo __('Total %s items', $betaTotalItems); ?>)</span>
        </div>
        <div class="beta-pagination-controls">
            <button type="button" class="btn btn-default btn-sm beta-page-btn" data-page-action="first" id="beta-page-first" title="<?php echo __('First page'); ?>"><i class="fa fa-angle-double-left"></i></button>
            <button type="button" class="btn btn-default btn-sm beta-page-btn" data-page-action="prev" id="beta-page-prev" title="<?php echo __('Previous page'); ?>"><i class="fa fa-angle-left"></i></button>
            <span style="font-size: 12px; color: #666; min-width: 60px; text-align: center;" id="beta-page-num-display-top"><?php echo __('1 / %s', $betaTotalPages); ?></span>
            <button type="button" class="btn btn-default btn-sm beta-page-btn" data-page-action="next" id="beta-page-next" title="<?php echo __('Next page'); ?>"><i class="fa fa-angle-right"></i></button>
            <button type="button" class="btn btn-default btn-sm beta-page-btn" data-page-action="last" id="beta-page-last" title="<?php echo __('Last page'); ?>"><i class="fa fa-angle-double-right"></i></button>
            <select class="form-control beta-page-size-select" id="beta-page-size" title="<?php echo __('Items per page'); ?>">
                <option value="20" <?php echo $betaPageSize == 20 ? 'selected' : ''; ?>>20</option>
                <option value="50" <?php echo $betaPageSize == 50 ? 'selected' : ''; ?>>50</option>
                <option value="100" <?php echo $betaPageSize == 100 ? 'selected' : ''; ?>>100</option>
                <option value="200" <?php echo $betaPageSize == 200 ? 'selected' : ''; ?>>200</option>
                <option value="0"><?php echo __('All'); ?></option>
            </select>
        </div>
    </div>

    <table class="beta-attr-table">
        <thead>
            <tr>
                <th style="width: 40px;"><input type="checkbox" class="select-all"></th>
                <th colspan="2"><?php echo __('Attribute Details'); ?></th>
                <th class="col-related" style="width: 50px;"><?php echo __('Corr.'); ?></th>
                <th class="col-comment" style="width: 20%;"><?php echo __('Comment'); ?></th>
                <th style="width: 30px;" title="<?php echo __('Recommend for blocking / alerting?'); ?>">IDS</th>
                <th class="col-correlation" style="width: 30px;" title="<?php echo __('Correlation'); ?>"><i class="fa fa-project-diagram"></i></th>
                <th class="col-sightings" style="width: 30px;" title="<?php echo __('Sightings'); ?>"><i class="fa fa-eye"></i></th>
                <th class="col-distribution" style="width: 40px;" title="<?php echo __('Distribution'); ?>"><i class="fa fa-share-alt"></i></th>
                <th class="col-date" style="width: 80px;"><?php echo __('Date'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $betaItemIndex = 0; ?>
            <?php foreach ($items as $item): ?>
                <?php
                    $isObject = $item['objectType'] === 'object';
                    $dataType = $isObject ? 'object' : 'attribute';
                    $dataName = $isObject ? $item['name'] : $item['type'];
                    $rowClass = $isObject ? 'object-header-row' : 'standalone-attr-row';
                    
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
                    data-primary-id="<?php echo h($item['id']); ?>"
                    data-page-item-index="<?php echo $betaItemIndex; ?>"
                    <?php if ($isObject): ?>data-object-name="<?php echo $dataName; ?>"<?php else: ?>data-attribute-type="<?php echo $dataName; ?>"<?php endif; ?>>
                    
                    <!-- Checkbox & Actions Dropdown -->
                    <td style="position: relative;" <?php if ($isObject) echo 'colspan="3"'; ?>>
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center;">
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

                                <!-- Tagging / Galaxies -->
                                <?php if (!$isObject): ?>
                                    <li class="divider"></li>
                                    <li class="dropdown-submenu">
                                        <a href="#"><i class="fa fa-tag"></i> Add tag</a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" onclick="getPopup('local:1/<?php echo h($item['id']); ?>/attribute', 'tags', 'selectTaxonomy'); return false;"><i class="fa fa-user"></i> Local</a></li>
                                            <li><a href="#" onclick="getPopup('<?php echo h($item['id']); ?>/attribute', 'tags', 'selectTaxonomy'); return false;"><i class="fa fa-globe"></i> Global</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a href="#"><i class="fa fa-bahai"></i> Galaxies</a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" onclick="getPopup('<?php echo h($item['id']); ?>/attribute/local:1', 'galaxies', 'selectGalaxyNamespace'); return false;"><i class="fa fa-user"></i> Local</a></li>
                                            <li><a href="#" onclick="getPopup('<?php echo h($item['id']); ?>/attribute/local:0', 'galaxies', 'selectGalaxyNamespace'); return false;"><i class="fa fa-globe"></i> Global</a></li>
                                        </ul>
                                    </li>
                                <?php endif; ?>

                                <!-- Context Specific Actions -->
                                <?php if (!$isObject && ($item['type'] == 'malware-sample' || $item['type'] == 'attachment')): ?>
                                    <li class="divider"></li>
                                    <li><a href="<?php echo $baseurl; ?>/attributes/download/<?php echo h($item['id']); ?>"><i class="fa fa-download"></i> Download</a></li>
                                <?php endif; ?>
                                
                                <!-- Sightings Actions -->
                                <li class="divider"></li>
                                <li><a href="#" onclick="simplePopup('<?php echo $baseurl; ?>/sightings/add/<?php echo h($item['id']); ?>');"><i class="fa fa-eye"></i> Add Sighting</a></li>
                                <li><a href="#" onclick="simplePopup('<?php echo $baseurl; ?>/sightings/setFalsePositive/<?php echo h($item['id']); ?>');"><i class="fa fa-eye-slash"></i> False Positive</a></li>
                                <li><a href="#" class="sightings_advanced_add" data-object-id="<?php echo h($item['id']); ?>" data-object-context="<?php echo $isObject ? 'object' : 'attribute'; ?>"><i class="fa fa-wrench"></i> Advanced Sightings</a></li>

                                <!-- Enrichment -->
                                <?php if (!$isObject): ?>
                                    <li class="divider"></li>
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
                                <?php if ($isObject): ?>
                                    <span class="object-label">Object</span>
                                    <span class="object-title"><?php echo h($item['name']); ?></span>
                                    <span class="object-desc" style="margin-left: 10px;"><?php echo h($item['description']); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($isObject): ?>
                                <div class="object-header-meta">
                                    <div style="font-size: 10px; color: #999;"><?php echo count($item['Attribute']); ?> attributes</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    
                    <?php if (!$isObject): ?>
                        <!-- Metadata (Category > Type + Tags) -->
                        <td colspan="2">
                            <div class="beta-attr-meta-block">
                                <div class="beta-attr-type-path">
                                    <span class="beta-category-label"><?php echo h($item['category']); ?></span>
                                    <i class="fa fa-chevron-right" style="font-size: 8px; color: #ccc;"></i>
                                    <span class="beta-type-insight"><?php echo h($item['type']); ?></span>
                                    <i class="fa fa-fingerprint beta-uuid-compact" title="<?php echo h($item['uuid']); ?>" onclick="copyToClipboard('<?php echo h($item['uuid']); ?>'); showMessage('success', 'UUID copied');"></i>
                                </div>
                                
                                <div class="beta-attr-value-container">
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
                                         <div class="beta-tagging-links"></div>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($item['AttributeTag'])): ?>
                                    <div class="beta-attr-tags-inline">
                                        <?php echo $this->element('ajaxTags', [
                                            'attributeId' => $item['id'],
                                            'tags' => $item['AttributeTag'] ?? [],
                                            'tagAccess' => $mayModify,
                                            'localTagAccess' => $this->Acl->canModifyTag($event, true),
                                            'scope' => 'attribute',
                                            'tagConflicts' => $item['tagConflicts'] ?? [],
                                            'static_tags_only' => true,
                                        ]); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Galaxies Inline -->
                                <?php if (!empty($item['Galaxy'])): ?>
                                    <div class="beta-attr-tags-inline" style="margin-top: 4px;">
                                        <?php 
                                            $clustersByGalaxy = [];
                                            foreach ($item['Galaxy'] as $galaxy) {
                                                foreach ($galaxy['GalaxyCluster'] as $cluster) {
                                                    $clustersByGalaxy[$galaxy['name']][] = $cluster;
                                                }
                                            }
                                            foreach ($clustersByGalaxy as $galaxyName => $clusters):
                                                echo $this->element('Events/View/galaxy_compact_beta', [
                                                    'galaxyName' => $galaxyName,
                                                    'clusters' => $clusters,
                                                    'baseurl' => $baseurl
                                                ]);
                                            endforeach;
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
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
                                <span class="badge" title="<?php echo __('Show correlations'); ?>" style="cursor: pointer; background-color: #428bca;" onclick="filterCorrelations('<?php echo h($item['id']); ?>'); return false;"><?php echo $relatedCount; ?></span>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>

                    <!-- Comment -->
                    <td class="col-comment" <?php if ($isObject) echo 'colspan="5"'; ?>>
                        <?php 
                            $comment = $item['comment'] ?? '';
                            if (mb_strlen($comment) > 50) {
                                echo h(mb_substr($comment, 0, 50)) . '... ';
                                echo '<i class="fa fa-comment-dots" style="cursor: pointer;" onclick="event.stopPropagation();" data-toggle="popover" data-trigger="click" data-placement="top" data-content="' . nl2br(h($comment)) . '"></i>';
                            } else {
                                echo h($comment);
                            }
                        ?>
                    </td>

                    <?php if (!$isObject): ?>
                        <!-- IDS Toggle -->
                        <td style="text-align: center;">
                            <i class="fa fa-shield-alt beta-ids-toggle" 
                               style="font-size: 1.5em; cursor: <?= ($mayModify ? 'pointer' : 'default') ?>; <?= ($item['to_ids'] ? 'color: #ff8c00;' : 'opacity: 0.2;') ?>" 
                                data-id="<?= h($item['id']) ?>"
                                data-to-ids="<?= (int)$item['to_ids'] ?>"
                                title="<?= ($item['to_ids'] ? __('Recommended for blocking / alerting') : __('Not recommended for blocking / alerting')) ?>"></i>
                        </td>

                        <!-- Correlation Toggle -->
                        <td class="col-correlation" style="text-align: center;">
                            <i class="fa fa-project-diagram beta-correlation-toggle" 
                               style="cursor: <?= ($mayModify ? 'pointer' : 'default') ?>; <?= ($item['disable_correlation'] ? 'opacity: 0.2;' : 'color: #428bca;') ?>"
                               data-id="<?= h($item['id']) ?>"
                               data-disable-correlation="<?= (int)$item['disable_correlation'] ?>"
                               title="<?= ($item['disable_correlation'] ? __('Correlation disabled') : __('Correlation enabled')) ?>"></i>
                        </td>

                        <!-- Sightings -->
                        <td class="col-sightings" style="text-align: center;">
                            <?php if ($isSighted): ?>
                                <span class="beta-sighting-alert" title="<?php echo __('Sighted'); ?>">!</span>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>

                    <!-- Distribution -->
                    <td class="col-distribution" style="text-align: center;">
                        <div class="dist-widget dist-<?= intval($item['distribution']) ?> distributionNetworkToggle"
                             title="<?= $item['distribution'] == 4 ? h($item['SharingGroup']['name'] ?? '') : (isset($distributionLevels[$item['distribution']]) ? h($distributionLevels[$item['distribution']]) : '') ?>"
                             data-event-distribution="<?= intval($item['distribution']) ?>"
                             data-event-distribution-name="<?= $item['distribution'] == 4 ? h($item['SharingGroup']['name'] ?? '') : (isset($shortDist[$item['distribution']]) ? h($shortDist[$item['distribution']]) : '') ?>"
                             data-scope-id="<?= h($item['id']) ?>">
                        </div>
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

                <!-- No more sub-rows for Standalone Tags/Galaxies -->
                
                <!-- Expanded Object Attributes -->
                <?php if ($isObject && !empty($item['Attribute'])): ?>
                    <?php 
                        $totalAttrs = count($item['Attribute']);
                        $attrIndex = 0;
                    ?>
                    <?php foreach ($item['Attribute'] as $subAttr): ?>
                        <?php 
                            $attrIndex++;
                            $isLast = ($attrIndex === $totalAttrs);

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
                        <?php 
                            $hasTags = !empty($subAttr['AttributeTag']);
                            $hasGalaxies = !empty($subAttr['Galaxy']);
                            $attributeIsLast = $isLast && !$hasTags && !$hasGalaxies;
                        ?>
                        <tr class="beta-attr-row object-attr-row" data-object-type="attribute" data-attribute-type="<?php echo h($subAttr['type']); ?>" data-parent-object="<?php echo $dataName; ?>" data-page-item-index="<?php echo $betaItemIndex; ?>">
                            <td class="tree-cell <?php echo $attributeIsLast ? 'last-item' : ''; ?>">
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

                                                <li class="divider"></li>
                                                <li class="dropdown-submenu">
                                                    <a href="#"><i class="fa fa-tag"></i> Add tag</a>
                                                    <ul class="dropdown-menu">
                                                        <li><a href="#" onclick="getPopup('local:1/<?php echo h($subAttr['id']); ?>/attribute', 'tags', 'selectTaxonomy'); return false;"><i class="fa fa-user"></i> Local</a></li>
                                                        <li><a href="#" onclick="getPopup('<?php echo h($subAttr['id']); ?>/attribute', 'tags', 'selectTaxonomy'); return false;"><i class="fa fa-globe"></i> Global</a></li>
                                                    </ul>
                                                </li>
                                                <li class="dropdown-submenu">
                                                    <a href="#"><i class="fa fa-bahai"></i> Galaxies</a>
                                                    <ul class="dropdown-menu">
                                                        <li><a href="#" onclick="getPopup('<?php echo h($subAttr['id']); ?>/attribute/local:1', 'galaxies', 'selectGalaxyNamespace'); return false;"><i class="fa fa-user"></i> Local</a></li>
                                                        <li><a href="#" onclick="getPopup('<?php echo h($subAttr['id']); ?>/attribute/local:0', 'galaxies', 'selectGalaxyNamespace'); return false;"><i class="fa fa-globe"></i> Global</a></li>
                                                    </ul>
                                                </li>

                                                <li class="divider"></li>
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
                            
                            
                            <!-- Metadata (Category > Name (type) :: Description + Tags + Value) -->
                            <td colspan="2">
                                <div class="beta-attr-meta-block">
                                    <div class="beta-attr-type-path">
                                        <span class="beta-category-label"><?php echo h($subAttr['category']); ?></span>
                                        <i class="fa fa-chevron-right" style="font-size: 8px; color: #ccc;"></i>
                                        <span class="beta-object-relation-insight"><?php echo h($subAttr['object_relation']); ?></span>
                                        <span class="beta-type-insight"><?php echo h($subAttr['type']); ?></span>
                                        <?php if (!empty($subAttr['comment'])): ?>
                                            <span class="beta-attr-comment-inline">:: <?php echo h($subAttr['comment']); ?></span>
                                        <?php endif; ?>
                                        <i class="fa fa-fingerprint beta-uuid-compact" title="<?php echo h($subAttr['uuid']); ?>" onclick="copyToClipboard('<?php echo h($subAttr['uuid']); ?>'); showMessage('success', 'UUID copied');"></i>
                                    </div>

                                    <div class="beta-attr-value-container">
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
                                             <div class="beta-tagging-links"></div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($subAttr['AttributeTag'])): ?>
                                        <div class="beta-attr-tags-inline">
                                            <?php echo $this->element('ajaxTags', [
                                                'attributeId' => $subAttr['id'],
                                                'tags' => $subAttr['AttributeTag'] ?? [],
                                                'tagAccess' => $mayModify,
                                                'localTagAccess' => $this->Acl->canModifyTag($event, true),
                                                'scope' => 'attribute',
                                                'tagConflicts' => $subAttr['tagConflicts'] ?? [],
                                                'static_tags_only' => true,
                                            ]); ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Galaxies Inline -->
                                    <?php if (!empty($subAttr['Galaxy'])): ?>
                                        <div class="beta-attr-tags-inline" style="margin-top: 4px;">
                                            <?php 
                                                $subClustersByGalaxy = [];
                                                foreach ($subAttr['Galaxy'] as $galaxy) {
                                                    foreach ($galaxy['GalaxyCluster'] as $cluster) {
                                                        $subClustersByGalaxy[$galaxy['name']][] = $cluster;
                                                    }
                                                }
                                                foreach ($subClustersByGalaxy as $galaxyName => $clusters):
                                                    echo $this->element('Events/View/galaxy_compact_beta', [
                                                        'galaxyName' => $galaxyName,
                                                        'clusters' => $clusters,
                                                        'baseurl' => $baseurl
                                                    ]);
                                                endforeach;
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
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
                                    <span class="badge" style="cursor: pointer; background-color: #428bca;" onclick="filterCorrelations('<?php echo h($subAttr['id']); ?>'); return false;"><?php echo $subRelatedCount; ?></span>
                                <?php endif; ?>
                            </td>

                            <!-- Comment -->
                            <td class="col-comment">
                                <?php 
                                    $comment = $subAttr['comment'] ?? '';
                                    if (mb_strlen($comment) > 50) {
                                        echo h(mb_substr($comment, 0, 50)) . '... ';
                                        echo '<i class="fa fa-comment-dots" style="cursor: pointer;" onclick="event.stopPropagation();" data-toggle="popover" data-trigger="click" data-placement="top" data-content="' . nl2br(h($comment)) . '"></i>';
                                    } else {
                                        echo h($comment);
                                    }
                                ?>
                            </td>

                            <!-- IDS Toggle for Sub-Attribute -->
                            <td style="text-align: center;">
                                <i class="fa fa-shield-alt beta-ids-toggle" 
                                   style="font-size: 1.5em; cursor: <?= ($mayModify ? 'pointer' : 'default') ?>; <?= ($subAttr['to_ids'] ? 'color: #ff8c00;' : 'opacity: 0.2;') ?>" 
                                   data-id="<?= h($subAttr['id']) ?>"
                                   data-to-ids="<?= (int)$subAttr['to_ids'] ?>"
                                   title="<?= ($subAttr['to_ids'] ? __('Recommended for blocking / alerting') : __('Not recommended for blocking / alerting')) ?>"></i>
                            </td>

                            <!-- Correlation -->
                            <td class="col-correlation" style="text-align: center;">
                                <i class="fa fa-project-diagram beta-correlation-toggle" 
                                   style="cursor: <?= ($mayModify ? 'pointer' : 'default') ?>; <?= ($subAttr['disable_correlation'] ? 'opacity: 0.2;' : 'color: #428bca;') ?>"
                                   data-id="<?= h($subAttr['id']) ?>"
                                   data-disable-correlation="<?= (int)$subAttr['disable_correlation'] ?>"
                                   title="<?= ($subAttr['disable_correlation'] ? __('Correlation disabled') : __('Correlation enabled')) ?>"></i>
                            </td>

                            <!-- Sightings -->
                            <td class="col-sightings" style="text-align: center;">
                                <?php if ($isSightedSub): ?>
                                    <span class="beta-sighting-alert" title="<?php echo __('Sighted'); ?>">!</span>
                                <?php endif; ?>
                            </td>
                            <!-- Distribution -->
                            <td class="col-distribution" style="text-align: center;">
                                <div class="dist-widget dist-<?= intval($subAttr['distribution']) ?> distributionNetworkToggle"
                                     title="<?= $subAttr['distribution'] == 4 ? h($subAttr['SharingGroup']['name'] ?? '') : (isset($distributionLevels[$subAttr['distribution']]) ? h($distributionLevels[$subAttr['distribution']]) : '') ?>"
                                     data-event-distribution="<?= intval($subAttr['distribution']) ?>"
                                     data-event-distribution-name="<?= $subAttr['distribution'] == 4 ? h($subAttr['SharingGroup']['name'] ?? '') : (isset($shortDist[$subAttr['distribution']]) ? h($shortDist[$subAttr['distribution']]) : '') ?>"
                                     data-scope-id="<?= h($subAttr['id']) ?>">
                                </div>
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

                    <?php endforeach; ?>
                <?php endif; ?>

            <?php $betaItemIndex++; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Beta Pagination Controls (Bottom) -->
    <div class="beta-pagination-container beta-pagination-bottom" id="beta-pagination-bottom">
        <div class="beta-pagination-info">
            <span class="beta-page-badge" id="beta-page-badge-bottom"><?php echo __('Page 1 of %s', $betaTotalPages); ?></span>
            <span id="beta-page-item-info-bottom">(<?php echo __('Total %s items', $betaTotalItems); ?>)</span>
        </div>
        <div class="beta-pagination-controls">
            <button type="button" class="btn btn-default btn-sm beta-page-btn" data-page-action="first" title="<?php echo __('First page'); ?>"><i class="fa fa-angle-double-left"></i></button>
            <button type="button" class="btn btn-default btn-sm beta-page-btn" data-page-action="prev" title="<?php echo __('Previous page'); ?>"><i class="fa fa-angle-left"></i></button>
            <span style="font-size: 12px; color: #666; min-width: 60px; text-align: center;" id="beta-page-num-display-bottom"><?php echo __('1 / %s', $betaTotalPages); ?></span>
            <button type="button" class="btn btn-default btn-sm beta-page-btn" data-page-action="next" title="<?php echo __('Next page'); ?>"><i class="fa fa-angle-right"></i></button>
            <button type="button" class="btn btn-default btn-sm beta-page-btn" data-page-action="last" title="<?php echo __('Last page'); ?>"><i class="fa fa-angle-double-right"></i></button>
        </div>
    </div>
</div>
    <script>
    var currentUri = "<?php echo isset($currentUri) ? h($currentUri) : $baseurl . '/events/viewEventAttributes/' . h($event['Event']['id']); ?>";

    // ===== Beta Pagination Logic =====
    var betaPagination = {
        currentPage: 1,
        pageSize: <?php echo $betaPageSize; ?>,
        totalItems: <?php echo $betaTotalItems; ?>,
        totalPages: <?php echo $betaTotalPages; ?>,
        searchActive: false
    };

    function betaPaginationRecalc() {
        if (betaPagination.pageSize === 0) {
            betaPagination.totalPages = 1;
        } else {
            betaPagination.totalPages = Math.max(1, Math.ceil(betaPagination.totalItems / betaPagination.pageSize));
        }
        if (betaPagination.currentPage > betaPagination.totalPages) {
            betaPagination.currentPage = betaPagination.totalPages;
        }
        if (betaPagination.currentPage < 1) {
            betaPagination.currentPage = 1;
        }
    }

    function betaPaginationApply() {
        // If search is active, don't interfere with search filtering
        if (betaPagination.searchActive) {
            return;
        }

        var page = betaPagination.currentPage;
        var size = betaPagination.pageSize;

        // Show all rows first (reset)
        $('.beta-attr-table tbody tr[data-page-item-index]').each(function() {
            var idx = parseInt($(this).attr('data-page-item-index'), 10);
            if (size === 0) {
                // Show all
                $(this).show();
            } else {
                var startIdx = (page - 1) * size;
                var endIdx = startIdx + size - 1;
                if (idx >= startIdx && idx <= endIdx) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            }
        });

        betaPaginationUpdateUI();
    }

    function betaPaginationUpdateUI() {
        var page = betaPagination.currentPage;
        var total = betaPagination.totalPages;
        var size = betaPagination.pageSize;
        var totalItems = betaPagination.totalItems;

        var pageText = '<?php echo __('Page'); ?> ' + page + ' <?php echo __('of'); ?> ' + total;
        var itemStart = size === 0 ? 1 : ((page - 1) * size + 1);
        var itemEnd = size === 0 ? totalItems : Math.min(page * size, totalItems);
        var itemInfo = '(<?php echo __('Showing'); ?> ' + itemStart + '-' + itemEnd + ' <?php echo __('of'); ?> ' + totalItems + ' <?php echo __('items'); ?>)';

        // Update top controls
        $('#beta-page-badge-top').text(pageText);
        $('#beta-page-item-info-top').text(itemInfo);
        $('#beta-page-num-display-top').text(page + ' / ' + total);

        // Update bottom controls
        $('#beta-page-badge-bottom').text(pageText);
        $('#beta-page-item-info-bottom').text(itemInfo);
        $('#beta-page-num-display-bottom').text(page + ' / ' + total);

        // Enable/disable buttons
        var isFirst = (page <= 1);
        var isLast = (page >= total);
        $('#beta-page-first, #beta-page-prev').prop('disabled', isFirst);
        $('#beta-page-next, #beta-page-last').prop('disabled', isLast);
    }

    function betaPaginationGo(target) {
        if (target === 'prev') {
            betaPagination.currentPage = Math.max(1, betaPagination.currentPage - 1);
        } else if (target === 'next') {
            betaPagination.currentPage = Math.min(betaPagination.totalPages, betaPagination.currentPage + 1);
        } else if (target === 'last') {
            betaPagination.currentPage = betaPagination.totalPages;
        } else {
            betaPagination.currentPage = parseInt(target, 10) || 1;
        }
        betaPaginationApply();
    }

    function betaPaginationChangeSize(newSize) {
        betaPagination.pageSize = parseInt(newSize, 10);
        betaPagination.currentPage = 1;
        betaPaginationRecalc();
        betaPaginationApply();
    }
    // ===== End Beta Pagination Logic =====

    // Column state
    var betaColumns = {
        date: true,
        sightings: true,
        distribution: true,
        correlation: true,
        related: true,
        comment: true,
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

        // Apply initial pagination
        betaPaginationApply();

        // Pagination button click handlers (using delegated events for robustness)
        $(document).on('click', '.beta-page-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var action = $(this).data('page-action');
            if (action === 'first') {
                betaPaginationGo(1);
            } else {
                betaPaginationGo(action);
            }
        });

        // Page size change handler
        $(document).on('change', '#beta-page-size', function() {
            betaPaginationChangeSize($(this).val());
        });

        // Search filtering (disables pagination while searching)
        $('#beta-attr-search').on('keyup', function() {
            var val = $(this).val().toLowerCase();
            if (val.length > 0) {
                // Disable pagination during search
                betaPagination.searchActive = true;
                $('.beta-pagination-container').hide();
                $('.beta-attr-row').each(function() {
                    var text = $(this).find('.attr-value').text().toLowerCase();
                    if (text === "") text = $(this).find('.object-title').text().toLowerCase();
                    $(this).toggle(text.indexOf(val) > -1);
                });
            } else {
                // Re-enable pagination when search is cleared
                betaPagination.searchActive = false;
                $('.beta-pagination-container').show();
                betaPaginationApply();
            }
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
        // Cleanup orphans from previous executions
        $('.beta-row-menu.active-moved').remove();

        $(document).off('click', '.beta-row-menu-trigger').on('click', '.beta-row-menu-trigger', function(e) {
            e.stopPropagation();
            var trigger = $(this);
            
            // Check for existing active menu
            var activeMenu = $('.beta-row-menu.active-moved');
            if (activeMenu.length) {
                var oldTrigger = activeMenu.data('trigger');
                
                // Put it back
                activeMenu.hide().removeClass('active-moved').css({top: '', left: '', position: '', zIndex: ''});
                if (oldTrigger && oldTrigger[0].parentNode) {
                    oldTrigger.after(activeMenu);
                } else {
                    activeMenu.remove();
                }
                
                if (oldTrigger && oldTrigger[0] === trigger[0]) return; // Toggle off
            }

            var menu = trigger.next('.beta-row-menu');
            $('.beta-row-menu').not(menu).hide();

            // Move to body
            menu.addClass('active-moved').appendTo('body');
            menu.data('trigger', trigger);
            
            var offset = trigger.offset();
            var triggerHeight = trigger.outerHeight();
            
            menu.css({
                display: 'block',
                position: 'absolute',
                top: (offset.top + triggerHeight) + 'px',
                left: offset.left + 'px',
                zIndex: 10000
            });
            
            var rect = menu[0].getBoundingClientRect();
            var viewportHeight = window.innerHeight || document.documentElement.clientHeight;
            
            if (rect.bottom > viewportHeight) {
                 menu.css({
                    top: (offset.top - menu.outerHeight()) + 'px'
                 });
            }
        });

        $(document).off('click.betaMenuClose').on('click.betaMenuClose', function(e) {
            var activeMenu = $('.beta-row-menu.active-moved');
            if (activeMenu.length) {
                if ($(e.target).closest('.beta-row-menu.active-moved').length) return;
                
                var oldTrigger = activeMenu.data('trigger');
                activeMenu.hide().removeClass('active-moved').css({top: '', left: '', position: '', zIndex: ''});
                if (oldTrigger && oldTrigger[0].parentNode) {
                    oldTrigger.after(activeMenu);
                } else {
                    activeMenu.remove();
                }
            }
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
        $('.distributionNetworkToggle').each(function() {
            $(this).distributionNetwork({
                distributionData: <?= json_encode($distributionData, JSON_UNESCAPED_UNICODE); ?>,
            });
        });
        popoverStartup();
    });
    </script>
