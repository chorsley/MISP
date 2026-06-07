<?php
/**
 * Beta version of Events/eventIndexTable element
 * 
 * This demonstrates the beta UI pattern by reordering columns:
 * Column 1: ID
 * Column 2: Info
 * Column 3: Publish Status
 * 
 * All other columns follow after these three.
 * 
 * @since 2.5.x (beta)
 */

$buildVisibleTagFamilies = function (array $tags) {
    $tagFamilies = [];
    foreach ($tags as $tag) {
        $tagName = $tag['Tag']['name'] ?? '';
        if ($tagName === '') {
            continue;
        }
        $tagFamily = strpos($tagName, ':') !== false ? explode(':', $tagName, 2)[0] : $tagName;
        if (!isset($tagFamilies[$tagFamily])) {
            $tagFamilies[$tagFamily] = [];
        }
        $tagFamilies[$tagFamily][] = $tag;
    }

    $visibleTags = [];
    foreach ($tagFamilies as $familyTags) {
        $visibleTags[] = reset($familyTags);
    }
    return $visibleTags;
};

$buildGalaxyCardsFromTags = function (array $galaxyTags) use ($baseurl) {
    if (empty($galaxyTags)) {
        return [];
    }
    $galaxies = [];
    foreach ($galaxyTags as $galaxyTag) {
        $tagName = $galaxyTag['Tag']['name'] ?? '';
        if (strpos($tagName, 'misp-galaxy:') !== 0) {
            continue;
        }
        $parts = explode(':', $tagName);
        if (count($parts) < 2) {
            continue;
        }
        $galaxyName = $parts[1];
        $clusterValue = '';
        if (count($parts) >= 3) {
            $clusterValue = trim($parts[2], '"');
            $clusterValue = explode('=', $clusterValue);
            $clusterValue = end($clusterValue);
            $clusterValue = trim($clusterValue, '"');
        }
        if (!isset($galaxies[$galaxyName])) {
            $galaxies[$galaxyName] = [];
        }
        $galaxies[$galaxyName][] = [
            'value' => $clusterValue,
            'local' => $galaxyTag['local'],
            'relationship_type' => $galaxyTag['relationship_type'],
            'tag_id' => $galaxyTag['Tag']['id'],
        ];
    }

    $galaxyCards = [];
    foreach ($galaxies as $galaxyName => $clusters) {
        $galaxyCards[] = $this->element('Events/View/galaxy_compact_beta', [
            'galaxyName' => $galaxyName,
            'clusters' => $clusters,
            'baseurl' => $baseurl,
        ]);
    }
    return $galaxyCards;
};
?>
<table class="table table-striped table-hover table-condensed beta-events-table">
    <tr>
        <th>
            <input class="select_all select" type="checkbox" title="<?php echo __('Select all');?>" role="button" tabindex="0" aria-label="<?php echo __('Select all events on current page');?>" onclick="toggleAllCheckboxes();">
        </th>
        <!-- BETA: Column 1 - ID -->
        <th><?= $this->Paginator->sort('id', __('ID'), ['direction' => 'desc']) ?></th>
        <!-- BETA: Column 2 - Info -->
        <th class="filter"><?= $this->Paginator->sort('info');?></th>
        <!-- BETA: Column 3 - Publish Status -->
        <th class="filter" title="<?= __('Published') ?>"><?= $this->Paginator->sort('published', '<i class="fa fa-upload"></i>', ['escape' => false]) ?></th>
        <?php
            if (Configure::read('MISP.showorgalternate') && Configure::read('MISP.showorg')):
        ?>
            <th class="filter"><?php echo $this->Paginator->sort('Orgc.name', __('Source org')); ?></th>
            <th class="filter"><?php echo $this->Paginator->sort('Orgc.name', __('Member org')); ?></th>
        <?php
            elseif (Configure::read('MISP.showorg') || $isAdmin):
        ?>
            <th class="filter col-creator-org"><?php echo $this->Paginator->sort('Orgc.name', __('Creator org')); ?></th>
        <?php
                endif;
            $date = time();
            $day = 86400;
        ?> 
        <?php if (in_array('owner_org', $columns, true)): ?><th class="filter col-owner-org"><?= $this->Paginator->sort('Org.name', __('Owner org')) ?></th><?php endif; ?>
        <?php if (in_array('clusters', $columns, true)): ?><th class="col-clusters"><?= __('Clusters') ?></th><?php endif; ?>
        <?php if (in_array('tags', $columns, true)): ?><th class="col-tags"><?= __('Tags') ?></th><?php endif; ?>
        <?php if (in_array('highlights', $columns, true)): ?><th class="col-highlights"><?= __('Highlight tags') ?></th><?php endif; ?>
        <?php if (in_array('attribute_count', $columns, true)): ?><th class="col-attr-count" title="<?= __('Attribute Count') ?>"><?= $this->Paginator->sort('attribute_count', __('#Attr.')) ?></th><?php endif; ?>
        <?php if (in_array('correlations', $columns, true)): ?><th class="col-corr-count" title="<?= __('Correlation Count')  ?>"><?= __('#Corr.') ?></th><?php endif; ?>
        <?php if (in_array('report_count', $columns, true)): ?><th class="col-report-count" title="<?= __('Report Count') ?>"><?= $this->Paginator->sort('report_count', __('#Reports')) ?></th><?php endif; ?>
        <?php if (in_array('sightings', $columns, true)): ?><th class="col-sightings-count" title="<?= __('Sighting Count')?>"><?= __('#Sightings') ?></th><?php endif; ?>
        <?php if (in_array('proposals', $columns, true)): ?><th class="col-prop-count" title="<?= __('Proposal Count') ?>"><?= __('#Prop') ?></th><?php endif; ?>
        <?php if (in_array('discussion', $columns, true)): ?><th class="col-post-count" title="<?= __('Post Count') ?>"><?= __('#Posts') ?></th><?php endif; ?>
        <?php if (in_array('creator_user', $columns, true)): ?><th class="col-creator-user"><?= $this->Paginator->sort('user_id', __('Creator user')) ?></th><?php endif; ?>
        <th class="filter col-date"><?= $this->Paginator->sort('date', null, array('direction' => 'desc'));?></th>
        <?php if (in_array('timestamp', $columns, true)): ?><th class="col-timestamp" title="<?= __('Last mod') ?>"><?= $this->Paginator->sort('timestamp', __('Last mod')) ?></th><?php endif; ?>
        <?php if (in_array('publish_timestamp', $columns, true)): ?><th class="col-publish-timestamp" title="<?= __('Pub time') ?>"><?= $this->Paginator->sort('publish_timestamp', __('Pub time')) ?></th><?php endif; ?>
    </tr>
    <?php foreach ($events as $event):
        $eventId = (int)$event['Event']['id'];
    ?>
    <tr id="event_<?= $eventId ?>">
        <td style="width:10px" class="beta-checkbox-actions-cell">
            <div class="beta-checkbox-actions-wrapper">
                <input class="select" type="checkbox" data-id="<?= $eventId ?>" data-can-modify="<?= $this->Acl->canModifyEvent($event) ? 1 : 0 ?>">
                <div class="btn-group beta-actions-dropdown">
                    <a class="beta-dropdown-toggle" data-toggle="dropdown" href="#" title="<?= __('Actions') ?>" aria-label="<?= __('Actions') ?>">
                        <i class="fa fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu beta-actions-menu" role="menu">
                        <li><a href="<?= $baseurl."/events/view/".$eventId ?>" title="<?= __('View') ?>"><i class="fa fa-eye"></i> <?= __('View') ?></a></li>
                        <?php if ($this->Acl->canModifyEvent($event)): ?>
                            <li><a href="<?= $baseurl."/events/edit/".$eventId ?>" title="<?= __('Edit') ?>"><i class="fa fa-edit"></i> <?= __('Edit') ?></a></li>
                            <li><a href="#" class="beta-delete-action" onclick="event.preventDefault();deleteEventPopup(<?= $eventId ?>)" title="<?= __('Delete') ?>"><i class="fa fa-trash"></i> <?= __('Delete') ?></a></li>
                        <?php endif; ?>
                        <li class="divider"></li>
                        <li><a href="#" onclick="event.preventDefault();return copyEventIndexUuid(this, '<?= h($event['Event']['uuid']) ?>');" title="<?= __('Copy UUID') ?>"><i class="fa fa-copy"></i> <?= __('Copy UUID') ?></a></li>
                        <?php if ($this->Acl->canAccess('collectionElements', 'addElementToCollection')): ?>
                            <li><a href="#" onclick="event.preventDefault();openAddToCollectionModal('<?= h($event['Event']['uuid']) ?>', <?= $eventId ?>)" title="<?= __('Add to Collection') ?>"><i class="fa fa-folder-plus"></i> <?= __('Add to Collection') ?></a></li>
                        <?php endif; ?>
                        <?php if (0 == $event['Event']['published'] && $this->Acl->canPublishEvent($event)): ?>
                            <li class="divider"></li>
                            <li><a href="#" class="beta-publish-action" onclick="event.preventDefault();publishPopup(<?= $eventId ?>)" title="<?= __('Publish Event') ?>"><i class="fa fa-upload"></i> <?= __('Publish Event') ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </td>
        <!-- BETA: Column 1 - ID -->
        <td class="short">
            <span><a href="<?= $baseurl."/events/view/".$eventId ?>" class="dblclickActionElement threat-level-<?= strtolower(h($event['ThreatLevel']['name'])) ?>" title="<?= h($event['Event']['info']) ?>"><?= $eventId ?></a> <?= !empty($event['Event']['protected']) ? sprintf('<i class="fas fa-lock" title="%s"></i>', __('Protected event')) : ''?></span>
        </td>
        <!-- BETA: Column 2 - Info -->
        <?php
            $extends_uuid = $event['Event']['extends_uuid'] ?? null;
            $extendedEventsInfoByUuid = array_column($extendedEvents, 'info', 'uuid');
            $extendedEventsIdByUuid = array_column($extendedEvents, 'id', 'uuid');
            $extends_info = $extendedEventsInfoByUuid[$extends_uuid] ?? null;
        ?>
        <td class="dblclickElement beta-info-cell" style="min-width: 20vi; white-space: normal;">
            <div class="beta-info-wrapper">
                <div class="dist-widget dist-<?= intval($event['Event']['distribution']) ?> distributionNetworkToggle"
                     title="<?= $event['Event']['distribution'] == 4 ? h($event['SharingGroup']['name']) : h($distributionLevels[$event['Event']['distribution']]) ?>"
                     data-event-distribution="<?= intval($event['Event']['distribution']) ?>"
                     data-event-distribution-name="<?= $event['Event']['distribution'] == 4 ? h($event['SharingGroup']['name']) : h($shortDist[$event['Event']['distribution']]) ?>"
                     data-scope-id="<?= $eventId ?>">
                </div>
                <a href="<?= $baseurl."/events/view/".$eventId ?>" class="beta-info-link" title="<?= h($event['Event']['info']) ?>">
                    <?= nl2br(h($event['Event']['info']), false) ?>
                </a>
                <?php if (!empty($event['Event']['report_count'])): ?>
                    <a href="<?= "$baseurl/events/view/$eventId#summary-reports-section" ?>" title="<?= __n('1 report available', '%s reports available', $event['Event']['report_count'], $event['Event']['report_count']) ?>">
                        <i class="fas fa-file-alt" style="margin-left: 5px; color: #428bca;"></i>
                    </a>
                <?php endif; ?>
            </div>

            <div id="event-collections-container-<?= $eventId ?>" data-event-uuid="<?= h($event['Event']['uuid']) ?>" style="margin-top: 0.35em;">
                <?php if (!empty($event['Event']['CollectionMemberships'])): ?>
                    <div class="beta-event-collections-chips">
                        <?php foreach ($event['Event']['CollectionMemberships'] as $collection): ?>
                            <?php
                                $collectionType = !empty($collection['type']) ? $collection['type'] : 'other';
                                $collectionTypeClass = preg_replace('/[^a-z0-9_-]/i', '', $collectionType);
                                $collectionDescription = !empty($collection['description']) ? mb_substr($collection['description'], 0, 80) : '';
                                $collectionTitle = h($collectionType);
                                if ($collectionDescription !== '') {
                                    $collectionTitle .= ': ' . h($collectionDescription);
                                }
                            ?>
                            <a
                                href="<?= h($baseurl) ?>/collections/view/<?= h($collection['id']) ?>"
                                class="beta-collection-chip beta-type-<?= h($collectionTypeClass) ?>"
                                title="<?= $collectionTitle ?>"
                                aria-label="<?= __('View collection %s', h($collection['name'])) ?>"
                            >
                                <i class="fa fa-folder" style="font-size:10px;margin-right:3px;"></i>
                                <?= h($collection['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

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
        <!-- BETA: Column 3 - Publish Status -->
        <td class="dblclickElement" style="width:30px">
            <a href="<?= "$baseurl/events/view/$eventId" ?>" title="<?= __('View') ?>" aria-label="<?= __('View') ?>">
                <i class="fa <?= $event['Event']['published'] ? 'fa-check green' : 'fa-times grey' ?>"></i>
            </a>
        </td>
        <?php if (Configure::read('MISP.showorg') || $isAdmin): ?>
        <td class="short col-creator-org" ondblclick="document.location.href ='<?php echo $baseurl . "/events/index/searchorg:" . $event['Orgc']['id'];?>'">
            <a href="<?= $baseurl ?>/organisations/view/<?= (int)$event['Orgc']['id'] ?>" class="beta-org-link" title="<?= h($event['Orgc']['name']) ?>">
                <img 
                    src="<?= $baseurl ?>/organisations/getOrgLogo/<?= h($event['Orgc']['id']) ?>.json"
                    title="<?= h($event['Org']['name']) ?>"
                    onError="this.onerror=null; this.outerHTML='';"
                    width=24
                    height=24
                >
                <span>
                    <?= h($event['Orgc']['name']) ?>
                </span>
            </a>
        </td>
        <?php endif;?>
        <?php if (in_array('owner_org', $columns, true) || (Configure::read('MISP.showorgalternate') && Configure::read('MISP.showorg'))): ?>
        <td class="short col-owner-org" ondblclick="document.location.href ='<?php echo $baseurl . "/events/index/searchorg:" . $event['Org']['id'];?>'">
            <a href="<?= $baseurl ?>/organisations/view/<?= (int)$event['Org']['id'] ?>" class="beta-org-link" title="<?= h($event['Org']['name']) ?>">
                <img 
                    src="<?= $baseurl ?>/organisations/getOrgLogo/<?= h($event['Org']['id']) ?>.json"
                    title="<?= h($event['Org']['name']) ?>"
                    onError="this.onerror=null; this.outerHTML='';"
                    width=24
                    height=24
                >
                <span>
                    <?= h($event['Org']['name']) ?>
                </span>
            </a>
        </td>
        <?php endif; ?>
        <?php if (in_array('clusters', $columns, true)): ?>
        <td class="col-clusters">
            <?php
                if (!empty($event['GalaxyCluster'])) {
                    $galaxies = array();
                    foreach ($event['GalaxyCluster'] as $galaxy_cluster) {
                        $galaxy_name = $galaxy_cluster['Galaxy']['name'];
                        if (!isset($galaxies[$galaxy_name])) {
                            $galaxies[$galaxy_name] = array();
                        }
                        $galaxies[$galaxy_name][] = $galaxy_cluster;
                    }
                    $galaxyCards = [];
                    foreach ($galaxies as $galaxyName => $clusters) {
                        $galaxyCards[] = $this->element('Events/View/galaxy_compact_beta', array(
                            'galaxyName' => $galaxyName,
                            'clusters' => $clusters,
                            'baseurl' => $baseurl
                        ));
                    }

                    echo '<div class="beta-galaxies-container" title="' . __('Galaxy clusters attached to this event') . '">';
                    foreach ($galaxyCards as $galaxyCard) {
                        echo $galaxyCard;
                    }
                    echo '</div>';
                }
            ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('tags', $columns, true)): ?>
        <td class="shortish col-tags">
            <?php
                $highlightedTags = $event['Event']['highlightedTags'] ?? [];
                $tags = $event['EventTag'];
                $galaxyTags = [];
                foreach ($tags as $k => $tag) {
                    if ($tag['Tag']['is_galaxy']) {
                        $galaxyTags[] = $tag;
                        unset($tags[$k]);
                    }
                }

                if (!empty($highlightedTags)) {
                    $highlightedTagNames = [];
                    foreach ($highlightedTags as $highlightedTaxonomy) {
                        if (empty($highlightedTaxonomy['tags'])) {
                            continue;
                        }

                        foreach ($highlightedTaxonomy['tags'] as $highlightedTag) {
                            if (!empty($highlightedTag['Tag']['name'])) {
                                $highlightedTagNames[$highlightedTag['Tag']['name']] = true;
                            }
                        }
                    }
                    if (!empty($highlightedTagNames)) {
                        foreach ($tags as $k => $tag) {
                            $tagName = $tag['Tag']['name'] ?? null;
                            if ($tagName !== null && isset($highlightedTagNames[$tagName])) {
                                unset($tags[$k]);
                            }
                        }
                    }
                }

                $totalRegularTagCount = count($tags);
                $visibleTags = $buildVisibleTagFamilies($tags);

                $maxVisibleTags = 3;
                if (count($visibleTags) > $maxVisibleTags) {
                    $visibleTags = array_slice($visibleTags, 0, $maxVisibleTags);
                }
                $hiddenTagCount = max(0, $totalRegularTagCount - count($visibleTags));

                $galaxyCards = $buildGalaxyCardsFromTags($galaxyTags);
                if (!empty($galaxyCards)) {
                    echo '<div class="beta-galaxies-container" title="' . __('Galaxy clusters attached to this event') . '">';
                    foreach ($galaxyCards as $galaxyCard) {
                        echo $galaxyCard;
                    }
                    echo '</div>';
                }
            ?>
            <?php
                $tagElementOptions = [
                    'event' => $event,
                    'tagAccess' => false,
                    'localTagAccess' => false,
                    'missingTaxonomies' => false,
                    'columnised' => true,
                    'static_tags_only' => 1,
                    'tag_display_style' => Configure::check('MISP.full_tags_on_event_index') ? Configure::read('MISP.full_tags_on_event_index') : 1,
                    'highlightedTags' => $highlightedTags
                ];

                echo $this->element('ajaxTags', $tagElementOptions + ['tags' => $visibleTags]);
                if ($hiddenTagCount > 0) {
                    echo '<span class="beta-context-more-count" title="' . h(__n('%s additional tag', '%s additional tags', $hiddenTagCount, $hiddenTagCount)) . '">(+'. (int)$hiddenTagCount .')</span>';
                }
            ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('highlights', $columns, true)): ?>
        <td class="shortish col-highlights">
            <?php
                // Display only highlighted tags using the standard rich_tag element
                $highlightedTags = $event['Event']['highlightedTags'] ?? [];
                if (!empty($highlightedTags)) {
                    foreach ($highlightedTags as $hTaxonomy) {
                        if (isset($hTaxonomy['tags'])) {
                            foreach ($hTaxonomy['tags'] as $hTag) {
                                echo $this->element('rich_tag', [
                                    'tag' => $hTag,
                                    'tagAccess' => false,
                                    'localTagAccess' => false,
                                    'searchUrl' => '/events/index/searchtag:',
                                    'scope' => 'event',
                                    'id' => $event['Event']['id'],
                                    'tag_display_style' => 1 // Use full tag style as requested
                                ]);
                            }
                        }
                    }
                }
            ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('attribute_count', $columns, true)): ?>
        <td class="dblclickElement col-attr-count" style="width:30px">
            <?= $event['Event']['attribute_count']; ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('correlations', $columns, true)): ?>
        <td class="bold col-corr-count" style="width:30px">
            <?php if (!empty($event['Event']['correlation_count'])): ?>
                <a href="<?= "$baseurl/events/view/$eventId/correlation:1" ?>" title="<?= __n('%s correlation', '%s correlations', $event['Event']['correlation_count'], $event['Event']['correlation_count']), '. ' . __('Show filtered event with correlation only.');?>">
                    <?= intval($event['Event']['correlation_count']); ?>
                </a>
            <?php endif; ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('report_count', $columns, true)): ?>
        <td class="bold col-report-count" style="width:30px">
            <?= $event['Event']['report_count']; ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('sightings', $columns, true)): ?>
        <td class="bold col-sightings-count" style="width:30px">
            <?php if (!empty($event['Event']['sightings_count'])): ?>
                <a href="<?= "$baseurl/events/view/$eventId/sighting:1" ?>" title="<?= __n("1 sighting. Show filtered event with sighting only.", "%s sightings. Show filtered event with sightings only.", $event['Event']['sightings_count'], intval($event['Event']['sightings_count'])) ?>">
                    <?= intval($event['Event']['sightings_count']) ?>
                </a>
            <?php endif; ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('proposals', $columns, true)): ?>
        <td class="bold dblclickElement col-prop-count" style="width:30px" title="<?= __n('%s proposal', '%s proposals', $event['Event']['proposals_count'], $event['Event']['proposals_count']) ?>">
            <?= !empty($event['Event']['proposals_count']) ? intval($event['Event']['proposals_count']) : ''; ?>
        </td>
        <?php endif;?>
        <?php if (in_array('discussion', $columns, true)): ?>
        <td class="bold dblclickElement col-post-count" style="width:30px">
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
        <td class="short dblclickElement col-creator-user">
            <?php echo h($event['User']['email']); ?>
        </td>
        <?php endif; ?>
        <td class="short dblclickElement col-date">
            <time><?= $event['Event']['date'] ?></time>
        </td>
        <?php if (in_array('timestamp', $columns, true)): ?>
        <td class="short dblclickElement col-timestamp beta-relative-timestamp" 
            data-timestamp="<?= h($event['Event']['timestamp']) ?>" 
            data-absolute="<?= h(date('Y-m-d H:i:s', $event['Event']['timestamp'])) ?>" 
            title="<?= h(date('Y-m-d H:i:s', $event['Event']['timestamp'])) ?> (click to copy)" 
            style="cursor: pointer;">
            <?= preg_replace('/\s+/', '<br>', $this->Time->time($event['Event']['timestamp'])) ?>
        </td>
        <?php endif; ?>
        <?php if (in_array('publish_timestamp', $columns, true)): ?>
        <td class="short dblclickElement col-publish-timestamp beta-relative-timestamp" 
            <?php if (!empty($event['Event']['publish_timestamp'])): ?>
            data-timestamp="<?= h($event['Event']['publish_timestamp']) ?>" 
            data-absolute="<?= h(date('Y-m-d H:i:s', $event['Event']['publish_timestamp'])) ?>" 
            title="<?= h(date('Y-m-d H:i:s', $event['Event']['publish_timestamp'])) ?> (click to copy)" 
            style="cursor: pointer;"
            <?php endif; ?>
        >
            <?= !empty($event['Event']['publish_timestamp']) ? preg_replace('/\s+/', '<br>', $this->Time->time($event['Event']['publish_timestamp'])) : '' ?>
        </td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>
</table>
<script>
    var lastSelected = false;

    function copyEventIndexUuid(linkElement, uuid) {
        var textArea = document.createElement('textarea');
        textArea.value = uuid;
        textArea.setAttribute('readonly', 'readonly');
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        textArea.setSelectionRange(0, textArea.value.length);

        var copied = false;
        try {
            copied = document.execCommand('copy');
        } catch (err) {
            copied = false;
        }

        document.body.removeChild(textArea);

        if (!copied && navigator.clipboard && navigator.clipboard.writeText && window.isSecureContext) {
            navigator.clipboard.writeText(uuid).then(function() {
                showMessage('success', 'UUID copied');
            }).catch(function() {
                showMessage('fail', 'Could not copy UUID');
            });
            return false;
        }

        showMessage(copied ? 'success' : 'fail', copied ? 'UUID copied' : 'Could not copy UUID');
        return false;
    }

    $(function() {
        // Prevent checkbox clicks from toggling the dropdown menu
        $('.beta-checkbox-actions-wrapper input.select').on('click', function(e) {
            e.stopPropagation();
        });

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
                distributionData: <?= json_encode($this->DistributionGraph->getGraphData(-1), JSON_UNESCAPED_UNICODE); ?>,
            });
        });

    });
</script>
