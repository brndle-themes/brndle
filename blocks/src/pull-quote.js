/**
 * brndle/pull-quote — editorial pull quote with three variants.
 */

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { quote as quoteIcon } from '@wordpress/icons';
import {
	BlockControls,
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	RadioControl,
	ToolbarGroup,
	ToolbarButton,
} from '@wordpress/components';
import { useUniqueId } from './components/use-unique-id';

const VARIANTS = [
	{ value: 'bordered-left', label: __( 'Bordered left', 'brndle' ) },
	{ value: 'centered-large', label: __( 'Centered display', 'brndle' ) },
	{ value: 'outset', label: __( 'Outset', 'brndle' ) },
];

const ACCENT_COLORS = [
	{ value: 'accent', label: __( 'Accent', 'brndle' ) },
	{ value: 'text-primary', label: __( 'Primary text', 'brndle' ) },
	{ value: 'text-tertiary', label: __( 'Subdued', 'brndle' ) },
];

registerBlockType( 'brndle/pull-quote', {
	icon: quoteIcon,

	edit: ( { attributes, setAttributes, clientId } ) => {
		const { quote, cite, citeUrl, variant, accentColor } = attributes;
		useUniqueId( clientId, attributes, setAttributes );

		const blockProps = useBlockProps( {
			className: [
				'brndle-pull-quote-editor',
				`is-${ variant || 'bordered-left' }`,
				`is-accent-${ accentColor || 'accent' }`,
			].join( ' ' ),
		} );

		const cycleVariant = () => {
			const idx = VARIANTS.findIndex( ( v ) => v.value === variant );
			const next = VARIANTS[ ( idx + 1 ) % VARIANTS.length ].value;
			setAttributes( { variant: next } );
		};

		return (
			<>
				<BlockControls>
					<ToolbarGroup>
						<ToolbarButton
							icon={ quoteIcon }
							label={ __( 'Cycle variant', 'brndle' ) }
							onClick={ cycleVariant }
						>
							{ VARIANTS.find( ( v ) => v.value === variant )?.label || '' }
						</ToolbarButton>
					</ToolbarGroup>
				</BlockControls>

				<InspectorControls>
					<PanelBody title={ __( 'Variant', 'brndle' ) } initialOpen={ true }>
						<RadioControl
							label={ __( 'Visual variant', 'brndle' ) }
							selected={ variant || 'bordered-left' }
							options={ VARIANTS }
							onChange={ ( v ) => setAttributes( { variant: v } ) }
						/>
						<RadioControl
							label={ __( 'Accent color', 'brndle' ) }
							help={ __( 'Used by the bordered-left rule and the centered open-quote glyph.', 'brndle' ) }
							selected={ accentColor || 'accent' }
							options={ ACCENT_COLORS }
							onChange={ ( v ) => setAttributes( { accentColor: v } ) }
						/>
					</PanelBody>
					<PanelBody title={ __( 'Citation', 'brndle' ) } initialOpen={ false }>
						<TextControl
							label={ __( 'Cite URL', 'brndle' ) }
							help={ __( 'Optional link on the cite line.', 'brndle' ) }
							value={ citeUrl || '' }
							onChange={ ( v ) => setAttributes( { citeUrl: v } ) }
							type="url"
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</PanelBody>
				</InspectorControls>

				<figure { ...blockProps }>
					<blockquote className="brndle-pull-quote__body">
						<RichText
							tagName="p"
							className="brndle-pull-quote__text"
							value={ quote || '' }
							onChange={ ( v ) => setAttributes( { quote: v } ) }
							placeholder={ __( 'Enter the quote…', 'brndle' ) }
							allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
						/>
					</blockquote>
					<RichText
						tagName="figcaption"
						className="brndle-pull-quote__cite"
						value={ cite || '' }
						onChange={ ( v ) => setAttributes( { cite: v } ) }
						placeholder={ __( 'Optional cite (name, source)…', 'brndle' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
				</figure>
			</>
		);
	},

	save: () => null,
} );
