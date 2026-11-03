<?php
$paginatorUrl = [
    'controller' => 'events',
    'action'     => 'viewEventReports',
    $event['Event']['id'],
];
if (!empty($extended)) {
    $paginatorUrl['extended'] = 1;
}
if (!empty($extending)) {
    $paginatorUrl['extending'] = 1;
}
$this->Paginator->options(['url' => $paginatorUrl]);
?>

<?php
$tabActions = [];
if (!empty($isSiteAdmin) || $this->Acl->canModifyEvent($event)) {
    $addUrl = $baseurl . '/event_reports/add/' . $event['Event']['id'];
    $tabActions[] = [
        'url'     => $addUrl,
        'label'   => __('Add report'),
        'icon'    => 'fas fa-plus',
        'onclick' => "event.preventDefault(); openModal('" . $addUrl . "');",
    ];
}

echo $this->element('EventReports/index', [
    'reports' => $reports,
    'eventView' => true,
    'tabActions' => $tabActions,
]); ?>
