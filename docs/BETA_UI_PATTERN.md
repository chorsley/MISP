# Beta UI Opt-In Pattern for MISP

## Overview

This document describes the pattern for implementing beta UI features in MISP that allows users to opt-in to new interface changes without affecting existing users who prefer the stable interface.

## Purpose

The beta UI pattern enables MISP developers to:

1. **Deploy UI changes gradually** - Test new interfaces with willing users before making them the default
2. **Gather feedback safely** - Allow users to try new features without breaking their workflows
3. **Maintain stability** - Keep the existing interface stable for users who depend on it
4. **Support external tooling** - Give time for external tools and integrations to adapt to UI changes
5. **Enable parallel development** - Work on major UI improvements without blocking releases

## Architecture

The beta UI pattern consists of three main components:

### 1. User Setting (`ui_beta_opt_in`)

A boolean user setting that controls whether a user sees beta UI features.

**Location**: `app/Model/UserSetting.php`

**Setting name**: `ui_beta_opt_in`

**Default value**: `false` (users must explicitly opt-in)

### 2. BetaUiHelper Tool

A helper class providing utilities for selecting appropriate views, elements, layouts, and stylesheets based on the user's beta UI preference.

**Location**: `app/Lib/Tools/BetaUiHelper.php`

**Key methods**:
- `getViewPath()` - Select view files
- `getElementPath()` - Select element files
- `getLayoutPath()` - Select layout files
- `getBetaCssFiles()` - Get beta-specific CSS files
- `isFeatureEnabled()` - Check granular feature flags
- `getCssModifier()` - Get CSS class modifiers for beta styling

### 3. AppController Integration

The `beforeRender()` method in `AppController` makes the `$uiBetaEnabled` variable available to all views.

**Location**: `app/Controller/AppController.php`

**Variable**: `$uiBetaEnabled` - Available in all views and layouts

## Usage Patterns

### Pattern 1: Separate Beta View Files

Create a completely separate view file for beta users.

**File naming convention**: Add `_beta` suffix to the filename.

**Example**:
```
app/View/Events/index.ctp       # Standard view
app/View/Events/index_beta.ctp  # Beta view
```

**Controller code**:
```php
// In EventsController.php
App::uses('BetaUiHelper', 'Tools');

public function index() {
    // ... standard controller logic ...
    
    // Automatically render the appropriate view
    $uiBetaEnabled = !empty($this->viewVars['uiBetaEnabled']) ? $this->viewVars['uiBetaEnabled'] : false;
    $viewPath = BetaUiHelper::getViewPath($uiBetaEnabled, 'Events/index');
    $this->render($viewPath);
}
```

**When to use**:
- Major UI redesigns
- Complete layout changes
- Significant structural differences

### Pattern 2: Conditional Elements

Use the same view but load different elements based on beta status.

**Example**:
```php
// In a view file (e.g., Events/index.ctp)
<?php
App::uses('BetaUiHelper', 'Tools');
$elementPath = BetaUiHelper::getElementPath($uiBetaEnabled, 'Events/eventIndexTable');
echo $this->element($elementPath);
?>
```

**When to use**:
- Modifying specific components
- Incremental improvements
- Testing new widgets or controls

### Pattern 3: Conditional Rendering in Views

Use conditional logic within a single view file.

**Example**:
```php
// In a view file
<?php if (!empty($uiBetaEnabled)): ?>
    <!-- Beta UI version -->
    <div class="event-list event-list--beta">
        <h2>Events (New Design)</h2>
        <!-- New UI elements -->
    </div>
<?php else: ?>
    <!-- Standard UI version -->
    <div class="event-list">
        <h2>Events</h2>
        <!-- Standard UI elements -->
    </div>
<?php endif; ?>
```

**When to use**:
- Minor UI tweaks
- Small visual changes
- Testing alternative layouts

### Pattern 4: CSS-Only Changes

Use CSS class modifiers to apply beta-specific styling.

**Example**:
```php
// In a view file
<?php
App::uses('BetaUiHelper', 'Tools');
$cssModifier = BetaUiHelper::getCssModifier($uiBetaEnabled, 'event-list');
?>
<div class="event-list<?= $cssModifier ?>">
    <!-- Content remains the same -->
</div>
```

**CSS file** (`app/webroot/css/main-beta.css`):
```css
.event-list--beta {
    /* Beta-specific styles */
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}
```

**When to use**:
- Visual-only changes
- Color scheme updates
- Layout adjustments without HTML changes

### Pattern 5: Beta Layouts

Use a completely different layout for beta users.

**Example**:
```php
// In a controller
App::uses('BetaUiHelper', 'Tools');

public function beforeRender() {
    parent::beforeRender();
    
    $uiBetaEnabled = !empty($this->viewVars['uiBetaEnabled']) ? $this->viewVars['uiBetaEnabled'] : false;
    $this->layout = BetaUiHelper::getLayoutPath($uiBetaEnabled, 'default');
}
```

**When to use**:
- Site-wide redesigns
- New navigation structures
- Different page frameworks

## Enabling Beta UI for Users

### Via API

Users can enable beta UI through the UserSettings API:

```bash
# Enable beta UI
curl -X POST \
  -H "Authorization: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"setting": "ui_beta_opt_in", "value": true}' \
  https://your-misp-instance/userSettings/setSetting
```

```bash
# Disable beta UI
curl -X POST \
  -H "Authorization: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"setting": "ui_beta_opt_in", "value": false}' \
  https://your-misp-instance/userSettings/setSetting
```

### Via UI (Future Enhancement)

A user settings page can be created to allow users to toggle beta UI through the web interface.

**Suggested location**: User profile settings or preferences page

## Best Practices

