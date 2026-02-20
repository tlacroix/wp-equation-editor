/**
 * Frontend script to render equations with KaTeX.
 * Only loaded when equation blocks are present on the page.
 */
document.addEventListener( 'DOMContentLoaded', () => {
	const equations = document.querySelectorAll(
		'.wp-block-equation-editor-equation[data-latex]'
	);

	if ( ! equations.length || ! window.katex ) {
		return;
	}

	equations.forEach( ( element ) => {
		const latex = element.getAttribute( 'data-latex' );
		const displayMode = element.getAttribute( 'data-display-mode' ) === 'true';

		try {
			window.katex.render( latex, element, {
				displayMode,
				throwOnError: false,
			} );
		} catch ( error ) {
			// eslint-disable-next-line no-console
			console.error( 'KaTeX render error:', error );
			element.textContent = latex;
		}
	} );
} );
