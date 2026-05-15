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

// Build JS-safe UUID list for batch lookup
$eventUuids = array_values(array_map(fn($el) => $el['element_uuid'], $eventElements));

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
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#collection-reports" aria-controls="collection-reports" role="tab" data-toggle="tab">
                    <?= __('Reports') ?>
                </a>
            </li>
            <li role="presentation">
                <a href="#collection-correlations" aria-controls="collection-correlations" role="tab" data-toggle="tab">
                    <?= __('Correlations') ?>
                </a>
            </li>
            <li role="presentation">
                <a href="#collection-force" aria-controls="collection-force" role="tab" data-toggle="tab">
                    <?= __('Force Graph') ?>
                </a>
            </li>
        </ul>

        <div class="tab-content" style="padding-top: 15px;">
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

            <div role="tabpanel" class="tab-pane" id="collection-force">
                <!-- ── Force-Directed Graph ─────────────────────────────────── -->
                <?php if (count($eventElements) > 1): ?>
                <div class="beta-card beta-collection-corr-card" id="collectionForceCard">
                    <div class="beta-collection-corr-header">
                        <span><i class="fa fa-project-diagram"></i> <?= __('Common Attribute Links') ?></span>
                        <span id="forceGraphStatus" class="muted" style="font-size:11px;"><?= __('Loading…') ?></span>
                    </div>
                    <div id="collectionCorrForce" class="beta-collection-corr-graph">
                        <div class="text-center" style="padding:30px;color:#aaa;">
                            <i class="fa fa-spinner fa-spin"></i> <?= __('Fetching correlation data…') ?>
                        </div>
                    </div>
                    <div class="beta-collection-corr-legend">
                        <span><svg width="14" height="14"><circle cx="7" cy="7" r="6" fill="#4e9af1"/></svg> <?= __('Event') ?></span>
                        <span><svg width="14" height="14"><rect x="1" y="1" width="12" height="12" fill="#f0ad4e"/></svg> <?= __('Attribute') ?></span>
                        <span><svg width="24" height="10"><line x1="0" y1="5" x2="24" y2="5" stroke="#999" stroke-width="2"/></svg> <?= __('Link') ?></span>
                        <span><svg width="18" height="14"><rect x="1" y="1" width="16" height="12" fill="rgba(70,130,180,0.18)" stroke="rgba(70,130,180,0.6)" stroke-width="1"/></svg> <?= __('Organisation region') ?></span>
                        <span id="collectionForceOrgLegend"></span>
                    </div>
                </div>
                <?php else: ?>
                <div class="beta-card" style="padding:20px;color:#888;">
                    <i class="fa fa-info-circle"></i> <?= __('Add at least two events to view correlations.') ?>
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
        var rangeLabel = document.getElementById('collectionEventTimelineRange');
        if (!markers) return;

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
        var range = Math.max(1, maxTs - minTs);

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
                    url: baseurl + '/correlations/eventCorrelations/' + ev.id + '.json?extended=1',
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

        var _forceGraphReady = false;
        var _forceGraphRendered = false;

        function isForceTabActive() {
            var tab = document.getElementById('collection-force');
            return tab && tab.classList.contains('active');
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
            _forceGraphReady = true;
            if (isForceTabActive()) {
                renderForceGraph();
                _forceGraphRendered = true;
            }
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

        function renderForceGraph() {
            var container = document.getElementById('collectionCorrForce');
            var statusEl  = document.getElementById('forceGraphStatus');
            var orgLegendEl = document.getElementById('collectionForceOrgLegend');
            if (!container || typeof d3 === 'undefined') {
                if (statusEl) statusEl.textContent = '<?= __('Correlation graph unavailable') ?>';
                return;
            }

            if (container.clientWidth < 100) {
                if (statusEl) statusEl.textContent = '<?= __('Waiting for layout…') ?>';
                return;
            }

            var nodes = [];
            var links = [];
            var nodeIndex = {};

            function addNode(key, data) {
                if (nodeIndex[key] !== undefined) return nodeIndex[key];
                nodes.push(data);
                nodeIndex[key] = nodes.length - 1;
                return nodeIndex[key];
            }

            function getOrgMeta(ev) {
                var orgName = '';
                var orgId = '';
                if (ev) {
                    orgName =
                        (ev.Orgc && ev.Orgc.name) ||
                        ev.orgc_name ||
                        (ev.orgc && ev.orgc.name) ||
                        (ev.Org && ev.Org.name) ||
                        ev.org_name ||
                        '';
                    orgId =
                        (ev.Orgc && ev.Orgc.id) ||
                        ev.orgc_id ||
                        (ev.orgc && ev.orgc.id) ||
                        (ev.Org && ev.Org.id) ||
                        ev.org_id ||
                        '';
                }
                var key = orgId ? ('org|' + orgId) : ('name|' + (orgName || 'unknown').toLowerCase());
                var label = orgName || '<?= __('Unknown organisation') ?>';
                return { key: key, label: label };
            }

            Object.keys(attrMap).forEach(function (attrKey) {
                var record = attrMap[attrKey];
                var eventList = Object.keys(record.events);
                if (eventList.length < 2) return;

                var attrLabel = buildAttrLabel(record.attr);
                var attrIdx = addNode('attr|' + attrKey, {
                    id: attrKey,
                    type: 'attribute',
                    attrType: record.attr ? record.attr.type : '',
                    name: attrLabel.name,
                    fullTitle: attrLabel.full
                });

                eventList.forEach(function (uuid) {
                    var ev = nodeData[uuid];
                    if (!ev) return;
                    var evLabel = buildEventLabel(ev, uuid);
                    var org = getOrgMeta(ev);
                    var evIdx = addNode('event|' + uuid, {
                        id: ev.id,
                        uuid: uuid,
                        type: 'event',
                        name: evLabel.name,
                        fullTitle: evLabel.full,
                        orgKey: org.key,
                        orgLabel: org.label
                    });
                    links.push({ source: evIdx, target: attrIdx, weight: 1 });
                });
            });

            if (nodes.length === 0 || links.length === 0) {
                container.innerHTML = '<div class="text-center" style="padding:30px;color:#aaa;"><i class="fa fa-info-circle"></i> <?= __('No common-attribute links found.') ?></div>';
                if (statusEl) statusEl.textContent = '<?= __('No common attributes between collection events') ?>';
                return;
            }

            if (statusEl) statusEl.textContent = links.length + ' <?= __('links') ?>';
            container.innerHTML = '';

            var bounds = container.getBoundingClientRect();
            var width  = Math.max(240, bounds.width || container.clientWidth || 760);
            var viewportH = window.innerHeight || 800;
            var availableH = Math.max(260, viewportH - bounds.top - 180);
            var height = Math.max(320, Math.min(availableH, Math.max(360, nodes.length * 18)));

            container.style.height = height + 'px';

            var svg = d3.select(container).append('svg')
                .attr('width', width)
                .attr('height', height)
                .attr('class', 'beta-corr-svg');

            var zoomLayer = svg.append('g').attr('class', 'force-zoom-layer');
            svg.style('cursor', 'move');

            var orgLayer = zoomLayer.append('g').attr('class', 'force-org-regions');

            function hashHue(str) {
                var h = 0;
                for (var i = 0; i < str.length; i++) h = (h * 31 + str.charCodeAt(i)) | 0;
                return Math.abs(h) % 360;
            }

            function orgFill(key) {
                var hue = hashHue(key || 'org');
                return 'hsla(' + hue + ', 68%, 54%, 0.20)';
            }

            function orgStroke(key) {
                var hue = hashHue(key || 'org');
                return 'hsla(' + hue + ', 72%, 40%, 0.75)';
            }

            var color = function (d) {
                return d.type === 'attribute' ? '#f0ad4e' : '#4e9af1';
            };

            var degree = {};
            links.forEach(function (l) {
                var s = typeof l.source === 'number' ? l.source : l.source.index;
                var t = typeof l.target === 'number' ? l.target : l.target.index;
                degree[s] = (degree[s] || 0) + 1;
                degree[t] = (degree[t] || 0) + 1;
            });

            function renderOrgLegend() {
                if (!orgLegendEl) return;
                function escapeHtml(str) {
                    return String(str || '').replace(/[&<>"']/g, function (ch) {
                        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch] || ch;
                    });
                }
                var orgEntries = {};
                nodes.forEach(function (n) {
                    if (n.type !== 'event') return;
                    if (!orgEntries[n.orgKey]) {
                        orgEntries[n.orgKey] = {
                            key: n.orgKey,
                            label: n.orgLabel || '<?= __('Unknown organisation') ?>'
                        };
                    }
                });
                var list = Object.keys(orgEntries).map(function (k) { return orgEntries[k]; });
                list.sort(function (a, b) {
                    return (a.label || '').localeCompare(b.label || '');
                });
                orgLegendEl.innerHTML = list.map(function (entry) {
                    var fill = orgFill(entry.key);
                    var stroke = orgStroke(entry.key);
                    return '<span style="display:inline-flex;align-items:center;gap:4px;margin-left:8px;">'
                        + '<svg width="14" height="14" aria-hidden="true"><rect x="1" y="1" width="12" height="12" fill="' + fill + '" stroke="' + stroke + '" stroke-width="1"/></svg>'
                        + '<span>' + escapeHtml(entry.label) + '</span>'
                        + '</span>';
                }).join('');
            }

            function buildCirclePath(cx, cy, r) {
                return 'M' + (cx - r) + ',' + cy
                    + 'a' + r + ',' + r + ' 0 1,0 ' + (2 * r) + ',0'
                    + 'a' + r + ',' + r + ' 0 1,0 ' + (-2 * r) + ',0';
            }

            function nodeEnvelopePoints(n, radius) {
                var points = [[n.x, n.y]];
                var step = Math.PI / 4;
                for (var a = 0; a < Math.PI * 2; a += step) {
                    points.push([n.x + Math.cos(a) * radius, n.y + Math.sin(a) * radius]);
                }
                return points;
            }

            function computeHull(points) {
                if (!points || points.length < 3) return null;
                if (d3.polygonHull) {
                    return d3.polygonHull(points);
                }
                if (d3.geom && typeof d3.geom.hull === 'function') {
                    return d3.geom.hull(points);
                }
                return null;
            }

            function hullPath(points) {
                if (!points || points.length < 3) return null;
                return 'M' + points.map(function (p) { return p[0] + ',' + p[1]; }).join('L') + 'Z';
            }

            function updateOrganisationRegions() {
                var byOrg = {};
                nodes.forEach(function (n) {
                    if (n.type !== 'event') return;
                    var orgKey = n.orgKey || 'org|unknown';
                    if (!byOrg[orgKey]) {
                        byOrg[orgKey] = {
                            key: orgKey,
                            label: n.orgLabel || '<?= __('Unknown organisation') ?>',
                            eventNodes: [],
                            attrNodes: {}
                        };
                    }
                    byOrg[orgKey].eventNodes.push(n);
                });

                links.forEach(function (l) {
                    var src = l.source;
                    var tgt = l.target;
                    var ev = src.type === 'event' ? src : (tgt.type === 'event' ? tgt : null);
                    var attr = src.type === 'attribute' ? src : (tgt.type === 'attribute' ? tgt : null);
                    if (!ev || !attr) return;
                    var orgKey = ev.orgKey || 'org|unknown';
                    if (!byOrg[orgKey]) return;
                    byOrg[orgKey].attrNodes[attr.id || attr.name] = attr;
                });

                var regions = Object.keys(byOrg).map(function (k) {
                    var bucket = byOrg[k];
                    var members = bucket.eventNodes.slice();
                    Object.keys(bucket.attrNodes).forEach(function (attrKey) {
                        members.push(bucket.attrNodes[attrKey]);
                    });
                    members = members.filter(function (n) {
                        return isFinite(n.x) && isFinite(n.y);
                    });
                    if (!members.length) return null;

                    var points = [];
                    members.forEach(function (n) {
                        var radius = n.type === 'event' ? 24 : 16;
                        points = points.concat(nodeEnvelopePoints(n, radius));
                    });

                    var hull = computeHull(points);
                    var path = hullPath(hull);
                    if (!path) {
                        var cx = 0;
                        var cy = 0;
                        members.forEach(function (n) { cx += n.x; cy += n.y; });
                        cx = cx / members.length;
                        cy = cy / members.length;
                        var maxR = 36;
                        members.forEach(function (n) {
                            var dx = n.x - cx;
                            var dy = n.y - cy;
                            maxR = Math.max(maxR, Math.sqrt(dx * dx + dy * dy) + (n.type === 'event' ? 30 : 20));
                        });
                        path = buildCirclePath(cx, cy, maxR);
                    }

                    return {
                        key: bucket.key,
                        label: bucket.label,
                        path: path
                    };
                }).filter(function (r) { return !!r; });

                var regionPaths = orgLayer.selectAll('path').data(regions, function (d) { return d.key; });

                regionPaths.enter()
                    .append('path')
                    .attr('pointer-events', 'none')
                    .attr('fill-opacity', 1)
                    .attr('stroke-width', 1.5);

                regionPaths
                    .attr('d', function (d) { return d.path; })
                    .attr('fill', function (d) { return orgFill(d.key); })
                    .attr('stroke', function (d) { return orgStroke(d.key); });

                regionPaths.exit().remove();
            }

            var link = zoomLayer.append('g')
                .attr('stroke', '#b0c4de')
                .attr('stroke-opacity', 0.4)
                .selectAll('line')
                .data(links)
                .enter().append('line')
                .attr('stroke-width', 1.2);

            var node = zoomLayer.append('g')
                .selectAll('g')
                .data(nodes)
                .enter().append('g')
                .attr('class', 'beta-corr-node')
                .style('cursor', function (d) { return d.type === 'event' ? 'pointer' : 'default'; });

            node.append(function (d) {
                return document.createElementNS('http://www.w3.org/2000/svg', d.type === 'attribute' ? 'rect' : 'circle');
            })
                .attr('r', function (d) { return d.type === 'event' ? 8 : null; })
                .attr('width', function (d) { return d.type === 'attribute' ? 12 : null; })
                .attr('height', function (d) { return d.type === 'attribute' ? 12 : null; })
                .attr('x', function (d) { return d.type === 'attribute' ? -6 : null; })
                .attr('y', function (d) { return d.type === 'attribute' ? -6 : null; })
                .attr('fill', color)
                .attr('stroke', '#fff')
                .attr('stroke-width', 1.5);

            node.append('title')
                .text(function (d) {
                    if (d.type === 'event') {
                        return (d.fullTitle || d.name) + '\n<?= __('Organisation') ?>: ' + (d.orgLabel || '<?= __('Unknown organisation') ?>');
                    }
                    return d.fullTitle || d.name;
                });

            node.on('click', function (d) {
                if (d.type === 'event' && d.id) window.location.href = baseurl + '/events/view/' + d.id;
            });

            var label = zoomLayer.append('g')
                .selectAll('text')
                .data(nodes)
                .enter().append('text')
                .attr('font-size', 9)
                .attr('fill', '#555')
                .attr('dx', 10)
                .attr('dy', '0.35em')
                .style('opacity', function (d) { return d.type === 'event' ? 0.85 : 0; })
                .text(function (d) { return d.name; });

            renderOrgLegend();

            if (d3.zoom) {
                var zoom = d3.zoom().scaleExtent([0.3, 4]).on('zoom', function () {
                    zoomLayer.attr('transform', d3.event.transform);
                });
                svg.call(zoom);
            } else if (d3.behavior && d3.behavior.zoom) {
                var zoomV3 = d3.behavior.zoom().scaleExtent([0.3, 4]).on('zoom', function () {
                    zoomLayer.attr('transform', 'translate(' + d3.event.translate + ') scale(' + d3.event.scale + ')');
                });
                svg.call(zoomV3);
            }

            if (d3.forceSimulation && d3.forceLink) {
                nodes.forEach(function (n, i) {
                    var colX = n.type === 'event' ? width * 0.32 : width * 0.68;
                    var bandCenter = n.type === 'event' ? height * 0.45 : height * 0.55;
                    var bandSpread = Math.max(140, height * 0.6);
                    var jitter = (Math.random() - 0.5) * bandSpread;
                    n.x = n.x || colX + (Math.random() * 40 - 20);
                    n.y = n.y || Math.max(24, Math.min(height - 24, bandCenter + jitter + (i % 9) * 5));
                    n._targetY = bandCenter + jitter;
                });

                var simulation = d3.forceSimulation(nodes)
                    .force('link', d3.forceLink(links)
                        .distance(function (d) {
                            var s = d.source.index;
                            var t = d.target.index;
                            var weight = (degree[s] || 1) + (degree[t] || 1);
                            return Math.max(80, Math.min(140, 70 + weight * 6));
                        })
                        .strength(0.65))
                    .force('charge', d3.forceManyBody().strength(-190))
                    .force('center', d3.forceCenter(width / 2, height / 2))
                    .force('collision', d3.forceCollide(function (d) { return d.type === 'event' ? 18 : 14; }))
                    .force('x', d3.forceX(function (d) { return d.type === 'event' ? width * 0.32 : width * 0.68; }).strength(0.18))
                    .force('y', d3.forceY(function (d) { return d._targetY || (d.type === 'event' ? height * 0.45 : height * 0.55); }).strength(0.12));

                var tickCount = 0;
                simulation.on('tick', function () {
                    link
                        .attr('x1', function (d) { return d.source.x; })
                        .attr('y1', function (d) { return d.source.y; })
                        .attr('x2', function (d) { return d.target.x; })
                        .attr('y2', function (d) { return d.target.y; });

                    node.attr('transform', function (d) {
                        d.x = Math.max(10, Math.min(width - 10, d.x));
                        d.y = Math.max(10, Math.min(height - 10, d.y));
                        return 'translate(' + d.x + ',' + d.y + ')';
                    });

                    label
                        .attr('x', function (d) { return d.x; })
                        .attr('y', function (d) { return d.y; });
                    tickCount++;
                    if (tickCount % 4 === 0) updateOrganisationRegions();
                });
            } else if (d3.layout && d3.layout.force) {
                nodes.forEach(function (n, i) {
                    var colX = n.type === 'event' ? width * 0.32 : width * 0.68;
                    var bandCenter = n.type === 'event' ? height * 0.45 : height * 0.55;
                    var bandSpread = Math.max(140, height * 0.6);
                    var jitter = (Math.random() - 0.5) * bandSpread;
                    n.x = n.x || colX + (Math.random() * 40 - 20);
                    n.y = n.y || Math.max(24, Math.min(height - 24, bandCenter + jitter + (i % 9) * 5));
                    n._targetY = bandCenter + jitter;
                });
                var force = d3.layout.force()
                    .nodes(nodes)
                    .links(links)
                    .size([width, height])
                    .linkDistance(function (d) {
                        var s = d.source.index || 0;
                        var t = d.target.index || 0;
                        var weight = (degree[s] || 1) + (degree[t] || 1);
                        return Math.max(80, Math.min(140, 70 + weight * 6));
                    })
                    .charge(-260)
                    .start();

                var tickCountV3 = 0;
                force.on('tick', function () {
                    nodes.forEach(function (n) {
                        if (n._targetY) n.y += (n._targetY - n.y) * 0.04;
                    });
                    link
                        .attr('x1', function (d) { return d.source.x; })
                        .attr('y1', function (d) { return d.source.y; })
                        .attr('x2', function (d) { return d.target.x; })
                        .attr('y2', function (d) { return d.target.y; });

                    node.attr('transform', function (d) {
                        d.x = Math.max(10, Math.min(width - 10, d.x));
                        d.y = Math.max(10, Math.min(height - 10, d.y));
                        return 'translate(' + d.x + ',' + d.y + ')';
                    });

                    label
                        .attr('x', function (d) { return d.x; })
                        .attr('y', function (d) { return d.y; });
                    tickCountV3++;
                    if (tickCountV3 % 4 === 0) updateOrganisationRegions();
                });
            }

            updateOrganisationRegions();

            function isConnected(a, b) {
                return links.some(function (l) {
                    return (l.source === a && l.target === b) || (l.source === b && l.target === a);
                });
            }

            function highlight(d) {
                link.style('stroke-opacity', function (l) {
                    return (l.source === d || l.target === d) ? 0.7 : 0.05;
                });
                node.style('opacity', function (n) {
                    return (n === d || isConnected(n, d)) ? 1 : 0.2;
                });
                label.style('opacity', function (n) {
                    if (n.type === 'attribute') return (n === d || isConnected(n, d)) ? 0.9 : 0.05;
                    return (n === d || isConnected(n, d)) ? 1 : 0.2;
                });
            }

            function resetHighlight() {
                link.style('stroke-opacity', 0.4);
                node.style('opacity', 1);
                label.style('opacity', function (d) { return d.type === 'event' ? 0.85 : 0; });
            }

            node.on('mouseover', highlight).on('mouseout', resetHighlight);
            label.on('mouseover', highlight).on('mouseout', resetHighlight);
        }

        // Defer force graph render until tab is visible
        $('a[href="#collection-force"]').on('shown.bs.tab', function () {
            if (_forceGraphReady && !_forceGraphRendered) {
                setTimeout(function () {
                    renderForceGraph();
                    _forceGraphRendered = true;
                }, 50);
            }
        });
    }

})();
</script>
