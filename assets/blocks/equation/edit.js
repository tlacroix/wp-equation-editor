/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextareaControl, ToggleControl } from '@wordpress/components';
import { useEffect, useRef, useState } from '@wordpress/element';

/**
 * Editor component for the Equation block.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to update attributes.
 * @return {Element} Block editor element.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { latex, displayMode } = attributes;
	const previewRef = useRef( null );
	const [ error, setError ] = useState( null );

	const blockProps = useBlockProps( {
		className: displayMode ? 'is-display-mode' : 'is-inline-mode',
	} );

	// Render KaTeX preview
	useEffect( () => {
		if ( ! previewRef.current || ! window.katex ) {
			return;
		}

		if ( ! latex ) {
			previewRef.current.textContent = '';
			setError( null );
			return;
		}

		try {
			window.katex.render( latex, previewRef.current, {
				displayMode,
				throwOnError: true,
				errorColor: '#cc0000',
			} );
			setError( null );
		} catch ( err ) {
			setError( err.message );
			previewRef.current.textContent = latex;
		}
	}, [ latex, displayMode ] );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Equation Settings', 'equation-editor' ) }>
					<ToggleControl
						label={ __( 'Display Mode', 'equation-editor' ) }
						help={
							displayMode
								? __( 'Equation centered on its own line', 'equation-editor' )
								: __( 'Equation inline with text', 'equation-editor' )
						}
						checked={ displayMode }
						onChange={ ( value ) => setAttributes( { displayMode: value } ) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<TextareaControl
					label={ __( 'LaTeX Equation', 'equation-editor' ) }
					value={ latex }
					onChange={ ( value ) => setAttributes( { latex: value } ) }
					placeholder={ __( 'Enter LaTeX equation (e.g., E = mc^2)', 'equation-editor' ) }
					rows={ 3 }
				/>

				<div className="equation-editor-preview">
					<span className="equation-editor-preview-label">
						{ __( 'Preview:', 'equation-editor' ) }
					</span>
					{ error && (
						<div className="equation-editor-error">
							{ __( 'Error:', 'equation-editor' ) } { error }
						</div>
					) }
					<div ref={ previewRef } className="equation-editor-katex-output" />
				</div>
			</div>
		</>
	);
}
