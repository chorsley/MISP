<?php
/**
 * Beta UI — Event Collection view
 *
 * Hero-style collection view with:
 *   - Collection name, description, type badge, metadata
 *   - D3 intra-collection correlation graph
 *   - Element list with batch-resolved event titles + IDs
 *
 * @since 2.5.x (beta)
 */

$collection   = $data['Collection'];
$orgcName     = !empty($collection['Orgc']['name'])         ? $collection['Orgc']['name']  : '';
$type         = !empty($collection['type'])                 ? $collection['type']          : 'other';
$distribution = isset($collection['distribution'])          ? (int)$collection['distribution'] : 0;
$sgName       = !empty($collection['SharingGroup']['name']) ? $collection['SharingGroup']['name'] : '';
$distLabel    = $distribution == 4 ? $sgName : (isset($distributionLevels[$distribution]) ? $distributionLevels[$distribution] : '');
$elements     = !empty($collection['CollectionElement'])    ? $collection['CollectionElement'] : [];
$mayModify    = !empty($mayModify);

$eventElements = [];
$otherElements = [];
foreach ($elements as $el) {
    if ($el['element_type'] === 'Event') {
        $eventElements[] = $el;
    } else {
        $otherElements[] = $el;
    }
}

// Build JS-safe UUID list for batch lookup (de-duplicated)
$eventUuids = array_values(array_unique(array_map(fn($el) => $el['element_uuid'], $eventElements)));

// Theme-local enrichment for creator org + tags + galaxies
$eventDetailsByUuid = [];
if (!empty($eventUuids)) {
    $_eventModel = ClassRegistry::init('Event');
    $_events = $_eventModel->find('all', [
        'recursive' => -1,
        'conditions' => ['Event.uuid' => $eventUuids],
        'contain' => [
            'Orgc' => ['fields' => ['id', 'name', 'uuid']],
            'EventTag' => ['fields' => ['EventTag.event_id', 'EventTag.tag_id', 'EventTag.local', 'EventTag.relationship_type']]
        ]
    ]);
    if (!empty($_events)) {
        $_events = $_eventModel->attachTagsToEvents($_events);
        $_galaxyClusterModel = ClassRegistry::init('GalaxyCluster');
        $_events = $_galaxyClusterModel->attachClustersToEventIndex($this->Session->read('Auth.User'), $_events, true);
        foreach ($_events as $_event) {
            if (!empty($_event['Event']['uuid'])) {
                $eventDetailsByUuid[$_event['Event']['uuid']] = $_event;
            }
        }
    }
}
?>
<?php echo $this->element('genericElements/assetLoader', ['js' => ['d3', 'd3.custom', 'd3-sankey.min']]); ?>

<style>
    .beta-tabs-container .beta-tabs {
        margin-top: 20px;
        border-bottom: 1px solid #ddd;
    }

    .beta-tabs-container .beta-tabs > li > a {
        padding: 10px 20px;
        font-weight: 600;
        color: #666;
    }

    .beta-tab-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 0 7px;
        margin-left: 6px;
        border-radius: 999px;
        background: #e9ecef;
        color: #495057;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
        vertical-align: middle;
    }

    .beta-tabs-container .beta-tab-content {
        background: #fff;
        border: 1px solid #ddd;
        border-top: none;
        padding: 20px;
        border-radius: 0 0 4px 4px;
    }
</style>

