/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Save component - outputs data attributes for frontend rendering.
 *
 * @param {Object} props            Block props.
 * @param {Object} props.attributes Block attributes.
 * @return {Element|null} Saved block element or null if empty.
 */
export default function save( { attributes } ) {
	const { latex, displayMode } = attributes;

	if ( ! latex ) {
		return null;
	}

	const blockProps = useBlockProps.save( {
		className: displayMode ? 'is-display-mode' : 'is-inline-mode',
		'data-latex': latex,
		'data-display-mode': displayMode ? 'true' : 'false',
	} );

	return <div { ...blockProps } />;
}
