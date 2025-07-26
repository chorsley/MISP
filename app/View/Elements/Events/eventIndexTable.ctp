<div class="event-index-grid">
    <div class="event-index-header">
        <div>
            <input class="select_all select" type="checkbox" title="<?php echo __('Select all');?>" role="button" tabindex="0" aria-label="<?php echo __('Select all events on current page');?>" onclick="toggleAllCheckboxes();">
        </div>
        <div class="filter" title="<?= __('Published') ?>"><?= $this->Paginator->sort('published', '<i class="fa fa-upload"></i>', ['escape' => false]) ?></div>
        <?php
            if (Configure::read('MISP.showorgalternate') && Configure::read('MISP.showorg')):
        ?>
            <div class="filter"><?php echo $this->Paginator->sort('Orgc.name', __('Source org')); ?></div>
            <div class="filter"><?php echo $this->Paginator->sort('Orgc.name', __('Member org')); ?></div>
        <?php
            elseif (Configure::read('MISP.showorg') || $isAdmin):
        ?>
            <div class="filter"><?php echo $this->Paginator->sort('Orgc.name', __('Creator org')); ?></div>
        <?php
                endif;
            $date = time();
            $day = 86400;
        ?> 
        <?php if (in_array('owner_org', $columns, true)): ?><div class="filter hide-tablet"><?= $this->Paginator->sort('Org.name', __('Owner org')) ?></div><?php endif; ?>
        <div><?= $this->Paginator->sort('id', __('ID'), ['direction' => 'desc']) ?></div>
        <?php if (in_array('clusters', $columns, true)): ?><div class="hide-tablet"><?= __('Clusters') ?></div><?php endif; ?>
        <?php if (in_array('tags', $columns, true)): ?><div><?= __('Tags') ?></div><?php endif; ?>
        <?php if (in_array('attribute_count', $columns, true)): ?><div title="<?= __('Attribute Count') ?>"><?= $this->Paginator->sort('attribute_count', __('#Attr.')) ?></div><?php endif; ?>
        <?php if (in_array('correlations', $columns, true)): ?><div class="hide-tablet" title="<?= __('Correlation Count')  ?>"><?= __('#Corr.') ?></div><?php endif; ?>
        <?php if (in_array('report_count', $columns, true)): ?><div class="hide-tablet" title="<?= __('Report Count') ?>"><?= $this->Paginator->sort('report_count', __('#Reports')) ?></div><?php endif; ?>
        <?php if (in_array('sightings', $columns, true)): ?><div class="hide-tablet" title="<?= __('Sighting Count')?>"><?= __('#Sightings') ?></div><?php endif; ?>
        <?php if (in_array('proposals', $columns, true)): ?><div class="hide-tablet" title="<?= __('Proposal Count') ?>"><?= __('#Prop') ?></div><?php endif; ?>
        <?php if (in_array('discussion', $columns, true)): ?><div class="hide-tablet" title="<?= __('Post Count') ?>"><?= __('#Posts') ?></div><?php endif; ?>
        <?php if (in_array('creator_user', $columns, true)): ?><div class="hide-tablet"><?= $this->Paginator->sort('user_id', __('Creator user')) ?></div><?php endif; ?>
        <div class="filter"><?= $this->Paginator->sort('date', null, array('direction' => 'desc'));?></div>
        <?php if (in_array('timestamp', $columns, true)): ?><div class="hide-tablet" title="<?= __('Last modified at') ?>"><?= $this->Paginator->sort('timestamp', __('Last modified at')) ?></div><?php endif; ?>
        <?php if (in_array('publish_timestamp', $columns, true)): ?><div class="hide-tablet" title="<?= __('Published at') ?>"><?= $this->Paginator->sort('publish_timestamp', __('Published at')) ?></div><?php endif; ?>
        <div class="filter"><?= $this->Paginator->sort('info');?></div>
        <div title="<?= $eventDescriptions['distribution']['desc'];?>"><?= $this->Paginator->sort('distribution');?></div>
        <div class="actions"><?php echo __('Actions');?></div>
    </div>
    <?php foreach ($events as $event): $eventId = (int)$event['Event']['id']; ?>
    <div class="event-index-row" id="event_<?= $eventId ?>">
        <div data-label="Select">
            <input class="select" type="checkbox" data-id="<?= $eventId ?>" data-can-modify="<?= $this->Acl->canModifyEvent($event) ? 1 : 0 ?>">
        </div>
        <div class="dblclickElement" data-label="Published">
            <a href="<?= "$baseurl/events/view/$eventId" ?>" title="<?= __('View') ?>" aria-label="<?= __('View') ?>">
                <i class="fa <?= $event['Event']['published'] ? 'fa-check green' : 'fa-times grey' ?>"></i>
            </a>
        </div>
        <?php if (Configure::read('MISP.showorg') || $isAdmin): ?>
        <div class="short" data-label="Creator Org" ondblclick="document.location.href ='<?php echo $baseurl . "/events/index/searchorg:" . $event['Orgc']['id'];?>'">
            <div class="org-logo"><?= $this->OrgImg->getOrgLogo($event['Orgc'], 24) ?></div>
        </div>
        <?php endif;?>
        <?php if (in_array('owner_org', $columns, true) || (Configure::read('MISP.showorgalternate') && Configure::read('MISP.showorg'))): ?>
        <div class="short hide-tablet" data-label="Owner Org" ondblclick="document.location.href ='<?php echo $baseurl . "/events/index/searchorg:" . $event['Org']['id'];?>'">
            <div class="org-logo"><?= $this->OrgImg->getOrgLogo($event['Org'], 24) ?></div>
        </div>
        <?php endif; ?>
        <div class="short" data-label="ID">
            <span><a href="<?= $baseurl."/events/view/".$eventId ?>" class="dblclickActionElement threat-level-<?= strtolower(h($event['ThreatLevel']['name'])) ?>" title="<?= h($event['Event']['info']) ?>"><?= $eventId ?></a> <?= !empty($event['Event']['protected']) ? sprintf('<i class="fas fa-lock" title="%s"></i>', __('Protected event')) : ''?></span>
        </div>
        <?php if (in_array('clusters', $columns, true)): ?>
        <div class="short hide-tablet" data-label="Clusters">
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
        </div>
        <?php endif; ?>
        <?php if (in_array('tags', $columns, true)): ?>
        <div class="shortish" data-label="Tags">
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
        </div>
        <?php endif; ?>
        <?php if (in_array('attribute_count', $columns, true)): ?>
        <div class="dblclickElement" data-label="Attributes">
            <?= $event['Event']['attribute_count']; ?>
        </div>
        <?php endif; ?>
        <?php if (in_array('correlations', $columns, true)): ?>
        <div class="bold hide-tablet" data-label="Correlations">
            <?php if (!empty($event['Event']['correlation_count'])): ?>
                <a href="<?= "$baseurl/events/view/$eventId/correlation:1" ?>" title="<?= __n('%s correlation', '%s correlations', $event['Event']['correlation_count'], $event['Event']['correlation_count']), '. ' . __('Show filtered event with correlation only.');?>">
                    <?= intval($event['Event']['correlation_count']); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if (in_array('report_count', $columns, true)): ?>
        <div class="bold hide-tablet" data-label="Reports">
            <?= $event['Event']['report_count']; ?>
        </div>
        <?php endif; ?>
        <?php if (in_array('sightings', $columns, true)): ?>
        <div class="bold hide-tablet" data-label="Sightings">
            <?php if (!empty($event['Event']['sightings_count'])): ?>
                <a href="<?= "$baseurl/events/view/$eventId/sighting:1" ?>" title="<?= __n("1 sighting. Show filtered event with sighting only.", "%s sightings. Show filtered event with sightings only.", $event['Event']['sightings_count'], intval($event['Event']['sightings_count'])) ?>">
                    <?= intval($event['Event']['sightings_count']) ?>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if (in_array('proposals', $columns, true)): ?>
        <div class="bold dblclickElement hide-tablet" data-label="Proposals" title="<?= __n('%s proposal', '%s proposals', $event['Event']['proposals_count'], $event['Event']['proposals_count']) ?>">
            <?= !empty($event['Event']['proposals_count']) ? intval($event['Event']['proposals_count']) : ''; ?>
        </div>
        <?php endif;?>
        <?php if (in_array('discussion', $columns, true)): ?>
        <div class="bold dblclickElement hide-tablet" data-label="Posts">
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
        </div>
        <?php endif;?>
        <?php if (in_array('creator_user', $columns, true)): ?>
        <div class="short dblclickElement hide-tablet" data-label="Creator">
            <?php echo h($event['User']['email']); ?>
        </div>
        <?php endif; ?>
        <div class="short dblclickElement" data-label="Date">
            <time><?= $event['Event']['date'] ?></time>
        </div>
        <?php if (in_array('timestamp', $columns, true)): ?>
        <div class="short dblclickElement hide-tablet" data-label="Modified">
            <?= $this->Time->time($event['Event']['timestamp']) ?>
        </div>
        <?php endif; ?>
        <?php if (in_array('publish_timestamp', $columns, true)): ?>
        <div class="short dblclickElement hide-tablet" data-label="Published">
            <?= $this->Time->time($event['Event']['publish_timestamp']) ?>
        </div>
        <?php endif; ?>
        <?php
            $extends_uuid = $event['Event']['extends_uuid'] ?? null;
            $extendedEventsInfoByUuid = array_column($extendedEvents, 'info', 'uuid');
            $extendedEventsIdByUuid = array_column($extendedEvents, 'id', 'uuid');
            $extends_info = $extendedEventsInfoByUuid[$extends_uuid] ?? null;
            $extends_id = $extendedEventsIdByUuid[$extends_uuid] ?? null;
        ?>

        <div class="dblclickElement" data-label="Info" style="min-width: 20vi; white-space: normal;">
            <?= nl2br(h($event['Event']['info']), false) ?>

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
        </div>
        <div class="short dblclickElement<?php if ($event['Event']['distribution'] == 0) echo ' privateRedText';?>" data-label="Distribution" title="<?= $event['Event']['distribution'] != 3 ? $distributionLevels[$event['Event']['distribution']] : __('All');?>">
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
        </div>
        <div class="short action-links" data-label="Actions">
            <?php
                if (0 == $event['Event']['published'] && $this->Acl->canPublishEvent($event)) {
                    echo sprintf('<a class="useCursorPointer fa fa-upload" title="%s" aria-label="%s" onclick="event.preventDefault();publishPopup(%s)"></a>', __('Publish Event'), __('Publish Event'), $eventId);
                }

                if ($this->Acl->canModifyEvent($event)):
            ?>
                    <a href="<?php echo $baseurl."/events/edit/".$eventId ?>" title="<?php echo __('Edit');?>" aria-label="<?php echo __('Edit');?>"><i class="black fa fa-edit"></i></a>
            <?php
                    echo sprintf('<a class="useCursorPointer fa fa-trash" title="%s" aria-label="%s" onclick="event.preventDefault();deleteEventPopup(%s)"></a>', __('Delete'), __('Delete'), $eventId);
                endif;
            ?>
            <a href="<?php echo $baseurl."/events/view/".$eventId ?>" title="<?php echo __('View');?>" aria-label="<?php echo __('View');?>"><i class="fa black fa-eye"></i></a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
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
