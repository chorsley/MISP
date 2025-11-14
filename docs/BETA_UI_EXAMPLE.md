# Beta UI Pattern - Working Example

This document provides a complete working example of implementing a beta UI feature in MISP.

## Example: Beta Event Index View

This example demonstrates how to create a beta version of the Events index page with a modern card-based layout.

### Step 1: Enable Beta UI for Your User

First, enable the beta UI setting for your user account:

```bash
# Via API
curl -X POST \
  -H "Authorization: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"setting": "ui_beta_opt_in", "value": true}' \
  https://your-misp-instance/userSettings/setSetting

# Response:
# {"saved":true,"success":true,"name":"ui_beta_opt_in","url":"/userSettings/setSetting"}
```

### Step 2: Create Beta View File

Create a new file: `app/View/Events/index_beta.ctp`

```php
<?php
/**
 * Beta version of Events/index view
 * 
 * This demonstrates a modern card-based layout for events
 * instead of the traditional table layout.
 * 
 * Changes from standard version:
 * - Grid layout with cards instead of table
 * - Enhanced visual hierarchy
 * - Modern styling with hover effects
 */

App::uses('BetaUiHelper', 'Tools');
$cssModifier = BetaUiHelper::getCssModifier($uiBetaEnabled, 'events');
?>

<div class="events index<?= $cssModifier ?>">
    <div class="page-header">
        <h2><?php echo __('Events (Beta UI)');?></h2>
        <p class="text-muted">You are viewing the beta interface. 
           <a href="<?= $baseurl ?>/userSettings/setSetting">Switch back to standard view</a>
        </p>
    </div>
    
    <div class="pagination">
        <ul>
        <?php
            $pagination = $this->Paginator->prev('&laquo; ' . __('previous'), array('tag' => 'li', 'escape' => false), null, array('tag' => 'li', 'class' => 'prev disabled', 'escape' => false, 'disabledTag' => 'span'));
            $pagination .= $this->Paginator->numbers(array('modulus' => 20, 'separator' => '', 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'span'));
            $pagination .= $this->Paginator->next(__('next') . ' &raquo;', array('tag' => 'li', 'escape' => false), null, array('tag' => 'li', 'class' => 'next disabled', 'escape' => false, 'disabledTag' => 'span'));
            echo $pagination;
        ?>
        </ul>
    </div>
    
    <!-- Beta: Grid layout with cards -->
    <div class="events-grid grid--beta grid-cols-3">
        <?php foreach ($events as $event): ?>
            <div class="event-card event-card--beta">
                <div class="event-card-header">
                    <h3 class="event-title">
                        <a href="<?= $baseurl ?>/events/view/<?= h($event['Event']['id']) ?>">
                            <?= h($event['Event']['info']) ?>
                        </a>
                    </h3>
                    <span class="badge badge--beta badge-<?= h($event['Event']['threat_level_id']) ?>">
                        Threat Level <?= h($event['Event']['threat_level_id']) ?>
                    </span>
                </div>
                
                <div class="event-card-body">
                    <div class="event-meta">
                        <span class="event-id">ID: <?= h($event['Event']['id']) ?></span>
                        <span class="event-date">
                            <?= $this->Time->format('Y-m-d', $event['Event']['date']) ?>
                        </span>
                    </div>
                    
                    <div class="event-stats mt-beta-2">
                        <span class="stat">
                            <i class="fa fa-list"></i> 
                            <?= h($event['Event']['attribute_count']) ?> attributes
                        </span>
                        <?php if (!empty($event['Event']['Tag'])): ?>
                            <span class="stat">
                                <i class="fa fa-tag"></i> 
                                <?= count($event['Event']['Tag']) ?> tags
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="event-org mt-beta-2">
                        <small class="text-beta-muted">
                            <?= h($event['Event']['Orgc']['name']) ?>
                        </small>
                    </div>
                </div>
                
                <div class="event-card-footer">
                    <a href="<?= $baseurl ?>/events/view/<?= h($event['Event']['id']) ?>" 
                       class="btn btn-sm btn--beta btn-primary">
                        View Event
                    </a>
                    <?php if ($isAclModify): ?>
                        <a href="<?= $baseurl ?>/events/edit/<?= h($event['Event']['id']) ?>" 
                           class="btn btn-sm btn--beta btn-secondary">
                            Edit
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <p class="mt-beta-4">
    <?php
    echo $this->Paginator->counter(array(
        'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total')
    ));
    ?>
    </p>
    
    <div class="pagination">
        <ul>
        <?= $pagination ?>
        </ul>
    </div>
</div>

<?php
// Load beta-specific CSS
echo $this->element('genericElements/assetLoader', [
    'css' => ['main-beta'],
]);
?>

<style>
/* Additional beta-specific styles for this view */
.events-grid {
    margin-top: 2rem;
}

.event-card-header {
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
}

.event-title {
    font-size: 1.125rem;
    margin: 0 0 0.5rem 0;
}

.event-title a {
    color: #333;
    text-decoration: none;
}

.event-title a:hover {
    color: #667eea;
}

.event-card-body {
    flex: 1;
}

.event-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.875rem;
    color: #6c757d;
}

.event-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.875rem;
}

.event-stats .stat {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.event-card-footer {
    border-top: 1px solid #e0e0e0;
    padding-top: 1rem;
    margin-top: 1rem;
    display: flex;
    gap: 0.5rem;
}

.page-header {
    margin-bottom: 2rem;
}

.page-header .text-muted {
    margin-top: 0.5rem;
}
</style>
```

