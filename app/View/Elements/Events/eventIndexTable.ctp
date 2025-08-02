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
        <th title="<?= __('Last modified at') ?>"><?= $this->Paginator->sort('timestamp', __('Last mod')) ?> <?= $this->Paginator->sortKey() == 'Event.timestamp' ? ($this->Paginator->sortDir() == 'asc' ? '<i class="fa fa-sort-up"></i>' : '<i class="fa fa-sort-down"></i>') : '<i class="fa fa-sort"></i>' ?></th>
        <th title="<?= __('Published at') ?>"><?= $this->Paginator->sort('publish_timestamp', __('Published at')) ?> <?= $this->Paginator->sortKey() == 'Event.publish_timestamp' ? ($this->Paginator->sortDir() == 'asc' ? '<i class="fa fa-sort-up"></i>' : '<i class="fa fa-sort-down"></i>') : '<i class="fa fa-sort"></i>' ?></th>
        <th class="filter" title="<?= __('Published') ?>"><?= $this->Paginator->sort('published', __('Published'), ['escape' => false]) ?> <?= $this->Paginator->sortKey() == 'Event.published' ? ($this->Paginator->sortDir() == 'asc' ? '<i class="fa fa-sort-up"></i>' : '<i class="fa fa-sort-down"></i>') : '<i class="fa fa-sort"></i>' ?></th>
        <?php if (Configure::read('MISP.showorg') || $isAdmin): ?>
            <th class="filter"><?php echo $this->Paginator->sort('Orgc.name', __('Orgc')); ?> <?= $this->Paginator->sortKey() == 'Orgc.name' ? ($this->Paginator->sortDir() == 'asc' ? '<i class="fa fa-sort-up"></i>' : '<i class="fa fa-sort-down"></i>') : '<i class="fa fa-sort"></i>' ?></th>
        <?php endif; ?>
        <?php if (in_array('owner_org', $columns, true)): ?><th class="filter"><?= $this->Paginator->sort('Org.name', __('Owner org')) ?></th><?php endif; ?>
        <?php if (in_array('tags', $columns, true)): ?><th><?= __('Tags') ?></th><?php endif; ?>
        <?php if (in_array('clusters', $columns, true)): ?><th><?= __('Galaxies') ?></th><?php endif; ?>
        <?php if (in_array('attribute_count', $columns, true)): ?><th title="<?= __('Attribute Count') ?>"><?= $this->Paginator->sort('attribute_count', __('#Attr.')) ?></th><?php endif; ?>
        <?php if (in_array('correlations', $columns, true)): ?><th title="<?= __('Correlation Count')  ?>"><?= __('#Corr.') ?></th><?php endif; ?>
        <?php if (in_array('report_count', $columns, true)): ?><th title="<?= __('Report Count') ?>"><?= $this->Paginator->sort('report_count', __('#Reports')) ?></th><?php endif; ?>
        <?php if (in_array('sightings', $columns, true)): ?><th title="<?= __('Sighting Count')?>"><?= __('#Sightings') ?></th><?php endif; ?>
        <?php if (in_array('proposals', $columns, true)): ?><th title="<?= __('Proposal Count') ?>"><?= __('#Prop') ?></th><?php endif; ?>
        <?php if (in_array('discussion', $columns, true)): ?><th title="<?= __('Post Count') ?>"><?= __('#Posts') ?></th><?php endif; ?>
        <?php if (in_array('creator_user', $columns, true)): ?><th><?= $this->Paginator->sort('user_id', __('Creator user')) ?></th><?php endif; ?>
        <?php if (in_array('publish_timestamp', $columns, true)): ?><th title="<?= __('Published at') ?>"><?= $this->Paginator->sort('publish_timestamp', __('Published at')) ?></th><?php endif; ?>
        <th title="<?= $eventDescriptions['distribution']['desc'];?>"><?= $this->Paginator->sort('distribution', __('Dist'));?></th>
        <th class="actions"><?php echo __('Actions');?></th>
    </tr>
    <?php 
    // Helper function to find and extract tag by prefix
    if (!function_exists('findTagByPrefix')) {
        function findTagByPrefix($tag_list, $tag_prefix) {
            foreach ($tag_list as $tag) {
                if (substr($tag['Tag']['name'], 0, strlen($tag_prefix)) == $tag_prefix) {
                    return $tag['Tag']['name'];
                }
            }
            return '';
        }
    }

    // Helper function to get most sensitive TLP tag
    if (!function_exists('getMostSensitiveTlp')) {
        function getMostSensitiveTlp($tag_list) {
            $tlp_sensitivity = [
                'tlp:white' => 1,
                'tlp:clear' => 2,
                'tlp:green' => 3,
                'tlp:amber' => 4,
                'tlp:amber+strict' => 5,
                'tlp:red' => 6
            ];
            
            $found_tlp = '';
            $max_sensitivity = 0;
            
            foreach ($tag_list as $tag) {
                $tag_name = strtolower($tag['Tag']['name']);
                if (strpos($tag_name, 'tlp:') === 0) {
                    $sensitivity = $tlp_sensitivity[$tag_name] ?? 0;
                    if ($sensitivity > $max_sensitivity) {
                        $max_sensitivity = $sensitivity;
                        $found_tlp = $tag['Tag']['name'];
                    }
                }
            }
            
            return $found_tlp;
        }
    }

    // Helper function to extract galaxy cluster values by type
    if (!function_exists('getGalaxyClusterByType')) {
        function getGalaxyClusterByType($galaxy_clusters, $types) {
            if (!is_array($types)) {
                $types = [$types];
            }
            
            foreach ($galaxy_clusters as $cluster) {
                if (isset($cluster['Galaxy']['type']) && in_array($cluster['Galaxy']['type'], $types)) {
                    return $cluster['value'] ?? '';
                }
            }
            return '';
        }
    }

    foreach ($events as $event): 
        $eventId = (int)$event['Event']['id']; 
        
        // Extract tags for the predictable tag bar
        $tlpTag = getMostSensitiveTlp($event['EventTag']);
        $workflowTag = findTagByPrefix($event['EventTag'], 'workflow:');
        
        $threatActorTag = '';
        $sectorTag = '';
        $malwareTag = '';
        
        if (!empty($event['GalaxyCluster'])) {
            $threatActorTag = getGalaxyClusterByType($event['GalaxyCluster'], 'threat-actor');
            $sectorTag = getGalaxyClusterByType($event['GalaxyCluster'], 'sector');
            $malwareTag = getGalaxyClusterByType($event['GalaxyCluster'], ['malpedia', 'ransomware', 'banker']);
        }
    ?>
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

            <div class="predictable-tag-bar">
                <?php if ($tlpTag): ?>
                    <div class="tag-column tlp-column">
                        <span class="tag" style="background-color: <?= $tlpTag === 'tlp:red' ? '#d9534f' : ($tlpTag === 'tlp:amber' || $tlpTag === 'tlp:amber+strict' ? '#f0ad4e' : ($tlpTag === 'tlp:green' ? '#5cb85c' : '#5bc0de')) ?>; color: white; font-size: 10px; padding: 1px 4px; border-radius: 2px;">
                            <?= h(strtoupper(str_replace('tlp:', '', $tlpTag))) ?>
                        </span>
                    </div>
                <?php else: ?>
                    <div class="tag-column tlp-column"></div>
                <?php endif; ?>

                <?php if ($threatActorTag): ?>
                    <div class="tag-column threat-actor-column">
                        <span class="tag" style="background-color: #337ab7; color: white; font-size: 10px; padding: 1px 4px; border-radius: 2px;" title="<?= h($threatActorTag) ?>">
                            <?= h($threatActorTag) ?>
                        </span>
                    </div>
                <?php else: ?>
                    <div class="tag-column threat-actor-column"></div>
                <?php endif; ?>

                <?php if ($sectorTag): ?>
                    <div class="tag-column sector-column">
                        <span class="tag" style="background-color: #5cb85c; color: white; font-size: 10px; padding: 1px 4px; border-radius: 2px;" title="<?= h($sectorTag) ?>">
                            <?= h($sectorTag) ?>
                        </span>
                    </div>
                <?php else: ?>
                    <div class="tag-column sector-column"></div>
                <?php endif; ?>

                <?php if ($workflowTag): ?>
                    <div class="tag-column workflow-column">
                        <span class="tag" style="background-color: #f0ad4e; color: white; font-size: 10px; padding: 1px 4px; border-radius: 2px;" title="<?= h($workflowTag) ?>">
                            <?= h(str_replace('workflow:', '', $workflowTag)) ?>
                        </span>
                    </div>
                <?php else: ?>
                    <div class="tag-column workflow-column"></div>
                <?php endif; ?>

                <?php if ($malwareTag): ?>
                    <div class="tag-column malware-column">
                        <span class="tag" style="background-color: #d9534f; color: white; font-size: 10px; padding: 1px 4px; border-radius: 2px;" title="<?= h($malwareTag) ?>">
                            <?= h($malwareTag) ?>
                        </span>
                    </div>
                <?php else: ?>
                    <div class="tag-column malware-column"></div>
                <?php endif; ?>
            </div>
        </td>
        <td class="date-cell dblclickElement">
            <time><?= $event['Event']['date'] ?></time>
        </td>
        <td class="short">
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
                $absoluteTime = date('Y-m-d H:i:s', $timestamp);
                echo sprintf('<span class="bold %s" title="%s">%s %s ago</span>', $colour, $absoluteTime, $offset, $unit);
            ?>
        </td>
        <td class="short">
            <?php
                $publishTimestamp = $event['Event']['publish_timestamp'];
                if ($publishTimestamp && $publishTimestamp != '0') {
                    $offset = time() - $publishTimestamp;
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
                    $absoluteTime = date('Y-m-d H:i:s', $publishTimestamp);
                    echo sprintf('<span class="bold %s" title="%s">%s %s ago</span>', $colour, $absoluteTime, $offset, $unit);
                } else {
                    echo '<span class="grey">Not published</span>';
                }
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
        <?php if (in_array('owner_org', $columns, true) || (Configure::read('MISP.showorgalternate') && Configure::read('MISP.showorg'))): ?>
        <td class="short" ondblclick="document.location.href ='<?php echo $baseurl . "/events/index/searchorg:" . $event['Org']['id'];?>'">
            <?= $this->OrgImg->getOrgLogo($event['Org'], 24) ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('tags', $columns, true)): ?>
        <td class="shortish">
            <?= $this->element('ajaxTags', [
                'event' => $event,
                'tags' => $event['EventTag'],
                'tagAccess' => false,
                'localTagAccess' => false,
                'missingTaxonomies' => false,
                'columnised' => true,
                'static_tags_only' => 1,
                'tag_display_style' => Configure::check('MISP.full_tags_on_event_index') ? Configure::read('MISP.full_tags_on_event_index') : 1,
                'highlightedTags' => $event['Event']['highlightedTags'] ?? [],
            ]);
            ?>
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
        <?php if (in_array('creator_user', $columns, true)): ?>
        <td class="short dblclickElement">
            <?php echo h($event['User']['email']); ?>
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

<style>
.predictable-tag-bar {
    display: flex;
    gap: 4px;
    margin-top: 4px;
    font-size: 10px;
    flex-wrap: nowrap;
}

.tag-column {
    min-width: 70px;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex-shrink: 0;
}

.tag-column .tag {
    display: inline-block;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.tlp-column {
    min-width: 50px;
    max-width: 80px;
}

.threat-actor-column {
    min-width: 80px;
    max-width: 140px;
}

.sector-column {
    min-width: 70px;
    max-width: 120px;
}

.workflow-column {
    min-width: 60px;
    max-width: 100px;
}

.malware-column {
    min-width: 70px;
    max-width: 120px;
}
</style>

</table>
</div>
