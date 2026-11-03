<?php

/**
 * Event action registry.
 *
 * One list of everything you can do to an event. The event header renders the
 * status, the state-changing primary and the palette trigger from it; the
 * actions palette — docked in the General column and opened as an overlay from
 * the header — renders the whole set. Because both surfaces read the same
 * list, an action can no longer exist in one and be missing from the other.
 *
 * The actions are the flat list the launch-bar card already understands:
 * 'divider' entries open a menu group, everything else is an action carrying
 * url/onclick/icon/label plus the optional tile, tone and tour hints.
 *
 * Callers pass a context of already-resolved permissions; the registry never
 * touches Acl itself so the same result can be built from a view.
 *
 * @param array $ctx
 *   baseurl           string
 *   event             array   the Event row
 *   orgc              array   the Event's Orgc (for 'local')
 *   me                array   the current user (for role flags)
 *   isSiteAdmin       bool
 *   isAclDelegate     bool
 *   isAclPublish      bool
 *   delegationRequest array|null
 *   canEdit           bool    may modify the event
 *   mayPublish        bool    may publish/unpublish it
 *   canCollection     bool    may add an element to a collection
 *   canWorkspace      bool    may add nodes to a workspace
 *   canAi             bool    may use the event AI actions
 * @return array
 *   actions  array        the flat action list (with dividers)
 *   status   array        publication status for the launch bar
 *   primary  array|null   the state-changing action (publish/unpublish)
 */
