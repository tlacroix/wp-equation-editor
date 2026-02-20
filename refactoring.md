# Refactoring Plan

This document outlines the refactoring plan for the Equation Editor WordPress plugin. Tasks are organized by priority and grouped by area.

## Phase 1: Critical Security Fixes

### 1.1 Sanitize Settings on Save (HIGH PRIORITY)
**File:** `equation-editor.php:82-87`

**Current Issue:** The entire `$_POST` array is saved directly to the database without sanitization.

```php
// Current (unsafe)
$set = update_option($this->option, $_POST);

// Should be
$settings = array(
    'enable_eq_editor' => isset($_POST['enable_eq_editor']) ? '1' : '0',
    'select_eq_editor' => sanitize_text_field(wp_unslash($_POST['select_eq_editor'] ?? 'wiris')),
);
$set = update_option($this->option, $settings);
```

### 1.2 Validate Editor Type (HIGH PRIORITY)
**File:** `equation-editor.php`

**Current Issue:** No validation that `select_eq_editor` is a valid option.

```php
// Add validation
$valid_editors = array('wiris', 'latex', 'both');
$editor = in_array($editor, $valid_editors, true) ? $editor : 'wiris';
```

### 1.3 Escape Output in Admin Template (HIGH PRIORITY)
**File:** `admin/mw_equation_editor.php`

**Current Issue:** Using `_e()` instead of `esc_html_e()` for output.

```php
// Current
<?php _e('Equation Editor', 'equation-editor');?>

// Should be
<?php esc_html_e('Equation Editor', 'equation-editor');?>
```

### 1.4 Use Proper Redirect (MEDIUM PRIORITY)
**File:** `equation-editor.php:90-92`

**Current Issue:** JavaScript redirect instead of `wp_safe_redirect()`.

```php
// Current
echo '<script>window.location.href="' . esc_js($url) . '"</script>';

// Should be
wp_safe_redirect(admin_url($url));
exit;
```

---

## Phase 2: WordPress Settings API

### 2.1 Register Settings Properly
**Current Issue:** Settings are not registered with WordPress Settings API.

**Tasks:**
- [ ] Use `register_setting()` for option registration
- [ ] Use `add_settings_section()` and `add_settings_field()`
- [ ] Add sanitization callback to `register_setting()`
- [ ] Remove manual save handling

**Benefits:**
- Built-in nonce verification
- Automatic sanitization
- Settings errors handling
- REST API compatibility

### 2.2 Create Settings Class
**New File:** `includes/class-settings.php`

```php
class Equation_Editor_Settings {
    public function register(): void;
    public function sanitize(array $input): array;
    public function render_page(): void;
    public function render_enable_field(): void;
    public function render_editor_type_field(): void;
}
```

---

## Phase 3: Code Organization

### 3.1 File Structure Reorganization
**Current:**
```
equation-editor.php
admin/mw_equation_editor.php
js/eq_editor.js
tiny_mce_wiris/
```

**Proposed:**
```
equation-editor.php          # Bootstrap only
includes/
  class-equation-editor.php  # Main plugin class
  class-settings.php         # Settings handling
  class-tinymce.php          # TinyMCE integration
admin/
  views/
    settings-page.php        # Admin template
  class-admin.php            # Admin functionality
assets/
  js/
    eq_editor.js
  css/
    admin.css                # Admin styles (new)
tiny_mce_wiris/              # Third-party (unchanged)
```

