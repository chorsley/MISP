<?php
$facetData = [
    'tags' => [],
    'orgs' => [],
    'tlp' => [],
    'galaxyClusters' => [],
    'dateRanges' => [
        'last_24h' => __('Last 24 hours'),
        'last_week' => __('Last week'),
        'last_month' => __('Last month'),
        'last_year' => __('Last year')
    ]
];

foreach ($events as $event) {
    if (!empty($event['EventTag'])) {
        foreach ($event['EventTag'] as $eventTag) {
            $tagName = $eventTag['Tag']['name'];
            if (!isset($facetData['tags'][$tagName])) {
                $facetData['tags'][$tagName] = [
                    'name' => $tagName,
                    'color' => $eventTag['Tag']['colour'],
                    'count' => 0
                ];
            }
            $facetData['tags'][$tagName]['count']++;
            
            if (strpos($tagName, 'tlp:') === 0) {
                $facetData['tlp'][$tagName] = $facetData['tags'][$tagName];
            }
        }
    }
    
    if (!empty($event['Orgc'])) {
        $orgName = $event['Orgc']['name'];
        if (!isset($facetData['orgs'][$orgName])) {
            $facetData['orgs'][$orgName] = ['name' => $orgName, 'count' => 0];
        }
        $facetData['orgs'][$orgName]['count']++;
    }
    
    if (!empty($event['GalaxyCluster'])) {
        foreach ($event['GalaxyCluster'] as $cluster) {
            $clusterName = $cluster['value'];
            if (!isset($facetData['galaxyClusters'][$clusterName])) {
                $facetData['galaxyClusters'][$clusterName] = [
                    'name' => $clusterName,
                    'galaxy' => $cluster['Galaxy']['name'],
                    'count' => 0
                ];
            }
            $facetData['galaxyClusters'][$clusterName]['count']++;
        }
    }
}

arsort($facetData['tags']);
arsort($facetData['orgs']);
arsort($facetData['galaxyClusters']);
?>

<div class="event-facet-panel" id="eventFacetPanel">
    <div class="facet-panel-header">
        <h4><?= __('Filters') ?></h4>
        <button class="btn btn-mini" onclick="clearAllFacets()" title="<?= __('Clear all active filters') ?>"><?= __('Clear All') ?></button>
    </div>
    
    <?php if (!empty($facetData['tlp'])): ?>
    <div class="facet-group">
        <h5 class="facet-title" onclick="toggleFacetGroup(this)" tabindex="0" role="button" aria-expanded="true">
            <i class="fa fa-chevron-down"></i> <?= __('TLP Levels') ?>
        </h5>
        <div class="facet-items">
            <?php foreach ($facetData['tlp'] as $tlpTag): ?>
            <div class="facet-item" onclick="applyTagFacet('<?= h($tlpTag['name']) ?>')" tabindex="0" role="button" aria-label="Filter by <?= h($tlpTag['name']) ?>">
                <span class="tag" style="background-color: <?= h($tlpTag['color']) ?>; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px;">
                    <?= h($tlpTag['name']) ?>
                </span>
                <span class="facet-count">(<?= $tlpTag['count'] ?>)</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($facetData['orgs'])): ?>
    <div class="facet-group">
        <h5 class="facet-title" onclick="toggleFacetGroup(this)" tabindex="0" role="button" aria-expanded="true">
            <i class="fa fa-chevron-down"></i> <?= __('Organizations') ?>
        </h5>
        <div class="facet-items">
            <?php foreach (array_slice($facetData['orgs'], 0, 10, true) as $org): ?>
            <div class="facet-item" onclick="applyOrgFacet('<?= h($org['name']) ?>')" tabindex="0" role="button" aria-label="Filter by organization <?= h($org['name']) ?>">
                <span class="facet-name"><?= h($org['name']) ?></span>
                <span class="facet-count">(<?= $org['count'] ?>)</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($facetData['galaxyClusters'])): ?>
    <div class="facet-group">
        <h5 class="facet-title" onclick="toggleFacetGroup(this)" tabindex="0" role="button" aria-expanded="true">
            <i class="fa fa-chevron-down"></i> <?= __('Galaxy Clusters') ?>
        </h5>
        <div class="facet-items">
            <?php foreach (array_slice($facetData['galaxyClusters'], 0, 10, true) as $cluster): ?>
            <div class="facet-item" onclick="applyClusterFacet('<?= h($cluster['name']) ?>')" tabindex="0" role="button" aria-label="Filter by galaxy cluster <?= h($cluster['name']) ?>">
                <span class="facet-name"><?= h($cluster['name']) ?></span>
                <span class="facet-count">(<?= $cluster['count'] ?>)</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="facet-group">
        <h5 class="facet-title" onclick="toggleFacetGroup(this)" tabindex="0" role="button" aria-expanded="true">
            <i class="fa fa-chevron-down"></i> <?= __('Date Ranges') ?>
        </h5>
        <div class="facet-items">
            <?php foreach ($facetData['dateRanges'] as $key => $label): ?>
            <div class="facet-item" onclick="applyDateFacet('<?= h($key) ?>')" tabindex="0" role="button" aria-label="Filter by <?= h($label) ?>">
                <span class="facet-name"><?= h($label) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php if (!empty($facetData['tags']) && count($facetData['tags']) > count($facetData['tlp'])): ?>
    <div class="facet-group">
        <h5 class="facet-title" onclick="toggleFacetGroup(this)" tabindex="0" role="button" aria-expanded="false">
            <i class="fa fa-chevron-right"></i> <?= __('All Tags') ?>
        </h5>
        <div class="facet-items" style="display: none;">
            <?php foreach (array_slice($facetData['tags'], 0, 20, true) as $tag): ?>
            <?php if (strpos($tag['name'], 'tlp:') !== 0): ?>
            <div class="facet-item" onclick="applyTagFacet('<?= h($tag['name']) ?>')" tabindex="0" role="button" aria-label="Filter by tag <?= h($tag['name']) ?>">
                <span class="tag" style="background-color: <?= h($tag['color']) ?>; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px;">
                    <?= h($tag['name']) ?>
                </span>
                <span class="facet-count">(<?= $tag['count'] ?>)</span>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
