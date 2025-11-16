<?php
/**
 * Beta version of Events/index view
 * 
 * Changes from standard version:
 * - No left sidebar navigation
 * - Added "Create Event" button with dropdown for "Create event from import"
 * - Added "Event Collections" link
 * 
 * @since 2.5.x (beta)
 */
?>
<div class="events <?php if (!$ajax) echo 'index'; ?> beta-events-index">
    <div class="beta-events-header-row">
        <h2><?php echo __('Events');?></h2>
        <div class="btn-group beta-create-event-group">
            <a href="<?= $baseurl ?>/events/add" class="btn btn-primary">
                <i class="fa fa-plus"></i> <?= __('Create Event') ?>
            </a>
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="caret"></span>
                <span class="sr-only"><?= __('Toggle Dropdown') ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <li><a href="<?= $baseurl ?>/events/add_misp_export"><i class="fa fa-file-import"></i> <?= __('Create event from import') ?></a></li>
            </ul>
        </div>
    </div>
    <div class="beta-events-filter-row">
        <a href="<?= $baseurl ?>/collections/index" class="btn btn-default beta-filter-button">
            <i class="fa fa-folder-open"></i> <?= __('Event Collections') ?>
        </a>
        <button class="btn btn-default beta-filter-button searchFilterButton" title="<?= __('My events only') ?>" data-searchemail="<?= h($me['email']) ?>">
            <?= __('My Events') ?>
        </button>
        <button class="btn btn-default beta-filter-button searchFilterButton" title="<?= __('My organisation\'s events only') ?>" data-searchorg="<?= h($me['org_id']) ?>">
            <?= __('Org Events') ?>
        </button>
    </div>
    <?php
        $searchScopes = [
            'searcheventinfo' => __('Event info'),
            'searchall' => __('All fields'),
            'searcheventid' => __('ID / UUID'),
            'searchtags' => __('Tag'),
        ];
        $searchKey = 'searcheventinfo';

        $filterParamsString = [];
        foreach ($passedArgsArray as $k => $v) {
            if (isset($searchScopes["search$k"])) {
                $searchKey = "search$k";
            }

            $filterParamsString[] = sprintf(
                '%s: %s',
                h(ucfirst($k)),
                h(is_array($v) ? http_build_query($v) : $v)
            );
        }
        $filterParamsString = implode(' & ', $filterParamsString);

        $columnsDescription = [
            'owner_org' => __('Owner org'),
            'is_extension' => __('Extended event'),
            'attribute_count' => __('Attribute count'),
            'creator_user' => __('Creator user'),
            'tags' => __('Tags'),
            'clusters' => __('Clusters'),
            'correlations' => __('Correlations'),
            'sightings' => __('Sightings'),
            'proposals' => __('Proposals'),
            'discussion' => __('Posts'),
            'report_count' => __('Report count'),
            'timestamp' => __('Last modified at'),
            'publish_timestamp' => __('Published at')
        ];

        $columnsMenu = [];
        foreach ($possibleColumns as $possibleColumn) {
            $html = in_array($possibleColumn, $columns, true) ? '<i class="fa fa-check"></i> ' : '<i class="fa fa-check" style="visibility: hidden"></i> ';
            $html .= $columnsDescription[$possibleColumn];
            $columnsMenu[] = [
                'html' => $html,
                'onClick' => 'eventIndexColumnsToggle',
                'onClickParams' => [$possibleColumn],
            ];
        }
    ?>
    <div class="beta-search-controls">
        <div class="beta-search-input-group">
            <select id="quickFilterScopeSelector" class="form-control beta-search-scope">
                <?php foreach ($searchScopes as $key => $value): ?>
                    <option value="<?= h($key) ?>" <?= $searchKey === $key ? 'selected' : '' ?>><?= h($value) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" id="quickFilterField" class="form-control beta-search-input" placeholder="<?= __('Enter value to search') ?>" data-searchkey="<?= h($searchKey) ?>">
            <button id="quickFilterButton" class="btn btn-primary beta-search-button"><?= __('Filter') ?></button>
            <button class="btn btn-default beta-advanced-filter-button" onclick="getPopup('<?= h($urlparams) ?>', 'events', 'filterEventIndex')">
                <i class="fa fa-search"></i> <?= __('Advanced Filter...') ?>
            </button>
        </div>
        <div class="beta-toolbar-actions">
            <button id="multi-delete-button" class="btn btn-default hidden mass-delete" onclick="multiSelectDeleteEvents()" title="<?= __('Delete selected events') ?>">
                <i class="fa fa-trash"></i>
            </button>
            <button id="multi-export-button" class="btn btn-default hidden mass-export" onclick="multiSelectExportEvents()" title="<?= __('Export selected events') ?>">
                <i class="fa fa-file-export"></i>
            </button>
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="<?= __('Choose columns to show') ?>">
                    <i class="fa fa-columns"></i> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right beta-columns-menu">
                    <?php foreach ($possibleColumns as $possibleColumn): ?>
                        <li>
                            <a href="#" onclick="eventIndexColumnsToggle('<?= h($possibleColumn) ?>'); return false;">
                                <i class="fa fa-check" style="<?= in_array($possibleColumn, $columns, true) ? '' : 'visibility: hidden' ?>"></i>
                                <?= h($columnsDescription[$possibleColumn]) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php if (count($passedArgsArray) > 0): ?>
        <div class="beta-active-filters">
            <span class="bold"><?= __('Filters') ?>:</span> <?= h($filterParamsString) ?>
            <a href="<?= $baseurl ?>/events/index" class="btn btn-xs btn-default" title="<?= __('Remove filters') ?>">
                <i class="fa fa-times"></i> <?= __('Clear') ?>
            </a>
        </div>
    <?php endif; ?>
    <?php
        App::uses('BetaUiHelper', 'Lib/Tools');
        $elementPath = BetaUiHelper::getElementPath(!empty($uiBetaEnabled) ? $uiBetaEnabled : false, 'Events/eventIndexTable');
        echo $this->element($elementPath);
    ?>
    <p>
    <?php
    echo $this->Paginator->counter(array(
    'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
    ));
    ?>
    </p>
    <div class="pagination">
        <ul>
        <?= $pagination ?>
        </ul>
    </div>
</div>
<script>
    var passedArgsArray = <?php echo $passedArgs; ?>;
    $(function() {
        $('.searchFilterButton').click(function() {
            runIndexFilter(this);
        });
        $('#quickFilterScopeSelector').change(function() {
            $('#quickFilterField').data('searchkey', this.value)
        });
        $('#quickFilterButton').click(function() {
            runIndexQuickFilter();
        });
    });
</script>
<?php
echo $this->element('genericElements/assetLoader', [
    'css' => ['vis', 'distribution-graph'],
    'js' => ['vis', 'jquery-ui.min', 'network-distribution-graph'],
]);
// Beta version: No sidebar navigation
?>
