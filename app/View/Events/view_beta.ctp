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
        color: #333;
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
</style>

<div class="events view beta-view-events">
    <!-- Header -->
    <div class="beta-header-container">
        <h2 class="beta-event-title">
            <?php echo h($event['Event']['info']); ?>
             <span class="beta-id-badge">#<?php echo h($event['Event']['id']); ?></span>
        </h2>
        <div class="beta-event-meta-row">
            <span class="meta-box date-box">
                <span class="meta-label"><?php echo __('Date'); ?></span>
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
                 <?php if ($this->Acl->canAccess('events', 'edit')): ?>
                    <a href="<?php echo $baseurl; ?>/events/edit/<?php echo h($event['Event']['id']); ?>" class="btn btn-default btn-sm"><i class="fa fa-edit"></i> <?php echo __('Edit'); ?></a>
                 <?php endif; ?>
                 <a href="<?php echo $baseurl; ?>/users/routeUserSetting/ui_beta/0" class="btn btn-default btn-sm" title="<?php echo __('Switch back to Classic View'); ?>"><i class="fa fa-exchange-alt"></i> Classic</a>
            </div>
        </div>
        
        <?php if (!empty($warnings)): ?>
            <div class="alert alert-warning beta-alert" style="margin-top: 15px;">
                 <?php if (is_array($warnings)): ?>
                    <?php echo implode('<br>', $warnings); ?>
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
            <li role="presentation"><a href="#attributes" aria-controls="attributes" role="tab" data-toggle="tab"><?php echo __('Attributes'); ?> (<?php echo h($attribute_count); ?>)</a></li>
            <li role="presentation"><a href="#correlations" aria-controls="correlations" role="tab" data-toggle="tab"><?php echo __('Correlations'); ?> (<?php echo isset($relatedEventCorrelationCount) ? count($relatedEventCorrelationCount) : 0; ?>)</a></li>
            <li role="presentation"><a href="#history" aria-controls="history" role="tab" data-toggle="tab"><?php echo __('History'); ?></a></li>
            <li role="presentation"><a href="#reports" aria-controls="reports" role="tab" data-toggle="tab"><?php echo __('Reports'); ?> (<?php echo h($eventReportCount); ?>)</a></li>
        </ul>

        <div class="tab-content beta-tab-content">
            <!-- Summary Tab -->
            <div role="tabpanel" class="tab-pane active" id="summary">
                 <div class="row-fluid">
                     <div class="span8">
                         <!-- Report Snippet (Placeholder) -->
                          <div class="beta-card summary-card">
                              <div class="beta-card-header"><?php echo __('Report'); ?></div>
                              <div class="beta-card-body">
                                  <?php if (!empty($eventReportSummary)): ?>
                                      <p><?php echo h($eventReportSummary); ?></p>
                                      <a href="#" onclick="openGenericModal('<?php echo $baseurl; ?>/eventReports/viewSummary/<?php echo h($firstEventReportId); ?>'); return false;"><?php echo __('Read more'); ?></a>
                                  <?php else: ?>
                                      <p class="muted"><?php echo __('No report content available.'); ?></p>
                                  <?php endif; ?>
                              </div>
                          </div>
                         
                         <!-- Composition -->
                         <div class="beta-card summary-card">
                             <div class="beta-card-header"><?php echo __('Composition'); ?></div>
                             <div class="beta-card-body">
                                  <div id="composition-treemap" style="width: 100%; height: 200px;"></div>
                             </div>
                         </div>
                     </div>
                     <div class="span4">
                         <!-- Context -->
                          <div class="beta-card summary-card">
                             <div class="beta-card-header"><?php echo __('Context'); ?></div>
                             <div class="beta-card-body">
                                 <strong><?php echo __('Tags'); ?></strong><br>
                                 <?php
                                     if (!empty($event['EventTag'])) {
                                        echo $this->element('ajaxTags', [
                                            'event' => $event,
                                            'tags' => $event['EventTag'],
                                            'tagAccess' => $this->Acl->canAccess('tags', 'edit'),
                                            'localTagAccess' => $this->Acl->canModifyTag($event, true),
                                            'missingTaxonomies' => $missingTaxonomies,
                                            'tagConflicts' => $tagConflicts
                                        ]);
                                     } else {
                                         echo '<span class="muted">' . __('No tags') . '</span>';
                                     }
                                 ?>
                                 <hr>
                                 <strong><?php echo __('Galaxies'); ?></strong><br>
                                  <?php if (!empty($event['Galaxy'])): ?>
                                    <?php
                                        echo $this->element('galaxyQuickViewNew', [
                                            'data' => $event['Galaxy'],
                                            'event' => $event,
                                            'target_id' => $event['Event']['id'],
                                            'target_type' => 'event'
                                        ]);
                                    ?>
                                 <?php else: ?>
                                     <span class="muted"><?php echo __('No galaxies'); ?></span>
                                 <?php endif; ?>
                                 </div>
                             </div>
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
                <?php if (!empty($event['RelatedEvent'])): ?>
                    <table class="table table-hover table-condensed">
                        <thead>
                            <tr>
                                <th><?php echo __('Date'); ?></th>
                                <th><?php echo __('Event ID'); ?></th>
                                <th><?php echo __('Info'); ?></th>
                                <th><?php echo __('Correlations'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($event['RelatedEvent'] as $related): ?>
                                <tr>
                                    <td><?php echo h($related['Event']['date']); ?></td>
                                    <td>
                                        <a href="<?php echo $baseurl; ?>/events/view/<?php echo h($related['Event']['id']); ?>">
                                            <?php echo h($related['Event']['id']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo h($related['Event']['info']); ?></td>
                                    <td>
                                        <span class="badge"><?php echo isset($relatedEventCorrelationCount[$related['Event']['id']]) ? $relatedEventCorrelationCount[$related['Event']['id']] : 0; ?></span>
                                    </td>
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
             <div role="tabpanel" class="tab-pane" id="reports">
                <h3><?php echo __('Reports'); ?></h3>
                <div id="event-reports-tab-content">
                    <div class="text-center" style="padding: 20px;">
                        <i class="fa fa-spinner fa-spin fa-2x"></i><br>
                        <?php echo __('Loading reports...'); ?>
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
        $objTypes = [];

        if (!empty($event['objects'])) {
            foreach ($event['objects'] as $obj) {
                if ($obj['objectType'] === 'attribute') {
                    $t = $obj['type'];
                    if (!isset($attrTypes[$t])) $attrTypes[$t] = 0;
                    $attrTypes[$t]++;
                } elseif ($obj['objectType'] === 'object') {
                    $t = $obj['name'];
                    if (!isset($objTypes[$t])) $objTypes[$t] = 0;
                    $objTypes[$t]++;
                }
            }
        } elseif (!empty($event['Attribute'])) {
             foreach ($event['Attribute'] as $attr) {
                $t = $attr['type'];
                if (!isset($attrTypes[$t])) $attrTypes[$t] = 0;
                $attrTypes[$t]++;
            }
            if (!empty($event['Object'])) {
                 foreach ($event['Object'] as $obj) {
                    $t = $obj['name'];
                    if (!isset($objTypes[$t])) $objTypes[$t] = 0;
                    $objTypes[$t]++;
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
    ?>
    var compositionData = <?php echo json_encode($compositionData); ?>;
    console.log('Composition Data:', compositionData);

    $(function() {
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
                    betaFilterAttributesByComposition(d.type, d.name, true);
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
        // Load Reports
        $.get("<?php echo $baseurl; ?>/eventReports/index/event_id:<?php echo h($event['Event']['id']); ?>/index_for_event:1", function(data) {
            $("#event-reports-tab-content").html(data);
        });

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

        // Listen for tab changes
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if (window.ignoreTabPush) return;

            var target = $(e.target).attr('href');
            var currentState = history.state;
            
            // Only push if it's different from the current tab in history
            if (!currentState || currentState.tab !== target) {
                history.pushState({ tab: target, filter: null }, '', window.location.pathname + target);
            }
        });

        // Handle browser back/forward
        window.onpopstate = function(event) {
            if (event.state) {
                window.ignoreTabPush = true;
                if (event.state.tab) {
                    $('.nav-tabs a[href="' + event.state.tab + '"]').tab('show');
                }
                if (event.state.filter) {
                    betaFilterAttributesByComposition(event.state.filter.type, event.state.filter.name, false);
                } else {
                    // Clear filter if we go back to a state without one
                    clearBetaAttributeFilter();
                }
                window.ignoreTabPush = false;
            }
        };

        $('.distributionNetworkToggle').each(function() {
            $(this).distributionNetwork({
                distributionData: <?= json_encode($distributionData, JSON_UNESCAPED_UNICODE); ?>,
            });
        });
    });

    function betaFilterAttributesByComposition(type, name, pushToHistory) {
        // Switch to Attributes tab
        window.ignoreTabPush = true;
        $('.nav-tabs a[href="#attributes"]').tab('show');
        window.ignoreTabPush = false;
        
        // Clear previous filters
        clearBetaAttributeFilter();

        // Apply filter
        $('.beta-attr-row').hide();
        
        if (type === 'object') {
            $('.beta-attr-row[data-object-name="' + name + '"]').show();
            $('.beta-attr-row[data-parent-object="' + name + '"]').show();
        } else {
            $('.beta-attr-row[data-attribute-type="' + name + '"]').show();
        }

        // Show message
        var msg = '<div class="alert alert-warning filter-active-msg" style="margin-top: 10px;">';
        msg += '<button type="button" class="close" data-dismiss="alert" onclick="clearBetaAttributeFilter()">×</button>';
        msg += 'Filtering by <strong>' + (type === 'object' ? 'Object: ' : 'Attribute: ') + name + '</strong>';
        msg += ' <a href="#" onclick="clearBetaAttributeFilter(); return false;">(Clear Filter)</a>';
        msg += '</div>';
        
        if ($('.beta-toolbar').length) {
             $('.beta-toolbar').after(msg);
        } else {
             $('#attributes').prepend(msg);
        }

        if (pushToHistory) {
            history.pushState({ tab: '#attributes', filter: { type: type, name: name } }, '', window.location.pathname + '#attributes');
        }
    }

    function clearBetaAttributeFilter() {
        $('.beta-attr-row').show();
        $('.filter-active-msg').remove();
    }
</script>
