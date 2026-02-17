<?php
    echo $this->element('genericElements/assetLoader', [
        'css' => ['main-beta', 'components-beta', 'query-builder.default', 'attack_matrix', 'analyst-data'],
        'js' => ['doT', 'extendext', 'moment.min', 'query-builder', 'network-distribution-graph', 'd3', 'd3.custom', 'jquery-ui.min', 'beta-events-timestamps', 'd3-sankey.min'],
    ]);
?>

<style>
    .beta-view-events {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
    .comment-bar-container {
        margin-bottom: 6px;
        position: relative;
        background-color: #deebfa;
        border-radius: 4px;
        overflow: hidden;
        min-height: 30px;
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    .comment-bar-container:hover {
        background-color: #d0e2f5;
    }
    .comment-bar-container:hover .comment-bar {
        opacity: 0.5;
    }
    .comment-bar {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        background-color: #428bca;
        opacity: 0.4;
        z-index: 1;
    }
    .comment-text {
        position: relative;
        z-index: 2;
        padding: 0 12px;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
        color: #222;
    }
    .comment-count {
        margin-left: auto;
        padding-right: 12px;
        font-weight: bold;
        font-size: 12px;
        z-index: 2;
        color: #444;
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
    .published-label {
        margin-right: 8px;
        font-weight: 600;
        color: #666;
        vertical-align: middle;
        font-size: 12px;
        text-transform: uppercase;
    }
    .beta-galaxy-link:hover {
        color: #428bca;
        text-decoration: underline;
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
            
            <div class="beta-header-actions" style="margin-left: auto;">
                 <?php if ($this->Acl->canAccess('events', 'edit') && $this->Acl->canAccess('events', 'publish')): ?>
                    <div style="display: inline-block; margin-right: 15px; vertical-align: middle;" title="<?php echo __('Toggle publication status'); ?>">
                        <span class="published-label"><?php echo __('Published'); ?></span>
                        <label class="switch">
                            <input type="checkbox" id="publishedToggle" data-id="<?php echo h($event['Event']['id']); ?>" <?php echo $event['Event']['published'] ? 'checked' : ''; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <a href="<?php echo $baseurl; ?>/events/edit/<?php echo h($event['Event']['id']); ?>" class="btn btn-default btn-sm"><i class="fa fa-edit"></i> <?php echo __('Edit'); ?></a>
                 <?php endif; ?>
            </div>
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
            <li role="presentation"><a href="#reports" aria-controls="reports" role="tab" data-toggle="tab"><?php echo __('Reports'); ?> (<?php echo h($eventReportCount); ?>)</a></li>
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
                                  <?php if (!empty($eventReportSummary)): ?>
                                      <p><?php echo h($eventReportSummary); ?></p>
                                      <a href="#" onclick="openGenericModal('<?php echo $baseurl; ?>/eventReports/viewSummary/<?php echo h($firstEventReportId); ?>'); return false;"><?php echo __('Read more'); ?></a>
                                  <?php else: ?>
                                      <p class="muted"><?php echo __('No report content available. Always consider adding an event report to explain the "so what" and context!'); ?></p>
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
                         
                         <!-- Composition -->
                         <div class="beta-card summary-card">
                             <div class="beta-card-header"><?php echo __('Composition'); ?></div>
                             <div class="beta-card-body">
                                  <div id="composition-treemap" style="width: 100%; min-height: 200px;"></div>
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
                          <!-- Context -->
                           <div class="beta-card summary-card">
                             <div class="beta-card-header"><?php echo __('Context'); ?></div>
                             <div class="beta-card-body">
                                 <strong><?php echo __('Tags'); ?></strong><br>
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
                                 <strong><?php echo __('Galaxies'); ?></strong><br>
                                 <div class="beta-galaxies-container" id="galaxies_div" style="margin-top: 5px;">
                                   <?php
                                       if (!empty($event['Galaxy'])) {
                                           foreach ($event['Galaxy'] as $galaxy) {
                                               echo $this->element('Events/View/galaxy_compact_beta', [
                                                   'galaxyName' => $galaxy['name'],
                                                   'clusters' => $galaxy['GalaxyCluster'],
                                                   'baseurl' => $baseurl
                                               ]);
                                           }
                                       } else {
                                           echo '<span class="muted" style="font-size: 11px;">' . __('No galaxies attached.') . ' </span>';
                                       }
                                       
                                       // Add Buttons
                                       $tagAccess = $this->Acl->canModifyTag($event);
                                       $localTagAccess = $this->Acl->canModifyTag($event, true);
                                       $targetId = $event['Event']['id'];
                                       
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
                          ?>
                          <div class="beta-card summary-card">
                              <div class="beta-card-header"><?php echo __('Warninglist Matches'); ?></div>
                              <div class="beta-card-body">
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
                                                          <a href="#attributes" data-toggle="tab" onclick="$('#beta-attr-search').val('<?php echo h($match['value']); ?>').trigger('keyup');" class="attr-value">
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
                      </div>
                  </div>
                 </div>

            <!-- Reports Tab -->
            <div role="tabpanel" class="tab-pane" id="reports">
                <div id="event-reports-tab-content">
                    <div class="text-center" style="padding: 20px;">
                        <i class="fa fa-spinner fa-spin fa-2x"></i><br>
                        <?php echo __('Loading reports...'); ?>
                    </div>
                </div>
            </div>

            <!-- Attributes Tab -->
            <div role="tabpanel" class="tab-pane" id="attributes">
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
                        <h4 style="margin-top: 0; margin-bottom: 15px; font-size: 14px; font-weight: 600; color: #555;"><?php echo __('Correlation Flow'); ?></h4>
                        <div id="correlations-sankey" style="width: 100%; height: 400px;"></div>
                    </div>
                    <div id="correlations-table-container"></div>
                </div>
            </div>
             <div role="tabpanel" class="tab-pane" id="history">
                <h3><?php echo __('History'); ?></h3>
                <?php if (!empty($contributors)): ?>
                    <p><strong><?php echo __('Contributors'); ?>:</strong> <?php echo implode(', ', $contributors); ?></p>
                <?php endif; ?>
                
                <div class="alert alert-info" style="margin-top: 20px;">
                    <i class="fa fa-info-circle"></i> <?php echo __('Full audit log is available in the dedicated view.'); ?>
                    <br><br>
                    <a href="<?php echo $baseurl; ?>/audit_logs/eventIndex/<?php echo h($event['Event']['id']); ?>" class="btn btn-primary"><?php echo __('View Full Audit Log'); ?></a>
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
        $objTypes = [];
        $commentCounts = [];

        if (!empty($event['objects'])) {
            foreach ($event['objects'] as $obj) {
                if ($obj['objectType'] === 'attribute') {
                    $t = $obj['type'];
                    if (!isset($attrTypes[$t])) $attrTypes[$t] = 0;
                    $attrTypes[$t]++;
                    if (!empty($obj['comment'])) {
                        $c = $obj['comment'];
                        if (!isset($commentCounts[$c])) $commentCounts[$c] = 0;
                        $commentCounts[$c]++;
                    }
                } elseif ($obj['objectType'] === 'object') {
                    $t = $obj['name'];
                    if (!isset($objTypes[$t])) $objTypes[$t] = 0;
                    $objTypes[$t]++;
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
        } elseif (!empty($event['Attribute'])) {
             foreach ($event['Attribute'] as $attr) {
                $t = $attr['type'];
                if (!isset($attrTypes[$t])) $attrTypes[$t] = 0;
                $attrTypes[$t]++;
                if (!empty($attr['comment'])) {
                    $c = $attr['comment'];
                    if (!isset($commentCounts[$c])) $commentCounts[$c] = 0;
                    $commentCounts[$c]++;
                }
            }
            if (!empty($event['Object'])) {
                 foreach ($event['Object'] as $obj) {
                    $t = $obj['name'];
                    if (!isset($objTypes[$t])) $objTypes[$t] = 0;
                    $objTypes[$t]++;
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
        
        foreach ($objTypes as $type => $count) {
            $compositionData[] = [
                'label' => "Object: $type",
                'name' => $type,
                'value' => $count,
                'type' => 'object'
            ];
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
    console.log('Composition Data:', compositionData);
    console.log('Comment Data:', commentData);

    $(function() {
        popoverStartup();

        // Horizontal Bar Chart
        if (compositionData && compositionData.length > 0) {
            var margin = {top: 20, right: 20, bottom: 20, left: 150};
            var width = $('#composition-treemap').width() - margin.left - margin.right;
            var barHeight = 25;
            var height = Math.max(100, compositionData.length * barHeight) + margin.top + margin.bottom;

            var svg = d3.select("#composition-treemap").append("svg")
                .attr("width", width + margin.left + margin.right)
                .attr("height", height)
                .append("g")
                .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

            var x = d3.scale.linear()
                .range([0, width])
                .domain([0, d3.max(compositionData, function(d) { return d.value; })]);

            var y = d3.scale.ordinal()
                .rangeRoundBands([0, height - margin.top - margin.bottom], .1)
                .domain(compositionData.map(function(d) { return d.label; }));

            var color = d3.scale.ordinal()
                .range(["#428bca", "#5cb85c", "#f0ad4e", "#d9534f", "#5bc0de"])
                .domain(compositionData.map(function(d) { return d.label; }));

            var bars = svg.selectAll(".bar")
                .data(compositionData)
                .enter().append("g")
                .attr("class", "bar-group")
                .style("cursor", "pointer")
                .on("click", function(d) {
                    betaFilterAttributesByComposition(d.type, d.name);
                });

            bars.append("rect")
                .attr("class", "bar")
                .attr("y", function(d) { return y(d.label); })
                .attr("height", y.rangeBand())
                .attr("x", 0)
                .attr("width", function(d) { return x(d.value); })
                .attr("fill", function(d) { return color(d.label); });

            bars.append("text")
                .attr("class", "label")
                .attr("y", function(d) { return y(d.label) + y.rangeBand() / 2 + 4; })
                .attr("x", -10)
                .attr("text-anchor", "end")
                .text(function(d) { return d.label; });

            bars.append("text")
                .attr("class", "value")
                .attr("y", function(d) { return y(d.label) + y.rangeBand() / 2 + 4; })
                .attr("x", function(d) { return x(d.value) + 5; })
                .text(function(d) { return d.value; });
        } else {
             d3.select("#composition-treemap").html('<div class="alert alert-info" style="margin: 20px;">No composition data available.</div>');
        }

        // Comments Bar Chart
        if (commentData && commentData.length > 0) {
            var maxVal = d3.max(commentData, function(d) { return d.value; });
            var container = d3.select("#comments-graph");
            container.html(""); // Clear

            commentData.forEach(function(d) {
                var percentage = (d.value / maxVal) * 100;
                var row = container.append("div")
                    .attr("class", "comment-bar-container")
                    .attr("title", d.label + " (" + d.value + ")")
                    .on("click", function() {
                        betaFilterAttributesByComment(d.label);
                    });

                row.append("div")
                    .attr("class", "comment-bar")
                    .style("width", percentage + "%");

                row.append("div")
                    .attr("class", "comment-text")
                    .text(d.label);

                row.append("div")
                    .attr("class", "comment-count")
                    .text(d.value);
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

        // Load Reports
        $.get("<?php echo $baseurl; ?>/eventReports/index/event_id:<?php echo h($event['Event']['id']); ?>/index_for_event:1/beta:1", function(data) {
            $("#event-reports-tab-content").html(data);
            if (window.betaTimestamps && typeof window.betaTimestamps.update === 'function') {
                window.betaTimestamps.update();
            }
        });
    });

    function betaClearAttributeFilter() {
        $('.filter-active-msg').remove();
        if (typeof betaPagination !== 'undefined') {
            betaPagination.searchActive = false;
            $('.beta-pagination-container').show();
            betaPaginationApply();
        } else {
            $('.beta-attr-row').show();
        }
    }

    function betaFilterAttributesByComposition(type, name) {
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
        
        if (type === 'object') {
            // Show the object header
            $('.beta-attr-row[data-object-name="' + name + '"]').show();
            // Show the attributes belonging to the object
            $('.beta-attr-row[data-parent-object="' + name + '"]').show();
        } else {
            // Show attributes of this type (both standalone and inside objects)
            $('.beta-attr-row[data-attribute-type="' + name + '"]').show();
        }

        // Show message
        var msg = '<div class="alert alert-warning filter-active-msg" style="margin-top: 10px;">';
        msg += '<button type="button" class="close" onclick="betaClearAttributeFilter(); $(this).parent().remove();">×</button>';
        msg += 'Filtering by <strong>' + (type === 'object' ? 'Object: ' : 'Attribute: ') + name + '</strong>';
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
            var rowComment = $(this).find('.col-comment').text().trim();
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

    $(document).ready(function() {
        $('a[data-toggle="tab"][href="#correlations"]').on('shown.bs.tab', function (e) {
            loadCorrelations();
        });

        // Check if we are already on the correlations tab on page load
        if (window.location.hash === '#correlations') {
            loadCorrelations();
        }

        function loadCorrelations() {
            if ($('#correlations-table').length > 0) return;
            var eventId = '<?php echo h($event['Event']['id']); ?>';
            $.ajax({
                url: '<?php echo $baseurl; ?>/correlations/eventCorrelations/' + eventId + '.json?extended=1',
                type: 'GET',
                success: function(response) {
                    $('#correlations-loader').hide();
                    $('#correlations-content').show();
                    renderCorrelations(response);
                },
                error: function() {
                    $('#correlations-loader').html('<p class="text-danger"><?php echo __('Failed to load correlations.'); ?></p>');
                }
            });
        }

        function renderCorrelations(data) {
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
                
                html += '<div class="beta-card correlation-event-card" style="margin-bottom: 20px; border-left: 4px solid #428bca;">';
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
                        html += '        <tr class="beta-attr-row standalone-attr-row">';
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
                                
                                html += '<div class="tag-container" style="display: inline-flex; align-items: center; margin-right: 4px; margin-bottom: 2px;">';
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
            
            renderSankey(data, eventDetails);
        }

        function renderSankey(data, eventDetails) {
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
            
            // Limit to top correlations to keep diagram readable
            var parentIds = Object.keys(data);
            if (parentIds.length > 20) parentIds = parentIds.slice(0, 20);

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
                    var targetEventName = 'Event #' + rel.id;
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

            if (links.length === 0) return;
            $('#correlations-sankey-container').show();

            var margin = {top: 10, right: 350, bottom: 10, left: 10},
                width = $('#correlations-sankey').width() - margin.left - margin.right,
                height = 400 - margin.top - margin.bottom;

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
                         showMessage('success', response.message || (response.response ? response.response.message : 'Event updated'));
                    } else {
                        // Revert
                        $toggle.prop('checked', !isChecked);
                        showMessage('fail', response.message || (response.response ? response.response.message : 'Action failed'));
                        if (response.errors) {
                             console.error(response.errors);
                        }
                    }
                },
                error: function(xhr) {
                    $toggle.prop('disabled', false);
                    $toggle.prop('checked', !isChecked);
                    xhrFailCallback(xhr);
                }
            });
        });
    });
    function filterCorrelations(attributeId) {
        $('.nav-tabs a[href="#attributes"]').tab('show');
        if (attributeId && typeof filterAttributes === 'function') {
            filterAttributes(attributeId);
        }
        var rows = $('#correlations-table tbody tr');
        if (attributeId) {
            rows.hide();
            rows.filter('[data-attribute-id="' + attributeId + '"]').show();
            $('#correlation-filter-msg').text('<?php echo __('Filtering by Attribute ID'); ?>: ' + attributeId);
            $('#correlation-filter-controls').show();
        } else {
            rows.show();
            $('#correlation-filter-controls').hide();
        }
        // Scroll to top of tab content
        $('html, body').animate({
            scrollTop: $(".beta-tabs-container").offset().top
        }, 500);
    }

    function resetCorrelationFilter() {
        filterCorrelations(null);
    }
</script>
