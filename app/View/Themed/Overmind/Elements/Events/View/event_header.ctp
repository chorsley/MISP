<?php
/**
 * Overmind event header.
 *
 * The event's identity, the essentials an analyst scans before opening a tab,
 * and the few actions that act on the event itself. Everything secondary —
 * ids, ownership, sharing details, timestamps, state and the modification
 * history — sits behind the Details disclosure.
 *
 * Read from the view: $baseurl, $analysisLevels, $orgGrading, $contributors,
 * $overviewMarkings, $sharingGroupOrgCount, $extended, $extending.
 *
 * Expects: $data — the event array; $eventActions — the action registry built
 * by view2 (the header draws its status, its primary action and its palette
 * trigger from it).
 */

App::uses('CakeTime', 'Utility');

$event = $data['Event'] ?? [];
$orgc = $data['Orgc'] ?? [];
$org = $data['Org'] ?? [];
$sg = $data['SharingGroup'] ?? [];

$eventId = (int)($event['id'] ?? 0);
$eventUuid = (string)($event['uuid'] ?? '');

$isPublished = !empty($event['published']);
$lastPublished = (int)($event['publish_timestamp'] ?? 0);
$lastModified = (int)($event['timestamp'] ?? 0);
$firstPublished = (int)($event['first_publication'] ?? 0);
$hasPending = $isPublished && $lastPublished > 0 && $lastModified > $lastPublished;

$distribution = (int)($event['distribution'] ?? 0);
$distMeta = $this->DistributionLevel->get($distribution);
$distStyle = sprintf(
    '--eh-tone: %s; --eh-tone-bg: %s; --eh-tone-border: %s;',
    h($this->DistributionLevel->themed($distMeta, 'fg')),
    h($this->DistributionLevel->themed($distMeta, 'bg')),
    h($this->DistributionLevel->themed($distMeta, 'border', '20'))
);

$threatLevelId = (int)($event['threat_level_id'] ?? 4);
$threatNames = [
    1 => __('High'),
    2 => __('Medium'),
    3 => __('Low'),
    4 => __('Undefined'),
];
$threatName = $data['ThreatLevel']['name'] ?? ($threatNames[$threatLevelId] ?? __('Undefined'));
$threatTone = [
    1 => 'is-high',
    2 => 'is-medium',
    3 => 'is-low',
    4 => 'is-undefined',
][$threatLevelId] ?? 'is-undefined';

$analysisValue = (int)($event['analysis'] ?? 0);
$analysisNames = [
    0 => __('Initial'),
    1 => __('Ongoing'),
    2 => __('Completed'),
];
$analysisName = ($analysisLevels ?? [])[$analysisValue] ?? ($analysisNames[$analysisValue] ?? __('Unknown'));
$analysisTone = [
    0 => 'is-initial',
    1 => 'is-ongoing',
    2 => 'is-completed',
][$analysisValue] ?? 'is-initial';

if (!$isPublished) {
    $publicationLabel = __('Unpublished');
} elseif ($hasPending) {
    $publicationLabel = __('Changes pending');
} else {
    $publicationLabel = __('Published');
}

/*
 * The event states render as compact meters: one segment per possible value,
 * every segment labelled, the active one carried in its tone colour so the
 * current state reads at a glance. Publication is not one of them any more —
 * the Published switch in the header carries it.
 */
$threatStates = [
    ['id' => 3, 'label' => __('Low'), 'tone' => 'is-yellow'],
    ['id' => 2, 'label' => __('Medium'), 'tone' => 'is-orange'],
    ['id' => 1, 'label' => __('High'), 'tone' => 'is-red'],
    ['id' => 4, 'label' => __('Unknown'), 'tone' => 'is-neutral'],
];
$analysisStates = [
    ['id' => 0, 'label' => __('Initial'), 'tone' => 'is-blue'],
    ['id' => 1, 'label' => __('Ongoing'), 'tone' => 'is-orange'],
    ['id' => 2, 'label' => __('Completed'), 'tone' => 'is-green'],
];
$meter = function ($fieldLabel, array $states, $active, $activeLabel = null, $activeTone = null) {
    $segs = '';
    $resolved = $activeLabel;
    foreach ($states as $state) {
        $on = $state['id'] === $active;
        $label = $state['label'];
        if ($on) {
            if ($resolved === null) {
                $resolved = $label;
            } else {
                $label = $resolved;
            }
        }
        $segs .= sprintf(
            '<span class="eh-seg%s">%s</span>',
            $on ? ' is-on ' . h($activeTone ?? $state['tone']) : '',
            h((string)$label)
        );
    }
    $title = $fieldLabel . ': ' . (string)$resolved;
    return sprintf(
        '<div class="eh-meter" title="%s">'
        . '<span class="eh-meter-label">%s</span>'
        . '<span class="eh-meter-track" role="img" aria-label="%s">%s</span>'
        . '</div>',
        h($title),
        h($fieldLabel),
        h($title),
        $segs
    );
};

