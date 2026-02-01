<?php
    echo $this->element('genericElements/assetLoader', [
        'css' => ['main-beta', 'components-beta', 'query-builder.default', 'attack_matrix', 'analyst-data'],
        'js' => ['doT', 'extendext', 'moment.min', 'query-builder', 'network-distribution-graph', 'd3', 'd3.custom', 'jquery-ui.min', 'beta-events-timestamps'],
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
    <div class="beta-tabs-container">
        <ul class="nav nav-tabs beta-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#summary" aria-controls="summary" role="tab" data-toggle="tab"><?php echo __('Summary'); ?></a></li>
            <li role="presentation"><a href="#reports" aria-controls="reports" role="tab" data-toggle="tab"><?php echo __('Reports'); ?> (<?php echo h($eventReportCount); ?>)</a></li>
            <li role="presentation"><a href="#attributes" aria-controls="attributes" role="tab" data-toggle="tab"><?php echo __('Attributes'); ?> (<?php echo h($attribute_count); ?>)</a></li>
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
                                 <div class="beta-galaxies-container" style="margin-top: 5px;">
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
                                               '<button class="%s" data-popover-popup="%s" role="button" tabindex="0" aria-label="' . __('Add new cluster') . '" title="' . __('Add new cluster') . '">%s</button>',
                                               'useCursorPointer addButton btn btn-inverse noPrint',
                                               $link,
                                               '<i class="fas fa-globe-americas"></i> <i class="fas fa-plus"></i>'
                                           );
                                       }
                                       if ($localTagAccess) {
                                           $link = "$baseurl/galaxies/selectGalaxyNamespace/$targetId/event/local:1";
                                           echo sprintf(
                                               '<button class="%s" data-popover-popup="%s" role="button" tabindex="0" aria-label="' . __('Add new local cluster') . '" title="' . __('Add new local cluster') . '">%s</button>',
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
                <h3><?php echo __('Reports'); ?></h3>
                <div id="event-reports-tab-content">
                    <div class="text-center" style="padding: 20px;">
                        <i class="fa fa-spinner fa-spin fa-2x"></i><br>
                        <?php echo __('Loading reports...'); ?>
                    </div>
                </div>
            </div>

            <!-- Attributes Tab -->
            <div role="tabpanel" class="tab-pane" id="attributes">
                 <?php echo $this->element('Events/View/event_attributes_beta'); ?>
            </div>
            
            <!-- Other Tabs Placeholders -->
             <div role="tabpanel" class="tab-pane" id="correlations">
                <h3><?php echo __('Correlations'); ?></h3>
                
                <?php
                    // Build Correlation Data
                    $allCorrelations = [];
                    $relatedEventsMap = [];
                    if (!empty($event['RelatedEvent'])) {
                        foreach ($event['RelatedEvent'] as $re) {
                            $relatedEventsMap[$re['Event']['id']] = $re['Event'];
                        }
                    }

                    $processAttr = function($attr) use (&$allCorrelations, $relatedEventsMap, $event) {
                        $relatedAttrs = $attr['RelatedAttribute'] ?? [];
                        if (empty($relatedAttrs) && !empty($event['RelatedAttribute'][$attr['id']])) {
                            $relatedAttrs = $event['RelatedAttribute'][$attr['id']];
                        }

                        if (!empty($relatedAttrs)) {
                            foreach ($relatedAttrs as $related) {
                                // Handle potential double nesting or direct relation
                                $relationsToProcess = [];
                                if (isset($related['event_id']) || isset($related['id'])) {
                                    $relationsToProcess[] = $related;
                                } elseif (is_array($related)) {
                                    foreach ($related as $sub) {
                                        if (is_array($sub) && (isset($sub['event_id']) || isset($sub['id']))) {
                                            $relationsToProcess[] = $sub;
                                        }
                                    }
                                }

                                foreach ($relationsToProcess as $rel) {
                                    $eventId = $rel['event_id'] ?? $rel['id'] ?? null;
                                    if (!$eventId) continue;
                                    // Permissive check to debug missing event info
                                    $eventDate = isset($relatedEventsMap[$eventId]) ? $relatedEventsMap[$eventId]['date'] : ($rel['date'] ?? 'N/A');
                                    $eventInfo = isset($relatedEventsMap[$eventId]) ? $relatedEventsMap[$eventId]['info'] : ($rel['info'] ?? 'Event info not available');
                                    $orgcId = isset($relatedEventsMap[$eventId]) ? ($relatedEventsMap[$eventId]['orgc_id'] ?? 0) : ($rel['org_id'] ?? 0);

                                    $allCorrelations[] = [
                                        'local_attr_id' => $attr['id'],
                                        'value' => $rel['value'] ?? $attr['value'],
                                        'type' => $attr['type'],
                                        'event_id' => $eventId,
                                        'event_date' => $eventDate,
                                        'event_info' => $eventInfo,
                                        'orgc_id' => $orgcId
                                    ];
                                }
                            }
                        }
                    };

                    if (!empty($event['Attribute'])) {
                        foreach ($event['Attribute'] as $attr) $processAttr($attr);
                    }
                    if (!empty($event['Object'])) {
                        foreach ($event['Object'] as $obj) {
                            if (!empty($obj['Attribute'])) {
                                foreach ($obj['Attribute'] as $attr) $processAttr($attr);
                            }
                        }
                    }
                    if (!empty($event['objects'])) {
                        foreach ($event['objects'] as $item) {
                            if ($item['objectType'] === 'attribute') {
                                $processAttr($item);
                            } elseif ($item['objectType'] === 'object' && !empty($item['Attribute'])) {
                                foreach ($item['Attribute'] as $attr) $processAttr($attr);
                            }
                        }
                    }
                    
                    // Sort by Date DESC
                    usort($allCorrelations, function($a, $b) {
                        return strcmp($b['event_date'], $a['event_date']);
                    });
                ?>

                <div id="correlation-filter-controls" style="display: none; margin-bottom: 15px;">
                    <span id="correlation-filter-msg" class="label label-info" style="font-size: 12px;"></span>
                    <button id="correlation-reset-btn" class="btn btn-default btn-xs" onclick="resetCorrelationFilter()"><i class="fa fa-times"></i> <?php echo __('Clear Filter'); ?></button>
                </div>

                <?php if (!empty($allCorrelations)): ?>
                    <table class="table table-hover table-condensed" id="correlations-table">
                        <thead>
                            <tr>
                                <th><?php echo __('Date'); ?></th>
                                <th><?php echo __('Event ID'); ?></th>
                                <th><?php echo __('Info'); ?></th>
                                <th><?php echo __('Type'); ?></th>
                                <th><?php echo __('Value'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allCorrelations as $corr): ?>
                                <tr data-attribute-id="<?php echo h($corr['local_attr_id']); ?>">
                                    <td><?php echo h($corr['event_date']); ?></td>
                                    <td>
                                        <a href="<?php echo $baseurl; ?>/events/view/<?php echo h($corr['event_id']); ?>">
                                            <?php echo h($corr['event_id']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo h($corr['event_info']); ?></td>
                                    <td><?php echo h($corr['type']); ?></td>
                                    <td style="word-break: break-all;"><?php echo h($corr['value']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="muted"><?php echo __('No correlations found.'); ?></p>
                <?php endif; ?>
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
        $.get("<?php echo $baseurl; ?>/eventReports/index/event_id:<?php echo h($event['Event']['id']); ?>/index_for_event:1", function(data) {
            $("#event-reports-tab-content").html(data);
        });
    });

    function betaFilterAttributesByComposition(type, name) {
        // Switch to Attributes tab
        $('.nav-tabs a[href="#attributes"]').tab('show');
        
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
        msg += '<button type="button" class="close" data-dismiss="alert" onclick="$(\'.beta-attr-row\').show(); $(this).parent().remove();">×</button>';
        msg += 'Filtering by <strong>' + (type === 'object' ? 'Object: ' : 'Attribute: ') + name + '</strong>';
        msg += ' <a href="#" onclick="$(\'.beta-attr-row\').show(); $(\'.filter-active-msg\').remove(); return false;">(Clear Filter)</a>';
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
                // If it's an attribute inside an object, we might need to show the object header too
                // but usually comments are specific to the row. 
                // If the object header itself has the comment, it will be shown.
            }
        });

        // Show message
        var msg = '<div class="alert alert-warning filter-active-msg" style="margin-top: 10px;">';
        msg += '<button type="button" class="close" data-dismiss="alert" onclick="$(\'.beta-attr-row\').show(); $(this).parent().remove();">×</button>';
        msg += 'Filtering by Comment: <strong>' + comment + '</strong>';
        msg += ' <a href="#" onclick="$(\'.beta-attr-row\').show(); $(\'.filter-active-msg\').remove(); return false;">(Clear Filter)</a>';
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
        $('.nav-tabs a[href="#correlations"]').tab('show');
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
