<div id="eventReportQuickIndex" class="beta-reports-list">
    <!-- Toolbar -->
    <div class="beta-toolbar clearfix" style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-start; gap: 15px;">
        <div class="pull-left" style="display: flex; gap: 10px; align-items: center;">
            <?php if ($canModify): ?>
                <a href="<?php echo $baseurl; ?>/eventReports/add/<?php echo h($event_id); ?>" class="btn btn-primary btn-sm modal-open"><i class="fa fa-plus"></i> <?php echo __('Add Event Report'); ?></a>
                <?php if ($importModuleEnabled): ?>
                    <a href="<?php echo $baseurl; ?>/eventReports/importReportFromUrl/<?php echo h($event_id); ?>" class="btn btn-primary btn-sm modal-open" title="<?php echo __('Content for this URL will be downloaded and converted to Markdown'); ?>"><i class="fa fa-link"></i> <?php echo __('Import from URL'); ?></a>
                <?php endif; ?>
                <a href="<?php echo $baseurl; ?>/eventReports/reportFromEvent/<?php echo h($event_id); ?>" class="btn btn-primary btn-sm modal-open" title="<?php echo __('Based on filters, create a report summarizing the event'); ?>"><i class="fa fa-list-alt"></i> <?php echo __('Generate from Event'); ?></a>
            <?php endif; ?>
        </div>

        <div id="eventReportSelectors" class="btn-group">
            <?php
                $contexts = [
                    'all' => __('All'),
                    'default' => __('Default'),
                    'deleted' => __('Deleted')
                ];
                foreach ($contexts as $ctx => $label):
                    $active = ($context === $ctx);
                    $url = sprintf('%s/eventReports/index/event_id:%s/index_for_event:1/context:%s/beta:1', $baseurl, h($event_id), h($ctx));
            ?>
                <a href="<?php echo $url; ?>" class="btn btn-default btn-sm <?php echo $active ? 'active' : ''; ?> <?php echo $ctx === 'default' ? 'defaultContext' : ''; ?>">
                    <?php echo $label; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($extendedEvent || $extendingEvent): ?>
        <div class="alert alert-info" style="margin-bottom: 15px;">
            <i class="fa fa-info-circle"></i> 
            <?php echo $extendedEvent ? __('Viewing reports in extended mode event view') : __('Viewing reports in extending mode event view'); ?>
        </div>
    <?php endif; ?>

    <div class="beta-reports-grid">
        <?php if (empty($reports)): ?>
            <div class="beta-no-reports text-center muted" style="padding: 40px; border: 1px dashed #ddd; border-radius: 8px; grid-column: 1 / -1;">
                <?php echo __('No reports found.'); ?>
            </div>
        <?php else: ?>
            <?php foreach ($reports as $report): ?>
                <div class="beta-report-tile" data-primary-id="<?php echo h($report['EventReport']['id']); ?>">
                    <div class="beta-report-tile-header">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                            <span class="beta-id-badge">#<?php echo h($report['EventReport']['id']); ?></span>
                            <div class="beta-row-actions">
                                <div class="beta-row-menu-trigger">
                                    <i class="fa fa-ellipsis-h"></i>
                                </div>
                                <div class="beta-row-menu">
                                    <ul>
                                        <li><a href="#" onclick="viewFullReport(<?php echo h($report['EventReport']['id']); ?>); return false;"><i class="fa fa-eye"></i> <?php echo __('View Summary'); ?></a></li>
                                        <li><a href="<?php echo $baseurl; ?>/eventReports/view/<?php echo h($report['EventReport']['id']); ?>"><i class="fa fa-columns"></i> <?php echo __('Splitscreen Editor'); ?></a></li>
                                        <?php if ($canModify): ?>
                                            <li class="divider"></li>
                                            <li><a href="<?php echo $baseurl; ?>/eventReports/edit/<?php echo h($report['EventReport']['id']); ?>" class="modal-open"><i class="fa fa-edit"></i> <?php echo __('Edit Metadata'); ?></a></li>
                                            <?php if (!$report['EventReport']['deleted']): ?>
                                                <li><a href="#" class="text-danger" onclick="simplePopup('<?php echo $baseurl; ?>/event_reports/delete/<?php echo h($report['EventReport']['id']); ?>');"><i class="fa fa-trash"></i> <?php echo __('Delete'); ?></a></li>
                                            <?php else: ?>
                                                <li><?php echo $this->Form->postLink('<i class="fa fa-trash-restore"></i> ' . __('Restore'), ['controller' => 'event_reports', 'action' => 'restore', $report['EventReport']['id']], ['escape' => false, 'confirm' => __('Are you sure you want to restore the Report?')]); ?></li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="report-name-cell" title="<?php echo __('Click to view summary'); ?>" style="font-weight: 700; font-size: 15px; color: #333; cursor: pointer; line-height: 1.3; margin-bottom: 4px;">
                            <?php echo h($report['EventReport']['name']); ?>
                        </div>
                        <div class="beta-uuid-compact" style="margin-bottom: 10px;" title="<?php echo h($report['EventReport']['uuid']); ?>" onclick="copyToClipboard('<?php echo h($report['EventReport']['uuid']); ?>'); showMessage('success', 'UUID copied');">
                            <?php echo h($report['EventReport']['uuid']); ?>
                        </div>
                    </div>
                    
                    <div class="beta-report-tile-body">
                        <?php if (!empty($report['EventReport']['content'])): ?>
                            <div class="report-snippet" style="font-size: 13px; color: #555; line-height: 1.5; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php 
                                    $snippet = strip_tags($report['EventReport']['content']);
                                    echo h(mb_strimwidth($snippet, 0, 300, '...')); 
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="beta-report-tile-footer" style="margin-top: auto;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 10px;">
                            <div class="beta-tags-container" style="flex: 1;">
                                <?php
                                    echo $this->element('ajaxTags', [
                                        'event' => $report,
                                        'tags' => $report['EventReportTag'],
                                        'tagAccess' => $canModify,
                                        'localTagAccess' => $canModify,
                                        'scope' => 'event_report',
                                        'attributeId' => $report['EventReport']['id'],
                                        'id_data_path' => 'EventReport.id'
                                    ]);
                                ?>
                            </div>
                            <div style="text-align: right; flex-shrink: 0;">
                                <div class="beta-relative-timestamp" 
                                      data-timestamp="<?php echo h($report['EventReport']['timestamp']); ?>"
                                      data-absolute="<?php echo h(date('Y-m-d H:i:s', $report['EventReport']['timestamp'])); ?>"
                                      title="<?php echo h(date('Y-m-d H:i:s', $report['EventReport']['timestamp'])); ?>"
                                      style="font-size: 11px; margin-bottom: 4px;">
                                    <?php echo $this->Time->time($report['EventReport']['timestamp']); ?>
                                </div>
                                <div class="dist-widget dist-<?php echo intval($report['EventReport']['distribution']); ?>"
                                     title="<?php echo $report['EventReport']['distribution'] == 4 ? h($report['SharingGroup']['name'] ?? '') : (isset($distributionLevels[$report['EventReport']['distribution']]) ? h($distributionLevels[$report['EventReport']['distribution']]) : ''); ?>"
                                     style="transform: scale(0.8); transform-origin: right bottom;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    var loadingSpanAnimation = '<span id="loadingSpan" class="fa fa-spin fa-spinner" style="margin-left: 5px;"></span>';
    $(function() {
        // Report title click -> View Summary Modal
        $('.report-name-cell').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var reportId = $(this).closest('.beta-report-tile').data('primary-id');
            viewFullReport(reportId);
        });

        // Context filter clicks
        $('#eventReportSelectors a').click(function(e) {
            e.preventDefault();
            var container = $("#event-reports-tab-content");
            container.empty().append(
                $('<div class="text-center" style="padding: 20px;"></div>')
                    .append(loadingSpanAnimation)
                    .append('<br>Loading reports...')
            );
            var url = $(this).attr('href');
            $.get(url, function(data) {
                container.html(data);
                if (window.betaTimestamps && typeof window.betaTimestamps.update === 'function') {
                    window.betaTimestamps.update();
                }
            });
        });

        // Update tab count
        <?php
            $paging = isset($this->params->params['paging']['EventReport']) ? $this->params->params['paging']['EventReport'] : [];
            $totalReportsCount = isset($paging['count']) ? (int)$paging['count'] : count($reports);
            if ($context === 'all' || $context === 'default'):
        ?>
            $('.beta-reports-count').text("<?php echo h($totalReportsCount); ?>");
        <?php endif; ?>
    });

    function reloadEventReportTable() {
        var url = $("#eventReportSelectors a.defaultContext").attr('href');
        var container = $("#event-reports-tab-content");
        $.ajax({
            dataType: "html",
            beforeSend: function() {
                container.empty().append(
                    $('<div class="text-center" style="padding: 20px;"></div>')
                        .append(loadingSpanAnimation)
                        .append('<br>Refreshing reports...')
                );
            },
            success: function (data) {
                container.html(data);
                if (window.betaTimestamps && typeof window.betaTimestamps.update === 'function') {
                    window.betaTimestamps.update();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                container.empty().text('<?php echo __('Failed to load Event report table'); ?>');
                showMessage('fail', textStatus + ": " + errorThrown);
            },
            url: url
        });
    }
</script>

<style>
    .beta-reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .beta-report-tile {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s, box-shadow 0.2s;
        min-height: 200px;
        position: relative;
    }
    .beta-report-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border-color: #428bca;
    }
    .beta-report-tile-header {
        margin-bottom: 10px;
    }
    .beta-report-tile .beta-row-menu {
        right: 0;
        left: auto;
    }
    .report-name-cell:hover {
        text-decoration: underline;
        color: #428bca !important;
    }
    .beta-report-tile-footer {
        border-top: 1px solid #f0f0f0;
        padding-top: 12px;
        margin-top: auto;
    }
    .beta-reports-list .beta-row-menu-trigger {
        font-size: 18px;
        padding: 0 5px;
    }
    .beta-no-reports {
        color: #999;
        font-style: italic;
    }
</style>