$time = function ($timestamp, $fallback = null) {
    $timestamp = (int)$timestamp;
    if ($timestamp <= 0) {
        return '<span class="eh-muted">' . h($fallback ?? __('—')) . '</span>';
    }
    $relative = CakeTime::timeAgoInWords($timestamp, [
        'end' => '+10 years',
        'accuracy' => ['day' => 'day', 'hour' => 'hour', 'minute' => 'minute'],
    ]);
    return sprintf(
        '<time datetime="%s" title="%s">%s</time>',
        h(date('c', $timestamp)),
        h(date('Y-m-d H:i:s', $timestamp)),
        h($relative)
    );
};

/* A timestamp: humanised, with the full value copied on click (and shown on
   hover). The caller's label heads the cell and names the value for the tooltip. */
$stamp = function ($timestamp, $label, $icon) {
    $timestamp = (int)$timestamp;
    if ($timestamp <= 0) {
        return '<span class="eh-muted">' . __('—') . '</span>';
    }
    $relative = CakeTime::timeAgoInWords($timestamp, [
        'end' => '+10 years',
        'accuracy' => ['day' => 'day', 'hour' => 'hour', 'minute' => 'minute'],
    ]);
    $absolute = date('Y-m-d H:i:s', $timestamp);
    return sprintf(
        '<button type="button" class="eh-stamp"'
        . ' title="%s" aria-label="%s"'
        . ' data-copy-value="%s" data-copy-msg="%s"'
        . ' onclick="copyValueToClipboard(this.dataset.copyValue, this.dataset.copyMsg)">'
        . '<i class="fas %s" aria-hidden="true"></i>'
        . '<time datetime="%s">%s</time></button>',
        h($label . ' ' . $absolute . ' — ' . __('click to copy')),
        h($label . ' ' . $absolute),
        h($absolute),
        h(__('Timestamp copied to clipboard')),
        h($icon),
        h(date('c', $timestamp)),
        h($relative)
    );
};

$logo = $this->OrgImg->getOrgLogoV2($orgc, 28);
$ownerDiffers = !empty($org['id']) && (int)$org['id'] !== (int)($orgc['id'] ?? 0);

$markings = $overviewMarkings ?? ['declared' => [], 'present' => [], 'absent' => []];
$hasMarkings = !empty($markings['declared']);

$modeUrl = function ($extendedOn, $extendingOn) use ($baseurl, $eventId) {
    return $baseurl . '/events/view/' . $eventId
        . ($extendedOn ? '/extended:1' : '')
        . ($extendingOn ? '/extending:1' : '');
};

$extLink = function (array $related) use ($baseurl) {
    $orgName = $related['Orgc']['name'] ?? '';
    return sprintf(
        '<a class="eh-ext-link" href="%s" title="%s">'
        . '<span class="eh-ext-id">#%d</span>'
        . '<span class="eh-ext-info">%s</span>%s</a>',
        h($baseurl . '/events/view/' . (int)$related['id']),
        h($orgName === '' ? $related['info'] : $orgName . ' — ' . $related['info']),
        (int)$related['id'],
        h($related['info']),
        $orgName === '' ? '' : '<span class="eh-ext-org">' . h($orgName) . '</span>'
    );
};

$extToggle = function ($on, $url, $onLabel, $offLabel) {
    return sprintf(
        '<a class="btn btn-sm %s eh-ext-toggle" href="%s" data-eo-keep-tab>'
        . '<i class="fas %s me-1"></i>%s</a>',
        $on ? 'btn-outline-primary' : 'btn-outline-secondary',
        h($url),
        $on ? 'fa-toggle-on' : 'fa-toggle-off',
        h($on ? $onLabel : $offLabel)
    );
};

$children = $event['ExtendedBy'] ?? [];
$parent = (!empty($event['Extends']) && is_array($event['Extends']))
    ? $event['Extends']
    : null;
$hasExtensions = !empty($children) || $parent !== null || !empty($event['Extends']);

$creatorUser = $data['User']['email'] ?? '';
$ownership = $ownership ?? [];
?>