### Step 3: Update Controller (Optional)

If you want to explicitly control which view is rendered, update the controller:

```php
// In app/Controller/EventsController.php
App::uses('BetaUiHelper', 'Tools');

public function index() {
    // ... existing controller logic ...
    
    // Automatically render the appropriate view based on user setting
    $uiBetaEnabled = !empty($this->viewVars['uiBetaEnabled']) ? $this->viewVars['uiBetaEnabled'] : false;
    
    // Optional: Load beta-specific CSS
    if ($uiBetaEnabled) {
        $this->set('additionalCss', [['main-beta']]);
    }
    
    // Render appropriate view (will automatically use index_beta.ctp if it exists)
    $viewPath = BetaUiHelper::getViewPath($uiBetaEnabled, 'Events/index');
    $this->render($viewPath);
}
```

### Step 4: Test the Implementation

1. **With Beta UI Enabled**:
   - Navigate to `/events/index`
   - You should see the new card-based layout
   - Cards should have hover effects
   - Layout should be responsive (3 columns on desktop, 1 on mobile)

2. **With Beta UI Disabled**:
   - Disable beta UI: `curl -X POST -H "Authorization: YOUR_API_KEY" -H "Content-Type: application/json" -d '{"setting": "ui_beta_opt_in", "value": false}' https://your-misp-instance/userSettings/setSetting`
   - Navigate to `/events/index`
   - You should see the standard table layout
   - No beta styles should be applied

### Step 5: Verify Fallback Behavior

To verify the fallback works correctly:

1. Temporarily rename `index_beta.ctp` to `index_beta.ctp.bak`
2. With beta UI enabled, navigate to `/events/index`
3. You should see the standard view (fallback behavior)
4. Rename the file back to `index_beta.ctp`

## Alternative Approach: Conditional Rendering

Instead of creating a separate file, you can use conditional rendering in the existing view:

```php
<?php
// In app/View/Events/index.ctp
App::uses('BetaUiHelper', 'Tools');
?>

<div class="events <?php if (!$ajax) echo 'index'; ?>">
    <h2><?php echo __('Events');?></h2>
    
    <?php if (!empty($uiBetaEnabled)): ?>
        <!-- Beta UI Version -->
        <div class="events-grid grid--beta grid-cols-3">
            <?php foreach ($events as $event): ?>
                <div class="event-card event-card--beta">
                    <!-- Card content -->
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Standard UI Version -->
        <?php echo $this->element('Events/eventIndexTable'); ?>
    <?php endif; ?>
</div>
```

## Testing Checklist

- [ ] Beta UI can be enabled via API
- [ ] Beta UI can be disabled via API
- [ ] Beta view is shown when enabled
- [ ] Standard view is shown when disabled
- [ ] Fallback works if beta file doesn't exist
- [ ] Beta CSS is loaded only when enabled
- [ ] No JavaScript errors in console
- [ ] Responsive layout works on mobile
- [ ] All links and buttons function correctly
- [ ] ACL permissions are respected in both views

## Common Issues and Solutions

### Issue: Beta view not showing

**Solution**: Check that:
1. User setting is correctly set: `SELECT * FROM user_settings WHERE setting = 'ui_beta_opt_in';`
2. File exists at correct path: `app/View/Events/index_beta.ctp`
3. File has correct permissions: `chmod 644 app/View/Events/index_beta.ctp`
4. Cache is cleared if using caching

### Issue: CSS not loading

**Solution**: Check that:
1. CSS file exists: `app/webroot/css/main-beta.css`
2. CSS is loaded in layout or view
3. File permissions are correct
4. Browser cache is cleared

### Issue: Both views showing

**Solution**: Ensure you're using either:
- Separate files with `BetaUiHelper::getViewPath()`, OR
- Conditional rendering with `if (!empty($uiBetaEnabled))`
- Not both at the same time

## Next Steps

After implementing and testing your beta UI:

1. **Gather Feedback**: Ask beta users for feedback on the new interface
2. **Iterate**: Make improvements based on feedback
3. **Document Changes**: Keep track of what's different from the standard UI
4. **Plan Migration**: Decide when to make beta the default
5. **Communicate**: Inform users about upcoming changes

## Additional Examples

See `docs/BETA_UI_PATTERN.md` for more examples including:
- Element-based approach
- Layout-based approach
- CSS-only approach
- Feature flag approach
