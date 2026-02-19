# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Equation Editor is a WordPress plugin that adds mathematical and scientific equation editing capabilities to the WordPress TinyMCE editor. It supports two editor backends: Wiris (math/chemistry formulas) and CodeCogs LaTeX.

## Architecture

### Main Plugin Entry
- `equation-editor.php` - Main plugin class (`mw_equation_editor`) that hooks into WordPress
  - Registers TinyMCE buttons via `mce_buttons` filter
  - Registers TinyMCE plugins via `mce_external_plugins` filter
  - Creates admin settings page under WordPress admin menu
  - Settings stored in `mw_equation_editor` option (enable/disable, editor type selection)

### Editor Backends

**LaTeX (CodeCogs)**
- `js/eq_editor.js` - TinyMCE plugin for CodeCogs LaTeX editor
- Opens external popup to CodeCogs editor, inserts equation images into content

**Wiris**
- `tiny_mce_wiris/` - Full Wiris editor integration
- `tiny_mce_wiris/editor_plugin.js` - TinyMCE plugin entry point (not present in tree, may be external)
- `tiny_mce_wiris/integration/` - PHP backend for Wiris service communication
- `tiny_mce_wiris/core/` - Client-side editor JS/CSS/HTML
- `tiny_mce_wiris/cache/` - Formula cache storage with language-specific files

### Admin Interface
- `admin/mw_equation_editor.php` - Settings page template (enable toggle, editor type dropdown)

## Development Notes

This is a WordPress plugin - no build process or package manager. Test by activating in a WordPress installation.

Settings page accessible at: WordPress Admin → Equation Editor

Editor type options: `wiris`, `latex`, `both`