### 1. Always Provide Fallback

Beta views should always fall back to standard views if the beta file doesn't exist.

```php
// BetaUiHelper automatically handles this
$viewPath = BetaUiHelper::getViewPath($uiBetaEnabled, 'Events/index');
// Returns 'Events/index' if 'Events/index_beta' doesn't exist
```

### 2. Keep Beta Files Synchronized

When updating standard views, consider whether beta views need similar updates.

### 3. Document Beta Features

Add comments in beta files explaining what's different from the standard version.

```php
<?php
/**
 * Beta version of Events/index view
 * 
 * Changes from standard version:
 * - Grid layout instead of table
 * - Card-based event display
 * - Enhanced filtering UI
 * 
 * @since 2.5.x (beta)
 */
?>
```

### 4. Test Both Versions

Always test both standard and beta versions when making changes.

### 5. Use Feature Flags for Granular Control

For complex beta features, use feature flags:

```php
if (BetaUiHelper::isFeatureEnabled($uiBetaEnabled, 'new_event_cards')) {
    // Show new card-based layout
} else {
    // Show standard table layout
}
```

Configure in `config.php`:
```php
$config['MISP']['beta_ui_features'] = [
    'new_event_cards',
    'enhanced_filtering',
    'modern_navigation'
];
```

### 6. Gradual Migration Path

Plan for eventually making beta features the default:

1. **Phase 1**: Beta opt-in (current pattern)
2. **Phase 2**: Beta becomes default, with opt-out option
3. **Phase 3**: Remove old version entirely

## Example Implementation

### Complete Example: Beta Event Index

**1. Create beta view** (`app/View/Events/index_beta.ctp`):

```php
<?php
/**
 * Beta version of Events index
 * Features: Grid layout, card-based display
 */
App::uses('BetaUiHelper', 'Tools');
?>
<div class="events-beta">
    <h2><?php echo __('Events (Beta)');?></h2>
    <div class="events-grid">
        <?php foreach ($events as $event): ?>
            <div class="event-card">
                <h3><?= h($event['Event']['info']) ?></h3>
                <p>ID: <?= h($event['Event']['id']) ?></p>
                <!-- More event details -->
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

**2. Create beta CSS** (`app/webroot/css/events-beta.css`):

```css
.events-beta {
    padding: 2rem;
}

.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.event-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.event-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
```

**3. Update controller** (`app/Controller/EventsController.php`):

```php
App::uses('BetaUiHelper', 'Tools');

public function index() {
    // ... existing logic ...
    
    // Load beta CSS if enabled
    $uiBetaEnabled = !empty($this->viewVars['uiBetaEnabled']) ? $this->viewVars['uiBetaEnabled'] : false;
    if ($uiBetaEnabled) {
        $this->set('additionalCss', [['events-beta']]);
    }
    
    // Render appropriate view
    $viewPath = BetaUiHelper::getViewPath($uiBetaEnabled, 'Events/index');
    $this->render($viewPath);
}
```

## Migration Strategy

When a beta feature is ready to become the default:

### Step 1: Announce the Change

Notify users that the beta feature will become the default in the next release.

### Step 2: Flip the Default

1. Rename files:
   - `index.ctp` → `index_legacy.ctp`
   - `index_beta.ctp` → `index.ctp`

2. Update logic to check for opt-out instead of opt-in

3. Update documentation

### Step 3: Deprecation Period

Keep the legacy version available for 1-2 releases with a deprecation notice.

### Step 4: Remove Legacy Version

After sufficient time, remove the legacy files completely.

## Troubleshooting

### Beta UI Not Showing

1. Check user setting:
   ```bash
   curl -H "Authorization: YOUR_API_KEY" \
     https://your-misp-instance/userSettings/getSetting/USER_ID/ui_beta_opt_in
   ```

2. Verify beta files exist in correct locations

3. Check file permissions

4. Clear cache if using caching

### Beta Files Not Found

Ensure files follow naming convention:
- Views: `viewname_beta.ctp`
- Elements: `elementname_beta.ctp`
- Layouts: `layoutname_beta.ctp`
- CSS: `filename-beta.css`

### Styling Issues

1. Ensure beta CSS files are loaded in the layout
2. Check CSS file paths
3. Verify CSS specificity (beta styles should override standard styles)

## Security Considerations

1. **No security through obscurity**: Beta UI should not expose functionality that standard UI doesn't have access to
2. **Same ACL checks**: Beta views must implement the same access control checks as standard views
3. **Input validation**: Beta forms must validate input the same way as standard forms
4. **XSS protection**: Beta views must properly escape output

## Performance Considerations

1. **File existence checks**: BetaUiHelper caches file existence checks to minimize filesystem operations
2. **Minimal overhead**: The pattern adds negligible overhead (one user setting lookup per request)
3. **CSS loading**: Only load beta CSS files when needed

## Future Enhancements

Potential improvements to the beta UI pattern:

1. **UI Settings Page**: Web interface for toggling beta UI
2. **Per-Feature Opt-In**: Allow users to enable specific beta features individually
3. **Beta Feedback System**: Built-in mechanism for users to provide feedback on beta features
4. **A/B Testing**: Randomly assign users to beta/standard for testing
5. **Analytics**: Track usage and performance of beta vs standard UI

## Support

For questions or issues with the beta UI pattern:

1. Check this documentation
2. Review example implementations in the codebase
3. Consult the MISP development team
4. Open an issue on GitHub with the `ui-beta` label

## Changelog

- **2025-11**: Initial implementation of beta UI pattern
  - Added `ui_beta_opt_in` user setting
  - Created `BetaUiHelper` tool
  - Integrated with `AppController`
  - Documented usage patterns
