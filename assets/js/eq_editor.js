/**
 * TinyMCE Equation Editor Plugin
 *
 * Integrates CodeCogs LaTeX editor with WordPress TinyMCE.
 * Opens a popup window for equation editing and inserts the result as an image.
 *
 * @package Equation_Editor
 * @since   1.0.0
 */

/**
 * Reference to the popup editor window.
 *
 * @type {Window|null}
 */
var popupEqnEditorwin = null;

( function() {
	/**
	 * Current page protocol (http: or https:).
	 *
	 * @type {string}
	 */
	var eq_protocol = window.location.protocol;

	/**
	 * Create the TinyMCE equation plugin.
	 */
	tinymce.create( 'tinymce.plugins.equation', {
		/**
		 * Initialize the plugin.
		 *
		 * @param {tinymce.Editor} ed  The TinyMCE editor instance.
		 * @param {string}         url The plugin URL.
		 */
		init: function( ed, url ) {
			/**
			 * Command to open the equation editor popup.
			 *
			 * @param {*}      a     Unused parameter.
			 * @param {string} latex Optional LaTeX string to edit.
			 */
			ed.addCommand( 'eqCmd', function( a, latex ) {
				if ( popupEqnEditorwin == null || popupEqnEditorwin.closed || ! popupEqnEditorwin.location ) {
					var editorUrl = eq_protocol + '//latex.codecogs.com/editor_json3.php?type=url&editor=TinyMCE';

					if ( latex !== undefined ) {
						latex = unescape( latex );
						latex = latex.replace( /\+/g, '&plus;' );
						editorUrl += '&latex=' + escape( latex );
					}

					popupEqnEditorwin = window.open( '', 'LaTexEditor', 'width=700,height=450,status=1,scrollbars=yes,resizable=1' );
					if ( ! popupEqnEditorwin.opener ) {
						popupEqnEditorwin.opener = self;
					}
					popupEqnEditorwin.document.open();
					popupEqnEditorwin.document.write( '<!DOCTYPE html><head><script src="' + editorUrl + '" type="text/javascript"></script></head><body></body></html>' );
					popupEqnEditorwin.document.close();
				} else if ( window.focus ) {
					popupEqnEditorwin.focus();
					if ( latex !== undefined ) {
						latex = unescape( latex );
						latex = latex.replace( /\\/g, '\\\\' );
						latex = latex.replace( /\'/g, '\\\'' );
						latex = latex.replace( /\"/g, '\\"' );
						latex = latex.replace( /\0/g, '\\0' );

						var old = popupEqnEditorwin.document.getElementById( 'JSONload' );
						if ( old != null ) {
							old.parentNode.removeChild( old );
						}

						var head = popupEqnEditorwin.document.getElementsByTagName( 'head' )[ 0 ];
						var script = document.createElement( 'script' );
						script.type = 'text/javascript';
						script.id = 'JSONload';
						script.innerHTML = 'EqEditor.load(\'' + latex + '\');';
						head.appendChild( script );
					}
				}
			} );

			// Add the equation button to the toolbar.
			ed.addButton( 'equation', {
				title: 'Equation Editor',
				image: url + '/images/fx.png',
				cmd: 'eqCmd',
			} );

			// Handle double-click on equation images to edit them.
			ed.onDblClick.add( function( ed, e ) {
				var protocol = window.location.protocol;
				if ( e.target.nodeName.toLowerCase() === 'img' ) {
					var pattern = protocol === 'https:'
						? /https:\/\/(latex\.codecogs\.com)\/(gif|svg)\.latex\?(.*)/
						: /http:\/\/(latex\.codecogs\.com)\/(gif|svg)\.latex\?(.*)/;

					var sName = e.target.src.match( pattern );
					if ( sName && sName[ 1 ] === 'latex.codecogs.com' ) {
						tinymce.execCommand( 'eqCmd', false, sName[ 3 ] );
					}
				}
			} );
		},

		/**
		 * Create control (not used).
		 *
		 * @return {null} Always returns null.
		 */
		createControl: function() {
			return null;
		},
	} );

	// Register the plugin with TinyMCE.
	tinymce.PluginManager.add( 'equation', tinymce.plugins.equation );
} )();

/**
 * Callback function called by CodeCogs editor to insert equation.
 *
 * @param {string} name The equation image URL from CodeCogs.
 */
function TinyMCE_Add( name ) {
	var eq_protocol = window.location.protocol;
	if ( eq_protocol === 'https:' ) {
		name = name.replace( /^http:\/\//i, 'https://' );
	}

	var sName = name.match( /(gif|svg)\.latex\?(.*)/ );
	var latex = unescape( sName[ 2 ] );
	latex = latex.replace( /@plus;/g, '+' );
	latex = latex.replace( /&plus;/g, '+' );
	latex = latex.replace( /&space;/g, ' ' );

	tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, '<img src="' + name + '" alt="' + latex + '" align="absmiddle" />' );
	tinyMCE.execCommand( 'mceFocus', false, tinymce.activeEditor.editorId );
}