<div class="beta-collections-view">

    <!-- ── Collection Hero ──────────────────────────────────────────────────── -->
    <div class="beta-collection-hero">
        <div class="beta-collection-hero-main">
            <div class="beta-collection-hero-title-row">
                <h2 class="beta-collection-hero-name"><?= h($collection['name']) ?></h2>
                <span class="beta-collection-type-badge beta-type-<?= h($type) ?>"><?= h($type) ?></span>
            </div>

            <?php if (!empty($collection['description'])): ?>
                <div class="beta-collection-hero-desc"><?= nl2br(h($collection['description'])) ?></div>
            <?php else: ?>
                <div class="beta-collection-hero-desc" style="color:#aaa;font-style:italic;"><?= __('No description provided.') ?></div>
            <?php endif; ?>

            <div class="beta-collection-hero-meta">
                <?php if (!empty($orgcName)): ?>
                    <span class="beta-hero-meta-item"><i class="fa fa-building"></i> <?= h($orgcName) ?></span>
                <?php endif; ?>
                <span class="beta-hero-meta-item">
                    <span class="dist-widget dist-<?= $distribution ?>"></span> <?= h($distLabel) ?>
                </span>
                <span class="beta-hero-meta-item"><i class="fa fa-calendar"></i> <?= h($collection['created']) ?></span>
                <span class="beta-hero-meta-item"><i class="fa fa-clock"></i> <?= h($collection['modified']) ?></span>
                <span class="beta-hero-meta-item">
                    <i class="fa fa-layer-group"></i>
                    <strong><?= count($elements) ?></strong> <?= count($elements) === 1 ? __('element') : __('elements') ?>
                </span>
            </div>
        </div>

        <div class="beta-collection-hero-actions">
            <a href="<?= $baseurl ?>/collections/index" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> <?= __('All Collections') ?>
            </a>
            <?php if ($mayModify): ?>
                <a href="#" onclick="openGenericModal('<?= $baseurl ?>/collections/edit/<?= h($collection['id']) ?>'); return false;"
                   class="btn btn-default btn-sm">
                    <i class="fa fa-edit"></i> <?= __('Edit') ?>
                </a>
                <a href="#" onclick="openGenericModal('<?= $baseurl ?>/collections/delete/<?= h($collection['id']) ?>'); return false;"
                   class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i> <?= __('Delete') ?>
                </a>
            <?php endif; ?>
            <?php if ($this->Acl->canAccess('collectionElements', 'addElementToCollection')): ?>
                <a href="<?= $baseurl ?>/events/index" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> <?= __('Add Events') ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Reports / Correlations Tabs ──────────────────────────────────────── -->
    <div class="beta-tabs-container" style="margin-top: 20px;">
        <ul class="nav nav-tabs beta-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#collection-reports" aria-controls="collection-reports" role="tab" data-toggle="tab">
                    <?= __('Reports') ?>
                    <span class="beta-tab-count-badge" title="<?= __('Number of reports in this collection') ?>">
                        <?= count($eventElements) ?>
                    </span>
                </a>
            </li>
            <li role="presentation">
                <a href="#collection-correlations" aria-controls="collection-correlations" role="tab" data-toggle="tab">
                    <?= __('Correlations') ?>
                    <span class="beta-tab-count-badge" id="collectionCorrelationsTabCount" title="<?= __('Correlations between reports in this collection') ?>">0</span>
                </a>
            </li>
            <li role="presentation">
                <a href="#collection-interconnectivity" aria-controls="collection-interconnectivity" role="tab" data-toggle="tab">
                    <?= __('Interconnectivity') ?>
                </a>
            </li>
        </ul>

        <div class="tab-content beta-tab-content">
            <div role="tabpanel" class="tab-pane active" id="collection-reports">
                <div class="beta-collection-elements-section">
                    <div class="beta-collection-elements-header">
                        <h4 class="beta-collection-elements-title">
                            <i class="fa fa-calendar-alt"></i> <?= __('Events in collection') ?>
                            <span class="beta-element-count-badge"><?= count($eventElements) ?></span>
                        </h4>
                        <?php if (!empty($eventElements)): ?>
                            <div class="beta-collection-element-filter">
                                <span class="beta-element-sort-wrap" title="<?= __('Sort event list') ?>">
                                <i class="fa fa-sort beta-element-sort-icon" aria-hidden="true"></i>
                                <select id="elementSortSelector" class="form-control input-sm beta-element-sort" title="<?= __('Sort event list') ?>">
                                    <option value="event_date_desc"><?= __('Newest event date') ?></option>
                                    <option value="title_asc"><?= __('Title (A-Z)') ?></option>
                                    <option value="updated_desc"><?= __('Recently updated') ?></option>
                                </select>
                                </span>
                                <input type="text" id="elementQuickFilter" class="form-control input-sm"
                                       placeholder="<?= __('Filter by title, ID, date, or comment…') ?>"
                                       style="width:320px;">
                                <span class="beta-element-filter-count" id="elementFilterCount"></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($eventElements)): ?>
                        <div class="beta-collection-empty-elements" style="margin: 10px 0;">
                            <i class="fa fa-inbox fa-2x" style="color:#dee2e6;margin-bottom:.75rem;"></i>
                            <p><?= __('This collection has no event elements yet.') ?></p>
                        </div>
                    <?php else: ?>
                        <div class="beta-event-timeline" id="collectionEventTimeline" style="display:none; margin: 0 0 12px 0;">
                            <div class="beta-event-timeline-header">
                                <span class="beta-event-timeline-title"><i class="fa fa-stream"></i> <?= __('Event timeline') ?></span>
                                <span class="beta-event-timeline-range" id="collectionEventTimelineRange"></span>
                            </div>
                            <div class="beta-event-timeline-track">
                                <div class="beta-event-timeline-ticks" aria-hidden="true"></div>
                                <div class="beta-event-timeline-markers"></div>
                            </div>
                        </div>

                        <div class="beta-elements-list" id="eventElementsList">
                            <?php foreach ($eventElements as $el): ?>
                                <?php
                                    $ev = !empty($eventDetailsByUuid[$el['element_uuid']]) ? $eventDetailsByUuid[$el['element_uuid']] : null;
                                    $orgName = !empty($ev['Orgc']['name']) ? $ev['Orgc']['name'] : '';
                                    $tagNames = '';
                                    if (!empty($ev['EventTag'])) {
                                        $tagNames = implode(' ', array_map(function ($t) {
                                            return $t['Tag']['name'] ?? '';
                                        }, $ev['EventTag']));
                                    }
                                    $searchBase = strtolower(
                                        $el['element_uuid'] . ' ' .
                                        ($el['description'] ?? '') . ' ' .
                                        ($orgName ?? '') . ' ' .
                                        $tagNames
                                    );
                                ?>
                                <div class="beta-element-row"
                                     data-uuid="<?= h($el['element_uuid']) ?>"
                                     data-event-id="<?= !empty($ev['Event']['id']) ? (int)$ev['Event']['id'] : '' ?>"
                                     id="<?= !empty($ev['Event']['id']) ? 'event_' . (int)$ev['Event']['id'] : '' ?>"
                                     data-sort-title="<?= h(mb_strtolower($ev['Event']['info'] ?? '')) ?>"
                                     data-sort-date="<?= !empty($ev['Event']['date']) ? h($ev['Event']['date']) : '' ?>"
                                     data-sort-updated="<?= !empty($ev['Event']['timestamp']) ? (int)$ev['Event']['timestamp'] : 0 ?>"
                                     data-search="<?= h($searchBase) ?>">
                                    <div class="beta-element-icon">
                                        <i class="fa fa-calendar-alt" style="color:#428bca;"></i>
                                    </div>
                                    <div class="beta-element-body">
                                        <div class="beta-element-title">
                                            <a href="<?= $baseurl ?>/events/view/<?= h($el['element_uuid']) ?>"
                                               class="beta-element-event-link event-title-link"
                                               data-uuid="<?= h($el['element_uuid']) ?>">
                                                <span class="beta-element-id-badge">#<span class="event-id-text"><?= !empty($ev['Event']['id']) ? h($ev['Event']['id']) : '?' ?></span></span>
                                                <span class="event-title-text" style="<?= empty($ev['Event']['info']) ? 'color:#aaa;font-style:italic;' : '' ?>"><?= !empty($ev['Event']['info']) ? h($ev['Event']['info']) : __('Loading…') ?></span>
                                            </a>
                                        </div>
                                        <div class="beta-element-meta">
                                            <span class="beta-element-date">
                                                <i class="fa fa-calendar"></i>
                                                <span class="event-date-text"><?= !empty($ev['Event']['date']) ? h($ev['Event']['date']) : __('Loading…') ?></span>
                                            </span>
                                            <?php if (!empty($orgName)): ?>
                                                <span class="beta-element-date" style="margin-left:10px;">
                                                    <i class="fa fa-building"></i>
                                                    <span><?= h($orgName) ?></span>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($ev['Event']['timestamp'])): ?>
                                                <span class="beta-element-date" style="margin-left:10px;">
                                                    <i class="fa fa-clock"></i>
                                                    <span><?= __('Updated %s', $this->Time->time($ev['Event']['timestamp'])) ?></span>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <?php
                                            $signalStats = [];
                                            if (!empty($ev['Event']['correlation_count'])) {
                                                $signalStats[] = sprintf('C:%d', (int)$ev['Event']['correlation_count']);
                                            }
                                            if (!empty($ev['Event']['sightings_count'])) {
                                                $signalStats[] = sprintf('S:%d', (int)$ev['Event']['sightings_count']);
                                            }
                                            if (!empty($ev['Event']['report_count'])) {
                                                $signalStats[] = sprintf('R:%d', (int)$ev['Event']['report_count']);
                                            }

                                            $contextTagPool = [];
                                            if (!empty($ev['EventTag'])) {
                                                foreach ($ev['EventTag'] as $eventTag) {
                                                    if (empty($eventTag['Tag']['name']) || !empty($eventTag['Tag']['is_galaxy'])) {
                                                        continue;
                                                    }
                                                    $contextTagPool[] = $eventTag;
                                                }
                                            }

                                            $visibleTagLimit = 4;
                                            $visibleTags = array_slice($contextTagPool, 0, $visibleTagLimit);
                                            $hiddenTags = array_slice($contextTagPool, $visibleTagLimit);

                                            $galaxyCards = [];
                                            if (!empty($ev['GalaxyCluster'])) {
                                                $galaxies = [];
                                                foreach ($ev['GalaxyCluster'] as $galaxyCluster) {
                                                    $galaxyName = $galaxyCluster['Galaxy']['name'] ?? null;
                                                    if (!$galaxyName) {
                                                        continue;
                                                    }
                                                    if (!isset($galaxies[$galaxyName])) {
                                                        $galaxies[$galaxyName] = [];
                                                    }
                                                    $galaxies[$galaxyName][] = $galaxyCluster;
                                                }
                                                foreach ($galaxies as $galaxyName => $clusters) {
                                                    $galaxyCards[] = $this->element('Events/View/galaxy_compact_beta', [
                                                        'galaxyName' => $galaxyName,
                                                        'clusters' => $clusters,
                                                        'baseurl' => $baseurl
                                                    ]);
                                                }
                                            }
                                            $visibleGalaxyLimit = 2;
                                            $visibleGalaxies = array_slice($galaxyCards, 0, $visibleGalaxyLimit);
                                            $hiddenGalaxies = array_slice($galaxyCards, $visibleGalaxyLimit);

                                            $hiddenIdSuffix = 'event-' . (int)$el['id'];
                                        ?>

                                        <div class="beta-element-context-row">
                                            <?php if (!empty($signalStats)): ?>
                                                <span class="beta-element-chip beta-chip-signals">
                                                    <i class="fa fa-chart-line"></i>
                                                    <strong><?= __('Signals') ?></strong>
                                                    <?= h(implode(' ', $signalStats)) ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($el['description'])): ?>
                                                <span class="beta-element-chip beta-chip-comment" title="<?= h($el['description']) ?>">
                                                    <i class="fa fa-comment-alt"></i>
                                                    <?= h(mb_strimwidth($el['description'], 0, 84, '...')) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (!empty($visibleGalaxies)): ?>
                                            <div class="beta-element-context-group" style="margin-top:6px;">
                                                <span class="beta-element-context-label"><?= __('Galaxies') ?></span>
                                                <div class="beta-element-context-values">
                                                    <?php foreach ($visibleGalaxies as $galaxyHtml): ?>
                                                        <?= $galaxyHtml ?>
                                                    <?php endforeach; ?>
                                                    <?php if (!empty($hiddenGalaxies)): ?>
                                                        <span id="hidden-galaxies-<?= h($hiddenIdSuffix) ?>" class="hidden beta-context-hidden-items">
                                                            <?php foreach ($hiddenGalaxies as $galaxyHtml): ?>
                                                                <?= $galaxyHtml ?>
                                                            <?php endforeach; ?>
                                                        </span>
                                                        <button type="button" class="btn btn-link btn-xs beta-context-toggle" data-target-id="hidden-galaxies-<?= h($hiddenIdSuffix) ?>" data-expand-label="+<?= count($hiddenGalaxies) ?> <?= __('more') ?>" data-collapse-label="<?= __('Show less') ?>">+<?= count($hiddenGalaxies) ?> <?= __('more') ?></button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($visibleTags)): ?>
                                            <div class="beta-element-context-group" style="margin-top:6px;">
                                                <span class="beta-element-context-label"><?= __('Tags') ?></span>
                                                <div class="beta-element-context-values">
                                                    <?php foreach ($visibleTags as $tag): ?>
                                                        <?= $this->element('rich_tag', [
                                                            'tag' => $tag,
                                                            'tagAccess' => false,
                                                            'localTagAccess' => false,
                                                            'searchUrl' => '/events/index/searchtag:',
                                                            'scope' => 'event',
                                                            'id' => $ev['Event']['id'] ?? null,
                                                            'tag_display_style' => 1
                                                        ]) ?>
                                                    <?php endforeach; ?>
                                                    <?php if (!empty($hiddenTags)): ?>
                                                        <span id="hidden-tags-<?= h($hiddenIdSuffix) ?>" class="hidden beta-context-hidden-items">
                                                            <?php foreach ($hiddenTags as $tag): ?>
                                                                <?= $this->element('rich_tag', [
                                                                    'tag' => $tag,
                                                                    'tagAccess' => false,
                                                                    'localTagAccess' => false,
                                                                    'searchUrl' => '/events/index/searchtag:',
                                                                    'scope' => 'event',
                                                                    'id' => $ev['Event']['id'] ?? null,
                                                                    'tag_display_style' => 1
                                                                ]) ?>
                                                            <?php endforeach; ?>
                                                        </span>
                                                        <button type="button" class="btn btn-link btn-xs beta-context-toggle" data-target-id="hidden-tags-<?= h($hiddenIdSuffix) ?>" data-expand-label="+<?= count($hiddenTags) ?> <?= __('more') ?>" data-collapse-label="<?= __('Show less') ?>">+<?= count($hiddenTags) ?> <?= __('more') ?></button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($el['description'])): ?>
                                            <div class="beta-element-desc">
                                                <i class="fa fa-comment-alt" style="color:#aaa;font-size:11px;"></i>
                                                <?= nl2br(h($el['description'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="beta-element-row-actions">
                                        <a href="<?= $baseurl ?>/events/view/<?= h($el['element_uuid']) ?>"
                                           class="btn btn-xs btn-default event-view-btn" title="<?= __('View Event') ?>">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <?php if ($mayModify): ?>
                                            <a href="#"
                                               onclick="openGenericModal('<?= $baseurl ?>/collectionElements/delete/<?= h($el['id']) ?>'); return false;"
                                               class="btn btn-xs btn-danger" title="<?= __('Remove from collection') ?>">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="collection-correlations">
                <!-- ── Correlation Graph ──────────────────────────────────────── -->
                <?php if (count($eventElements) > 1): ?>
                <div class="beta-card beta-collection-corr-card" id="collectionCorrCard">
                    <div class="beta-collection-corr-header">
                        <span><i class="fa fa-project-diagram"></i> <?= __('Intra-Collection Correlations') ?></span>
                        <span id="corrGraphStatus" class="muted" style="font-size:11px;"><?= __('Loading…') ?></span>
                    </div>
                    <div id="collectionCorrGraph" class="beta-collection-corr-graph">
                        <div class="text-center" style="padding:30px;color:#aaa;">
                            <i class="fa fa-spinner fa-spin"></i> <?= __('Fetching correlation data…') ?>
                        </div>
                    </div>
                    <div class="beta-collection-corr-legend">
                        <span><svg width="14" height="14"><rect x="1" y="1" width="12" height="12" fill="#4e9af1"/></svg> <?= __('Event node (click to view)') ?></span>
                        <span><svg width="14" height="14"><rect x="1" y="1" width="12" height="12" fill="#f0ad4e"/></svg> <?= __('Common attribute') ?></span>
                        <span><svg width="24" height="10"><line x1="0" y1="5" x2="24" y2="5" stroke="#999" stroke-width="2"/></svg> <?= __('Correlation') ?></span>
                    </div>
                </div>
                <?php else: ?>
                <div class="beta-card" style="padding:20px;color:#888;">
                    <i class="fa fa-info-circle"></i> <?= __('Add at least two events to view correlations.') ?>
                </div>
                <?php endif; ?>
            </div>

            <div role="tabpanel" class="tab-pane" id="collection-interconnectivity">
                <?php if (count($eventElements) > 1): ?>
                <div class="beta-card beta-collection-corr-card" id="collectionInterconnectivityCard">
                    <div class="beta-collection-corr-header">
                        <span><i class="fa fa-circle-notch"></i> <?= __('Report Attribute Chord') ?></span>
                        <label for="interconnectivityConnectedOnlyToggle" style="margin:0 8px 0 auto; font-size:11px; color:#666; font-weight:normal; display:flex; align-items:center; gap:5px;">
                            <input type="checkbox" id="interconnectivityConnectedOnlyToggle" style="margin:0;" />
                            <span><?= __('Only interconnected ticks') ?></span>
                        </label>
                        <span id="interconnectivityStatus" class="muted" style="font-size:11px;"><?= __('Loading…') ?></span>
                    </div>
                    <div id="collectionInterconnectivityGraph" class="beta-collection-corr-graph beta-chord-graph">
                        <div class="text-center" style="padding:30px;color:#aaa;">
                            <i class="fa fa-spinner fa-spin"></i> <?= __('Fetching report attribute data…') ?>
                        </div>
                    </div>
                    <div class="beta-collection-corr-legend">
                        <span><svg width="14" height="14"><rect x="1" y="1" width="12" height="12" fill="#4e9af1"/></svg> <?= __('Report arc') ?></span>
                        <span><svg width="18" height="10"><line x1="2" y1="5" x2="16" y2="5" stroke="#6c757d" stroke-width="1.5"/></svg> <?= __('Tick = report attribute') ?></span>
                        <span><svg width="24" height="10"><path d="M1,8 Q12,1 23,8" stroke="#f0ad4e" stroke-width="1.5" fill="none"/></svg> <?= __('Shared attribute chord') ?></span>
                    </div>
                </div>
                <?php else: ?>
                <div class="beta-card" style="padding:20px;color:#888;">
                    <i class="fa fa-info-circle"></i> <?= __('Add at least two events to view interconnectivity.') ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    var baseurl   = <?= json_encode($baseurl) ?>;
    var eventUuids = <?= json_encode($eventUuids) ?>;
    var sortSelector = document.getElementById('elementSortSelector');
    var chordState = null;
    var interconnectivityConnectedOnly = false;

    function getSortableRows() {
        return Array.prototype.slice.call(document.querySelectorAll('.beta-element-row[data-uuid]'));
    }

    function safeParseEventDate(rawDate) {
        if (!rawDate) return 0;
        var ts = Date.parse(rawDate + 'T00:00:00Z');
        return isFinite(ts) ? ts : 0;
    }

    function sortRowsInList(mode) {
        var listEl = document.getElementById('eventElementsList');
        if (!listEl) return;

        var rows = getSortableRows();
        rows.sort(function (a, b) {
            var titleA = a.getAttribute('data-sort-title') || '';
            var titleB = b.getAttribute('data-sort-title') || '';
            var dateA = safeParseEventDate(a.getAttribute('data-sort-date'));
            var dateB = safeParseEventDate(b.getAttribute('data-sort-date'));
            var updatedA = parseInt(a.getAttribute('data-sort-updated') || '0', 10) || 0;
            var updatedB = parseInt(b.getAttribute('data-sort-updated') || '0', 10) || 0;

            if (mode === 'title_asc') {
                var cmp = titleA.localeCompare(titleB);
                if (cmp !== 0) return cmp;
                return dateB - dateA;
            }

            if (mode === 'updated_desc') {
                if (updatedB !== updatedA) return updatedB - updatedA;
                return dateB - dateA;
            }

            if (dateB !== dateA) return dateB - dateA;
            return updatedB - updatedA;
        });

        rows.forEach(function (row) {
            listEl.appendChild(row);
        });
    }

    // ── 1. Batch-resolve event titles & IDs ────────────────────────────────
    if (eventUuids.length > 0) {
        $.ajax({
            url: baseurl + '/events/restSearch.json',
            method: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                uuid: eventUuids,
                returnFormat: 'json',
                metadata: 1,
                limit: 500
            }),
            success: function (resp) {
                // Build uuid→event map from response
                var eventMap = {};
                var list = (resp && resp.response) ? resp.response : (Array.isArray(resp) ? resp : []);
                list.forEach(function (row) {
                    var ev = row.Event || row;
                    if (ev && ev.uuid) eventMap[ev.uuid] = ev;
                });

                // Update each event row
                var rows = Array.prototype.slice.call(document.querySelectorAll('.beta-element-row[data-uuid]'));
                rows.forEach(function (row) {
                    var uuid = row.getAttribute('data-uuid');
                    var ev   = eventMap[uuid];
                    if (!ev) return;

                    var idEl    = row.querySelector('.event-id-text');
                    var titleEl = row.querySelector('.event-title-text');
                    var dateEl  = row.querySelector('.event-date-text');
                    var links   = row.querySelectorAll('.event-title-link, .event-view-btn');

                    if (idEl)    idEl.textContent = ev.id;
                    row.setAttribute('data-event-id', ev.id);
                    row.id = 'event_' + ev.id;
                    row.setAttribute('data-sort-title', (ev.info || '').toLowerCase());
                    row.setAttribute('data-sort-date', ev.date || '');
                    row.setAttribute('data-sort-updated', ev.timestamp || 0);
                    if (titleEl) {
                        titleEl.textContent = ev.info;
                        titleEl.style.color      = '';
                        titleEl.style.fontStyle  = '';
                    }
                    if (dateEl) {
                        dateEl.textContent = ev.date ? ev.date : '<?= __('Unknown date') ?>';
                        dateEl.classList.remove('beta-loading');
                    }
                    links.forEach(function (a) {
                        a.href = baseurl + '/events/view/' + ev.id;
                    });
                    var cur = row.getAttribute('data-search') || '';
                    var comment = row.querySelector('.beta-element-desc');
                    var commentText = comment ? comment.textContent : '';
                    row.setAttribute('data-search', (cur + ' ' + ev.id + ' ' + ev.info + ' ' + (ev.date || '') + ' ' + commentText).toLowerCase());
                });

                sortRowsInList(sortSelector ? sortSelector.value : 'event_date_desc');

                // Build event timeline once we have event data
                if (eventUuids.length > 0) {
                    buildEventTimeline(eventMap);
                }

                // Build correlation graph once we have event data
                if (eventUuids.length > 1) {
                    buildCorrGraph(eventMap);
                    buildInterconnectivityChord(eventMap);
                }
            },
            error: function () {
                document.querySelectorAll('.event-title-text').forEach(function (el) {
                    el.textContent = '<?= __('Failed to load') ?>';
                    el.style.color = '#d9534f';
                    el.style.fontStyle = 'normal';
                });
                document.querySelectorAll('.event-date-text').forEach(function (el) {
                    el.textContent = '<?= __('Failed to load') ?>';
                });
                var s = document.getElementById('corrGraphStatus');
                if (s) s.textContent = '<?= __('Could not load event data') ?>';
                var interStatus = document.getElementById('interconnectivityStatus');
                if (interStatus) interStatus.textContent = '<?= __('Could not load event data') ?>';
            }
        });
    }

    // ── 2. Quick filter ────────────────────────────────────────────────────
    var filterInput = document.getElementById('elementQuickFilter');
    var filterCount = document.getElementById('elementFilterCount');
    if (filterInput) {
        filterInput.addEventListener('input', function () {
            var q = this.value.toLowerCase().trim();
            var rows = document.querySelectorAll('.beta-element-row');
            var visible = 0;
            rows.forEach(function (row) {
                var match = !q || (row.getAttribute('data-search') || '').indexOf(q) !== -1;
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            if (filterCount) {
                filterCount.textContent = q ? '(' + visible + ' <?= __('shown') ?>)' : '';
            }
        });
    }

    if (sortSelector) {
        sortSelector.addEventListener('change', function () {
            sortRowsInList(this.value || 'event_date_desc');
        });
    }

    document.addEventListener('click', function (event) {
        var toggle = event.target.closest('.beta-context-toggle');
        if (!toggle) return;
        event.preventDefault();
        var targetId = toggle.getAttribute('data-target-id');
        if (!targetId) return;
        var target = document.getElementById(targetId);
        if (!target) return;

        var isHidden = target.classList.contains('hidden');
        if (isHidden) {
            target.classList.remove('hidden');
            toggle.textContent = toggle.getAttribute('data-collapse-label') || '<?= __('Show less') ?>';
        } else {
            target.classList.add('hidden');
            toggle.textContent = toggle.getAttribute('data-expand-label') || '<?= __('Show more') ?>';
        }
    });

    // ── 3. D3 intra-collection correlation graph ───────────────────────────
    function buildCorrGraph(eventMap) {
        var container = document.getElementById('collectionCorrGraph');
        var statusEl  = document.getElementById('corrGraphStatus');
        if (!container || typeof d3 === 'undefined' || typeof d3.sankey !== 'function') {
            if (statusEl) statusEl.textContent = '<?= __('Correlation graph unavailable') ?>';
            return;
        }

        // Fetch each event's related-events list via the view endpoint (metadata only)
        var uuidsInCollection = new Set(eventUuids);
        var nodeData  = {}; // uuid → {id, info, uuid, threat_level_id}
        var edgeSet   = {}; // "uuidA|uuidB" → count
        var pending   = 0;
        var loaded    = 0;

        // Populate nodeData from already-loaded eventMap
        eventUuids.forEach(function (uuid) {
            if (eventMap[uuid]) nodeData[uuid] = eventMap[uuid];
        });

        // Fetch correlations for each event
        eventUuids.forEach(function (uuid) {
            var ev = eventMap[uuid];
            if (!ev || !ev.id) { checkDone(); return; }
            pending++;
            $.ajax({
                url: baseurl + '/events/view/' + ev.id + '.json',
                method: 'GET',
                data: { noSightings: 1, noEventReports: 1, fetchFullClusters: 0, metadata: 1 },
                dataType: 'json',
                success: function (data) {
                    var relatedEvents = data && data.Event && data.Event.RelatedEvent ? data.Event.RelatedEvent : [];
                    relatedEvents.forEach(function (rel) {
                        var relUuid = rel.Event ? rel.Event.uuid : null;
                        if (!relUuid || !uuidsInCollection.has(relUuid)) return;
                        // Only store edge once (canonical key = sorted pair)
                        var pair = [uuid, relUuid].sort().join('|');
                        edgeSet[pair] = (edgeSet[pair] || 0) + 1;
                    });
                },
                complete: function () { loaded++; checkDone(); }
            });
        });

        function checkDone() {
            if (loaded < pending) return;
            renderGraph(nodeData, edgeSet, container, statusEl);
        }

        // If no events had IDs yet, render immediately with empty edges
        if (pending === 0) renderGraph(nodeData, edgeSet, container, statusEl);
    }

    function buildEventTimeline(eventMap) {
        var timeline = document.getElementById('collectionEventTimeline');
        if (!timeline) return;
        var markers = timeline.querySelector('.beta-event-timeline-markers');
        var ticks = timeline.querySelector('.beta-event-timeline-ticks');
        var rangeLabel = document.getElementById('collectionEventTimelineRange');
        if (!markers) return;

        function formatDateUtc(ts) {
            var d = new Date(ts);
            var y = d.getUTCFullYear();
            var m = String(d.getUTCMonth() + 1).padStart(2, '0');
            var day = String(d.getUTCDate()).padStart(2, '0');
            return y + '-' + m + '-' + day;
        }

        var items = [];
        eventUuids.forEach(function (uuid) {
            var ev = eventMap[uuid];
            if (!ev || !ev.date) return;
            var ts = Date.parse(ev.date + 'T00:00:00Z');
            if (!isFinite(ts)) return;
            items.push({
                uuid: uuid,
                id: ev.id,
                title: ev.info || '',
                date: ev.date,
                ts: ts
            });
        });

        if (!items.length) return;
        items.sort(function (a, b) { return a.ts - b.ts; });
        var minTs = items[0].ts;
        var maxTs = items[items.length - 1].ts;
        var rawRange = maxTs - minTs;
        var range = Math.max(1, rawRange);

        if (ticks) {
            ticks.innerHTML = '';
            var tickCount = rawRange === 0 ? 1 : Math.min(7, Math.max(3, items.length + 1));
            for (var i = 0; i < tickCount; i++) {
                var tick = document.createElement('span');
                var pctTick = tickCount === 1 ? 50 : (i / (tickCount - 1)) * 100;
                var isEdgeTick = i === 0 || i === tickCount - 1;
                tick.className = 'beta-event-timeline-tick' + (isEdgeTick ? ' beta-event-timeline-tick-edge' : '');
                tick.style.left = pctTick + '%';

                var tickTs = rawRange === 0
                    ? minTs
                    : minTs + ((range * i) / Math.max(1, tickCount - 1));
                var label = document.createElement('span');
                label.className = 'beta-event-timeline-tick-label';
                if (i === 0) {
                    label.className += ' beta-event-timeline-tick-label-start';
                } else if (i === tickCount - 1) {
                    label.className += ' beta-event-timeline-tick-label-end';
                }
                label.textContent = formatDateUtc(tickTs);
                tick.appendChild(label);

                ticks.appendChild(tick);
            }
        }

        markers.innerHTML = '';
        items.forEach(function (item) {
            var dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'beta-event-timeline-marker';
            var pct = range > 0 ? ((item.ts - minTs) / range) * 100 : 50;
            dot.style.left = pct + '%';
            dot.title = (item.title || 'Event') + ' - ' + item.date;
            dot.setAttribute('data-event-uuid', item.uuid);
            dot.setAttribute('data-event-id', item.id);
            markers.appendChild(dot);
        });

        if (rangeLabel) {
            var start = items[0].date;
            var end = items[items.length - 1].date;
            rangeLabel.textContent = start === end ? start : (start + ' → ' + end);
        }

        timeline.style.display = '';
        markers.addEventListener('click', function (e) {
            var target = e.target.closest('.beta-event-timeline-marker');
            if (!target) return;
            var eventId = target.getAttribute('data-event-id');
            var row = eventId ? document.getElementById('event_' + eventId) : null;
            if (!row) return;

            document.querySelectorAll('.beta-element-row.beta-element-row-highlight').forEach(function (highlightedRow) {
                highlightedRow.classList.remove('beta-element-row-highlight');
            });

            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            row.classList.add('beta-element-row-highlight');
            setTimeout(function () {
                row.classList.remove('beta-element-row-highlight');
            }, 1400);
        });
    }

    function renderGraph(nodeData, edgeSet, container, statusEl) {
        var edges = Object.keys(edgeSet).map(function (key) {
            var parts = key.split('|');
            return { source: parts[0], target: parts[1], count: edgeSet[key] };
        });

        var edgeCount = edges.length;
        var tabCountEl = document.getElementById('collectionCorrelationsTabCount');
        if (tabCountEl) {
            tabCountEl.textContent = edgeCount;
        }
        if (statusEl) {
            statusEl.textContent = edgeCount > 0
                ? edgeCount + ' <?= __('correlation(s) found between collection events') ?>'
                : '<?= __('No direct correlations found between collection events') ?>';
        }

        // Clear loading spinner
        container.innerHTML = '';

        if (edgeCount === 0) return;

        var nodes = [];
        var links = [];
        var nodeIndex = {};

        function buildEventLabel(ev, fallbackUuid) {
            var title = ev && ev.info ? ev.info : '';
            var id    = ev && ev.id ? ev.id : '?';
            var short = title.length > 40 ? title.substring(0, 39) + '…' : title;
            var name  = '#'+ id + (short ? (': ' + short) : '');
            var full  = '#'+ id + (title ? (': ' + title) : (' (' + fallbackUuid + ')'));
            return { name: name, full: full };
        }

        function buildAttrLabel(attr) {
            var value = attr.value || '';
            var type  = attr.type || '';
            var shortValue = value.length > 44 ? value.substring(0, 43) + '…' : value;
            var name = (type ? (type + ': ') : '') + shortValue;
            var full = (type ? (type + ': ') : '') + value;
            return { name: name, full: full };
        }

        function addNode(key, data) {
            if (nodeIndex[key] !== undefined) return nodeIndex[key];
            nodes.push(data);
            nodeIndex[key] = nodes.length - 1;
            return nodeIndex[key];
        }

        function addEventNode(ev, uuid) {
            var key = 'event|' + uuid;
            var label = buildEventLabel(ev, uuid);
            return addNode(key, { name: label.name, fullTitle: label.full, type: 'event', id: ev ? ev.id : null, uuid: uuid });
        }

        function addAttrNode(attrKey, attr) {
            var label = buildAttrLabel(attr);
            return addNode('attr|' + attrKey, { name: label.name, fullTitle: label.full, type: 'attribute', attrKey: attrKey });
        }

        // Build attribute links for common attributes (event A -> attribute -> event B)
        var attrMap = {}; // attrKey -> { attr, events: {uuid: true}, weight }
        var uuidsInCollection = new Set(eventUuids);
        var eventIdToUuid = {};
        Object.keys(nodeData || {}).forEach(function (uuid) {
            var ev = nodeData[uuid];
            if (ev && ev.id) eventIdToUuid[String(ev.id)] = uuid;
        });
        var attrFetchCount = 0;
        var attrFetchTargets = eventUuids.filter(function (uuid) {
            var ev = nodeData[uuid];
            return ev && ev.id;
        });
        var attrFetchTotal = attrFetchTargets.length;

        if (attrFetchTotal === 0) {
            finalizeGraph();
        } else {
            attrFetchTargets.forEach(function (uuid) {
                var ev = nodeData[uuid];
                $.ajax({
                    url: baseurl + '/correlations/eventCorrelations/' + ev.id + '.json?include_attributes=1&include_org_names=1',
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        Object.keys(data || {}).forEach(function (attrId) {
                            var relations = data[attrId] || [];
                            relations.forEach(function (rel) {
                                if (!rel) return;
                                var relId = rel.id || (rel.Event && rel.Event.id);
                                if (!relId) return;
                                var relUuid = eventIdToUuid[String(relId)];
                                if (!uuidsInCollection.has(relUuid)) return;
                                var value = rel.value || (rel.Attribute && rel.Attribute.value) || '';
                                var type  = rel.type || (rel.Attribute && rel.Attribute.type) || '';
                                var attrKey = (type ? type : 'attr') + '|' + value;
                                if (!attrMap[attrKey]) {
                                    attrMap[attrKey] = { attr: { value: value, type: type }, events: {}, weight: 0 };
                                }
                                attrMap[attrKey].events[uuid] = true;
                                attrMap[attrKey].events[relUuid] = true;
                                attrMap[attrKey].weight++;
                            });
                        });
                    },
                    complete: function () {
                        attrFetchCount++;
                        if (attrFetchCount >= attrFetchTotal) {
                            finalizeGraph();
                        }
                    }
                });
            });
        }

        function finalizeGraph() {
            var attrEntries = Object.keys(attrMap).map(function (attrKey) {
                return { key: attrKey, record: attrMap[attrKey] };
            });
            attrEntries.sort(function (a, b) { return (b.record.weight || 0) - (a.record.weight || 0); });
            var maxAttrNodes = 80;
            attrEntries.slice(0, maxAttrNodes).forEach(function (entry) {
                var attrKey = entry.key;
                var record = entry.record;
                var eventList = Object.keys(record.events);
                if (eventList.length < 2) return;
                var attrIdx = addAttrNode(attrKey, record.attr);
                eventList.forEach(function (uuid) {
                    var ev = nodeData[uuid];
                    var evIdx = addEventNode(ev, uuid);
                    links.push({ source: evIdx, target: attrIdx, value: 1 });
                });
            });

            if (links.length === 0) return;
            renderSankey();
        }

        function renderSankey() {
            var margin = {top: 10, right: 0, bottom: 10, left: 0};
            var width  = Math.max(220, (container.clientWidth || 760));
            var height = Math.max(260, nodes.length * 14);

            container.style.height = (height + margin.top + margin.bottom) + 'px';

            var svg = d3.select(container).append('svg')
                .attr('width', width)
                .attr('height', height + margin.top + margin.bottom)
                .append('g')
                .attr('transform', 'translate(0,' + margin.top + ')');

            var sankey = d3.sankey()
                .nodeWidth(14)
                .nodePadding(10)
                .extent([[1, 1], [width - 1, height - 6]]);

            var graph = sankey({
                nodes: nodes.map(function (d) { return Object.assign({}, d); }),
                links: links.map(function (d) { return Object.assign({}, d); })
            });

            graph.nodes.forEach(function (n) {
                n.y0 = Math.max(1, Math.min(height - 2, n.y0));
                n.y1 = Math.max(n.y0 + 1, Math.min(height - 1, n.y1));
            });

            var color = d3.scale ? d3.scale.category10() : (d3.scaleOrdinal ? d3.scaleOrdinal(d3.schemeCategory10) : function () { return '#428bca'; });
            var typeColor = function (type) {
                if (type === 'attribute') return '#f0ad4e';
                if (type === 'event') return '#4e9af1';
                return typeof color === 'function' ? color(type) : color;
            };

            var node = svg.append('g')
                .selectAll('rect')
                .data(graph.nodes)
                .enter()
                .append('rect')
                .attr('x', function (d) { return d.x0; })
                .attr('y', function (d) { return d.y0; })
                .attr('height', function (d) { return d.y1 - d.y0; })
                .attr('width', function (d) { return d.x1 - d.x0; })
                .attr('fill', function (d) { return typeColor(d.type); })
                .attr('cursor', function (d) { return (d.type === 'attribute') ? 'default' : 'pointer'; })
                .on('click', function (d) {
                    if (d.type !== 'attribute' && d.id) window.location.href = baseurl + '/events/view/' + d.id;
                });

            node.append('title')
                .text(function (d) { return d.fullTitle || d.name; });

            var link = svg.append('g')
                .attr('fill', 'none')
                .attr('stroke-opacity', 0.35)
                .selectAll('path')
                .data(graph.links.filter(function (d) {
                    return isFinite(d.y0) && isFinite(d.y1) && isFinite(d.width);
                }))
                .enter()
                .append('path')
                .attr('class', 'sankey-link')
                .attr('d', function (d) {
                    var x0 = d.source.x1,
                        x1 = d.target.x0,
                        xi = d3.interpolateNumber(x0, x1),
                        x2 = xi(0.5),
                        x3 = xi(0.5),
                        y0 = d.y0,
                        y1 = d.y1;
                    return 'M' + x0 + ',' + y0
                         + 'C' + x2 + ',' + y0
                         + ' ' + x3 + ',' + y1
                         + ' ' + x1 + ',' + y1;
                })
                .attr('stroke', function (d) { return typeColor(d.source.type); })
                .attr('stroke-width', function (d) { return Math.max(1, d.width); });

            var label = svg.append('g')
                .style('font', '10px sans-serif')
                .selectAll('text')
                .data(graph.nodes)
                .enter()
                .append('text')
                .attr('class', 'sankey-label')
                .attr('x', function (d) { return d.x0 < width / 2 ? d.x1 + 6 : d.x0 - 6; })
                .attr('y', function (d) { return (d.y1 + d.y0) / 2; })
                .attr('dy', '0.35em')
                .attr('text-anchor', function (d) { return d.x0 < width / 2 ? 'start' : 'end'; })
                .attr('cursor', function (d) { return (d.type === 'attribute') ? 'default' : 'pointer'; })
                .style('font-weight', function (d) { return (d.type === 'attribute') ? 'normal' : 'bold'; })
                .text(function (d) { return d.name; })
                .on('click', function (d) {
                    if (d.type !== 'attribute' && d.id) window.location.href = baseurl + '/events/view/' + d.id;
                });

            label.append('title')
                .text(function (d) { return d.fullTitle || d.name; });

            function isConnected(a, b) {
                return graph.links.some(function (l) {
                    return (l.source === a && l.target === b) || (l.source === b && l.target === a);
                });
            }

            function highlight(d) {
                link.style('stroke-opacity', function (l) {
                    return (l.source === d || l.target === d) ? 0.6 : 0.08;
                });
                node.style('opacity', function (n) {
                    return (n === d || isConnected(n, d)) ? 1 : 0.2;
                });
                label.style('opacity', function (n) {
                    return (n === d || isConnected(n, d)) ? 1 : 0.2;
                });
            }

            function resetHighlight() {
                link.style('stroke-opacity', 0.35);
                node.style('opacity', 1);
                label.style('opacity', 1);
            }

            node.on('mouseover', highlight).on('mouseout', resetHighlight);
            label.on('mouseover', highlight).on('mouseout', resetHighlight);
        }

    }

    function buildInterconnectivityChord(eventMap) {
        var statusEl = document.getElementById('interconnectivityStatus');
        if (!statusEl) return;
        if (typeof d3 === 'undefined') {
            statusEl.textContent = '<?= __('D3 unavailable') ?>';
            return;
        }

        var targets = eventUuids
            .map(function (uuid) {
                return {
                    uuid: uuid,
                    event: eventMap[uuid] || null
                };
            })
            .filter(function (entry) {
                return entry.event && entry.event.id;
            });

        if (targets.length < 2) {
            statusEl.textContent = '<?= __('Need at least two reports with IDs') ?>';
            return;
        }

        var reportAttrSets = {};
        var attrPresence = {};
        var pending = targets.length;
        statusEl.textContent = '<?= __('Loading attributes…') ?>';

        targets.forEach(function (entry) {
            $.ajax({
                url: baseurl + '/events/view/' + entry.event.id + '.json',
                method: 'GET',
                dataType: 'json',
                data: {
                    noEventReports: 1,
                    noSightings: 1,
                    fetchFullClusters: 0,
                    includeDecayScore: 0,
                    includeGranularCorrelations: 0
                },
                success: function (data) {
                    var payload = data && data.Event ? data.Event : data;
                    var attrs = collectEventAttributes(payload);
                    var setForReport = {};

                    attrs.forEach(function (attr) {
                        var norm = normalizeAttributeKey(attr);
                        if (!norm) return;
                        if (setForReport[norm.key]) return;

                        setForReport[norm.key] = {
                            key: norm.key,
                            type: norm.type,
                            value: norm.value,
                            label: norm.label
                        };

                        if (!attrPresence[norm.key]) {
                            attrPresence[norm.key] = {
                                type: norm.type,
                                value: norm.value,
                                label: norm.label,
                                reports: {}
                            };
                        }
                        attrPresence[norm.key].reports[entry.uuid] = true;
                    });

                    reportAttrSets[entry.uuid] = setForReport;
                },
                complete: function () {
                    pending--;
                    if (pending <= 0) {
                        chordState = {
                            reportAttrSets: reportAttrSets,
                            attrPresence: attrPresence,
                            eventMap: eventMap
                        };
                        renderInterconnectivityChord(chordState);
                    }
                }
            });
        });
    }

    function collectEventAttributes(eventPayload) {
        var attrs = [];
        if (!eventPayload) return attrs;

        if (Array.isArray(eventPayload.Attribute)) {
            attrs = attrs.concat(eventPayload.Attribute);
        }

        if (Array.isArray(eventPayload.Object)) {
            eventPayload.Object.forEach(function (objectEntry) {
                if (objectEntry && Array.isArray(objectEntry.Attribute)) {
                    attrs = attrs.concat(objectEntry.Attribute);
                }
            });
        }

        return attrs;
    }

    function normalizeAttributeKey(attr) {
        if (!attr) return null;
        var value = (attr.value || '').toString().trim();
        var type = (attr.type || 'attribute').toString().trim();
        if (!value) return null;

        var normalizedType = type.toLowerCase();
        var normalizedValue = value.toLowerCase();
        return {
            key: normalizedType + '|' + normalizedValue,
            type: type,
            value: value,
            label: type + ': ' + value
        };
    }

    function renderInterconnectivityChord(state) {
        var container = document.getElementById('collectionInterconnectivityGraph');
        var statusEl = document.getElementById('interconnectivityStatus');
        if (!container || !statusEl || !state) return;

        var reportAttrSets = state.reportAttrSets || {};
        var attrPresence = state.attrPresence || {};
        var eventMap = state.eventMap || {};

        var reports = eventUuids
            .map(function (uuid) {
                var ev = eventMap[uuid] || {};
                var attrs = reportAttrSets[uuid] || {};
                return {
                    uuid: uuid,
                    id: ev.id || null,
                    name: '#'+ (ev.id || '?') + (ev.info ? ': ' + ev.info : ''),
                    attrs: attrs,
                    allAttrKeys: Object.keys(attrs),
                    attrKeys: []
                };
            })
            .filter(function (report) {
                return report.id && report.allAttrKeys.length > 0;
            });

        reports.sort(function (a, b) {
            return (a.id || 0) - (b.id || 0);
        });

        reports.forEach(function (report) {
            report.allAttrKeys.sort(function (a, b) {
                var aCount = Object.keys((attrPresence[a] && attrPresence[a].reports) || {}).length;
                var bCount = Object.keys((attrPresence[b] && attrPresence[b].reports) || {}).length;
                if (bCount !== aCount) return bCount - aCount;
                return a.localeCompare(b);
            });
            report.attrKeys = interconnectivityConnectedOnly
                ? report.allAttrKeys.filter(function (attrKey) {
                    return Object.keys((attrPresence[attrKey] && attrPresence[attrKey].reports) || {}).length > 1;
                })
                : report.allAttrKeys.slice();
        });

        reports = reports.filter(function (report) {
            return report.attrKeys.length > 0;
        });

        if (reports.length < 2) {
            container.innerHTML = '<div class="text-center" style="padding:30px;color:#888;"><i class="fa fa-info-circle"></i> <?= __('Not enough report attributes to build the chord diagram.') ?></div>';
            statusEl.textContent = '<?= __('Not enough data') ?>';
            return;
        }

        var totalTicks = reports.reduce(function (acc, report) { return acc + report.attrKeys.length; }, 0);
        var connectedAttrCount = 0;
        var uniqueAttrCount = 0;
        Object.keys(attrPresence).forEach(function (attrKey) {
            var count = Object.keys(attrPresence[attrKey].reports || {}).length;
            if (count > 1) connectedAttrCount++;
            else if (count === 1) uniqueAttrCount++;
        });

        var width = Math.max(620, container.clientWidth || 620);
        var height = Math.max(620, Math.min(1100, width));
        var outerRadius = Math.min(width, height) / 2 - 56;
        var innerRadius = outerRadius - 24;
        var centerX = width / 2;
        var centerY = height / 2;

        var gap = 0;
        var full = Math.PI * 2;
        var usable = full - (reports.length * gap);
        var cursor = -Math.PI / 2;
        var slotAngles = {};
        var reportArcs = [];
        var reportByUuid = {};
        var reportTickWidth = {};

        reports.forEach(function (report) {
            var slice = usable * (report.attrKeys.length / totalTicks);
            var start = cursor;
            var end = cursor + slice;
            reportArcs.push({ report: report, startAngle: start, endAngle: end });
            reportByUuid[report.uuid] = true;

            if (report.attrKeys.length > 0) {
                var unit = slice / report.attrKeys.length;
                reportTickWidth[report.uuid] = unit;
                report.attrKeys.forEach(function (attrKey, idx) {
                    slotAngles[report.uuid + '|' + attrKey] = start + (idx + 0.5) * unit;
                });
            }
            cursor = end + gap;
        });

        var links = [];
        Object.keys(attrPresence).forEach(function (attrKey) {
            var record = attrPresence[attrKey];
            var reportList = Object.keys(record.reports || {}).filter(function (uuid) {
                return !!reportByUuid[uuid];
            });
            if (reportList.length < 2) return;
            for (var i = 0; i < reportList.length; i++) {
                for (var j = i + 1; j < reportList.length; j++) {
                    var sourceUuid = reportList[i];
                    var targetUuid = reportList[j];
                    if (sourceUuid === targetUuid) continue;
                    var sourceAngle = slotAngles[sourceUuid + '|' + attrKey];
                    var targetAngle = slotAngles[targetUuid + '|' + attrKey];
                    if (!isFinite(sourceAngle) || !isFinite(targetAngle)) continue;
                    links.push({
                        sourceUuid: sourceUuid,
                        targetUuid: targetUuid,
                        sourceAngle: sourceAngle,
                        targetAngle: targetAngle,
                        sourceTickWidth: reportTickWidth[sourceUuid] || 0,
                        targetTickWidth: reportTickWidth[targetUuid] || 0,
                        attrKey: attrKey,
                        attrLabel: record.label || attrKey,
                        reportCount: reportList.length
                    });
                }
            }
        });

        var maxLinks = 4000;
        if (links.length > maxLinks) {
            links = links.slice(0, maxLinks);
        }

        container.innerHTML = '';
        var svg = d3.select(container)
            .append('svg')
            .attr('width', width)
            .attr('height', height)
            .append('g')
            .attr('transform', 'translate(' + centerX + ',' + centerY + ')');

        var reportColor = d3.scale.category10();
        var reportColorByUuid = {};
        reports.forEach(function (report, idx) {
            reportColorByUuid[report.uuid] = reportColor(idx);
        });

        // d3.svg.arc uses 0 rad at 12 o'clock (not 3 o'clock), so shift
        // manual polar calculations by -PI/2 to keep labels/ticks/chords aligned.
        var radialPoint = function (angle, radius) {
            var a = angle - (Math.PI / 2);
            return {
                x: Math.cos(a) * radius,
                y: Math.sin(a) * radius
            };
        };

        var arc = d3.svg.arc()
            .innerRadius(innerRadius)
            .outerRadius(outerRadius);

        var arcGroup = svg.append('g').attr('class', 'beta-chord-report-arcs');
        var linkGroup = svg.append('g').attr('class', 'beta-chord-links');
        var tickGroup = svg.append('g').attr('class', 'beta-chord-ticks');

        arcGroup.selectAll('path')
            .data(reportArcs)
            .enter()
            .append('path')
            .attr('d', function (d) {
                return arc({ startAngle: d.startAngle, endAngle: d.endAngle });
            })
            .style('fill', function (d) { return reportColorByUuid[d.report.uuid]; })
            .style('stroke', '#f5f5f5')
            .style('stroke-width', 2)
            .style('cursor', 'pointer')
            .on('click', function (d) {
                if (d.report.id) {
                    window.location.href = baseurl + '/events/view/' + d.report.id;
                }
            })
            .append('title')
            .text(function (d) {
                return d.report.name + '\n' + d.report.attrKeys.length + ' <?= __('visible attribute tick(s)') ?>';
            });

        var arcLabelGroup = svg.append('g').attr('class', 'beta-chord-report-labels');
        arcLabelGroup.selectAll('text')
            .data(reportArcs)
            .enter()
            .append('text')
            .attr('x', function (d) {
                var angle = (d.startAngle + d.endAngle) / 2;
                return radialPoint(angle, outerRadius + 20).x;
            })
            .attr('y', function (d) {
                var angle = (d.startAngle + d.endAngle) / 2;
                return radialPoint(angle, outerRadius + 20).y;
            })
            .attr('dy', '0.35em')
            .attr('text-anchor', function (d) {
                var angle = (d.startAngle + d.endAngle) / 2;
                var p = radialPoint(angle, 1);
                return p.x >= 0 ? 'start' : 'end';
            })
            .style('font-size', '10px')
            .style('font-weight', 'bold')
            .style('fill', '#333')
            .style('pointer-events', 'none')
            .text(function (d) {
                var label = d.report.name || '';
                return label.length > 34 ? label.substring(0, 33) + '…' : label;
            });

        var tickData = [];
        reports.forEach(function (report) {
            report.attrKeys.forEach(function (attrKey) {
                var angle = slotAngles[report.uuid + '|' + attrKey];
                var sharedCount = Object.keys((attrPresence[attrKey] && attrPresence[attrKey].reports) || {}).length;
                tickData.push({
                    reportUuid: report.uuid,
                    angle: angle,
                    attrKey: attrKey,
                    attrLabel: (attrPresence[attrKey] && attrPresence[attrKey].label) || attrKey,
                    sharedCount: sharedCount
                });
            });
        });

        tickGroup.selectAll('line')
            .data(tickData)
            .enter()
            .append('line')
            .attr('x1', function (d) {
                var p = radialPoint(d.angle, outerRadius);
                return p.x;
            })
            .attr('y1', function (d) {
                var p = radialPoint(d.angle, outerRadius);
                return p.y;
            })
            .attr('x2', function (d) {
                var p = radialPoint(d.angle, outerRadius + 7);
                return p.x;
            })
            .attr('y2', function (d) {
                var p = radialPoint(d.angle, outerRadius + 7);
                return p.y;
            })
            .style('stroke', function (d) { return d.sharedCount > 1 ? '#2c3e50' : '#c3c9ce'; })
            .style('stroke-width', function (d) { return d.sharedCount > 1 ? 1.2 : 0.8; })
            .append('title')
            .text(function (d) {
                return d.attrLabel + '\n' + (d.sharedCount > 1
                    ? d.sharedCount + ' <?= __('reports share this attribute') ?>'
                    : '<?= __('Unique to this report') ?>');
            });

        var linkSelection = linkGroup.selectAll('path')
            .data(links)
            .enter()
            .append('path')
            .attr('d', function (d) {
                var halfSource = Math.max(0.0012, (d.sourceTickWidth || 0) * 0.45);
                var halfTarget = Math.max(0.0012, (d.targetTickWidth || 0) * 0.45);

                var a0s = d.sourceAngle - halfSource;
                var a1s = d.sourceAngle + halfSource;
                var a0t = d.targetAngle - halfTarget;
                var a1t = d.targetAngle + halfTarget;

                var s0 = radialPoint(a0s, innerRadius);
                var s1 = radialPoint(a1s, innerRadius);
                var t0 = radialPoint(a0t, innerRadius);
                var t1 = radialPoint(a1t, innerRadius);

                var cRadius = Math.max(0, innerRadius - 28);
                var cS0 = radialPoint(a0s, cRadius);
                var cS1 = radialPoint(a1s, cRadius);
                var cT0 = radialPoint(a0t, cRadius);
                var cT1 = radialPoint(a1t, cRadius);

                return 'M' + s0.x + ',' + s0.y
                    + ' C' + cS0.x + ',' + cS0.y
                    + ' ' + cT0.x + ',' + cT0.y
                    + ' ' + t0.x + ',' + t0.y
                    + ' A' + innerRadius + ',' + innerRadius + ' 0 0,1 ' + t1.x + ',' + t1.y
                    + ' C' + cT1.x + ',' + cT1.y
                    + ' ' + cS1.x + ',' + cS1.y
                    + ' ' + s1.x + ',' + s1.y
                    + ' A' + innerRadius + ',' + innerRadius + ' 0 0,0 ' + s0.x + ',' + s0.y
                    + ' Z';
            })
            .style('fill', function (d) {
                return reportColorByUuid[d.sourceUuid] || '#f0ad4e';
            })
            .style('stroke', function (d) {
                return reportColorByUuid[d.sourceUuid] || '#f0ad4e';
            })
            .style('fill-opacity', 0.20)
            .style('stroke-opacity', 0.36)
            .style('stroke-width', 0.8);

        linkSelection.append('title')
            .text(function (d) {
                return d.attrLabel + '\n' +
                    '<?= __('Shared between') ?> ' +
                    ((eventMap[d.sourceUuid] && eventMap[d.sourceUuid].id) ? ('#' + eventMap[d.sourceUuid].id) : d.sourceUuid) +
                    ' <?= __('and') ?> ' +
                    ((eventMap[d.targetUuid] && eventMap[d.targetUuid].id) ? ('#' + eventMap[d.targetUuid].id) : d.targetUuid);
            });

        function highlightAttr(attrKey) {
            linkSelection
                .style('fill-opacity', function (d) {
                    return d.attrKey === attrKey ? 0.52 : 0.03;
                })
                .style('stroke-opacity', function (d) {
                    return d.attrKey === attrKey ? 0.75 : 0.05;
                });
            tickGroup.selectAll('line').style('opacity', function (d) {
                return d.attrKey === attrKey ? 1 : 0.18;
            });
        }

        function resetHighlight() {
            linkSelection
                .style('fill-opacity', 0.20)
                .style('stroke-opacity', 0.36);
            tickGroup.selectAll('line').style('opacity', 1);
            arcGroup.selectAll('path').style('opacity', 1);
            arcLabelGroup.selectAll('text').style('opacity', 1);
        }

        function highlightReport(reportUuid) {
            linkSelection
                .style('fill-opacity', function (d) {
                    return (d.sourceUuid === reportUuid || d.targetUuid === reportUuid) ? 0.58 : 0.02;
                })
                .style('stroke-opacity', function (d) {
                    return (d.sourceUuid === reportUuid || d.targetUuid === reportUuid) ? 0.82 : 0.04;
                });

            tickGroup.selectAll('line').style('opacity', function (d) {
                var isConnectedTick = d.reportUuid === reportUuid || links.some(function (l) {
                    if (l.attrKey !== d.attrKey) return false;
                    return (l.sourceUuid === reportUuid && l.targetUuid === d.reportUuid) ||
                        (l.targetUuid === reportUuid && l.sourceUuid === d.reportUuid);
                });
                return isConnectedTick ? 1 : 0.12;
            });

            arcGroup.selectAll('path').style('opacity', function (d) {
                if (d.report.uuid === reportUuid) return 1;
                var connected = links.some(function (l) {
                    return (l.sourceUuid === reportUuid && l.targetUuid === d.report.uuid) ||
                        (l.targetUuid === reportUuid && l.sourceUuid === d.report.uuid);
                });
                return connected ? 0.72 : 0.20;
            });

            arcLabelGroup.selectAll('text').style('opacity', function (d) {
                if (d.report.uuid === reportUuid) return 1;
                var connected = links.some(function (l) {
                    return (l.sourceUuid === reportUuid && l.targetUuid === d.report.uuid) ||
                        (l.targetUuid === reportUuid && l.sourceUuid === d.report.uuid);
                });
                return connected ? 0.78 : 0.18;
            });
        }

        tickGroup.selectAll('line')
            .on('mouseover', function (d) { highlightAttr(d.attrKey); })
            .on('mouseout', resetHighlight);

        arcGroup.selectAll('path')
            .on('mouseover', function (d) { highlightReport(d.report.uuid); })
            .on('mouseout', resetHighlight);

        arcLabelGroup.selectAll('text')
            .on('mouseover', function (d) { highlightReport(d.report.uuid); })
            .on('mouseout', resetHighlight);

        statusEl.textContent =
            reports.length + ' <?= __('reports') ?> · ' +
            totalTicks + ' <?= __('attribute ticks') ?> · ' +
            links.length + ' <?= __('chord(s)') ?> · ' +
            connectedAttrCount + ' <?= __('shared attributes') ?> · ' +
            uniqueAttrCount + ' <?= __('unique attributes') ?>' +
            (interconnectivityConnectedOnly ? ' · <?= __('connected-only mode') ?>' : '');
    }

    $(document).on('shown.bs.tab', 'a[href="#collection-interconnectivity"]', function () {
        if (chordState) {
            renderInterconnectivityChord(chordState);
        }
    });

    var debounceChordRerender = null;
    $(window).on('resize', function () {
        if (!chordState || !$('#collection-interconnectivity').hasClass('active')) return;
        clearTimeout(debounceChordRerender);
        debounceChordRerender = setTimeout(function () {
            renderInterconnectivityChord(chordState);
        }, 180);
    });

    var connectedOnlyToggle = document.getElementById('interconnectivityConnectedOnlyToggle');
    if (connectedOnlyToggle) {
        connectedOnlyToggle.checked = interconnectivityConnectedOnly;
        connectedOnlyToggle.addEventListener('change', function () {
            interconnectivityConnectedOnly = !!this.checked;
            if (chordState) {
                renderInterconnectivityChord(chordState);
            }
        });
    }

})();
</script>
