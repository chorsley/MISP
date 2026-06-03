<?php
    echo $this->element('genericElements/assetLoader', [
        'css' => ['main-beta', 'components-beta', 'query-builder.default', 'attack_matrix', 'analyst-data'],
        'js' => ['doT', 'extendext', 'moment.min', 'query-builder', 'network-distribution-graph', 'd3', 'd3.custom', 'jquery-ui.min', 'beta-events-timestamps', 'd3-sankey.min'],
    ]);
?>

<style>
    .beta-view-events {
        padding: 20px;
        background-color: #f9f9f9;
        min-height: 100vh;
    }
    .beta-header-container {
        margin-bottom: 20px;
    }
    .beta-event-title {
        font-weight: 600;
        margin-top: 0;
        margin-bottom: 2px;
        color: #333;
    }
    .beta-event-subtitle {
        font-size: 12px;
        color: #888;
        margin-bottom: 10px;
    }
    .beta-id-badge {
        font-size: 0.6em;
        color: #999;
        font-weight: 400;
        vertical-align: middle;
    }
    .beta-event-meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
        margin-top: 15px;
    }
    .meta-box {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        padding: 5px 12px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 58px;
        box-sizing: border-box;
    }
    .meta-label {
        font-size: 10px;
        text-transform: uppercase;
        color: #999;
        font-weight: 600;
    }
    .meta-value {
        font-size: 14px;
        font-weight: 600;
        color: #444;
    }
    .beta-tabs {
        margin-top: 20px;
        border-bottom: 1px solid #ddd;
    }
    .beta-tabs > li > a {
        padding: 10px 20px;
        font-weight: 600;
        color: #666;
    }
    .beta-tab-content {
        background: #fff;
        border: 1px solid #ddd;
        border-top: none;
        padding: 20px;
        border-radius: 0 0 4px 4px;
    }
    .beta-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .beta-card-header {
        padding: 10px 15px;
        border-bottom: 1px solid #f0f0f0;
        font-weight: 600;
        background-color: #fbfbfb;
    }
    .beta-card-body {
        padding: 15px;
    }
    .beta-expandable-header {
        cursor: pointer;
        user-select: none;
    }
    .beta-expandable-header .fa {
        color: #7b8791;
    }
    .beta-header-count {
        color: #8a939c;
    }
    .beta-collections-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 2px;
    }
    .beta-collections-header-actions {
        display: inline-flex;
        gap: 8px;
        align-items: center;
    }
    .beta-collections-header-left {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .beta-collections-header-left .addButton {
        padding-top: 1px;
        padding-bottom: 1px;
        line-height: 1.2;
    }
    .beta-view-events .eventTagContainer .addButton,
    .beta-view-events .beta-collections-header-left .addButton {
        opacity: 0.6;
        transition: opacity 0.15s ease, filter 0.15s ease, box-shadow 0.15s ease;
    }
    .beta-view-events .eventTagContainer .addButton:hover,
    .beta-view-events .eventTagContainer .addButton:focus,
    .beta-view-events .beta-collections-header-left .addButton:hover,
    .beta-view-events .beta-collections-header-left .addButton:focus {
        opacity: 1;
        filter: brightness(1.08);
        box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.25) inset;
    }
    .beta-view-events #galaxies_div {
        position: static;
        padding: 0;
        background: transparent;
        border: 0;
        border-radius: 0;
        width: auto;
    }
    .beta-view-events #galaxies_div > .title-section {
        position: static;
        padding: 0;
        border: 0;
        background: transparent;
    }
    .comment-bullet-list {
        margin: 0;
        padding-left: 18px;
        list-style-type: disc;
    }
    .comment-bullet-item {
        margin: 0 0 6px 0;
        cursor: pointer;
        color: #2f2f2f;
    }
    .comment-bullet-item:hover {
        color: #0b6a9b;
    }
    .comment-bullet-label {
        font-size: 14px;
    }
    .comment-bullet-count {
        font-size: 12px;
        font-weight: 600;
        color: #5f6b76;
        margin-left: 4px;
    }
    .composition-singlebar-wrap {
        width: 100%;
    }
    .composition-singlebar {
        width: 100%;
        height: 30px;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #d7dfe8;
        background: #f7f9fc;
        display: flex;
    }
    .composition-singlebar .segment {
        height: 100%;
        min-width: 1px;
        cursor: pointer;
        transition: opacity 0.15s ease;
    }
    .composition-singlebar .segment:hover {
        opacity: 0.82;
    }
    .composition-label-grid {
        margin-top: 8px;
    }
    .composition-label-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }
    .composition-label-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        line-height: 1.2;
        color: #3c4854;
        border: 1px solid #d7dfe8;
        border-radius: 12px;
        padding: 3px 8px;
        background: #fbfdff;
        cursor: pointer;
        user-select: none;
    }
    .composition-label-chip:hover {
        background: #f0f6ff;
        border-color: #c2d2e6;
    }
    .composition-label-chip .swatch {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex: 0 0 8px;
    }
    .composition-label-chip strong {
        color: #26313d;
    }
    .composition-inline-label {
        font-size: 11px;
        font-weight: 600;
        fill: #ffffff;
        text-shadow: 0 1px 1px rgba(0, 0, 0, 0.35);
        pointer-events: none;
    }
    /* Toggle Switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 20px;
        vertical-align: middle;
    }
    .switch input { 
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 20px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #428bca;
    }
    input:focus + .slider {
        box-shadow: 0 0 1px #428bca;
    }
    input:checked + .slider:before {
        -webkit-transform: translateX(20px);
        -ms-transform: translateX(20px);
        transform: translateX(20px);
    }
    .publish-box .meta-value {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .edit-box .meta-value {
        display: flex;
        align-items: center;
    }
    .published-label {
        font-weight: 700;
        font-size: 12px;
        line-height: 1;
    }
    .published-label.state-published {
        color: #2f8f46;
    }
    .published-label.state-unpublished {
        color: #c0392b;
    }


    /* Common Beta UI Styles */
    .beta-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    .beta-attr-table {
        width: 100%;
        border-collapse: separate; 
        border-spacing: 0;
        margin-top: 10px;
        table-layout: fixed;
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
        padding: 8px 10px;
        border-bottom: 1px solid #f9f9f9;
        vertical-align: top;
        font-size: 13px;
    }
    .beta-attr-row:hover {
        background-color: #f5f5f5;
    }
    .beta-row-actions {
        display: flex;
        align-items: center;
        gap: 5px;
        position: relative;
    }
    .beta-row-menu-trigger {
        cursor: pointer;
        padding: 2px 6px;
        color: #777;
        border-radius: 3px;
        transition: background 0.2s;
    }
    .beta-row-menu-trigger:hover {
        background: #eee;
        color: #333;
    }
    .beta-row-menu {
        display: none;
        position: absolute;
        z-index: 10000;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        min-width: 160px;
        padding: 5px 0;
        right: 0;
        top: 100%;
    }
    .beta-row-menu.active-moved {
        right: auto;
        width: max-content;
        max-width: 320px;
    }
    .beta-row-menu ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .beta-row-menu li a {
        display: block;
        padding: 8px 15px;
        color: #444;
        text-decoration: none;
        font-size: 13px;
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
    .beta-uuid-compact {
        font-size: 10px;
        color: #bbb;
        cursor: pointer;
        font-family: monospace;
    }
    .beta-uuid-compact:hover {
        color: #428bca;
        text-decoration: underline;
    }
    .beta-tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    .beta-relative-timestamp {
        font-size: 12px;
        color: #666;
        cursor: pointer;
    }
    .beta-relative-timestamp:hover {
        text-decoration: underline;
    }
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
    .beta-id-badge {
        font-size: 0.8em;
        color: #999;
        font-weight: 400;
    }
    .report-snippet {
        font-family: inherit;
        line-height: 1.4;
    }
    .report-name-cell:hover {
        text-decoration: underline;
    }
</style>

<div class="events view beta-view-events">
    <!-- Header -->
    <div class="beta-header-container">
        <h2 class="beta-event-title">
            <?php echo h($event['Event']['info']); ?>
        </h2>
        <div class="beta-event-subtitle">
            ID: <?php echo h($event['Event']['id']); ?> / UUID: <?php echo h($event['Event']['uuid']); ?>
        </div>
        <div class="beta-event-meta-row">
            <span class="meta-box date-box">
                <span class="meta-label"><?php echo __('Event Date'); ?></span>
                <span class="meta-value"><?php echo h($event['Event']['date']); ?></span>
            </span>
            <span class="meta-box org-box">
                <span class="meta-label"><?php echo __('Creator Org'); ?></span>
                <span class="meta-value">
                     <a href="<?= $baseurl ?>/organisations/view/<?= (int)$event['Orgc']['id'] ?>" class="beta-org-link" title="<?= h($event['Orgc']['name']) ?>">
                        <span class="beta-org-name"><?= h($event['Orgc']['name']) ?></span>
                        <?php
                            $orgLogo = $this->OrgImg->getOrgLogo($event['Orgc'], 24, false);
                            if (strpos($orgLogo, '<img') !== false): // Check if the output contains an image tag
                                echo $orgLogo;
                            endif;
                        ?>
                    </a>
                </span>
            </span>
             <span class="meta-box dist-box" title="<?php echo h($distributionLevels[$event['Event']['distribution']]); ?>">
                <span class="meta-label"><?php echo __('Distribution'); ?></span>
                <span class="meta-value" style="display: flex; align-items: center; gap: 5px;">
                    <div class="dist-widget dist-<?= intval($event['Event']['distribution']) ?> distributionNetworkToggle"
                         title="<?= $event['Event']['distribution'] == 4 ? h($event['SharingGroup']['name']) : h($distributionLevels[$event['Event']['distribution']]) ?>"
                         data-event-distribution="<?= intval($event['Event']['distribution']) ?>"
                         data-event-distribution-name="<?= $event['Event']['distribution'] == 4 ? h($event['SharingGroup']['name']) : h($shortDist[$event['Event']['distribution']]) ?>"
                         data-scope-id="<?= h($event['Event']['id']) ?>">
                    </div>
                    <?php 
                        if ($event['Event']['distribution'] == 4):
                            echo $this->Html->link($event['SharingGroup']['name'], array('controller' => 'sharing_groups', 'action' => 'view', $event['SharingGroup']['id']));
                        else:
                            echo h($shortDist[$event['Event']['distribution']]);
                        endif;
                    ?>
                </span>
            </span>
            <span class="meta-box mod-box">
                <span class="meta-label"><?php echo __('Last Mod'); ?></span>
                <span class="meta-value beta-relative-timestamp"
                    data-timestamp="<?= h($event['Event']['timestamp']) ?>"
                    data-absolute="<?= h(date('Y-m-d H:i:s', $event['Event']['timestamp'])) ?>"
                    title="<?= h(date('Y-m-d H:i:s', $event['Event']['timestamp'])) ?> (click to copy)"
                    style="cursor: pointer;">
                    <?php echo $this->Time->time($event['Event']['timestamp']); ?>
                </span>
            </span>
            
            <?php if ($this->Acl->canAccess('events', 'publish')): ?>
                <span class="meta-box publish-box" title="<?php echo __('Toggle publication status'); ?>">
                    <span class="meta-label"><?php echo __('Published'); ?></span>
                    <span class="meta-value">
                        <span id="publishedLabel" class="published-label <?php echo !empty($event['Event']['published']) ? 'state-published' : 'state-unpublished'; ?>"><?php echo !empty($event['Event']['published']) ? __('Published') : __('Unpublished'); ?></span>
                        <label class="switch">
                            <input type="checkbox" id="publishedToggle" data-id="<?php echo h($event['Event']['id']); ?>" <?php echo $event['Event']['published'] ? 'checked' : ''; ?>>
                            <span class="slider round"></span>
                        </label>
                    </span>
                </span>
            <?php endif; ?>

            <?php if ($this->Acl->canAccess('events', 'edit')): ?>
                <span class="meta-box edit-box">
                    <span class="meta-label"><?php echo __('Action'); ?></span>
                    <span class="meta-value">
                        <a href="<?php echo $baseurl; ?>/events/edit/<?php echo h($event['Event']['id']); ?>" class="btn btn-default btn-sm"><i class="fa fa-edit"></i> <?php echo __('Edit'); ?></a>
                    </span>
                </span>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($warnings)): ?>
            <div class="alert alert-warning beta-alert" style="margin-top: 15px;">
                 <?php if (is_array($warnings)): ?>
                    <?php
                        foreach ($warnings as $k => $warning) {
                            if (is_array($warning)) {
                                $warnings[$k] = implode('<br>', $warning);
                            }
                        }
                        echo implode('<br>', $warnings);
                    ?>
                <?php else: ?>
                    <?php echo $warnings; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabs -->
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

    // Server-side pagination params from CustomPaginationTool
    $paging = isset($this->params->params['paging']['Event']) ? $this->params->params['paging']['Event'] : [];
    $betaCurrentPage = isset($paging['page']) ? (int)$paging['page'] : 1;
    $betaPageSize = isset($paging['limit']) ? (int)$paging['limit'] : 50;
    $betaTotalItems = isset($paging['count']) ? (int)$paging['count'] : 0;
    $betaTotalPages = isset($paging['pageCount']) ? (int)$paging['pageCount'] : 1;

    // Count total items for display purposes
    $betaTotalAttributes = $betaTotalItems > 0 ? $betaTotalItems : count($items);

    // Calculate display range for "Showing X-Y of Z"
    $betaShowStart = ($betaCurrentPage - 1) * $betaPageSize + 1;
    $betaShowEnd = min($betaCurrentPage * $betaPageSize, $betaTotalItems);
    if ($betaTotalItems == 0) {
        $betaShowStart = 0;
        $betaShowEnd = 0;
    }
    ?>
    <div class="beta-tabs-container">
        <ul class="nav nav-tabs beta-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#summary" aria-controls="summary" role="tab" data-toggle="tab"><?php echo __('Summary'); ?></a></li>
            <li role="presentation"><a href="#attributes" aria-controls="attributes" role="tab" data-toggle="tab"><?php echo __('Data'); ?> (<?php echo h($betaTotalAttributes); ?>)</a></li>
            <li role="presentation"><a href="#correlations" aria-controls="correlations" role="tab" data-toggle="tab"><?php echo __('Correlations'); ?> (<?php echo isset($relatedEventCorrelationCount) ? count($relatedEventCorrelationCount) : 0; ?>)</a></li>
            <li role="presentation"><a href="#history" aria-controls="history" role="tab" data-toggle="tab"><?php echo __('History'); ?></a></li>
        </ul>

        <div class="tab-content beta-tab-content">
            <!-- Summary Tab -->
            <div role="tabpanel" class="tab-pane active" id="summary">
                 <div class="row-fluid">
                     <div class="span8">
                         <!-- Report Snippet -->
                          <div class="beta-card summary-card">
                              <div class="beta-card-header"><?php echo __('Report preview'); ?></div>
                              <div class="beta-card-body">
                                  <?php if (!empty($firstEventReportMarkdown)): ?>
                                       <iframe id="summary-report-iframe"
                                           src="<?php echo $baseurl; ?>/eventReports/viewRendered/<?php echo h($firstEventReportId); ?>"
                                           style="width: 100%; max-height: 400px; min-height: 120px; border: 1px solid #e0e0e0; border-radius: 4px; background: #fff; overflow: hidden;"
                                           frameborder="0"
                                           scrolling="auto"
                                           sandbox="allow-same-origin allow-scripts allow-popups allow-forms"
                                           loading="lazy"></iframe>
                                        <div style="margin-top: 10px;">
                                            <a href="#" onclick="viewFullReport(<?php echo h($firstEventReportId); ?>); return false;"><?php echo __('View full report'); ?></a>
                                            |
                                            <a href="#summary-reports-section" onclick="betaToggleSummaryReports(true); document.getElementById('summary-reports-section').scrollIntoView({behavior: 'smooth', block: 'start'}); return false;"><?php echo __('See all reports'); ?></a>
                                         </div>
                                    <?php else: ?>
                                        <p class="muted"><?php echo __('No report content available. Always consider adding an event report to explain the "so what" and context!'); ?></p>
                                        <?php if ((int)$eventReportCount === 0 && $this->Acl->canAccess('eventReports', 'add') && $this->Acl->canModifyEvent($event)): ?>
                                            <a href="<?php echo $baseurl; ?>/eventReports/add/<?php echo h($event['Event']['id']); ?>" class="btn btn-link modal-open" style="padding-left: 0;" title="<?php echo __('Add Event Report'); ?>">
                                                <i class="fa fa-plus"></i> <?php echo __('Add an event report'); ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                  <!-- Analysis Links Sub-section -->
                                  <?php
                                      $analysisLinks = [];
                                      $seenIds = [];
                                      $extractFromAttributes = function($attributes) use (&$analysisLinks, &$seenIds) {
                                          if (empty($attributes)) return;
                                          foreach ($attributes as $attr) {
                                              if (isset($seenIds[$attr['id']])) continue;
                                              if (isset($attr['category']) && $attr['category'] === 'External analysis') {
                                                  if ($attr['type'] === 'link' || $attr['type'] === 'url') {
                                                      $analysisLinks[] = ['value' => $attr['value'], 'type' => 'link', 'id' => $attr['id']];
                                                      $seenIds[$attr['id']] = true;
                                                  } elseif ($attr['type'] === 'attachment' && stripos($attr['value'], '.pdf') !== false) {
                                                      $analysisLinks[] = ['value' => $attr['value'], 'type' => 'attachment', 'id' => $attr['id']];
                                                      $seenIds[$attr['id']] = true;
                                                  }
                                              }
                                          }
                                      };

                                      if (!empty($event['Attribute'])) {
                                          $extractFromAttributes($event['Attribute']);
                                      }
                                      if (!empty($event['Object'])) {
                                          foreach ($event['Object'] as $obj) {
                                              if (!empty($obj['Attribute'])) {
                                                  $extractFromAttributes($obj['Attribute']);
                                              }
                                          }
                                      }
                                      if (!empty($event['objects'])) {
                                          $betaAttrs = [];
                                          foreach ($event['objects'] as $item) {
                                              if (isset($item['objectType']) && $item['objectType'] === 'attribute') {
                                                  $betaAttrs[] = $item;
                                              } elseif (isset($item['objectType']) && $item['objectType'] === 'object' && !empty($item['Attribute'])) {
                                                  $betaAttrs = array_merge($betaAttrs, $item['Attribute']);
                                              }
                                          }
                                          if (!empty($betaAttrs)) {
                                              $extractFromAttributes($betaAttrs);
                                          }
                                      }

                                      usort($analysisLinks, function($a, $b) {
                                          return strcasecmp($a['value'], $b['value']);
                                      });
                                  ?>
                                  <div class="analysis-links-section" style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">
                                      <h5 style="margin-top: 0; font-size: 13px; color: #666;"><?php echo __('Analysis Links <i class="fa fa-exclamation-triangle"></i> (open links cautiously!)'); ?></h5>
                                      <?php if (!empty($analysisLinks)): ?>
                                          <ul style="list-style: none; padding: 0; margin: 0;">
                                              <?php foreach ($analysisLinks as $link): ?>
                                                  <li style="margin-bottom: 8px; border-bottom: 1px solid #f0f0f0; padding-bottom: 5px; word-break: break-all;">
                                                      <?php if ($link['type'] === 'link'): ?>
                                                          <i class="fa fa-external-link-alt" style="color: #428bca; margin-right: 5px;"></i>
                                                          <a href="<?php echo h($link['value']); ?>" target="_blank" rel="noreferrer noopener"><?php echo h($link['value']); ?></a>
                                                      <?php else: ?>
                                                          <i class="fa fa-file-pdf" style="color: #d9534f; margin-right: 5px;"></i>
                                                          <a href="<?php echo $baseurl; ?>/attributes/download/<?php echo h($link['id']); ?>"><?php echo h($link['value']); ?></a> (<?php echo __('PDF Attachment'); ?>)
                                                      <?php endif; ?>
                                                  </li>
                                              <?php endforeach; ?>
                                          </ul>
                                      <?php else: ?>
                                          <p class="muted" style="font-size: 12px;"><?php echo __('No external analysis links or PDF attachments available.'); ?></p>
                                      <?php endif; ?>
                                  </div>
                              </div>
                          </div>
                         
                          <!-- Analysis comments -->
                          <div class="beta-card summary-card">
                              <div class="beta-card-header"><?php echo __('Analysis comments'); ?></div>
                              <div class="beta-card-body">
                                   <div id="comments-graph" style="width: 100%;"></div>
                              </div>
                         </div>

                      </div>
                      <div class="span4">
                          <div class="beta-card summary-card" id="summary-reports-section">
                              <div class="beta-card-header beta-expandable-header" onclick="betaToggleSummaryReports();" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();betaToggleSummaryReports();}" role="button" tabindex="0" aria-label="<?php echo __('Toggle event reports'); ?>">
                                  <?php echo __('Event reports'); ?> <span class="beta-header-count">(<?php echo h($eventReportCount); ?>)</span>
                                  <i id="summary-reports-toggle-icon" class="fa fa-chevron-right pull-right"></i>
                              </div>
                              <div class="beta-card-body" id="summary-reports-content-wrap" style="display:none;">
                                  <div id="summary-reports-content">
                                      <div class="text-center" style="padding: 12px 0;">
                                          <i class="fa fa-spinner fa-spin"></i>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <!-- Context -->
                           <div class="beta-card summary-card">
                              <div class="beta-card-header"><?php echo __('Context'); ?></div>
                              <div class="beta-card-body">
                                 <strong><?php echo __('Tags'); ?> <span class="beta-header-count">(<?php echo h(!empty($event['EventTag']) ? count($event['EventTag']) : 0); ?>)</span></strong><br>
                                 <span class="eventTagContainer">
                                     <?php
                                           echo $this->element('ajaxTags', [
                                               'event' => $event,
                                               'tags' => $event['EventTag'],
                                               'tagAccess' => $this->Acl->canAccess('tags', 'edit'),
                                               'localTagAccess' => $this->Acl->canModifyTag($event, true),
                                               'missingTaxonomies' => $missingTaxonomies,
                                               'tagConflicts' => $tagConflicts,
                                               'popoverPlacement' => 'left'
                                           ]);
                                     ?>
                                  </span>
                                  <hr>
                                  <?php
                                      $tagAccess = $this->Acl->canModifyTag($event);
                                      $localTagAccess = $this->Acl->canModifyTag($event, true);
                                      $targetId = $event['Event']['id'];
                                      $galaxyCount = !empty($event['Galaxy']) ? count($event['Galaxy']) : 0;
                                  ?>
                                  <div class="beta-collections-header-row" style="margin-bottom:5px;">
                                      <span class="beta-collections-header-left">
                                          <strong><?php echo __('Galaxies'); ?> <span class="beta-header-count">(<?php echo h($galaxyCount); ?>)</span></strong>
                                          <?php
                                              if ($tagAccess) {
                                                  $link = "$baseurl/galaxies/selectGalaxyNamespace/$targetId/event/local:0";
                                                  echo sprintf(
                                                      '<button class="%s" data-popover-popup="%s" data-popover-placement="left" role="button" tabindex="0" aria-label="' . __('Add new cluster') . '" title="' . __('Add new cluster') . '">%s</button>',
                                                      'useCursorPointer addButton btn btn-inverse noPrint',
                                                      $link,
                                                      '<i class="fas fa-globe-americas"></i> <i class="fas fa-plus"></i>'
                                                  );
                                              }
                                              if ($localTagAccess) {
                                                  $link = "$baseurl/galaxies/selectGalaxyNamespace/$targetId/event/local:1";
                                                  echo sprintf(
                                                      '<button class="%s" data-popover-popup="%s" data-popover-placement="left" role="button" tabindex="0" aria-label="' . __('Add new local cluster') . '" title="' . __('Add new local cluster') . '">%s</button>',
                                                      'useCursorPointer addButton btn btn-inverse noPrint',
                                                      $link,
                                                      '<i class="fas fa-user"></i> <i class="fas fa-plus"></i>'
                                                  );
                                              }
                                          ?>
                                      </span>
                                  </div>
                                  <div class="beta-galaxies-container" id="galaxies_div" style="margin-top: 5px;">
                                    <?php
                                        if (!empty($event['Galaxy'])) {
                                            foreach ($event['Galaxy'] as $galaxy) {
                                                echo $this->element('Events/View/galaxy_compact_beta', [
                                                    'galaxyName' => $galaxy['name'],
                                                    'clusters' => $galaxy['GalaxyCluster'],
                                                    'baseurl' => $baseurl,
                                                    'canModify' => $tagAccess,
                                                    'canModifyLocal' => $localTagAccess,
                                                    'target_type' => 'event',
                                                    'target_id' => $targetId,
                                                ]);
                                            }
                                        }
                                    ?>
                                  </div>

                                  <!-- Collections this event belongs to -->
                                  <hr>
                                  <div class="beta-expandable-header beta-collections-header-row" onclick="betaToggleCollectionsSection();" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();betaToggleCollectionsSection();}" role="button" tabindex="0" aria-label="<?php echo __('Toggle collections'); ?>">
                                      <span class="beta-collections-header-left">
                                          <strong><?php echo __('Collections'); ?> <span class="beta-header-count">(<span id="beta-collections-count">0</span>)</span></strong>
                                          <?php if ($this->Acl->canAccess('collectionElements', 'addElementToCollection')): ?>
                                              <a href="#"
                                                 onclick="event.stopPropagation(); openGenericModal('<?php echo $baseurl; ?>/collectionElements/addElementToCollection/Event/<?php echo h($event['Event']['uuid']); ?>'); return false;"
                                                 class="useCursorPointer addButton btn btn-inverse noPrint"
                                                 role="button"
                                                 tabindex="0"
                                                 aria-label="<?php echo __('Add to Collection'); ?>"
                                                 title="<?php echo __('Add to Collection'); ?>">
                                                  <i class="fa fa-folder-plus icon-white" style="color:#fff;"></i>
                                              </a>
                                          <?php endif; ?>
                                      </span>
                                      <span class="beta-collections-header-actions">
                                          <i id="beta-collections-toggle-icon" class="fa fa-chevron-down"></i>
                                      </span>
                                  </div>
                                  <div id="beta-collections-content-wrap" style="margin-top:5px;">
                                      <div id="event-collections-container">
                                          <span class="muted" style="font-size:11px;"><?php echo __('Loading…'); ?></span>
                                      </div>
                                  </div>
                                    </div>
                                </div>
                           <!-- Warninglist Matches -->
                          <?php
                              $warninglistMatches = [];
                              $extractWarnings = function($attributes) use (&$warninglistMatches) {
                                  if (empty($attributes)) return;
                                  foreach ($attributes as $attr) {
                                      if (!empty($attr['warnings'])) {
                                          foreach ($attr['warnings'] as $w) {
                                              $key = $w['warninglist_name'] . '||' . $attr['value'];
                                              if (!isset($warninglistMatches[$key])) {
                                                  $warninglistMatches[$key] = [
                                                      'warninglist_name' => $w['warninglist_name'],
                                                      'value' => $attr['value'],
                                                      'count' => 0
                                                  ];
                                              }
                                              $warninglistMatches[$key]['count']++;
                                          }
                                      }
                                  }
                              };

                              if (!empty($event['Attribute'])) {
                                  $extractWarnings($event['Attribute']);
                              }
                              if (!empty($event['Object'])) {
                                  foreach ($event['Object'] as $obj) {
                                      if (!empty($obj['Attribute'])) {
                                          $extractWarnings($obj['Attribute']);
                                      }
                                  }
                              }
                              if (!empty($event['objects'])) {
                                  $betaAttrs = [];
                                  foreach ($event['objects'] as $item) {
                                      if (isset($item['objectType']) && $item['objectType'] === 'attribute') {
                                          $betaAttrs[] = $item;
                                      } elseif (isset($item['objectType']) && $item['objectType'] === 'object' && !empty($item['Attribute'])) {
                                          $betaAttrs = array_merge($betaAttrs, $item['Attribute']);
                                      }
                                  }
                                  if (!empty($betaAttrs)) {
                                      $extractWarnings($betaAttrs);
                                  }
                              }
                              usort($warninglistMatches, function($a, $b) {
                                  return strcasecmp($a['warninglist_name'], $b['warninglist_name']) ?: strcasecmp($a['value'], $b['value']);
                              });
                              $warninglistMatchCount = count($warninglistMatches);
                              $warninglistExpanded = $warninglistMatchCount > 0;
                          ?>
                          <div class="beta-card summary-card">
                                <div class="beta-card-header beta-expandable-header" onclick="betaToggleWarninglistSection();" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();betaToggleWarninglistSection();}" role="button" tabindex="0" aria-label="<?php echo __('Toggle warninglist matches'); ?>">
                                    <?php echo __('Warninglist Matches'); ?> <span class="beta-header-count">(<?php echo h($warninglistMatchCount); ?>)</span>
                                    <i id="beta-warninglist-toggle-icon" class="fa <?php echo $warninglistExpanded ? 'fa-chevron-down' : 'fa-chevron-right'; ?> pull-right"></i>
                                </div>
                                <div class="beta-card-body" id="beta-warninglist-content-wrap" style="display: <?php echo $warninglistExpanded ? 'block' : 'none'; ?>;">
                                    <?php if (!empty($warninglistMatches)): ?>
                                        <table class="table table-condensed table-hover" style="font-size: 12px; margin-bottom: 0;">
                                            <thead>
                                               <tr>
                                                   <th><?php echo __('Warninglist'); ?></th>
                                                   <th><?php echo __('Value'); ?></th>
                                               </tr>
                                           </thead>
                                           <tbody>
                                               <?php foreach ($warninglistMatches as $match): ?>
                                                   <tr>
                                                       <td><span class="label label-warning"><?php echo h($match['warninglist_name']); ?></span></td>
                                                       <td>
                                                            <a href="#attributes" onclick="$('.nav-tabs a[href=\'#attributes\']').tab('show'); $('#beta-attr-search').val('<?php echo h($match['value']); ?>').trigger('keyup'); return false;" class="attr-value">
                                                               <?php echo h($match['value']); ?>
                                                           </a>
                                                       </td>
                                                   </tr>
                                               <?php endforeach; ?>
                                           </tbody>
                                       </table>
                                   <?php else: ?>
                                       <p class="muted" style="font-size: 12px;"><?php echo __('No warninglist matches found.'); ?></p>
                                   <?php endif; ?>
                               </div>
                           </div>

                           <!-- Export Card -->
                           <?php
                               $eventId = $event['Event']['id'];
                               $isPublished = !empty($event['Event']['published']);
                               $betaExportFormats = [
                                   'json' => [
                                       'label' => __('MISP JSON'),
                                       'url' => $baseurl . '/events/restSearch/json/includeAnalystData:1/eventid:' . $eventId . '.json',
                                       'checkbox' => true,
                                       'checkbox_label' => __('Encode Attachments'),
                                       'checkbox_url' => $baseurl . '/events/restSearch/json/withAttachments:1/includeAnalystData:1/eventid:' . $eventId . '.json',
                                       'icon' => 'fa-file-code',
                                   ],
                                   'xml' => [
                                       'label' => __('MISP XML'),
                                       'url' => $baseurl . '/events/restSearch/xml/eventid:' . $eventId . '.xml',
                                       'checkbox' => true,
                                       'checkbox_label' => __('Encode Attachments'),
                                       'checkbox_url' => $baseurl . '/events/restSearch/xml/eventid:' . $eventId . '/withAttachments:1.xml',
                                       'icon' => 'fa-file-code',
                                   ],
                                   'csv' => [
                                       'label' => $isPublished ? __('CSV') : __('CSV (IDS flag ignored)'),
                                       'url' => $isPublished
                                           ? $baseurl . '/events/restSearch/returnFormat:csv/to_ids:1/published:1/includeContext:0/eventid:' . $eventId
                                           : $baseurl . '/events/restSearch/returnFormat:csv/includeContext:0/eventid:' . $eventId,
                                       'checkbox' => $isPublished,
                                       'checkbox_label' => __('Include non-IDS marked attributes'),
                                       'checkbox_url' => $baseurl . '/events/restSearch/returnFormat:csv/to_ids:1||0/published:1||0/includeContext:0/eventid:' . $eventId,
                                       'icon' => 'fa-file-csv',
                                   ],
                                   'csv_context' => [
                                       'label' => __('CSV with context'),
                                       'url' => $isPublished
                                           ? $baseurl . '/events/restSearch/returnFormat:csv/to_ids:1/published:1/includeContext:1/eventid:' . $eventId
                                           : $baseurl . '/events/restSearch/returnFormat:csv/includeContext:1/eventid:' . $eventId,
                                       'checkbox' => $isPublished,
                                       'checkbox_label' => __('Include non-IDS marked attributes'),
                                       'checkbox_url' => $baseurl . '/events/restSearch/returnFormat:csv/to_ids:1||0/published:1||0/includeContext:1/eventid:' . $eventId,
                                       'icon' => 'fa-file-csv',
                                   ],
                                   'stix_xml' => [
                                       'label' => __('STIX 1 XML'),
                                       'url' => $baseurl . '/events/restSearch/stix/eventid:' . $eventId,
                                       'checkbox' => true,
                                       'checkbox_label' => __('Encode Attachments'),
                                       'checkbox_url' => $baseurl . '/events/restSearch/stix/eventid:' . $eventId . '/withAttachments:1',
                                       'icon' => 'fa-file-alt',
                                   ],
                                   'stix_json' => [
                                       'label' => __('STIX 1 JSON'),
                                       'url' => $baseurl . '/events/restSearch/stix-json/eventid:' . $eventId,
                                       'checkbox' => true,
                                       'checkbox_label' => __('Encode Attachments'),
                                       'checkbox_url' => $baseurl . '/events/restSearch/stix-json/withAttachments:1/eventid:' . $eventId,
                                       'icon' => 'fa-file-alt',
                                   ],
                                   'stix2' => [
                                       'label' => __('STIX 2'),
                                       'url' => $baseurl . '/events/restSearch/stix2/eventid:' . $eventId,
                                       'checkbox' => true,
                                       'checkbox_label' => __('Encode Attachments'),
                                       'checkbox_url' => $baseurl . '/events/restSearch/stix2/eventid:' . $eventId . '/withAttachments:1',
                                       'icon' => 'fa-file-alt',
                                   ],
                                   'openioc' => [
                                       'label' => __('OpenIOC'),
                                       'url' => $baseurl . '/events/restSearch/openioc/to_ids:1/published:1/eventid:' . $eventId . '.json',
                                       'checkbox' => false,
                                       'icon' => 'fa-file-alt',
                                   ],
                                   'rpz' => [
                                       'label' => __('RPZ Zone file'),
                                       'url' => $baseurl . '/attributes/restSearch/returnFormat:rpz/published:1||0/eventid:' . $eventId,
                                       'checkbox' => false,
                                       'icon' => 'fa-file-alt',
                                   ],
                                   'suricata' => [
                                       'label' => __('Suricata rules'),
                                       'url' => $baseurl . '/events/restSearch/returnFormat:suricata/published:1||0/eventid:' . $eventId,
                                       'checkbox' => false,
                                       'icon' => 'fa-shield-alt',
                                   ],
                                   'snort' => [
                                       'label' => __('Snort rules'),
                                       'url' => $baseurl . '/events/restSearch/returnFormat:snort/published:1||0/eventid:' . $eventId,
                                       'checkbox' => false,
                                       'icon' => 'fa-shield-alt',
                                   ],
                                   'text' => [
                                       'label' => __('Text (attribute values)'),
                                       'url' => $baseurl . '/attributes/restSearch/returnFormat:text/published:1||0/eventid:' . $eventId,
                                       'checkbox' => true,
                                       'checkbox_label' => __('Include non-IDS marked attributes'),
                                       'checkbox_url' => $baseurl . '/attributes/restSearch/returnFormat:text/published:1||0/to_ids:1||0/eventid:' . $eventId,
                                       'icon' => 'fa-file-alt',
                                   ],
                               ];
                           ?>
                            <div class="beta-card summary-card" id="beta-export-card">
                                <div class="beta-card-header beta-expandable-header" onclick="betaToggleExportSection();" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();betaToggleExportSection();}" role="button" tabindex="0" aria-label="<?php echo __('Toggle export options'); ?>">
                                    <i class="fa fa-download" style="margin-right: 6px;"></i><?php echo __('Export'); ?>
                                    <i id="beta-export-toggle-icon" class="fa fa-chevron-right pull-right"></i>
                                </div>
                                <div class="beta-card-body" id="beta-export-content-wrap" style="display:none;">
                                    <div style="margin-bottom: 10px;">
                                        <label for="beta-export-format" style="font-size: 12px; font-weight: 600; color: #666; display: block; margin-bottom: 4px;"><?php echo __('Format'); ?></label>
                                        <select id="beta-export-format" class="form-control input-sm" onchange="betaExportFormatChanged(this.value)" style="width: 100%;">
                                           <?php foreach ($betaExportFormats as $fmtKey => $fmt): ?>
                                               <option value="<?php echo h($fmtKey); ?>"
                                                   data-url="<?php echo h($fmt['url']); ?>"
                                                   data-checkbox="<?php echo $fmt['checkbox'] ? '1' : '0'; ?>"
                                                   data-checkbox-label="<?php echo isset($fmt['checkbox_label']) ? h($fmt['checkbox_label']) : ''; ?>"
                                                   data-checkbox-url="<?php echo isset($fmt['checkbox_url']) ? h($fmt['checkbox_url']) : ''; ?>"
                                               ><?php echo h($fmt['label']); ?></option>
                                           <?php endforeach; ?>
                                       </select>
                                   </div>
                                   <div id="beta-export-checkbox-row" style="margin-bottom: 10px; display: none;">
                                       <label style="font-size: 12px; font-weight: normal; color: #555; cursor: pointer;">
                                           <input type="checkbox" id="beta-export-checkbox" style="margin-right: 5px; vertical-align: middle;">
                                           <span id="beta-export-checkbox-label"></span>
                                       </label>
                                   </div>
                                   <a id="beta-export-download-btn"
                                      href="#"
                                      class="btn btn-primary btn-sm"
                                      style="display: block; text-align: center;"
                                      onclick="betaExportDownload(); return false;">
                                       <i class="fa fa-download"></i> <?php echo __('Download'); ?>
                                   </a>
                               </div>
                           </div>
                       </div>
                  </div>
                 </div>

            <!-- Attributes Tab -->
            <div role="tabpanel" class="tab-pane" id="attributes">
                 <div class="beta-card" style="margin-bottom: 15px;">
                     <div class="beta-card-header"><?php echo __('Composition'); ?></div>
                     <div class="beta-card-body">
                         <div id="composition-treemap" class="composition-singlebar-wrap"></div>
                     </div>
                 </div>
                 <div id="beta-filter-banner-slot"></div>
                 <div id="beta-attributes-container">
                     <?php echo $this->element('eventattribute', [
                         'items' => $items,
                         'betaTotalAttributes' => $betaTotalAttributes,
                         'paging' => $paging,
                         'betaCurrentPage' => $betaCurrentPage,
                         'betaPageSize' => $betaPageSize,
                         'betaTotalItems' => $betaTotalItems,
                         'betaTotalPages' => $betaTotalPages,
                         'betaShowStart' => $betaShowStart,
                         'betaShowEnd' => $betaShowEnd
                     ]); ?>
                 </div>
            </div>
            
            <!-- Other Tabs Placeholders -->
             <div role="tabpanel" class="tab-pane" id="correlations">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;"><?php echo __('Correlations'); ?></h3>
                    <div id="correlation-filter-controls" style="display: none;">
                        <span id="correlation-filter-msg" class="label label-info" style="font-size: 12px;"></span>
                        <button id="correlation-reset-btn" class="btn btn-default btn-xs" onclick="resetCorrelationFilter()"><i class="fa fa-times"></i> <?php echo __('Clear Filter'); ?></button>
                    </div>
                </div>
                <div id="correlations-loader" style="text-align: center; padding: 40px;">
                    <i class="fa fa-spinner fa-spin fa-3x" style="color: #428bca; margin-bottom: 15px;"></i>
                    <p style="color: #666; font-size: 1.1em;"><?php echo __('Analyzing correlations...'); ?></p>
                </div>
                <div id="correlations-content" style="display: none;">
                    <div id="correlations-sankey-container" style="margin-bottom: 30px; background: #fff; border: 1px solid #e0e0e0; border-radius: 4px; padding: 15px; display: none;">
                        <h4 style="margin-top: 0; margin-bottom: 15px; font-size: 14px; font-weight: 600; color: #555; display: flex; justify-content: space-between; align-items: center;">
                            <span><?php echo __('Correlation Flow'); ?></span>
                            <span style="display: flex; align-items: center; gap: 10px;">
                                <span id="sankey-filter-badge" style="display: none; background: #d9534f; color: #fff; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 12px; white-space: nowrap;">
                                    <i class="fa fa-filter"></i> <span id="sankey-filter-label"></span>
                                    <a href="#" onclick="resetCorrelationFilter(); return false;" style="color: #fff; margin-left: 6px; text-decoration: none;" title="<?php echo __('Remove filter'); ?>"><i class="fa fa-times-circle"></i></a>
                                </span>
                                <span id="sankey-limit-msg" style="font-weight: normal; font-size: 12px; color: #888;"></span>
                            </span>
                        </h4>
                        <div id="correlations-sankey" style="width: 100%; height: 400px;"></div>
                    </div>
                    <div id="correlations-table-filter-banner" style="display: none; margin-bottom: 15px; padding: 10px 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; align-items: center; justify-content: space-between;">
                        <span><i class="fa fa-filter" style="color: #856404;"></i> <strong><?php echo __('Filtered view'); ?></strong> &mdash; <span id="correlations-table-filter-msg"></span></span>
                        <a href="#" onclick="resetCorrelationFilter(); return false;" class="btn btn-xs btn-warning" style="margin-left: 10px;"><i class="fa fa-times"></i> <?php echo __('Clear Filter'); ?></a>
                    </div>
                    <div id="correlations-table-container"></div>
                </div>
            </div>
             <div role="tabpanel" class="tab-pane" id="history">
                <div style="margin-bottom: 20px;">
                    <h3 style="margin: 0;"><?php echo __('History'); ?></h3>
                </div>
                
                <?php if (!empty($contributors)): ?>
                    <p style="margin-bottom: 20px;"><strong><?php echo __('Contributors'); ?>:</strong> <?php echo implode(', ', $contributors); ?></p>
                <?php endif; ?>
                
                <div id="history-content-container">
                    <div class="text-center" style="padding: 40px;">
                        <i class="fa fa-spinner fa-spin fa-2x"></i><br>
                        <?php echo __('Loading history...'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Improved Treemap Logic
    <?php
        $compositionData = [];
        $attrTypes = [];
        $seenAttrKeys = [];
        $commentCounts = [];

        $registerAttrType = function($attr) use (&$attrTypes, &$seenAttrKeys) {
            if (empty($attr) || empty($attr['type'])) {
                return;
            }

            $key = null;
            if (!empty($attr['id'])) {
                $key = 'id:' . $attr['id'];
            } elseif (!empty($attr['uuid'])) {
                $key = 'uuid:' . $attr['uuid'];
            }

            if ($key !== null) {
                if (isset($seenAttrKeys[$key])) {
                    return;
                }
                $seenAttrKeys[$key] = true;
            }

            $t = $attr['type'];
            if (!isset($attrTypes[$t])) {
                $attrTypes[$t] = 0;
            }
            $attrTypes[$t]++;
        };

        if (!empty($event['objects'])) {
            foreach ($event['objects'] as $obj) {
                if ($obj['objectType'] === 'attribute') {
                    $registerAttrType($obj);
                    if (!empty($obj['comment'])) {
                        $c = $obj['comment'];
                        if (!isset($commentCounts[$c])) $commentCounts[$c] = 0;
                        $commentCounts[$c]++;
                    }
                } elseif ($obj['objectType'] === 'object') {
                    if (!empty($obj['Attribute'])) {
                        foreach ($obj['Attribute'] as $attr) {
                            $registerAttrType($attr);
                        }
                    }
                    if (!empty($obj['comment'])) {
                        $c = $obj['comment'];
                        if (!isset($commentCounts[$c])) $commentCounts[$c] = 0;
                        $commentCounts[$c]++;
                    }
                    if (!empty($obj['Attribute'])) {
                        foreach ($obj['Attribute'] as $attr) {
                            if (!empty($attr['comment'])) {
                                $c = $attr['comment'];
                                if (!isset($commentCounts[$c])) $commentCounts[$c] = 0;
                                $commentCounts[$c]++;
                            }
                        }
                    }
                }
            }
        }

        if (!empty($event['Attribute'])) {
             foreach ($event['Attribute'] as $attr) {
                $registerAttrType($attr);
                if (!empty($attr['comment'])) {
                    $c = $attr['comment'];
                    if (!isset($commentCounts[$c])) $commentCounts[$c] = 0;
                    $commentCounts[$c]++;
                }
            }
        }

        if (!empty($event['Object'])) {
             foreach ($event['Object'] as $obj) {
                if (!empty($obj['Attribute'])) {
                    foreach ($obj['Attribute'] as $attr) {
                        $registerAttrType($attr);
                    }
                }
                if (!empty($obj['comment'])) {
                    $c = $obj['comment'];
                    if (!isset($commentCounts[$c])) $commentCounts[$c] = 0;
                    $commentCounts[$c]++;
                }
                if (!empty($obj['Attribute'])) {
                    foreach ($obj['Attribute'] as $attr) {
                        if (!empty($attr['comment'])) {
                            $c = $attr['comment'];
                            if (!isset($commentCounts[$c])) $commentCounts[$c] = 0;
                            $commentCounts[$c]++;
                        }
                    }
                }
            }
        }
        
        foreach ($attrTypes as $type => $count) {
            $compositionData[] = [
                'label' => "Attribute: $type",
                'name' => $type,
                'value' => $count,
                'type' => 'attribute'
            ];
        }
        
        // Sort by value desc
        usort($compositionData, function($a, $b) {
            return $b['value'] - $a['value'];
        });

        if (isset($betaCompositionData) && is_array($betaCompositionData)) {
            $compositionData = $betaCompositionData;
            usort($compositionData, function($a, $b) {
                return $b['value'] - $a['value'];
            });
        }

        $commentData = [];
        foreach ($commentCounts as $comment => $count) {
            $commentData[] = [
                'label' => $comment,
                'value' => $count
            ];
        }
        usort($commentData, function($a, $b) {
            return $b['value'] - $a['value'];
        });
    ?>
    var compositionData = <?php echo json_encode($compositionData); ?>;
    var commentData = <?php echo json_encode($commentData); ?>;

    function betaEscapeHtml(value) {
        return $('<div/>').text(value == null ? '' : String(value)).html();
    }

    function betaRenderCompositionBar() {
        var compositionContainer = $('#composition-treemap');
        if (!compositionContainer.length) return;

        if (!compositionData || compositionData.length === 0) {
             d3.select("#composition-treemap").html('<div class="alert alert-info" style="margin: 20px;">No composition data available.</div>');
            return;
        }

        var width = compositionContainer.width() || 0;
        if (width < 10) return;

        compositionContainer.empty();
        compositionContainer.css('position', 'relative');

        var total = d3.sum(compositionData, function(d) { return d.value; });
        var color = d3.scale.ordinal()
            .range(["#3f6f9e", "#66a683", "#d3a259", "#c66b6b", "#5f98ad", "#8a7bb8", "#8aa05d", "#b58562"])
            .domain(compositionData.map(function(d) { return d.label; }));

        var barWrap = $('<div class="composition-singlebar"></div>');
        compositionContainer.append(barWrap);

        var xOffset = 0;
        var layout = [];

        compositionData.forEach(function(d) {
            var percent = total > 0 ? (d.value / total) * 100 : 0;
            var pixelWidth = (percent / 100) * width;
            var segmentColor = color(d.label);

            var segment = $('<div class="segment" title="' + betaEscapeHtml(d.label) + ' (' + d.value + ', ' + percent.toFixed(2) + '%)"></div>');
            segment.css({
                width: percent + '%',
                background: segmentColor
            });
            segment.on('click', function() {
                betaFilterAttributesByComposition(d.type, d.name);
            });
            barWrap.append(segment);

            layout.push({
                data: d,
                startX: xOffset,
                centerX: xOffset + (pixelWidth / 2),
                pixelWidth: pixelWidth,
                color: segmentColor
            });

            xOffset += pixelWidth;
        });

        var overlaySvg = d3.select('#composition-treemap')
            .append('svg')
            .attr('width', width)
            .attr('height', 30)
            .style('position', 'absolute')
            .style('top', '0')
            .style('left', '0')
            .style('pointer-events', 'none');

        layout.forEach(function(item) {
            if (item.pixelWidth >= 70) {
                overlaySvg.append('text')
                    .attr('class', 'composition-inline-label')
                    .attr('x', item.centerX)
                    .attr('y', 19)
                    .attr('text-anchor', 'middle')
                    .text(item.data.name + ' (' + item.data.value + ')');
            }
        });

        var labelGrid = $('<div class="composition-label-grid"></div>');
        compositionContainer.append(labelGrid);
        var smallItems = layout.filter(function(item) {
            return item.pixelWidth < 70;
        });

        if (smallItems.length > 0) {
            smallItems.sort(function(a, b) {
                if (b.data.value !== a.data.value) {
                    return b.data.value - a.data.value;
                }
                return a.data.name.localeCompare(b.data.name);
            });

            var labelList = $('<div class="composition-label-list"></div>');
            labelGrid.append(labelList);

            smallItems.forEach(function(item) {
                var chip = $('<div class="composition-label-chip" title="' + betaEscapeHtml(item.data.label) + '"></div>');
                chip.append('<span class="swatch" style="background:' + item.color + ';"></span>');
                chip.append('<span><strong>' + betaEscapeHtml(item.data.name) + '</strong> (' + item.data.value + ')</span>');
                chip.on('click', function() {
                    betaFilterAttributesByComposition(item.data.type, item.data.name);
                });
                labelList.append(chip);
            });
        }
    }

    $(function() {
        popoverStartup();

        $('a[data-toggle="tab"][href="#attributes"]').on('shown.bs.tab', function () {
            betaRenderCompositionBar();
        });

        $(window).on('resize', function() {
            if ($('#attributes').hasClass('active')) {
                betaRenderCompositionBar();
            }
        });

        if ($('#attributes').hasClass('active')) {
            betaRenderCompositionBar();
        }

        // Comments List
        if (commentData && commentData.length > 0) {
            var container = d3.select("#comments-graph");
            container.html(""); // Clear
            var list = container.append("ul")
                .attr("class", "comment-bullet-list");

            commentData.forEach(function(d) {
                var row = list.append("li")
                    .attr("class", "comment-bullet-item")
                    .attr("title", d.label + " (" + d.value + ")")
                    .on("click", function() {
                        betaFilterAttributesByComment(d.label);
                    });

                row.append("span")
                    .attr("class", "comment-bullet-label")
                    .text(d.label);

                row.append("span")
                    .attr("class", "comment-bullet-count")
                    .text("[" + d.value + "]");
            });
        } else {
             d3.select("#comments-graph").html('<div class="alert alert-info" style="margin: 20px;">No comment data available.</div>');
        }

        // Initialize history state on load
        var initialTab = window.location.hash || '#summary';
        if (window.location.hash) {
            $('.nav-tabs a[href="' + window.location.hash + '"]').tab('show');
        }

        var initialState = {
            tab: initialTab,
            filter: null
        };
        history.replaceState(initialState, '', window.location.pathname + initialTab);

        window.ignoreTabPush = false;

        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            if (window.ignoreTabPush) return;
            var target = $(e.target).attr("href");
            var currentState = history.state;
            
            // Only push if it's different from the current tab in history
            if (!currentState || currentState.tab !== target) {
                history.pushState({ tab: target, filter: null }, '', window.location.pathname + target);
            }
        });

        window.onpopstate = function(event) {
            if (event.state && event.state.tab) {
                window.ignoreTabPush = true;
                $('.nav-tabs a[href="' + event.state.tab + '"]').tab('show');
                window.ignoreTabPush = false;
            }
        };

        window.betaSummaryPrimaryReportId = <?php echo !empty($firstEventReportId) ? (int)$firstEventReportId : 0; ?>;

        // Load reports into summary tab
        $.get("<?php echo $baseurl; ?>/eventReports/index/event_id:<?php echo h($event['Event']['id']); ?>/index_for_event:1/beta:1", function(data) {
            $("#summary-reports-content").html(data);
            if (window.betaTimestamps && typeof window.betaTimestamps.update === 'function') {
                window.betaTimestamps.update();
            }
        });

        // Load History when tab activated
        $('a[data-toggle="tab"][href="#history"]').on('shown.bs.tab', function (e) {
            loadHistory();
        });

        // Check if we are already on the history tab on page load
        if (window.location.hash === '#history') {
            loadHistory();
        }

        function loadHistory() {
            if ($('#history-content-container .beta-history-container').length > 0) return;
            var eventId = '<?php echo h($event['Event']['id']); ?>';
            $.get("<?php echo $baseurl; ?>/audit_logs/eventIndex/" + eventId, function(data) {
                $("#history-content-container").html(data);
            }).fail(function() {
                $("#history-content-container").html('<div class="alert alert-danger"><?php echo __('Failed to load history.'); ?></div>');
            });
        }

        // Initialize export card
        var initialExportKey = $('#beta-export-format').val();
        if (initialExportKey) {
            betaExportFormatChanged(initialExportKey);
        }
    });

    function betaSetSidebarSectionExpanded(contentSelector, iconSelector, expanded) {
        var $content = $(contentSelector);
        var $icon = $(iconSelector);
        if (!$content.length || !$icon.length) {
            return;
        }
        $content.toggle(!!expanded);
        $icon.toggleClass('fa-chevron-down', !!expanded).toggleClass('fa-chevron-right', !expanded);
    }

    function betaToggleWarninglistSection() {
        var isExpanded = $('#beta-warninglist-content-wrap').is(':visible');
        betaSetSidebarSectionExpanded('#beta-warninglist-content-wrap', '#beta-warninglist-toggle-icon', !isExpanded);
    }

    function betaToggleExportSection() {
        var isExpanded = $('#beta-export-content-wrap').is(':visible');
        betaSetSidebarSectionExpanded('#beta-export-content-wrap', '#beta-export-toggle-icon', !isExpanded);
    }

    function betaToggleCollectionsSection() {
        var isExpanded = $('#beta-collections-content-wrap').is(':visible');
        betaSetSidebarSectionExpanded('#beta-collections-content-wrap', '#beta-collections-toggle-icon', !isExpanded);
    }

    // Export card logic
    var betaExportFormats = <?php
        $betaExportFormatsJs = [];
        foreach ($betaExportFormats as $k => $fmt) {
            $betaExportFormatsJs[$k] = [
                'url' => $fmt['url'],
                'checkbox' => !empty($fmt['checkbox']),
                'checkbox_label' => isset($fmt['checkbox_label']) ? $fmt['checkbox_label'] : '',
                'checkbox_url' => isset($fmt['checkbox_url']) ? $fmt['checkbox_url'] : '',
            ];
        }
        echo json_encode($betaExportFormatsJs);
    ?>;

    function betaExportFormatChanged(key) {
        var fmt = betaExportFormats[key];
        if (!fmt) return;
        var $row = $('#beta-export-checkbox-row');
        var $label = $('#beta-export-checkbox-label');
        var $cb = $('#beta-export-checkbox');
        if (fmt.checkbox && fmt.checkbox_label) {
            $label.text(fmt.checkbox_label);
            $cb.prop('checked', false);
            $row.show();
        } else {
            $row.hide();
            $cb.prop('checked', false);
        }
    }

    function betaExportDownload() {
        var key = $('#beta-export-format').val();
        var fmt = betaExportFormats[key];
        if (!fmt) return;
        var url = fmt.url;
        if (fmt.checkbox && $('#beta-export-checkbox').prop('checked') && fmt.checkbox_url) {
            url = fmt.checkbox_url;
        }
        window.location.href = url;
    }

    function betaClearAttributeFilter() {
        $('.filter-active-msg').remove();
        if (typeof betaPagination !== 'undefined') {
            betaPagination.searchActive = false;
            betaPagination.attributeType = '';
            $('.beta-pagination-container').show();
            if (typeof window.betaPaginationLoadPage === 'function') {
                window.betaPaginationLoadPage(1, window.betaPagination.pageSize);
            } else {
                $('.beta-attr-row').show();
            }
        } else {
            $('.beta-attr-row').show();
        }
    }

    function betaFilterAttributesByComposition(type, name) {
        // Switch to Attributes tab
        $('.nav-tabs a[href="#attributes"]').tab('show');

        $('.filter-active-msg').remove();

        if (type === 'attribute' && typeof betaPagination !== 'undefined' && typeof window.betaPaginationLoadPage === 'function') {
            betaPagination.searchActive = false;
            betaPagination.attributeType = name;
            $('.beta-pagination-container').show();
            window.betaPaginationLoadPage(1, betaPagination.pageSize);
        } else {
            // Fallback for non-attribute data
            $('.beta-attr-row').show();
            $('.beta-attr-row').hide();
            if (type === 'object') {
                $('.beta-attr-row[data-object-name="' + name + '"]').show();
                $('.beta-attr-row[data-parent-object="' + name + '"]').show();
            } else {
                $('.beta-attr-row[data-attribute-type="' + name + '"]').show();
            }
        }

        // Show message
        var msg = '<div class="alert alert-warning filter-active-msg" style="margin-top: 10px;">';
        msg += '<button type="button" class="close" onclick="betaClearAttributeFilter(); $(this).parent().remove();">×</button>';
        msg += 'Filtering by <strong>' + (type === 'object' ? 'Object: ' : 'Attribute: ') + name + '</strong>';
        msg += ' <a href="#" onclick="betaClearAttributeFilter(); return false;">(Clear Filter)</a>';
        msg += '</div>';

        if ($('#beta-filter-banner-slot').length) {
            $('#beta-filter-banner-slot').html(msg);
        } else {
            $('#attributes').prepend(msg);
        }
    }

    // Auto-resize report preview iframe based on content height
    <?php if (!empty($firstEventReportId)): ?>
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'reportPreviewResize') {
            var iframe = document.getElementById('summary-report-iframe');
            if (iframe) {
                var maxHeight = 300;
                var newHeight = Math.min(event.data.height + 10, maxHeight);
                iframe.style.height = newHeight + 'px';
                // Show scrollbar if content exceeds max height
                iframe.scrolling = event.data.height > maxHeight ? 'auto' : 'no';
            }
        }
    });
    <?php endif; ?>

    function betaFilterAttributesByComment(comment) {
        // Switch to Attributes tab
        $('.nav-tabs a[href="#attributes"]').tab('show');
        
        // Disable pagination during filtering
        if (typeof betaPagination !== 'undefined') {
            betaPagination.searchActive = true;
            $('.beta-pagination-container').hide();
        }

        // Reset previous filters
        $('.beta-attr-row').show();
        $('.filter-active-msg').remove();

        // Apply filter
        $('.beta-attr-row').hide();
        
        // Show rows where the comment column matches
        $('.beta-attr-row').each(function() {
            var $commentCell = $(this).find('.col-comment').first();
            var rowComment = ($commentCell.data('comment-full') || $commentCell.text() || '').trim();
            if (rowComment === comment) {
                $(this).show();
            }
        });

        // Show message
        var msg = '<div class="alert alert-warning filter-active-msg" style="margin-top: 10px;">';
        msg += '<button type="button" class="close" onclick="betaClearAttributeFilter(); $(this).parent().remove();">×</button>';
        msg += 'Filtering by Comment: <strong>' + comment + '</strong>';
        msg += ' <a href="#" onclick="betaClearAttributeFilter(); return false;">(Clear Filter)</a>';
        msg += '</div>';
        
        // Insert message after toolbar in attributes tab
        if ($('.beta-toolbar').length) {
             $('.beta-toolbar').after(msg);
        } else {
             // Fallback
             $('#attributes').prepend(msg);
        }
    }

    function betaToggleSummaryReports(forceOpen) {
        var $wrap = $('#summary-reports-content-wrap');
        if (!$wrap.length) return;

        var open = (typeof forceOpen === 'boolean') ? forceOpen : !$wrap.is(':visible');
        var $icon = $('#summary-reports-toggle-icon');

        if (open) {
            $wrap.stop(true, true).slideDown(140);
            $icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
        } else {
            $wrap.stop(true, true).slideUp(140);
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
        }
    }

    var _correlationData = null;
    var _correlationEventDetails = null;
    var _correlationsLoading = false;

    function loadCorrelations() {
        if (_correlationData !== null || _correlationsLoading) return;
        _correlationsLoading = true;
        var eventId = '<?php echo h($event['Event']['id']); ?>';
        $.ajax({
            url: '<?php echo $baseurl; ?>/correlations/eventCorrelations/' + eventId + '.json?extended=1',
            type: 'GET',
            success: function(response) {
                _correlationsLoading = false;
                $('#correlations-loader').hide();
                $('#correlations-content').show();
                renderCorrelations(response);
                // Apply any pending filter
                if (_pendingCorrelationFilter !== null) {
                    var pendingFilter = _pendingCorrelationFilter;
                    _pendingCorrelationFilter = null;
                    _applyCorrelationFilter(pendingFilter);
                    $('html, body').animate({
                        scrollTop: $(".beta-tabs-container").offset().top
                    }, 500);
                }
            },
            error: function() {
                _correlationsLoading = false;
                $('#correlations-loader').html('<p class="text-danger"><?php echo __('Failed to load correlations.'); ?></p>');
            }
        });
    }

    function renderCorrelations(data) {
            _correlationData = data;
            var eventCounts = {};
            var eventDetails = {};
            var attributeMap = {};

            // Process data
            for (var parentId in data) {
                var relations = data[parentId];
                relations.forEach(function(rel) {
                    var eid = rel.id;
                    if (!eventCounts[eid]) {
                        eventCounts[eid] = 0;
                        eventDetails[eid] = {info: rel.info, date: rel.date, org: rel.org_id};
                    }
                    eventCounts[eid]++;
                    
                    if (!attributeMap[eid]) attributeMap[eid] = [];
                    attributeMap[eid].push({
                        id: parentId,
                        value: rel.value || parentId, // Fallback to ID if value not present
                        type: rel.type || '',
                        attribute: rel.Attribute || null
                    });
                });
            }

            var sortedEvents = Object.keys(eventCounts).sort(function(a,b){return eventCounts[b]-eventCounts[a]});
            if (sortedEvents.length === 0) {
                $('#correlations-table-container').html('<p class="muted"><?php echo __('No correlations found.'); ?></p>');
                return;
            }

            var max = eventCounts[sortedEvents[0]];
            var html = '<div class="beta-correlations-container">';
            
            sortedEvents.forEach(function(eid) {
                var count = eventCounts[eid];
                var details = eventDetails[eid];
                var percent = (count / max) * 100;
                var attrs = attributeMap[eid];
                var attrIds = attrs.map(function(a) { return a.id; }).join(',');
                
                html += '<div class="beta-card correlation-event-card" data-attribute-ids=",' + attrIds + '," style="margin-bottom: 20px; border-left: 4px solid #428bca;">';
                html += '  <div class="beta-card-header" style="display: flex; justify-content: space-between; align-items: center; background: #f8fbfe;">';
                html += '    <div style="display: flex; align-items: center; gap: 10px;">';
                html += '      <a href="<?php echo $baseurl; ?>/events/view/' + eid + '" style="font-weight: 700; font-size: 1.1em;">#' + eid + ' ' + details.info + '</a>';
                html += '      <span class="label label-default" style="font-weight: normal;">' + details.date + '</span>';
                html += '    </div>';
                html += '    <div style="text-align: right;">';
                html += '      <span style="font-size: 12px; font-weight: 600; color: #666;">' + count + ' ' + (count === 1 ? 'match' : 'matches') + '</span>';
                html += '      <div style="width: 100px; height: 4px; background: #eee; border-radius: 2px; margin-top: 4px;">';
                html += '        <div style="width: ' + percent + '%; height: 100%; background: #428bca; border-radius: 2px;"></div>';
                html += '      </div>';
                html += '    </div>';
                html += '  </div>';
                html += '  <div class="beta-card-body" style="padding: 0;">';
                html += '    <table class="beta-attr-table" style="margin-top: 0;">';
                html += '      <tbody>';
                
                attrs.forEach(function(a) {
                    var attr = a.attribute;
                    if (attr) {
                        html += '        <tr class="beta-attr-row standalone-attr-row" data-attribute-id="' + a.id + '">';
                        html += '          <td style="width: 40px; text-align: center;"><i class="fa fa-link" style="color: #ccc;"></i></td>';
                        html += '          <td colspan="2">';
                        html += '            <div class="beta-attr-meta-block">';
                        html += '              <div class="beta-attr-type-path">';
                        html += '                <span class="beta-category-label">' + attr.category + '</span>';
                        html += '                <i class="fa fa-chevron-right" style="font-size: 8px; color: #ccc;"></i>';
                        html += '                <span class="beta-type-insight">' + attr.type + '</span>';
                        if (attr.Object) {
                            html += '                <i class="fa fa-cube" style="font-size: 10px; color: #31708f; margin-left: 5px;"></i>';
                            html += '                <span class="beta-object-relation-insight">' + attr.Object.name + '</span>';
                        }
                        html += '              </div>';
                        html += '              <div class="beta-attr-value-container">';
                        html += '                <span class="attr-value">' + attr.value + '</span>';
                        html += '              </div>';
                        
                        if (attr.AttributeTag && attr.AttributeTag.length > 0) {
                            html += '              <div class="beta-attr-tags-inline">';
                            attr.AttributeTag.forEach(function(at) {
                                var tag = at.Tag;
                                var tagColor = tag.colour || '#0088cc';
                                var hex = tagColor.replace('#', '');
                                var r, g, b;
                                if (hex.length === 3) {
                                    r = parseInt(hex[0] + hex[0], 16);
                                    g = parseInt(hex[1] + hex[1], 16);
                                    b = parseInt(hex[2] + hex[2], 16);
                                } else {
                                    r = parseInt(hex.substring(0, 2), 16);
                                    g = parseInt(hex.substring(2, 4), 16);
                                    b = parseInt(hex.substring(4, 6), 16);
                                }
                                var rgba = 'rgba(' + r + ', ' + g + ', ' + b + ', 0.7)';
                                var luminance = (0.2126 * r + 0.7152 * g + 0.0722 * b) / 255;
                                var iconColor = luminance > 0.5 ? '#000' : '#fff';
                                
                                 html += '<div class="tag-container tag-wrapper-beta" style="display: inline-flex; align-items: stretch; margin-right: 4px; margin-bottom: 2px;">';
                                 html += '  <span class="tag-scope-icon" style="background-color: ' + rgba + '; color: ' + iconColor + '; display: inline-flex; align-items: center; justify-content: center; padding: 4px 6px; border-radius: 4px 0 0 4px; border: 1px solid #d0d0d0; border-right: none;"><i class="fas fa-' + (tag.local ? 'user' : 'globe-americas') + '" style="font-size: 11px;"></i></span>';
                                html += '  <span class="tag nowrap" style="background-color: transparent; border: 1px solid #d0d0d0; color: #000; padding: 3px 8px; font-size: 12px; border-radius: 0 4px 4px 0;">' + tag.name + '</span>';
                                html += '</div>';
                            });
                            html += '              </div>';
                        }
                        html += '            </div>';
                        html += '          </td>';
                        html += '          <td class="col-related"></td>';
                        html += '          <td class="col-comment" style="width: 20%;">';
                        if (attr.comment) {
                            if (attr.comment.length > 50) {
                                html += attr.comment.substring(0, 50) + '... ';
                                html += '<i class="fa fa-comment-dots" style="cursor: pointer;" data-toggle="popover" data-trigger="click" data-placement="top" data-content="' + attr.comment + '"></i>';
                            } else {
                                html += attr.comment;
                            }
                        }
                        html += '          </td>';
                        html += '          <td style="text-align: center;">';
                        html += '            <i class="fa fa-shield-alt" style="font-size: 1.5em; ' + (attr.to_ids ? 'color: #ff8c00;' : 'opacity: 0.2;') + '" title="' + (attr.to_ids ? 'Recommended for blocking / alerting' : 'Not recommended for blocking / alerting') + '"></i>';
                        html += '          </td>';
                        html += '          <td class="col-correlation" style="text-align: center;">';
                        html += '            <i class="fa fa-project-diagram" style="' + (attr.disable_correlation ? 'opacity: 0.2;' : 'color: #428bca;') + '" title="' + (attr.disable_correlation ? 'Correlation disabled' : 'Correlation enabled') + '"></i>';
                        html += '          </td>';
                        html += '          <td class="col-sightings" style="text-align: center;"><i class="fa fa-eye" style="color: #ccc;"></i></td>';
                        html += '          <td class="col-distribution" style="text-align: center;">';
                        html += '            <div class="dist-widget dist-' + parseInt(attr.distribution) + '" title="' + (attr.SharingGroup ? attr.SharingGroup.name : "") + '"></div>';
                        html += '          </td>';
                        html += '          <td class="col-date" style="width: 80px;">' + moment.unix(attr.timestamp).format('YYYY-MM-DD') + '</td>';
                        html += '        </tr>';
                    } else {
                        // Fallback for when attribute data is missing
                        html += '        <tr class="beta-attr-row">';
                        html += '          <td style="width: 40px;"></td>';
                        html += '          <td colspan="5"><span class="label label-info">' + a.value + '</span></td>';
                        html += '        </tr>';
                    }
                });
                
                html += '      </tbody>';
                html += '    </table>';
                html += '  </div>';
                html += '</div>';
            });
            
            html += '</div>';
            $('#correlations-table-container').html(html);
            
            _correlationEventDetails = eventDetails;
            renderSankey(data, eventDetails);
        }

        function renderSankey(data, eventDetails, filterAttributeId) {
            var nodes = [];
            var links = [];
            var nodeMap = {};
            var currentEventId = '<?php echo h($event['Event']['id']); ?>';
            var currentEventInfo = '<?php echo addslashes(h($event['Event']['info'])); ?>';
            var currentEventName = 'Event #' + currentEventId;
            var currentEventFullTitle = currentEventName;
            if (currentEventInfo) {
                currentEventFullTitle += ': ' + currentEventInfo;
                if (currentEventInfo.length > 40) {
                    currentEventName += ': ' + currentEventInfo.substring(0, 40) + '...';
                } else {
                    currentEventName += ': ' + currentEventInfo;
                }
            }
            
            function addNode(name, type, id, fullTitle) {
                var key = name + '_' + type;
                if (nodeMap[key] === undefined) {
                    nodeMap[key] = nodes.length;
                    nodes.push({name: name, type: type, id: id, fullTitle: fullTitle});
                }
                return nodeMap[key];
            }

            var sourceIdx = addNode(currentEventName, 'source', currentEventId, currentEventFullTitle);
            
            // If a filter is active, only show the filtered attribute; otherwise limit to top correlations
            var maxSankeyAttributes = 100;
            var parentIds;
            if (filterAttributeId) {
                // Only include the filtered attribute if it exists in data
                parentIds = Object.keys(data).filter(function(id) { return id == filterAttributeId; });
            } else {
                parentIds = Object.keys(data).sort(function(a, b) {
                    return data[b].length - data[a].length;
                });
            }
            
            var totalAttributes = parentIds.length;
            if (!filterAttributeId && parentIds.length > maxSankeyAttributes) {
                parentIds = parentIds.slice(0, maxSankeyAttributes);
            }

            var eventLinkCounts = {};
            if (eventDetails) {
                for (var eid in eventDetails) {
                    eventLinkCounts[eid] = 0;
                }
            }
            
            // Calculate how many attributes lead to each event
            parentIds.forEach(function(parentId) {
                var relations = data[parentId];
                relations.forEach(function(rel) {
                    if (eventLinkCounts[rel.id] !== undefined) {
                        eventLinkCounts[rel.id]++;
                    } else {
                        eventLinkCounts[rel.id] = 1;
                    }
                });
            });

            parentIds.forEach(function(parentId) {
                var relations = data[parentId];
                var attrValue = relations[0].value || ('Attr #' + parentId);
                var attrIdx = addNode(attrValue, 'attribute', parentId);
                
                links.push({
                    source: sourceIdx,
                    target: attrIdx,
                    value: relations.length
                });

                relations.forEach(function(rel) {
                    var count = eventLinkCounts[rel.id] || 1;
                    var targetEventName = 'Event #' + rel.id;
                    if (count > 1) {
                        targetEventName = '(' + count + ') ' + targetEventName;
                    }
                    var fullTitle = targetEventName;
                    if (rel.info) {
                        fullTitle += ': ' + rel.info;
                        if (rel.info.length > 40) {
                            targetEventName += ': ' + rel.info.substring(0, 40) + '...';
                        } else {
                            targetEventName += ': ' + rel.info;
                        }
                    }
                    var targetIdx = addNode(targetEventName, 'target', rel.id, fullTitle);
                    
                    links.push({
                        source: attrIdx,
                        target: targetIdx,
                        value: 1
                    });
                });
            });

            var displayedEventsNodeIds = {};
            links.forEach(function(l) {
                if (nodes[l.target] && nodes[l.target].type === 'target') {
                    displayedEventsNodeIds[nodes[l.target].id] = true;
                }
            });
            var totalDisplayedEvents = Object.keys(displayedEventsNodeIds).length;
            var totalPossibleEvents = Object.keys(eventDetails || {}).length;

            if (totalPossibleEvents > totalDisplayedEvents) {
                $('#sankey-limit-msg').text('<?php echo __("Showing top %s of %s correlating events", "' + totalDisplayedEvents + '", "' + totalPossibleEvents + '"); ?>');
            } else {
                $('#sankey-limit-msg').text('<?php echo __("Showing %s correlating events", "' + totalDisplayedEvents + '"); ?>');
            }

            if (links.length === 0) return;
            $('#correlations-sankey-container').show();

            var margin = {top: 10, right: 350, bottom: 10, left: 10},
                width = $('#correlations-sankey').width() - margin.left - margin.right;
            
            // Dynamic height: base height + extra per attribute node
            var height = Math.max(400, (parentIds.length * 20) + 100);
            $('#correlations-sankey').css('height', height + 'px');
            
            height = height - margin.top - margin.bottom;

            $('#correlations-sankey').empty();
            var svg = d3.select("#correlations-sankey").append("svg")
                .attr("width", width + margin.left + margin.right)
                .attr("height", height + margin.top + margin.bottom)
                .append("g")
                .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

            var sankey = d3.sankey()
                .nodeWidth(15)
                .nodePadding(10)
                .extent([[1, 1], [width - 1, height - 6]]);

            var graph = sankey({
                nodes: nodes.map(function(d) { return Object.assign({}, d); }),
                links: links.map(function(d) { return Object.assign({}, d); })
            });

            // D3 v3 compatibility for scale and color
            var color = d3.scale ? d3.scale.category10() : (d3.scaleOrdinal ? d3.scaleOrdinal(d3.schemeCategory10) : function() { return '#428bca'; });

            // D3 v3 compatibility: use enter().append() instead of join()
            svg.append("g")
                .selectAll("rect")
                .data(graph.nodes)
                .enter()
                .append("rect")
                .attr("x", function(d) { return d.x0; })
                .attr("y", function(d) { return d.y0; })
                .attr("height", function(d) { return d.y1 - d.y0; })
                .attr("width", function(d) { return d.x1 - d.x0; })
                .attr("fill", function(d) { return typeof color === 'function' ? color(d.type) : color; })
                .attr("cursor", function(d) { return (d.type === 'target' || d.type === 'attribute') ? 'pointer' : 'default'; })
                .on("click", function(d) {
                    if (d.type === 'target' && d.id) {
                        window.location.href = '<?php echo $baseurl; ?>/events/view/' + d.id;
                    } else if (d.type === 'attribute' && d.id) {
                        filterCorrelations(d.id);
                    }
                })
                .on("mouseover", function(d) {
                    if (d.type === 'attribute' || d.type === 'target') {
                        svg.selectAll(".sankey-link")
                            .transition()
                            .duration(200)
                            .style("stroke-opacity", function(l) {
                                if (d.type === 'attribute') {
                                    // Highlight links connected to this attribute (both from source and to targets)
                                    return (l.source === d || l.target === d) ? 0.7 : 0.1;
                                } else if (d.type === 'target') {
                                    // Highlight links leading to this target, AND the links from source to the attributes that lead to this target
                                    var isDirectLink = (l.target === d);
                                    var isPathFromSource = false;
                                    if (!isDirectLink) {
                                        // Check if l is a link from source to an attribute that connects to d
                                        graph.links.forEach(function(l2) {
                                            if (l2.target === d && l2.source === l.target && l.source.type === 'source') {
                                                isPathFromSource = true;
                                            }
                                        });
                                    }
                                    return (isDirectLink || isPathFromSource) ? 0.7 : 0.1;
                                }
                                return 0.1;
                            });
                        svg.selectAll(".sankey-label")
                            .transition()
                            .duration(200)
                            .style("opacity", function(n) {
                                if (n === d) return 1;
                                if (d.type === 'attribute') {
                                    // Highlight source and targets connected to this attribute
                                    var connected = false;
                                    graph.links.forEach(function(l) {
                                        if ((l.source === d && l.target === n) || (l.target === d && l.source === n)) connected = true;
                                    });
                                    return connected ? 1 : 0.1;
                                } else if (d.type === 'target') {
                                    // Highlight source and attributes connected to this target
                                    var connected = false;
                                    if (n.type === 'source') {
                                        // Source is always connected to any target via some attribute
                                        connected = true;
                                    } else {
                                        graph.links.forEach(function(l) {
                                            if (l.target === d && l.source === n) {
                                                connected = true;
                                            }
                                        });
                                    }
                                    return connected ? 1 : 0.1;
                                }
                                return 0.1;
                            });
                    }
                })
                .on("mouseout", function(d) {
                    if (d.type === 'attribute' || d.type === 'target') {
                        svg.selectAll(".sankey-link")
                            .transition()
                            .duration(200)
                            .style("stroke-opacity", 0.5);
                        svg.selectAll(".sankey-label")
                            .transition()
                            .duration(200)
                            .style("opacity", 1);
                    }
                })
                .append("title")
                .text(function(d) { return d.fullTitle || d.name; });

            var link = svg.append("g")
                .attr("fill", "none")
                .attr("stroke-opacity", 0.5)
                .selectAll("path")
                .data(graph.links)
                .enter()
                .append("path")
                .attr("class", "sankey-link")
                .attr("d", function(d) {
                    var x0 = d.source.x1,
                        x1 = d.target.x0,
                        xi = d3.interpolateNumber(x0, x1),
                        x2 = xi(0.5),
                        x3 = xi(0.5),
                        y0 = d.y0,
                        y1 = d.y1;
                    return "M" + x0 + "," + y0
                         + "C" + x2 + "," + y0
                         + " " + x3 + "," + y1
                         + " " + x1 + "," + y1;
                })
                .attr("stroke", function(d) { return typeof color === 'function' ? color(d.source.type) : color; })
                .attr("stroke-width", function(d) { return Math.max(1, d.width); });

            svg.append("g")
                .style("font", "10px sans-serif")
                .selectAll("text")
                .data(graph.nodes)
                .enter()
                .append("text")
                .attr("class", "sankey-label")
                .attr("x", function(d) { return d.x0 < width / 2 ? d.x1 + 6 : d.x0 - 6; })
                .attr("y", function(d) { return (d.y1 + d.y0) / 2; })
                .attr("dy", "0.35em")
                .attr("text-anchor", function(d) { return d.x0 < width / 2 ? "start" : "end"; })
                .attr("cursor", function(d) { return (d.type === 'target' || d.type === 'attribute') ? 'pointer' : 'default'; })
                .style("font-weight", function(d) { return (d.type === 'target' || d.type === 'attribute') ? 'bold' : 'normal'; })
                .on("click", function(d) {
                    if (d.type === 'target' && d.id) {
                        window.location.href = '<?php echo $baseurl; ?>/events/view/' + d.id;
                    } else if (d.type === 'attribute' && d.id) {
                        filterCorrelations(d.id);
                    }
                })
                .on("mouseover", function(d) {
                    if (d.type === 'attribute' || d.type === 'target') {
                        svg.selectAll(".sankey-link")
                            .transition()
                            .duration(200)
                            .style("stroke-opacity", function(l) {
                                var isConnected = (l.source === d || l.target === d);
                                if (!isConnected && d.type === 'target') {
                                    // Check if this link is part of the path to the target
                                    graph.links.forEach(function(l2) {
                                        if (l2.target === d && l2.source === l.target && l.source.type === 'source') isConnected = true;
                                    });
                                }
                                return isConnected ? 0.5 : 0.1;
                            });
                        svg.selectAll(".sankey-label")
                            .transition()
                            .duration(200)
                            .style("opacity", function(n) {
                                if (n === d) return 1;
                                if (d.type === 'attribute') {
                                    var connected = false;
                                    graph.links.forEach(function(l) {
                                        if ((l.source === d && l.target === n) || (l.target === d && l.source === n)) connected = true;
                                    });
                                    return connected ? 1 : 0.1;
                                } else if (d.type === 'target') {
                                    var connected = false;
                                    graph.links.forEach(function(l) {
                                        if (l.target === d && l.source === n) {
                                            connected = true;
                                        } else if (l.target === d) {
                                            graph.links.forEach(function(l2) {
                                                if (l2.target === l.source && l2.source === n) connected = true;
                                            });
                                        }
                                    });
                                    return connected ? 1 : 0.1;
                                }
                                return 0.1;
                            });
                    }
                })
                .on("mouseout", function(d) {
                    if (d.type === 'attribute' || d.type === 'target') {
                        svg.selectAll(".sankey-link")
                            .transition()
                            .duration(200)
                            .style("stroke-opacity", 0.5);
                        svg.selectAll(".sankey-label")
                            .transition()
                            .duration(200)
                            .style("opacity", 1);
                    }
                })
                .text(function(d) {
                    if (d.type === 'source') return d.name;
                    var maxLength = d.x0 < width / 2 ? 50 : 70;
                    return d.name.length > maxLength ? d.name.substring(0, maxLength - 3) + '...' : d.name;
                });
        }

    $(document).ready(function() {
        function updatePublishedLabelState(isPublished) {
            var $label = $('#publishedLabel');
            if (!$label.length) {
                return;
            }
            $label.removeClass('state-published state-unpublished')
                .addClass(isPublished ? 'state-published' : 'state-unpublished')
                .text(isPublished ? '<?php echo addslashes(__('Published')); ?>' : '<?php echo addslashes(__('Unpublished')); ?>');
        }

        $('a[data-toggle="tab"][href="#correlations"]').on('shown.bs.tab', function (e) {
            loadCorrelations();
        });

        // Check if we are already on the correlations tab on page load
        if (window.location.hash === '#correlations') {
            loadCorrelations();
        }

        if ($('#publishedToggle').length) {
            updatePublishedLabelState($('#publishedToggle').is(':checked'));
        }

        $('#publishedToggle').change(function() {
            var $toggle = $(this);
            var id = $toggle.data('id');
            var isChecked = $toggle.is(':checked');
            var action = isChecked ? 'publish' : 'unpublish';
            var url = '<?php echo $baseurl; ?>/events/' + action + '/' + id + '.json';
            
            // Disable to prevent double clicks
            $toggle.prop('disabled', true);

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    $toggle.prop('disabled', false);
                    if (response.saved || (response.response && response.response.saved)) {
                         updatePublishedLabelState(isChecked);
                         showMessage('success', response.message || (response.response ? response.response.message : 'Event updated'));
                    } else {
                        // Revert
                        $toggle.prop('checked', !isChecked);
                        updatePublishedLabelState(!isChecked);
                        showMessage('fail', response.message || (response.response ? response.response.message : 'Action failed'));
                        if (response.errors) {
                             console.error(response.errors);
                        }
                    }
                },
                error: function(xhr) {
                    $toggle.prop('disabled', false);
                    $toggle.prop('checked', !isChecked);
                    updatePublishedLabelState(!isChecked);
                    xhrFailCallback(xhr);
                }
            });
        });
    });

    window.viewFullReport = function(reportId) {
        var url = baseurl + '/eventReports/viewRendered/' + reportId;
        var modalHtml = 
            '<div id="reportViewModal" class="modal hide fade" tabindex="-1" role="dialog" style="width: 94%; left: 3%; margin-left: 0; top: 3%; height: 94%;">' +
            '    <div class="modal-header" style="padding: 10px 15px;">' +
            '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>' +
            '        <h3 style="margin: 0; line-height: 1.5;"><?php echo __('Report Preview'); ?></h3>' +
            '    </div>' +
            '    <div class="modal-body" style="max-height: none; height: calc(100% - 100px); padding: 0; overflow: hidden;">' +
            '        <iframe src="' + url + '" style="width: 100%; height: 100%; border: none;"></iframe>' +
            '    </div>' +
            '    <div class="modal-footer" style="padding: 10px 15px;">' +
            '        <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php echo __('Close'); ?></button>' +
            '    </div>' +
            '</div>';
        
        $('#reportViewModal').remove();
        $('body').append(modalHtml);
        $('#reportViewModal').modal();
    };
    // Pending filter to apply once correlations are loaded
    var _pendingCorrelationFilter = null;

    function filterCorrelations(attributeId) {
        // Always switch to correlations tab
        $('.nav-tabs a[href="#correlations"]').tab('show');

        // If correlations haven't loaded yet, store the filter and apply it once loaded
        if (!_correlationData) {
            _pendingCorrelationFilter = attributeId;
            return;
        }

        _applyCorrelationFilter(attributeId);

        // Scroll to top of correlations tab
        $('html, body').animate({
            scrollTop: $(".beta-tabs-container").offset().top
        }, 500);
    }

    function _applyCorrelationFilter(attributeId) {
        // Re-render the Sankey with or without filter
        if (_correlationData && _correlationEventDetails) {
            if (attributeId) {
                // Get the attribute value for the label
                var attrValue = attributeId;
                if (_correlationData[attributeId] && _correlationData[attributeId].length > 0) {
                    attrValue = _correlationData[attributeId][0].value || attributeId;
                }
                renderSankey(_correlationData, _correlationEventDetails, attributeId);
                $('#sankey-filter-label').text('<?php echo __('Filtered'); ?>: ' + attrValue);
                $('#sankey-filter-badge').show();
            } else {
                renderSankey(_correlationData, _correlationEventDetails);
                $('#sankey-filter-badge').hide();
                $('#sankey-filter-label').text('');
            }
        }

        // Remove any existing "this event" card
        $('#correlations-this-event-card').remove();

        // Filter the correlations table cards and rows within them
        var cards = $('.correlation-event-card');
        if (attributeId) {
            var attrValue = attributeId;
            var attrType = '';
            var attrCategory = '';
            if (_correlationData && _correlationData[attributeId] && _correlationData[attributeId].length > 0) {
                var firstRel = _correlationData[attributeId][0];
                attrValue = firstRel.value || attributeId;
            }
            // Try to get type/category from the DOM (attributes table)
            var domRow = $('[data-primary-id="' + attributeId + '"]');
            if (domRow.length) {
                attrType = domRow.find('.beta-type-insight').first().text().trim();
                attrCategory = domRow.find('.beta-category-label').first().text().trim();
            }

            // Show only cards that contain this attribute; within each card, show only matching rows
            cards.each(function() {
                var card = $(this);
                var attrIds = card.data('attribute-ids') || '';
                var hasAttr = attrIds.indexOf(',' + attributeId + ',') !== -1;
                if (hasAttr) {
                    card.show();
                    // Hide non-matching rows, show matching rows
                    card.find('.standalone-attr-row').each(function() {
                        var row = $(this);
                        var rowAttrId = row.data('attribute-id');
                        row.toggle(rowAttrId == attributeId);
                    });
                } else {
                    card.hide();
                }
            });

            // Build "This Event" card showing the current event's attribute
            // Clone the meta block from the DOM to include tags, comments, etc.
            var currentEventId = '<?php echo h($event['Event']['id']); ?>';
            var currentEventInfo = '<?php echo addslashes(h($event['Event']['info'])); ?>';

            // Clone cells from the DOM row for a complete display
            var metaBlockHtml = '';
            var attrComment = '';
            var idsHtml = '';
            var correlationHtml = '';
            var sightingsHtml = '';
            var distributionHtml = '';
            var dateHtml = '';

            if (domRow.length) {
                // Clone the full meta block (includes type path, value, tags, galaxies)
                var metaBlock = domRow.find('.beta-attr-meta-block').first().clone();
                metaBlock.find('.beta-tagging-links').remove();
                metaBlock.find('.beta-row-menu').remove();
                // Make the attr-value non-clickable
                metaBlock.find('.attr-value-correlatable').removeClass('attr-value-correlatable').removeAttr('onclick').css({'cursor': 'default', 'border-bottom': 'none'});
                metaBlockHtml = metaBlock.prop('outerHTML');

                // Clone other cells
                attrComment = domRow.find('.col-comment').first().html() || '';
                // IDS toggle cell (the shield icon)
                var idsCell = domRow.find('td:has(.beta-ids-toggle)').first();
                if (idsCell.length) {
                    var idsClone = idsCell.clone();
                    idsClone.find('.beta-ids-toggle').removeAttr('onclick').css('cursor', 'default');
                    idsHtml = idsClone.html();
                }
                // Correlation toggle cell
                var corrCell = domRow.find('.col-correlation').first();
                if (corrCell.length) {
                    var corrClone = corrCell.clone();
                    corrClone.find('.beta-correlation-toggle').removeAttr('onclick').css('cursor', 'default');
                    correlationHtml = corrClone.html();
                }
                // Sightings cell
                var sightCell = domRow.find('.col-sightings').first();
                if (sightCell.length) {
                    sightingsHtml = sightCell.clone().html();
                }
                // Distribution cell
                var distCell = domRow.find('.col-distribution').first();
                if (distCell.length) {
                    distributionHtml = distCell.clone().html();
                }
                // Date cell
                var dateCell = domRow.find('.col-date').first();
                if (dateCell.length) {
                    dateHtml = dateCell.clone().html();
                }
            }

            var thisEventHtml = '<div id="correlations-this-event-card" class="beta-card" style="margin-bottom: 20px; border-left: 4px solid #5cb85c; background: #f0fff4;">';
            thisEventHtml += '  <div class="beta-card-header" style="display: flex; justify-content: space-between; align-items: center; background: #e8f8ed;">';
            thisEventHtml += '    <div style="display: flex; align-items: center; gap: 10px;">';
            thisEventHtml += '      <span class="label label-success" style="font-size: 12px; padding: 4px 8px;"><i class="fa fa-star"></i> <?php echo __('This Event'); ?></span>';
            thisEventHtml += '      <a href="<?php echo $baseurl; ?>/events/view/' + currentEventId + '" style="font-weight: 700; font-size: 1.1em;">#' + currentEventId + ' ' + currentEventInfo + '</a>';
            thisEventHtml += '    </div>';
            thisEventHtml += '    <span style="font-size: 11px; color: #3d8b5e; font-style: italic;"><?php echo __('Source attribute'); ?></span>';
            thisEventHtml += '  </div>';
            thisEventHtml += '  <div class="beta-card-body" style="padding: 0;">';
            thisEventHtml += '    <table class="beta-attr-table" style="margin-top: 0;">';
            thisEventHtml += '      <tbody>';
            thisEventHtml += '        <tr class="beta-attr-row standalone-attr-row" style="background: #f0fff4;">';
            thisEventHtml += '          <td style="width: 40px; text-align: center;"><i class="fa fa-star" style="color: #5cb85c;"></i></td>';
            if (metaBlockHtml) {
                thisEventHtml += '          <td colspan="2">' + metaBlockHtml + '</td>';
            } else {
                // Fallback: build manually
                thisEventHtml += '          <td colspan="2">';
                thisEventHtml += '            <div class="beta-attr-meta-block">';
                if (attrCategory || attrType) {
                    thisEventHtml += '              <div class="beta-attr-type-path">';
                    if (attrCategory) {
                        thisEventHtml += '                <span class="beta-category-label">' + attrCategory + '</span>';
                        thisEventHtml += '                <i class="fa fa-chevron-right" style="font-size: 8px; color: #ccc;"></i>';
                    }
                    if (attrType) {
                        thisEventHtml += '                <span class="beta-type-insight">' + attrType + '</span>';
                    }
                    thisEventHtml += '              </div>';
                }
                thisEventHtml += '              <div class="beta-attr-value-container">';
                thisEventHtml += '                <span class="attr-value" style="font-weight: 600;">' + attrValue + '</span>';
                thisEventHtml += '              </div>';
                thisEventHtml += '            </div>';
                thisEventHtml += '          </td>';
            }
            thisEventHtml += '          <td class="col-related"></td>';
            thisEventHtml += '          <td class="col-comment" style="width: 20%;">' + attrComment + '</td>';
            thisEventHtml += '          <td style="text-align: center;">' + idsHtml + '</td>';
            thisEventHtml += '          <td class="col-correlation" style="text-align: center;">' + correlationHtml + '</td>';
            thisEventHtml += '          <td class="col-sightings" style="text-align: center;">' + sightingsHtml + '</td>';
            thisEventHtml += '          <td class="col-distribution" style="text-align: center;">' + distributionHtml + '</td>';
            thisEventHtml += '          <td class="col-date" style="width: 80px;">' + dateHtml + '</td>';
            thisEventHtml += '        </tr>';
            thisEventHtml += '      </tbody>';
            thisEventHtml += '    </table>';
            thisEventHtml += '  </div>';
            thisEventHtml += '</div>';

            // Insert "This Event" card before the first correlation card
            var container = $('#correlations-table-container .beta-correlations-container');
            if (container.length) {
                container.prepend(thisEventHtml);
            } else {
                $('#correlations-table-container').prepend(thisEventHtml);
            }

            // Show filter banner above the table
            $('#correlations-table-filter-msg').text('<?php echo __('Showing correlations for'); ?>: ' + attrValue);
            $('#correlations-table-filter-banner').css('display', 'flex');

            $('#correlation-filter-msg').text('<?php echo __('Filtered by'); ?>: ' + attrValue);
            $('#correlation-filter-controls').show();
        } else {
            // Restore all cards and all rows
            cards.show();
            cards.find('.standalone-attr-row').show();
            $('#correlations-table-filter-banner').hide();
            $('#correlation-filter-controls').hide();
        }
    }

    function resetCorrelationFilter() {
        filterCorrelations(null);
    }

    // ── Collections widget ────────────────────────────────────────────────────
    // Load all collections that contain this event and render compact linked
    // chips in the Context card. Uses the dedicated read-only JSON endpoint.
    window.betaLoadEventCollections = function() {
        var eventUuid = <?php echo json_encode($event['Event']['uuid']); ?>;
        var baseurl   = <?php echo json_encode($baseurl); ?>;
        var container = document.getElementById('event-collections-container');
        var countNode = document.getElementById('beta-collections-count');
        if (!container) return;

        container.innerHTML = ''; // Clear "Loading…" placeholder immediately

        $.ajax({
            url: baseurl + '/collections/getForElement/Event/' + eventUuid + '.json',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data || data.length === 0) {
                    container.innerHTML = '';
                    if (countNode) {
                        countNode.textContent = '0';
                    }
                    return;
                }
                if (countNode) {
                    countNode.textContent = String(data.length);
                }
                var html = '<div class="beta-event-collections-chips">';
                data.forEach(function(c) {
                    html += '<a href="' + baseurl + '/collections/view/' + c.id + '" ' +
                            'class="beta-collection-chip beta-type-' + (c.type || 'other').replace(/[^a-z0-9_-]/gi, '') + '" ' +
                            'title="' + (c.type || '') + (c.description ? ': ' + c.description.substring(0, 80) : '') + '">' +
                            '<i class="fa fa-folder" style="font-size:10px;margin-right:3px;"></i>' +
                            c.name +
                            '</a>';
                });
                html += '</div>';
                container.innerHTML = html;
            },
            error: function() {
                container.innerHTML = '';
                if (countNode) {
                    countNode.textContent = '0';
                }
            }
        });
    };
    window.betaLoadEventCollections();
    // ─────────────────────────────────────────────────────────────────────────
</script>
