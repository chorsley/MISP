<?php
    // The event header is drawn by Events/View/event_header below.
    $this->set('hideHeaderSection', true);

    $viewJs = ['markdown-it', 'font-awesome-helper', 'misp-report-markdown', 'event-report-reader', 'Chart.min',
        'event-overview', 'misp-matrix', 'misp-brush', 'misp-timeline', 'event-seen-timeline', 'tile-drawer',
        'event-object-views', 'objects-index'];
    $viewCss = ['event-overview', 'event-header', 'misp-matrix', 'misp-brush', 'misp-timeline', 'value-widgets',
        'tile-drawer', 'event-object-views', 'objects-index', 'attributes-index', 'event-reports-index',
        'event-correlations'];
    if (Configure::read('Plugin.Geolocation_enabled')) {
        $viewJs = array_merge($viewJs, ['leaflet', 'object-map']);
        $viewCss[] = 'leaflet';
    }
    echo $this->element('genericElements/assetLoader', [
        'js'  => $viewJs,
        'css' => $viewCss,
    ]);

    $extensionSuffix = $extensionSuffix ?? '';

    // What is shared more narrowly than the event: one chip per level and
    // kind, each opening its tab filtered to that level.
    $outliers = [];
    foreach (['Attribute' => 'attributes', 'Object' => 'objects'] as $outlierModel => $outlierTab) {
        foreach ($distributionOutliers[$outlierModel] ?? [] as $outlierLevel => $outlierCount) {
            $outliers[] = [
                'level' => $outlierLevel,
                'text' => $outlierModel === 'Attribute'
                    ? __n('%s attribute', '%s attributes', $outlierCount, $outlierCount)
                    : __n('%s object', '%s objects', $outlierCount, $outlierCount),
                'url' => sprintf(
                    '%s/events/view/%d%s/distribution:%d#open-tab-%s',
                    $baseurl,
                    $event['Event']['id'],
                    $extensionSuffix,
                    $outlierLevel,
                    $outlierTab
                ),
            ];
        }
    }
    usort($outliers, function ($a, $b) {
        return $a['level'] - $b['level'];
    });

    // Who made the event, who holds it and how far it travels is drawn inside
    // the header now, so the layout's ownership card is not asked for.
    $ownership = [
        'orgc' => $event['Orgc'] ?? [],
        'org' => $event['Org'] ?? [],
        'user' => $event['User'] ?? [],
        'contributors' => $contributors ?? [],
        'distribution' => $event['Event']['distribution'] ?? null,
        'sharingGroup' => ($event['SharingGroup'] ?? [])
            + (isset($sharingGroupOrgCount) ? ['org_count' => $sharingGroupOrgCount] : []),
        'markings' => ($overviewMarkings ?? []) + ($this->Acl->canModifyTag($event)
            ? ['editUrl' => $baseurl . '/events/editEventTags/' . $event['Event']['id']]
            : []),
        'outliers' => $outliers,
    ];

    echo $this->element('Events/View/event_header', [
        'data' => $event,
        'ownership' => $ownership,
    ]);

    // Extended / extending view: say so, and carry the mode into every lazy
    // tab so a tab load never drops back to the atomic view.
    echo $this->element('Events/View/extension_banner');

    $searchFor = $this->request->params['named']['searchFor'] ?? '';
    $distributionFilter = $this->request->params['named']['distribution'] ?? '';
    $listSuffix = $extensionSuffix
        . ($searchFor !== '' ? '/searchFor:' . rawurlencode($searchFor) : '')
        . ($distributionFilter !== '' ? '/distribution:' . (int)$distributionFilter : '');
    $objectFilter = (int)($this->request->params['named']['object'] ?? 0);
    $objectsSuffix = $listSuffix . ($objectFilter > 0 ? '/object:' . $objectFilter : '');
    $focusTab = $focusTab ?? null;
    $focusSuffix = $focusTab ? '/focus:' . rawurlencode($this->request->params['named']['focus']) : '';

    echo $this->element('genericElementsBS5/Layout/view_layout',
    [
        'data' => $event,
        'report' => $event['EventReport'] ?? null,
        'tabs' => [
            [
                'id' => 'general',
                'title' => __('General'),
                'icon' => 'fas fa-info-circle',

                // Content
                'left' => [
                    'Events/View/overview_story',
                    'Events/View/overview_facts',
                    'Events/View/overview_annex',
                ],
                'right' => [
                    'Events/View/event_warnings',
                    'Events/View/event_actions',
                    'Events/View/event_matrix',
                    'Events/View/event_warninglists',
                    'Events/View/event_sightings',
                    'Events/View/event_related',
                    'Events/View/event_collections',
                    'Events/View/event_graphs',
                ]
            ],
            [
                'id' => 'objects',
                'title' => __('Objects'),
                'icon' => 'misp-icon misp-icon-object misp-simple',
                'count' => $object_count ?? 0,
                'active' => $focusTab === 'objects',

                // Content
                'left' => [
                    [
                        'ajax' => sprintf('/events/viewObjects/%s%s', h($event['Event']['id']),
                            $objectsSuffix . ($focusTab === 'objects' ? $focusSuffix : ''))
                    ]
                ],
            ],
            [
                'id' => 'attributes',
                'title' => __('Attributes'),
                'icon' => 'misp-icon misp-icon-attribute misp-simple',
                'count' => $attribute_count ?? 0,
                'active' => $focusTab === 'attributes',

                // Content
                'left' => [
                    [
                        'ajax' => sprintf('/events/viewAttributes/%s%s', h($event['Event']['id']),
                            $listSuffix . ($focusTab === 'attributes' ? $focusSuffix : ''))
                    ]
                ],
            ],
            [
                'id' => 'reports',
                'title' => __('Reports'),
                'icon' => 'misp-icon misp-icon-report misp-simple',
                'count' => $report_count ?? 0,

                // Content
                'left' => [
                    [
                        'ajax' => sprintf('/events/viewEventReports/%s%s', h($event['Event']['id']), $extensionSuffix)
                    ]
                ],
            ],
            [
                'id' => 'correlation',
                'title' => __('Correlation'),
                'icon' => 'fas fa-link',
                'iconColor' => 'var(--bs-correlation)',
                'count' => $correlation_count ?? 0,

                // Content
                'left' => [
                    'Events/View/event_correlation_graph',
                ],
            ],
            [
                'id' => 'pivot-explorer',
                'title' => __('Pivot Explorer'),
                'icon' => 'fas fa-circle-nodes',

                // Content
                'left' => [
                    'Events/View/event_pivot_explorer',
                ],
            ],
            [
                'id' => 'timeline',
                'title' => __('Timeline'),
                'icon' => 'fas fa-clock',

                // Content
                'left' => [
                    'Events/View/event_timeline',
                ],
            ],
            [
                'id' => 'history',
                'title' => __('History'),
                'icon' => 'fas fa-history',

                // Content
                'left' => [
                    [
                        'ajax' => sprintf('/audit_logs/eventHistory/%s', h($event['Event']['id']))
                    ]
                ]
            ]
        ]
    ]);

    echo $this->element('Events/View/report_reader');
?>