class EventActions
{
    public static function build(array $ctx)
    {
        $baseurl = $ctx['baseurl'] ?? '';
        $event = $ctx['event'] ?? [];
        $me = $ctx['me'] ?? [];

        $eventId = (int)($event['id'] ?? 0);
        $eventUuid = (string)($event['uuid'] ?? '');
        $eventInfo = (string)($event['info'] ?? '');
        $orgcLocal = !empty($ctx['orgc']['local']);

        $isPublished = !empty($event['published']);
        $lastPublished = (int)($event['publish_timestamp'] ?? 0);
        $hasPending = $isPublished && $lastPublished > 0
            && (int)($event['timestamp'] ?? 0) > $lastPublished;

        $isSiteAdmin = !empty($ctx['isSiteAdmin']);
        $canEdit = !empty($ctx['canEdit']);
        $mayPublish = !empty($ctx['mayPublish']);

        // Open a MISP modal for an action; the size is optional.
        $modal = function ($url, $size = null) {
            return sprintf(
                "event.preventDefault(); openModal('%s'%s);",
                $url,
                $size === null ? '' : ", '" . $size . "'"
            );
        };

        $actions = [];
        $add = function ($label, $url, $icon, array $extra = []) use (&$actions, $modal) {
            $size = $extra['modalSize'] ?? null;
            unset($extra['modalSize']);
            $actions[] = array_merge([
                'url' => $url,
                'onclick' => !empty($extra['raw']) ? null : $modal($url, $size),
                'icon' => $icon,
                'label' => $label,
            ], $extra);
        };

        if ($canEdit) {
            $actions[] = ['divider' => true, 'label' => __('Add')];
            $add(__('Add Attribute'), "$baseurl/attributes/add/$eventId",
                'misp-icon misp-icon-attribute misp-simple', [
                    'tour' => 'action-add-attribute', 'add' => true,
                    'short' => __('Attribute'), 'entity' => 'attribute',
                    'pinned' => true, 'tileOrder' => 1,
                ]);
            $add(__('Add Object'), "$baseurl/objects/add/$eventId",
                'misp-icon misp-icon-object misp-simple', [
                    'tour' => 'action-add-object', 'add' => true,
                    'short' => __('Object'), 'entity' => 'object',
                    'pinned' => true, 'tileOrder' => 2,
                ]);
            $add(__('Add Attachment'), "$baseurl/attributes/add_attachment/$eventId",
                'fas fa-paperclip', [
                    'tour' => 'action-add-attachment', 'add' => true,
                    'short' => __('Attach'), 'entity' => 'attribute',
                    'pinned' => true, 'tileOrder' => 3,
                ]);
            $add(__('Add Event Report'), "$baseurl/event_reports/add/$eventId",
                'misp-icon misp-icon-report misp-simple', [
                    'tour' => 'action-add-report', 'add' => true,
                    'short' => __('Report'), 'entity' => 'report',
                    'pinned' => true, 'tileOrder' => 4,
                ]);

            $actions[] = ['divider' => true, 'label' => __('Edit')];
            $add(__('Edit Event'), "$baseurl/events/edit/$eventId", 'fas fa-pen', [
                'tour' => 'action-edit-event', 'short' => __('Edit'),
                'entity' => 'event', 'pinned' => true, 'tileOrder' => 6,
            ]);

            $actions[] = ['divider' => true, 'label' => __('Import & enrichment')];
            $add(__('Import from'), "$baseurl/events/populateFrom/$eventId", 'fas fa-sign-in-alt', [
                'tour' => 'action-populate-from', 'short' => __('Import'),
                'entity' => 'attribute', 'pinned' => true, 'tileOrder' => 5,
            ]);
            if (Configure::read('Plugin.AI_services_enable') && !empty($ctx['canAi'])) {
                $add(__('AI actions'), "$baseurl/events/aiActions/$eventId", 'fas fa-robot', [
                    'short' => __('AI'), 'entity' => 'enrichment', 'modalSize' => 'md',
                ]);
            }
            $add(__('Merge attributes from'), "$baseurl/events/merge/$eventId", 'fas fa-layer-group', [
                'short' => __('Merge'), 'entity' => 'attribute', 'modalSize' => 'md',
            ]);
            if (Configure::read('Plugin.Enrichment_services_enable')) {
                $add(__('Enrich Event'), "$baseurl/events/enrichEvent/$eventId", 'fas fa-wand-magic-sparkles', [
                    'tour' => 'action-enrich', 'short' => __('Enrich'), 'entity' => 'enrichment',
                ]);
            }

            $mayToggleCorrelation = !Configure::read('MISP.completely_disable_correlation')
                && ($isSiteAdmin || Configure::read('MISP.allow_disabling_correlation'));
            $mayProtect = (int)($me['org_id'] ?? 0) === (int)($event['orgc_id'] ?? 0)
                && !empty($ctx['hostOrgUser']) && empty($event['locked']);
            if ($mayToggleCorrelation || $mayProtect) {
                $actions[] = ['divider' => true, 'label' => __('Event settings')];
            }
            if ($mayToggleCorrelation) {
                $correlationOff = !empty($event['disable_correlation']);
                $add($correlationOff ? __('Enable correlation') : __('Disable correlation'),
                    "$baseurl/events/toggleCorrelation/$eventId",
                    $correlationOff ? 'fas fa-link' : 'fas fa-unlink', [
                        'short' => __('Correlation'), 'entity' => 'correlation', 'modalSize' => 'md',
                    ]);
            }
            if ($mayProtect) {
                $isProtected = !empty($event['protected']);
                $protectAction = $isProtected ? 'unprotect' : 'protect';
                $add($isProtected ? __('Remove protection') : __('Protect event'),
                    "$baseurl/events/$protectAction/$eventId",
                    $isProtected ? 'fas fa-unlock' : 'fas fa-shield-alt', [
                        'short' => $isProtected ? __('Unprotect') : __('Protect'),
                        'entity' => 'event', 'modalSize' => 'md',
                    ]);
                if ($isProtected) {
                    $add(__('Add signing key'), "$baseurl/CryptographicKeys/add/Event/$eventId",
                        'fas fa-key', ['short' => __('Signing key'), 'entity' => 'event']);
                }
            }
        }

        $actions[] = ['divider' => true, 'label' => __('Share')];

        if (!empty($ctx['canCollection'])) {
            $add(__('Add to collection'),
                "$baseurl/collectionElements/addElementToCollection/Event/$eventUuid",
                'fas fa-folder-plus', [
                    'short' => __('Collection'), 'entity' => 'collection', 'modalSize' => 'xl',
                ]);
        }
        if (!empty($ctx['canWorkspace'])) {
            $actions[] = [
                'url' => '#',
                'icon' => 'misp-icon misp-icon-analyst-graph misp-simple',
                'label' => __('Add to workspace'),
                'short' => __('Workspace'),
                'entity' => 'analystGraph',
                'attributes' => ['data-intel-graph-add' => json_encode([[
                    'type' => 'Event', 'uuid' => $eventUuid, 'label' => $eventInfo,
                ]])],
            ];
        }
        if ($mayPublish && (!$isPublished || $hasPending)) {
            $add($hasPending ? __('Publish changes') : __('Publish Event'), '',
                'fas fa-upload', [
                    'onclick' => $modal("$baseurl/events/publish/$eventId", 'md'),
                    'tour' => 'action-publish', 'short' => __('Publish'),
                    'primary' => true, 'success' => true, 'raw' => true,
                ]);
        }
        if ($mayPublish && $isPublished) {
            $add(__('Unpublish Event'), '', 'fas fa-eye-slash', [
                'onclick' => $modal("$baseurl/events/unpublish/$eventId", 'md'),
                'tour' => 'action-unpublish', 'short' => __('Unpublish'),
                'primary' => !$hasPending, 'warning' => true, 'raw' => true,
            ]);
        }
        if ($orgcLocal) {
            $add(__('Contact Reporter'), "$baseurl/events/contact/$eventId", 'fas fa-envelope', [
                'short' => __('Contact'), 'modalSize' => 'md',
            ]);
        }
        $add(__('Download as'), "$baseurl/events/exportChoice/$eventId", 'fas fa-download', [
            'tour' => 'action-download', 'short' => __('Download'), 'modalSize' => 'md',
        ]);
        if ($isPublished && !empty($me['Role']['perm_sighting'])) {
            $add(__('Publish Sightings'), '', 'fas fa-eye', [
                'onclick' => $modal("$baseurl/events/publishSightings/$eventId", 'md'),
                'short' => __('Sightings'), 'entity' => 'sighting', 'raw' => true,
            ]);
        }

        if (Configure::read('MISP.delegation')) {
            self::_delegation($actions, $ctx, $modal);
        }

        if ($isSiteAdmin) {
            $actions[] = ['divider' => true, 'label' => __('Maintenance')];
            if (Configure::read('Plugin.Workflow_enable')) {
                $add(__('Run Ad-Hoc Workflow'), '',
                    'fas fa-diagram-project', [
                        'onclick' => $modal("$baseurl/events/runWorkflow/$eventId"),
                        'short' => __('Workflow'), 'raw' => true,
                    ]);
            }
            $add(__('Recorrelate Event'), "$baseurl/events/recorrelateEvent/$eventId",
                'fas fa-arrows-rotate', [
                    'short' => __('Recorrelate'), 'entity' => 'correlation', 'modalSize' => 'md',
                ]);
        }

        if ($canEdit) {
            // card_launch_bar lifts danger actions into their own group at the foot.
            $actions[] = ['divider' => true, 'label' => __('Danger')];
            $actions[] = [
                'url' => "$baseurl/events/delete/$eventId",
                'onclick' => $modal("$baseurl/events/delete/$eventId", 'md'),
                'icon' => 'fas fa-trash',
                'label' => __('Delete Event'),
                'danger' => true,
            ];
        }

        $primary = null;
        foreach ($actions as $action) {
            if (!empty($action['primary']) && $primary === null) {
                $primary = $action;
                break;
            }
        }

        return [
            'actions' => $actions,
            'primary' => $primary,
            'status' => [
                'published' => $isPublished,
                'pending' => $hasPending,
                'readOnly' => !$canEdit,
                'lastPublished' => $lastPublished,
            ],
        ];
    }

