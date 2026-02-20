/**
 * Frontend Equation Renderer
 *
 * Renders LaTeX equations on the frontend using KaTeX.
 * This script is only loaded when equation blocks are present on the page.
 *
 * @package Equation_Editor
 * @since   2.1.0
 */

/**
 * Initialize KaTeX rendering for all equation blocks on the page.
 *
 * Finds all elements with the equation block class and data-latex attribute,
 * then renders them using KaTeX with the specified display mode.
 */
document.addEventListener( 'DOMContentLoaded', function() {
	/**
	 * All equation block elements on the page.
	 *
	 * @type {NodeListOf<Element>}
	 */
	var equations = document.querySelectorAll(
		'.wp-block-equation-editor-equation[data-latex]'
	);

	// Exit early if no equations or KaTeX not loaded.
	if ( ! equations.length || ! window.katex ) {
		return;
	}

	/**
	 * Render each equation element with KaTeX.
	 */
	equations.forEach( function( element ) {
		/** @type {string} The LaTeX source code. */
		var latex = element.getAttribute( 'data-latex' );

		/** @type {boolean} Whether to use display mode (centered) or inline. */
		var displayMode = element.getAttribute( 'data-display-mode' ) === 'true';

		try {
			window.katex.render( latex, element, {
				displayMode: displayMode,
				throwOnError: false,
			} );
		} catch ( error ) {
			// Log error and show raw LaTeX as fallback.
			// eslint-disable-next-line no-console
			console.error( 'KaTeX render error:', error );
			element.textContent = latex;
		}
	} );
} );
