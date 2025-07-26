<div class="event-index-container">
<table class="table table-striped table-hover event-index-table">
    <tr>
        <th>
            <input class="select_all select" type="checkbox" title="<?php echo __('Select all');?>" role="button" tabindex="0" aria-label="<?php echo __('Select all events on current page');?>" onclick="toggleAllCheckboxes();">
        </th>
        <?php
            if (Configure::read('MISP.showorgalternate') && Configure::read('MISP.showorg')):
        ?>
            <th class="filter"><?php echo $this->Paginator->sort('Orgc.name', __('Source org')); ?></th>
            <th class="filter"><?php echo $this->Paginator->sort('Orgc.name', __('Member org')); ?></th>
        <?php
            elseif (Configure::read('MISP.showorg') || $isAdmin):
        ?>
        <?php
                endif;
            $date = time();
            $day = 86400;
        ?> 
        <th><?= $this->Paginator->sort('id', __('ID'), ['direction' => 'desc']) ?> <?= $this->Paginator->sortKey() == 'Event.id' ? ($this->Paginator->sortDir() == 'asc' ? '<i class="fa fa-sort-up"></i>' : '<i class="fa fa-sort-down"></i>') : '<i class="fa fa-sort"></i>' ?></th>
        <th class="filter"><?= $this->Paginator->sort('info') ?> <?= $this->Paginator->sortKey() == 'Event.info' ? ($this->Paginator->sortDir() == 'asc' ? '<i class="fa fa-sort-up"></i>' : '<i class="fa fa-sort-down"></i>') : '<i class="fa fa-sort"></i>' ?></th>
        <th class="filter"><?= $this->Paginator->sort('date', null, array('direction' => 'desc')) ?> <?= $this->Paginator->sortKey() == 'Event.date' ? ($this->Paginator->sortDir() == 'asc' ? '<i class="fa fa-sort-up"></i>' : '<i class="fa fa-sort-down"></i>') : '<i class="fa fa-sort"></i>' ?></th>
        <th class="filter" title="<?= __('Published') ?>"><?= $this->Paginator->sort('published', __('Published'), ['escape' => false]) ?> <?= $this->Paginator->sortKey() == 'Event.published' ? ($this->Paginator->sortDir() == 'asc' ? '<i class="fa fa-sort-up"></i>' : '<i class="fa fa-sort-down"></i>') : '<i class="fa fa-sort"></i>' ?></th>
        <?php if (Configure::read('MISP.showorg') || $isAdmin): ?>
            <th class="filter"><?php echo $this->Paginator->sort('Orgc.name', __('Orgc')); ?> <?= $this->Paginator->sortKey() == 'Orgc.name' ? ($this->Paginator->sortDir() == 'asc' ? '<i class="fa fa-sort-up"></i>' : '<i class="fa fa-sort-down"></i>') : '<i class="fa fa-sort"></i>' ?></th>
        <?php endif; ?>
        <?php if (in_array('clusters', $columns, true)): ?><th><?= __('Clusters') ?></th><?php endif; ?>
        <?php if (in_array('attribute_count', $columns, true)): ?><th title="<?= __('Attribute Count') ?>"><?= $this->Paginator->sort('attribute_count', __('#Attr.')) ?></th><?php endif; ?>
        <?php if (in_array('correlations', $columns, true)): ?><th title="<?= __('Correlation Count')  ?>"><?= __('#Corr.') ?></th><?php endif; ?>
        <?php if (in_array('report_count', $columns, true)): ?><th title="<?= __('Report Count') ?>"><?= $this->Paginator->sort('report_count', __('#Reports')) ?></th><?php endif; ?>
        <?php if (in_array('sightings', $columns, true)): ?><th title="<?= __('Sighting Count')?>"><?= __('#Sightings') ?></th><?php endif; ?>
        <?php if (in_array('proposals', $columns, true)): ?><th title="<?= __('Proposal Count') ?>"><?= __('#Prop') ?></th><?php endif; ?>
        <?php if (in_array('discussion', $columns, true)): ?><th title="<?= __('Post Count') ?>"><?= __('#Posts') ?></th><?php endif; ?>
        <?php if (in_array('timestamp', $columns, true)): ?><th title="<?= __('Last modified at') ?>"><?= $this->Paginator->sort('timestamp', __('Last modified at')) ?></th><?php endif; ?>
        <?php if (in_array('publish_timestamp', $columns, true)): ?><th title="<?= __('Published at') ?>"><?= $this->Paginator->sort('publish_timestamp', __('Published at')) ?></th><?php endif; ?>
        <th title="<?= $eventDescriptions['distribution']['desc'];?>"><?= $this->Paginator->sort('distribution', __('Dist'));?></th>
        <th class="actions"><?php echo __('Actions');?></th>
    </tr>
    <?php foreach ($events as $event): $eventId = (int)$event['Event']['id']; ?>
    <tr id="event_<?= $eventId ?>">
        <td class="checkbox-cell">
            <input class="select" type="checkbox" data-id="<?= $eventId ?>" data-can-modify="<?= $this->Acl->canModifyEvent($event) ? 1 : 0 ?>">
        </td>
        <td class="id-cell">
            <span class="threat-level-<?= strtolower(h($event['ThreatLevel']['name'])) ?>"><?= $eventId ?></span> <?= !empty($event['Event']['protected']) ? sprintf('<i class="fas fa-lock" title="%s"></i>', __('Protected event')) : ''?>
        </td>
        <?php
            $extends_uuid = $event['Event']['extends_uuid'] ?? null;
            $extendedEventsInfoByUuid = array_column($extendedEvents, 'info', 'uuid');
            $extendedEventsIdByUuid = array_column($extendedEvents, 'id', 'uuid');
            $extends_info = $extendedEventsInfoByUuid[$extends_uuid] ?? null;
            $extends_id = $extendedEventsIdByUuid[$extends_uuid] ?? null;
        ?>

        <td class="dblclickElement" style="min-width: 20vi; white-space: normal;">
            <a href="<?= $baseurl."/events/view/".$eventId ?>" class="dblclickActionElement" title="<?= h($event['Event']['info']) ?>"><?= nl2br(h($event['Event']['info']), false) ?></a>

            <?php if ($extends_info): ?>
                <?php if (in_array('is_extension', $columns, true)): ?>
                    <div style="padding-left: 1em;">
                        <span class="apply_css_arrow">
                            <p style="display: inline;">
                                Extends 
                                <a href="<?= h($baseurl) ?>/events/view/<?= h($extends_id) ?>" 
                                title="<?= __('See extended event') ?>" 
                                aria-label="<?= __('See extended event') ?>">
                                    <?= h($extends_id)?>
                                </a>
                                : <?= h($extends_info) ?>
                            </p>
                        </span>
                    </div>
                <?php else: ?>
                    <a href="<?= h($baseurl) ?>/events/view/<?= h($extends_id) ?>" 
                    title="<?= __('Extends event %s', h($extends_id)) ?>"
                    aria-label="<?= __('Extends event %s', h($extends_id)) ?>">
                        <i class="fas fa-external-link-square-alt"></i>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </td>
        <td class="date-cell dblclickElement">
            <time><?= $event['Event']['date'] ?></time>
            <?php
                $timestamp = $event['Event']['timestamp'];
                $offset = time() - $timestamp;
                $unit = 'second(s)';
                $colour = 'red';
                if ($offset >= 60) {
                    $offset = ceil($offset / 60);
                    $unit = 'minute(s)';
                    $colour = 'orange';
                    if ($offset >= 60) {
                        $offset = ceil($offset / 60);
                        $unit = 'hour(s)';
                        $colour = 'green';
                        if ($offset >= 24) {
                            $offset = floor($offset / 24);
                            $unit = 'day(s)';
                            $colour = 'blue';
                            if ($offset >= 365) {
                                $offset = floor($offset / 365);
                                $unit = 'year(s)';
                                $colour = 'grey';
                            }
                        }
                    }
                }
                echo sprintf(' <span class="bold %s">(%s %s ago)</span>', $colour, $offset, $unit);
            ?>
        </td>
        <td class="published-cell dblclickElement">
            <a href="<?= "$baseurl/events/view/$eventId" ?>" title="<?= __('View') ?>" aria-label="<?= __('View') ?>">
                <i class="fa <?= $event['Event']['published'] ? 'fa-check green' : 'fa-times grey' ?>"></i>
            </a>
        </td>
        <?php if (Configure::read('MISP.showorg') || $isAdmin): ?>
        <td class="short" ondblclick="document.location.href ='<?php echo $baseurl . "/events/index/searchorg:" . $event['Orgc']['id'];?>'">
            <?= $this->OrgImg->getOrgLogo($event['Orgc'], 24) ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('clusters', $columns, true)): ?>
        <td class="short">
            <?php
                $galaxies = array();
                if (!empty($event['GalaxyCluster'])) {
                    foreach ($event['GalaxyCluster'] as $galaxy_cluster) {
                        $galaxy_id = $galaxy_cluster['Galaxy']['id'];
                        if (!isset($galaxies[$galaxy_id])) {
                            $galaxies[$galaxy_id] = $galaxy_cluster['Galaxy'];
                        }
                        unset($galaxy_cluster['Galaxy']);
                        $galaxies[$galaxy_id]['GalaxyCluster'][] = $galaxy_cluster;
                    }
                    echo $this->element('galaxyQuickViewNew', array(
                      'data' => $galaxies,
                      'event' => $event,
                      'target_id' => $eventId,
                      'target_type' => 'event',
                      'static_tags_only' => true,
                    ));
                }
            ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('attribute_count', $columns, true)): ?>
        <td class="dblclickElement">
            <?= $event['Event']['attribute_count']; ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('correlations', $columns, true)): ?>
        <td class="bold">
            <?php if (!empty($event['Event']['correlation_count'])): ?>
                <a href="<?= "$baseurl/events/view/$eventId/correlation:1" ?>" title="<?= __n('%s correlation', '%s correlations', $event['Event']['correlation_count'], $event['Event']['correlation_count']), '. ' . __('Show filtered event with correlation only.');?>">
                    <?= intval($event['Event']['correlation_count']); ?>
                </a>
            <?php endif; ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('report_count', $columns, true)): ?>
        <td class="bold">
            <?= $event['Event']['report_count']; ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('sightings', $columns, true)): ?>
        <td class="bold">
            <?php if (!empty($event['Event']['sightings_count'])): ?>
                <a href="<?= "$baseurl/events/view/$eventId/sighting:1" ?>" title="<?= __n("1 sighting. Show filtered event with sighting only.", "%s sightings. Show filtered event with sightings only.", $event['Event']['sightings_count'], intval($event['Event']['sightings_count'])) ?>">
                    <?= intval($event['Event']['sightings_count']) ?>
                </a>
            <?php endif; ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('proposals', $columns, true)): ?>
        <td class="bold dblclickElement" title="<?= __n('%s proposal', '%s proposals', $event['Event']['proposals_count'], $event['Event']['proposals_count']) ?>">
            <?= !empty($event['Event']['proposals_count']) ? intval($event['Event']['proposals_count']) : ''; ?>
        </td>
        <?php endif;?>
        <?php if (in_array('discussion', $columns, true)): ?>
        <td class="bold dblclickElement">
            <?php
                if (!empty($event['Event']['post_count'])) {
                    $post_count = h($event['Event']['post_count']);
                    if (($date - $event['Event']['last_post']) < $day) {
                        $post_count .=  ' (<span class="red bold">' . __('NEW') . '</span>)';
                    }
                } else {
                    $post_count = '';
                }
            ?>
            <span style=" white-space: nowrap;"><?php echo $post_count?></span>
        </td>
        <?php endif;?>
        <?php if (in_array('timestamp', $columns, true)): ?>
        <td class="short dblclickElement">
            <?= $this->Time->time($event['Event']['timestamp']) ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('publish_timestamp', $columns, true)): ?>
        <td class="short dblclickElement">
            <?= $this->Time->time($event['Event']['publish_timestamp']) ?>
        </td>
        <?php endif; ?>
        <td class="short dblclickElement<?php if ($event['Event']['distribution'] == 0) echo ' privateRedText';?>" title="<?= $event['Event']['distribution'] != 3 ? $distributionLevels[$event['Event']['distribution']] : __('All');?>">
            <?php if ($event['Event']['distribution'] == 4):?>
                <a href="<?php echo $baseurl;?>/sharingGroups/view/<?= intval($event['SharingGroup']['id']); ?>"><?= h($event['SharingGroup']['name']) ?></a>
            <?php else:
                echo h($shortDist[$event['Event']['distribution']]);
            endif;
            ?>
            <?php
            echo sprintf(
                '<it type="button" title="%s" class="%s" aria-hidden="true" style="font-size: x-small;" data-event-distribution="%s" data-event-distribution-name="%s" data-scope-id="%s"></it>',
                __('Toggle advanced sharing network viewer'),
                'fa fa-share-alt useCursorPointer distributionNetworkToggle',
                intval($event['Event']['distribution']),
                $event['Event']['distribution'] == 4 ? h($event['SharingGroup']['name']) : h($shortDist[$event['Event']['distribution']]),
                $eventId
            )
            ?>
        </td>
        <td class="actions-cell action-links">
            <div class="btn-group">
                <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#" title="<?= __('Actions') ?>" aria-label="<?= __('Actions') ?>">
                    ⋮
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu pull-right">
                    <li><a href="<?= $baseurl."/events/view/".$eventId ?>" title="<?= __('View') ?>" aria-label="<?= __('View') ?>"><i class="fa fa-eye"></i> <?= __('View') ?></a></li>
                    <?php if ($this->Acl->canModifyEvent($event)): ?>
                        <li><a href="<?= $baseurl."/events/edit/".$eventId ?>" title="<?= __('Edit') ?>" aria-label="<?= __('Edit') ?>"><i class="fa fa-edit"></i> <?= __('Edit') ?></a></li>
                        <li><a class="useCursorPointer" title="<?= __('Delete') ?>" aria-label="<?= __('Delete') ?>" onclick="event.preventDefault();deleteEventPopup(<?= $eventId ?>)"><i class="fa fa-trash"></i> <?= __('Delete') ?></a></li>
                    <?php endif; ?>
                    <?php if (0 == $event['Event']['published'] && $this->Acl->canPublishEvent($event)): ?>
                        <li class="divider"></li>
                        <li><a class="useCursorPointer" title="<?= __('Publish Event') ?>" aria-label="<?= __('Publish Event') ?>" onclick="event.preventDefault();publishPopup(<?= $eventId ?>)"><i class="fa fa-upload"></i> <?= __('Publish Event') ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<script>
    var lastSelected = false;
    $(function() {
        $('.select').on('change', function() {
            listCheckboxesCheckedEventIndex();
        }).click(function(e) {
            if ($(this).is(':checked')) {
                if (e.shiftKey) {
                    selectAllInbetween(lastSelected, this);
                }
                lastSelected = this;
            }
        });

        $('.distributionNetworkToggle').each(function() {
            $(this).distributionNetwork({
                distributionData: <?= json_encode($distributionData, JSON_UNESCAPED_UNICODE); ?>,
            });
        });
    });
</script>
</table>
</div>
