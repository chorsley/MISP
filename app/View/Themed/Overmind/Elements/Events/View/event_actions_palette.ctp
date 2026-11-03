<?php
/**
 * The event actions palette as an overlay.
 *
 * The same registry and the same launch-bar card as the docked copy, wrapped
 * in a fixed dialog so the header can open it from any tab. Rendered once, at
 * page level, outside the tab panes.
 */

App::uses('EventActions', 'Tools');

if (empty($eventActions)) {
    $eventActions = EventActions::build([
        'baseurl' => $baseurl,
        'event' => $data['Event'] ?? [],
        'orgc' => $data['Orgc'] ?? [],
        'me' => $me ?? [],
        'isSiteAdmin' => !empty($isSiteAdmin),
        'isAclDelegate' => !empty($isAclDelegate),
        'isAclPublish' => !empty($isAclPublish),
        'delegationRequest' => $delegationRequest ?? null,
        'canEdit' => !empty($isSiteAdmin) || $this->Acl->canModifyEvent($data),
        'mayPublish' => !empty($isSiteAdmin)
            || ($this->Acl->canModifyEvent($data) && $this->Acl->canPublishEvent($data)),
        'canCollection' => $this->Acl->canAccess('collectionElements', 'addElementToCollection'),
        'canWorkspace' => $this->Acl->canAccess('workspaces', 'addNodes'),
        'canAi' => $this->Acl->canAccess('events', 'aiActions'),
        'hostOrgUser' => !empty($hostOrgUser),
    ]);
}

echo $this->element('genericElementsBS5/Cards/card_launch_bar', [
    'actions' => $eventActions['actions'],
    'status' => $eventActions['status'],
    'variant' => 'overlay',
    'withTour' => true,
    'showStatus' => false,
    'columns' => 4,
    'autoPin' => false,
]);