    /**
     * The delegation actions: the offer, or the accept/discard pair for a
     * request already on the table.
     */
    private static function _delegation(array &$actions, array $ctx, callable $modal)
    {
        $baseurl = $ctx['baseurl'] ?? '';
        $event = $ctx['event'] ?? [];
        $me = $ctx['me'] ?? [];
        $eventId = (int)($event['id'] ?? 0);
        $isSiteAdmin = !empty($ctx['isSiteAdmin']);

        $pendingDelegation = empty($ctx['delegationRequest']) ? null : $ctx['delegationRequest'];

        if ($pendingDelegation === null) {
            $onlyMyOrg = (int)($event['distribution'] ?? -1) === 0;
            if ((Configure::read('MISP.unpublishedprivate') || $onlyMyOrg)
                && ($isSiteAdmin || !empty($ctx['isAclDelegate']))
            ) {
                $actions[] = [
                    'url' => "$baseurl/event_delegations/delegateEvent/$eventId",
                    'onclick' => $modal("$baseurl/event_delegations/delegateEvent/$eventId"),
                    'icon' => 'fas fa-handshake',
                    'label' => __('Delegate Publishing'),
                    'short' => __('Delegate'),
                ];
            }
            return;
        }

        $delegationId = (int)($pendingDelegation['EventDelegation']['id'] ?? 0);
        $myOrg = $me['org_id'] ?? null;
        $isTarget = $myOrg !== null && $myOrg == ($pendingDelegation['EventDelegation']['org_id'] ?? null);
        $isRequester = $myOrg !== null && $myOrg == ($pendingDelegation['EventDelegation']['requester_org_id'] ?? null);

        if (!$isSiteAdmin && (empty($ctx['isAclPublish']) || (!$isTarget && !$isRequester))) {
            return;
        }
        if ($isSiteAdmin || (!empty($ctx['isAclPublish']) && $isTarget)) {
            $actions[] = [
                'url' => "$baseurl/event_delegations/acceptDelegation/$delegationId",
                'onclick' => $modal("$baseurl/event_delegations/acceptDelegation/$delegationId", 'md'),
                'icon' => 'fas fa-handshake',
                'label' => __('Accept Delegation Request'),
                'short' => __('Accept'),
                'success' => true,
                'modalSize' => 'md',
            ];
        }
        $actions[] = [
            'url' => "$baseurl/event_delegations/deleteDelegation/$delegationId",
            'onclick' => $modal("$baseurl/event_delegations/deleteDelegation/$delegationId", 'md'),
            'icon' => 'fas fa-handshake-slash',
            'label' => __('Discard Delegation Request'),
            'short' => __('Discard'),
            'warning' => true,
            'modalSize' => 'md',
        ];
    }
}