<div class="container-fluid">
    <header class="eh">
        <div class="eh-main">
            <div class="eh-identity" data-tour="page-title">
                <h1 class="eh-title"><?= h($event['info'] ?? '') ?></h1>
            </div>
        </div>

        <div class="eh-strip">
            <div class="eh-strip-main">
                <?php if (!empty($ownership)): ?>
                    <div class="eh-ownership">
                        <?= $this->element('genericElementsBS5/Cards/ownership_card', $ownership) ?>
                    </div>
                <?php endif; ?>

                <div class="eh-meta">
                    <div class="eh-cell">
                        <span class="eh-cell-label"><?= __('Event date') ?></span>
                        <span class="eh-cell-value">
                            <i class="fas fa-calendar-day" aria-hidden="true"></i>
                            <?= !empty($event['date']) ? h($event['date']) : '<span class="eh-muted">' . __('—') . '</span>' ?>
                        </span>
                    </div>
                    <div class="eh-cell <?= $hasPending ? 'is-pending' : ($isPublished ? 'is-published' : '') ?>">
                        <span class="eh-cell-label"><?= __('Published') ?></span>
                        <span class="eh-cell-value"><?= $stamp($lastPublished, __('Published'), 'fa-paper-plane') ?></span>
                    </div>
                    <div class="eh-cell">
                        <span class="eh-cell-label"><?= __('Last mod') ?></span>
                        <span class="eh-cell-value"><?= $stamp($lastModified, __('Last mod'), 'fa-pen') ?></span>
                    </div>
                </div>
            </div>

            <div class="eh-states">
                <span class="eh-meters" data-tour="event-general">
                    <?= $meter(__('Threat'), $threatStates, $threatLevelId) ?>
                    <?= $meter(__('Analysis'), $analysisStates, $analysisValue) ?>
                </span>

                <div class="eh-actions" data-tour="page-actions">
                    <?php $primary = $eventActions['primary'] ?? null; ?>
                    <?php if (!empty($primary)): ?>
                        <button type="button"
                                class="eh-switch<?= $isPublished ? ' is-on' : ' is-off' ?><?= $hasPending ? ' is-pending' : '' ?>"
                                role="switch"
                                aria-checked="<?= $isPublished ? 'true' : 'false' ?>"
                                <?= !empty($primary['onclick']) ? 'onclick="' . h($primary['onclick']) . '"' : '' ?>
                                title="<?= h($hasPending
                                    ? __('Published; changes made since are not published yet — click to publish them')
                                    : $primary['label'])
                                ?>">
                            <span class="eh-switch-track" aria-hidden="true"><span class="eh-switch-knob"></span></span>
                            <span class="eh-switch-text"><?= $isPublished ? __('Published') : __('Unpublished') ?></span>
                        </button>
                    <?php endif; ?>

                    <button type="button"
                            class="btn btn-sm btn-outline-secondary eh-actions-trigger"
                            data-event-actions-open
                            data-tour="quick-actions"
                            aria-haspopup="dialog"
                            title="<?= h(__('All actions')) ?>">
                        <i class="fas fa-bolt" aria-hidden="true"></i>
                        <span><?= __('Actions') ?></span>
                        <kbd class="eh-actions-kbd" aria-hidden="true" data-ea-kbd>⌘K</kbd>
                    </button>
                </div>
            </div>
        </div>

        <div class="eh-details">
            <div class="collapse" id="eh-details">
                <div class="eh-details-body">
                    <div class="eh-details-grid">
                        <section class="eh-details-section">
                            <h2 class="eh-details-label"><?= __('Identity') ?></h2>
                            <div class="mb-2">
                                <?= $this->element('genericElementsBS5/Badges/identity_pill', [
                                    'id' => $eventId,
                                    'uuid' => $eventUuid,
                                ]) ?>
                            </div>
                            <dl class="eh-dl">
                                <dt><?= __('Creator') ?></dt>
                                <dd>
                                    <a class="eh-inline-link" href="<?= h($baseurl . '/organisations/view/' . ($orgc['id'] ?? '')) ?>">
                                        <?= h($orgc['name'] ?? '') ?>
                                    </a>
                                </dd>
                                <?php if ($ownerDiffers): ?>
                                    <dt><?= __('Owner') ?></dt>
                                    <dd>
                                        <a class="eh-inline-link" href="<?= h($baseurl . '/organisations/view/' . (int)$org['id']) ?>">
                                            <?= h($org['name'] ?? '') ?>
                                        </a>
                                    </dd>
                                <?php endif; ?>
                                <dt><?= __('User') ?></dt>
                                <dd><?= $creatorUser !== '' ? h($creatorUser) : '<span class="eh-muted">' . __('Unknown') . '</span>' ?></dd>
                            </dl>
                        </section>

                        <section class="eh-details-section">
                            <h2 class="eh-details-label"><?= __('Timeline') ?></h2>
                            <dl class="eh-dl">
                                <dt><?= __('Event date') ?></dt>
                                <dd><?= !empty($event['date']) ? h($event['date']) : '<span class="eh-muted">' . __('—') . '</span>' ?></dd>
                                <dt><?= __('Last modified') ?></dt>
                                <dd><?= $time($lastModified) ?></dd>
                                <dt><?= __('First published') ?></dt>
                                <dd><?= $time($firstPublished, __('Never')) ?></dd>
                                <dt><?= __('Last published') ?></dt>
                                <dd><?= $time($lastPublished, __('Never')) ?></dd>
                            </dl>
                        </section>

                        <section class="eh-details-section">
                            <h2 class="eh-details-label"><?= __('State') ?></h2>
                            <dl class="eh-dl">
                                <dt><?= __('Protection') ?></dt>
                                <dd>
                                    <?php if (!empty($event['protected'])): ?>
                                        <span class="eh-value is-warning"><i class="fas fa-shield-alt" aria-hidden="true"></i><?= __('Protected') ?></span>
                                    <?php else: ?>
                                        <span class="eh-muted"><?= __('Unprotected') ?></span>
                                    <?php endif; ?>
                                </dd>
                                <dt><?= __('Correlation') ?></dt>
                                <dd>
                                    <?php if (!empty($event['disable_correlation'])): ?>
                                        <span class="eh-value is-danger"><i class="fas fa-unlink" aria-hidden="true"></i><?= __('Disabled') ?></span>
                                    <?php else: ?>
                                        <span class="eh-value"><i class="fas fa-link" aria-hidden="true"></i><?= __('Enabled') ?></span>
                                    <?php endif; ?>
                                </dd>
                                <dt><?= __('Publication') ?></dt>
                                <dd><?= h($publicationLabel) ?></dd>
                            </dl>
                        </section>
                    </div>

                    <?php if ($hasExtensions): ?>
                        <section class="eh-details-section eh-details-extensions">
                            <h2 class="eh-details-label"><?= __('Related events') ?></h2>
                            <div class="eh-ext-list">
                                <?php if (!empty($children)): ?>
                                    <div class="eh-ext-row">
                                        <span class="eh-ext-kind"><?= __('Extended by') ?></span>
                                        <span class="eh-ext-links">
                                            <?php foreach ($children as $child): ?>
                                                <?= $extLink($child) ?>
                                            <?php endforeach; ?>
                                        </span>
                                        <?= $extToggle(
                                            !empty($extended),
                                            $modeUrl(!$extended, !empty($extending)),
                                            __('Stop merging'),
                                            __('Merge these events')
                                        ) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($parent !== null): ?>
                                    <div class="eh-ext-row">
                                        <span class="eh-ext-kind"><?= __('Extends') ?></span>
                                        <span class="eh-ext-links"><?= $extLink($parent) ?></span>
                                        <?= $extToggle(
                                            !empty($extending),
                                            $modeUrl(!empty($extended), !$extending),
                                            __('Stop merging'),
                                            __('Merge that event')
                                        ) ?>
                                    </div>
                                <?php elseif (!empty($event['Extends'])): ?>
                                    <div class="eh-ext-row">
                                        <span class="eh-ext-kind"><?= __('Extends') ?></span>
                                        <span class="eh-muted"><?= __('A related event you cannot view.') ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <section class="eh-details-activity">
                        <h2 class="eh-details-label"><?= __('Activity') ?></h2>
                        <div class="eo-details-activity" data-eo-fragment-lazy="<?= h($baseurl . '/events/viewEventActivity/' . $eventId) ?>">
                            <div class="text-center text-muted py-3">
                                <div class="misp-loader misp-loader-sm" role="status"></div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <button type="button"
                    class="eh-details-toggle collapsed"
                    data-bs-toggle="collapse"
                    data-bs-target="#eh-details"
                    aria-expanded="false"
                    aria-controls="eh-details">
                <span><?= __('Details') ?></span>
                <i class="fas fa-chevron-down eh-chevron" aria-hidden="true"></i>
            </button>
        </div>
    </header>
</div>
