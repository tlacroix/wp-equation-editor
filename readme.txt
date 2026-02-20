=== Equation Editor ===
Contributors: modalweb, nuagelab
Tags: equation, math, latex, katex, gutenberg, block, tinymce, formula, chemistry, wiris
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 2.1.0
Requires PHP: 8.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add mathematical equations to WordPress using LaTeX syntax. Supports both the Block Editor (Gutenberg) and Classic Editor (TinyMCE).

== Description ==

Equation Editor makes it easy to add beautiful mathematical, physics, and chemistry equations to your WordPress posts and pages.

= Block Editor (Gutenberg) =

The **Equation block** provides a modern editing experience:

* Write equations using standard LaTeX syntax
* Live preview powered by KaTeX
* Display mode (centered) or inline equations
* Fast client-side rendering

= Classic Editor (TinyMCE) =

For Classic Editor users, the plugin adds toolbar buttons for:

* **CodeCogs LaTeX Editor** - Visual equation editor with LaTeX output
* **Wiris Editor** - Full-featured math and chemistry editor
* Use both editors together

= LaTeX Examples =

`E = mc^2`
`\frac{-b \pm \sqrt{b^2 - 4ac}}{2a}`
`\int_0^\infty e^{-x^2} dx`
`\sum_{n=1}^{\infty} \frac{1}{n^2} = \frac{\pi^2}{6}`

= Key Features =

* **Gutenberg Block** - Native block editor support with live KaTeX preview
* **TinyMCE Integration** - Classic Editor support with Wiris and CodeCogs
* **LaTeX Syntax** - Industry-standard equation notation
* **KaTeX Rendering** - Fast, high-quality equation display
* **Display Modes** - Centered (display) or inline equations
* **Responsive** - Equations scale properly on all devices

== Installation ==

1. Upload the plugin to `/wp-content/plugins/equation-editor`
2. Activate through the 'Plugins' menu in WordPress
3. **Block Editor**: Add the "Equation" block to your post
4. **Classic Editor**: Click the "fx" button in the toolbar

== Frequently Asked Questions ==

= How do I add an equation in the Block Editor? =

1. Click the "+" button to add a new block
2. Search for "Equation" or find it in the Text category
3. Enter your LaTeX equation
4. Toggle "Display Mode" for centered equations

= How do I add an equation in the Classic Editor? =

1. Click the "fx" button in the TinyMCE toolbar
2. Use the CodeCogs visual editor to create your equation
3. Click "Copy" to insert the equation into your post

= Can I use both Wiris and LaTeX editors? =

Yes! Go to Settings → Equation Editor and select "Both" as the editor type.

= What LaTeX syntax is supported? =

The Gutenberg block uses KaTeX for rendering, which supports most common LaTeX math commands. See the [KaTeX documentation](https://katex.org/docs/supported.html) for the full list.

= Is MathML supported? =

The Wiris editor supports MathML input and output. The Gutenberg block currently uses LaTeX only.

== Screenshots ==

1. Equation block in the Block Editor with live preview
2. Settings page with editor type selection
3. TinyMCE toolbar with equation buttons
4. Wiris math editor
5. Wiris chemistry editor
6. CodeCogs LaTeX editor

== Changelog ==

= 2.1.0 (2025-02-19) =

* **New**: Gutenberg Equation block with live KaTeX preview
* **New**: Display mode toggle (centered vs inline equations)
* **New**: KaTeX rendering for fast, high-quality equations
* **New**: Server-side rendering for dynamic content
* **Improved**: PHP 8.0+ with modern code architecture
* **Improved**: WordPress Settings API integration
* **Improved**: Comprehensive test suite (100+ tests)
* **Improved**: WordPress Coding Standards compliance
* **Security**: Input sanitization and output escaping

= 2.0.0 (2025-02-19) =

* **Breaking**: Requires PHP 8.0 or higher
* **New**: PSR-4 autoloading with namespaces
* **New**: Separated TinyMCE, Settings, and Plugin classes
* **New**: Plugin constants for paths and URLs
* **New**: Uninstall and deactivation hooks
* **Improved**: Complete code refactoring

= 1.9.0 (2025-02-19) =

* **New**: WordPress Settings API migration
* **New**: Settings link in plugins list
* **Improved**: Admin UI with WordPress standards

= 1.8.0 (2025-02-19) =

* **Security**: Input sanitization on settings save
* **Security**: Editor type validation
* **Security**: Output escaping in admin template

= 1.7 (2021-01-31) =

* Bug fixes and WordPress 5.6 compatibility

= 1.6 (2020-04-16) =

* WordPress 5.2 compatibility

= 1.5 (2019-05-15) =

* WordPress 5.2 compatibility

= 1.4 (2018-03-15) =

* Removed unwanted Pro message

= 1.3 (2018-03-08) =

* Minor fixes

= 1.2 (2018-02-24) =

* HTTPS issue resolved

= 1.1 (2018-01-31) =

* Fixed equation issues

== Upgrade Notice ==

= 2.1.0 =
New Gutenberg block with live KaTeX preview! Requires PHP 8.0+.

= 2.0.0 =
Major refactoring with modern PHP. Requires PHP 8.0 or higher.

== Developer Documentation ==

= Hooks and Filters =

The plugin provides several hooks for customization:

`mce_buttons` - Filter TinyMCE buttons
`mce_external_plugins` - Filter TinyMCE plugins
`equation_editor_settings` - Filter plugin settings

= Block Attributes =

The Equation block supports these attributes:

* `latex` (string) - The LaTeX equation source
* `displayMode` (boolean) - Whether to center the equation (default: true)

= File Structure =

`
equation-editor/
├── equation-editor.php      # Plugin bootstrap
├── includes/
│   ├── class-plugin.php     # Main plugin class
│   ├── class-settings.php   # Settings API handler
│   ├── class-tinymce.php    # TinyMCE integration
│   └── class-gutenberg.php  # Block Editor integration
├── assets/
│   ├── blocks/equation/     # Block source files
│   └── js/eq_editor.js      # TinyMCE plugin
├── build/blocks/equation/   # Compiled block assets
└── tiny_mce_wiris/          # Wiris editor (third-party)
`

= Building from Source =

`
npm install
npm run build
`

= Running Tests =

`
composer install
composer test
`