### 3.2 Rename Main Class (MEDIUM PRIORITY)
**Current:** `mw_equation_editor` (doesn't follow WordPress naming)

**Proposed:** `Equation_Editor` with namespace

```php
namespace NuageLab\EquationEditor;

class Plugin {
    // ...
}
```

### 3.3 Use Autoloading
- [ ] Add PSR-4 autoloading to `composer.json`
- [ ] Create namespace structure
- [ ] Update class references

---

## Phase 4: WordPress Coding Standards

### 4.1 Enable Full WPCS
**File:** `phpcs.xml.dist`

Gradually enable excluded rules:
- [ ] `WordPress.Files.FileName` - Rename files to hyphenated lowercase
- [ ] `Squiz.Commenting` - Add docblocks
- [ ] `WordPress.PHP.YodaConditions` - Use Yoda conditions
- [ ] `Universal.Operators.StrictComparisons` - Use strict comparisons

### 4.2 Fix File Naming
| Current | Proposed |
|---------|----------|
| `mw_equation_editor.php` | `class-equation-editor.php` |
| `admin/mw_equation_editor.php` | `admin/views/settings-page.php` |

### 4.3 Add PHPDoc Comments
All classes, methods, and properties should have proper documentation:

```php
/**
 * Main plugin class.
 *
 * @since 2.0.0
 */
class Equation_Editor {
    /**
     * Plugin settings.
     *
     * @var array
     */
    private array $settings;

    /**
     * Add TinyMCE buttons.
     *
     * @param array $buttons Existing buttons.
     * @return array|null Modified buttons or null if disabled.
     */
    public function add_buttons(array $buttons): ?array {
        // ...
    }
}
```

---

## Phase 5: Admin UI Improvements

### 5.1 Use WordPress Admin Styles
**File:** `admin/mw_equation_editor.php`

- [ ] Remove inline styles
- [ ] Use WordPress admin CSS classes properly
- [ ] Add custom admin stylesheet if needed

### 5.2 Improve Settings Page Layout
- [ ] Use `<h1>` instead of `<h2>` for page title (WordPress standard)
- [ ] Remove outdated "rate us" image/messaging
- [ ] Add success/error notices using `add_settings_error()`
- [ ] Use `selected()` and `checked()` helper functions

```php
// Current
<?php echo (isset($this->settings['select_eq_editor']) && $this->settings['select_eq_editor'] == 'wiris') ? 'selected="checked"' : '';?>

// Should be
<?php selected($this->settings['select_eq_editor'] ?? '', 'wiris');?>
```

### 5.3 Add Settings Link to Plugins Page
```php
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=equation-editor') . '">' . __('Settings', 'equation-editor') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
});
```

---

## Phase 6: Modern PHP & Best Practices

### 6.1 Use Constants for Plugin Data
```php
define('EQUATION_EDITOR_VERSION', '2.0.0');
define('EQUATION_EDITOR_FILE', __FILE__);
define('EQUATION_EDITOR_PATH', plugin_dir_path(__FILE__));
define('EQUATION_EDITOR_URL', plugin_dir_url(__FILE__));
```

### 6.2 Singleton Pattern or Dependency Injection
Consider using a container or proper dependency injection instead of global state.

### 6.3 Add Uninstall Hook
**New File:** `uninstall.php`

```php
<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('mw_equation_editor');
```

### 6.4 Add Deactivation Hook
Clean up if necessary (transients, scheduled events, etc.).

---

## Phase 7: Block Editor Support (Future)

### 7.1 Evaluate Block Editor Integration
The Classic Editor (TinyMCE) is being phased out. Consider:
- [ ] Research Gutenberg block for equation editing
- [ ] Evaluate MathJax or KaTeX for rendering
- [ ] Create custom block for equations
- [ ] Maintain TinyMCE support for Classic Editor users

### 7.2 Create Equation Block
```
blocks/
  equation/
    block.json
    index.js
    editor.scss
    style.scss
```

---

## Phase 8: Testing & Documentation

### 8.1 Increase Test Coverage
- [ ] Add integration tests for settings save flow
- [ ] Add tests for edge cases in Wiris integration
- [ ] Test with multiple WordPress versions
- [ ] Test with Classic Editor plugin

### 8.2 Add Inline Documentation
- [ ] PHPDoc for all public methods
- [ ] JSDoc for JavaScript files
- [ ] README.md improvements

### 8.3 Create Developer Documentation
- [ ] Hooks and filters reference
- [ ] Extending the plugin guide

---

## Implementation Order

### Sprint 1: Security & Stability
1. [ ] 1.1 Sanitize settings on save
2. [ ] 1.2 Validate editor type
3. [ ] 1.3 Escape output in admin template
4. [ ] 1.4 Use proper redirect
5. [ ] Update tests for security changes

### Sprint 2: Settings API
1. [ ] 2.1 Register settings properly
2. [ ] 2.2 Create Settings class
3. [ ] 5.2 Improve settings page layout
4. [ ] 5.3 Add settings link to plugins page

### Sprint 3: Code Organization
1. [ ] 3.1 File structure reorganization
2. [ ] 3.2 Rename main class
3. [ ] 3.3 Use autoloading
4. [ ] 6.1 Use constants for plugin data
5. [ ] 6.3 Add uninstall hook

### Sprint 4: Standards & Polish
1. [ ] 4.1 Enable full WPCS
2. [ ] 4.2 Fix file naming
3. [ ] 4.3 Add PHPDoc comments
4. [ ] 5.1 Use WordPress admin styles
5. [ ] Bump version to 2.0.0

### Sprint 5: Future (Block Editor)
1. [ ] 7.1 Evaluate Block Editor integration
2. [ ] 7.2 Create Equation block (if viable)

---

## Version Bump Plan

| Version | Changes |
|---------|---------|
| 1.8.0 | Security fixes (Phase 1) |
| 1.9.0 | Settings API migration (Phase 2) |
| 2.0.0 | Code reorganization, full WPCS compliance (Phases 3-6) |
| 3.0.0 | Block Editor support (Phase 7) |

---

## Notes

- All changes should maintain backward compatibility with existing settings
- Test thoroughly with both Classic Editor and Block Editor
- Keep Wiris integration files (`tiny_mce_wiris/`) unchanged unless necessary
- Deprecate features gracefully with `_deprecated_function()` notices
