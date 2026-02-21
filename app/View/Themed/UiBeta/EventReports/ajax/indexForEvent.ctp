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

    <table class="beta-attr-table" style="table-layout: auto;">
        <thead>
            <tr>
                <th style="width: 50px;"><input type="checkbox" class="select-all"></th>
                <th style="width: 50px;"><?php echo __('ID'); ?></th>
                <th><?php echo __('Report Details'); ?></th>
                <th style="width: 150px; text-align: center;"><?php echo __('Tags'); ?></th>
                <th style="width: 120px; text-align: right;"><?php echo __('Last Update'); ?></th>
                <th style="width: 50px; text-align: center;" title="<?php echo __('Distribution'); ?>"><i class="fa fa-share-alt"></i></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reports)): ?>
                <tr>
                    <td colspan="6" class="text-center muted" style="padding: 20px;">
                        <?php echo __('No reports found.'); ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($reports as $report): ?>
                    <tr class="beta-attr-row" data-primary-id="<?php echo h($report['EventReport']['id']); ?>">
                        <td style="position: relative; vertical-align: middle !important;">
                            <div class="beta-row-actions" style="display: flex; align-items: center; min-height: 24px;">
                                <input type="checkbox" class="select-row" value="<?php echo h($report['EventReport']['id']); ?>">
                                <div class="beta-row-menu-trigger">
                                    <i class="fa fa-caret-down"></i>
                                </div>
                                <div class="beta-row-menu" style="left: 0; right: auto;">
                                    <ul>
                                        <li><a href="#" onclick="viewFullReport(<?php echo h($report['EventReport']['id']); ?>); return false;"><i class="fa fa-eye"></i> <?php echo __('View Full'); ?></a></li>
                                        <li><a href="#" class="report-name-cell-inner"><i class="fa fa-file-text"></i> <?php echo __('View Summary'); ?></a></li>
                                        <?php if ($canModify): ?>
                                            <li class="divider"></li>
                                            <li><a href="<?php echo $baseurl; ?>/eventReports/edit/<?php echo h($report['EventReport']['id']); ?>"><i class="fa fa-edit"></i> <?php echo __('Edit'); ?></a></li>
                                            <?php if (!$report['EventReport']['deleted']): ?>
                                                <li><a href="#" class="text-danger" onclick="simplePopup('<?php echo $baseurl; ?>/event_reports/delete/<?php echo h($report['EventReport']['id']); ?>');"><i class="fa fa-trash"></i> <?php echo __('Delete'); ?></a></li>
                                            <?php else: ?>
                                                <li><?php echo $this->Form->postLink('<i class="fa fa-trash-restore"></i> ' . __('Restore'), ['controller' => 'event_reports', 'action' => 'restore', $report['EventReport']['id']], ['escape' => false, 'confirm' => __('Are you sure you want to restore the Report?')]); ?></li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </td>
                        <td style="vertical-align: middle !important;">
                            <span class="beta-id-badge" style="display: inline-block; vertical-align: middle;">#<?php echo h($report['EventReport']['id']); ?></span>
                        </td>
                        <td style="vertical-align: middle !important;">
                            <div class="beta-attr-meta-block">
                                <div class="beta-attr-type-path" style="margin-bottom: 0;">
                                    <span class="beta-uuid-compact" title="<?php echo h($report['EventReport']['uuid']); ?>" onclick="copyToClipboard('<?php echo h($report['EventReport']['uuid']); ?>'); showMessage('success', 'UUID copied');">
                                        <?php echo h(substr($report['EventReport']['uuid'], 0, 8)); ?>...
                                    </span>
                                </div>
                                <div class="report-name-cell" style="font-weight: 600; font-size: 14px; color: #333; cursor: pointer; line-height: 1.2;">
                                    <?php echo h($report['EventReport']['name']); ?>
                                </div>
                                <?php if (!empty($report['EventReport']['content'])): ?>
                                    <div class="report-snippet" style="font-size: 12px; color: #777; margin-top: 4px; max-width: 800px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                        <?php 
                                            $snippet = strip_tags($report['EventReport']['content']);
                                            echo h(mb_strimwidth($snippet, 0, 200, '...')); 
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td style="text-align: center; vertical-align: middle !important;">
                            <div class="beta-tags-container" style="justify-content: center; align-items: center; display: flex; flex-wrap: wrap;">
                                <?php
                                    echo $this->element('ajaxTags', [
                                        'event' => $report,
                                        'tags' => $report['EventReportTag'],
                                        'tagAccess' => $canModify,
                                        'localTagAccess' => $canModify,
                                        'scope' => 'event_report',
                                        'id_data_path' => 'EventReport.id',
                                        'addButtonOnly' => true
                                    ]);
                                ?>
                            </div>
                        </td>
                        <td style="text-align: right; vertical-align: middle !important;">
                            <span class="beta-relative-timestamp" 
                                  data-timestamp="<?php echo h($report['EventReport']['timestamp']); ?>"
                                  data-absolute="<?php echo h(date('Y-m-d H:i:s', $report['EventReport']['timestamp'])); ?>"
                                  title="<?php echo h(date('Y-m-d H:i:s', $report['EventReport']['timestamp'])); ?> (<?php echo __('click to copy'); ?>)"
                                  style="cursor: pointer; display: inline-block; vertical-align: middle;">
                                <?php echo preg_replace('/\s+/', '<br>', $this->Time->time($report['EventReport']['timestamp'])); ?>
                            </span>
                        </td>
                        <td style="text-align: center; vertical-align: middle !important;">
                            <div class="dist-widget dist-<?php echo intval($report['EventReport']['distribution']); ?>"
                                 title="<?php echo $report['EventReport']['distribution'] == 4 ? h($report['SharingGroup']['name'] ?? '') : (isset($distributionLevels[$report['EventReport']['distribution']]) ? h($distributionLevels[$report['EventReport']['distribution']]) : ''); ?>"
                                 style="margin: 0 auto;">
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    var loadingSpanAnimation = '<span id="loadingSpan" class="fa fa-spin fa-spinner" style="margin-left: 5px;"></span>';
    $(function() {
        // Report name click -> View Summary Modal
        $('.report-name-cell, .report-name-cell-inner').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var reportId = $(this).closest('tr').data('primary-id');
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
    .beta-reports-list .beta-attr-table th {
        background-color: #fbfbfb;
    }
    .beta-reports-list .beta-row-menu {
        right: 0;
        left: auto;
    }
    .report-name-cell:hover {
        text-decoration: underline;
        cursor: pointer;
    }
</style>
