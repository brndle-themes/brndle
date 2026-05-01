import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	SelectControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'brndle/hero', {
	icon: (
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
			<rect x="2" y="3" width="20" height="18" rx="2" />
			<line x1="6" y1="8" x2="18" y2="8" />
			<line x1="8" y1="12" x2="16" y2="12" />
			<line x1="10" y1="16" x2="14" y2="16" />
		</svg>
	),

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Content', 'brndle' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'Eyebrow', 'brndle' ) }
							value={ attributes.eyebrow }
							onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
						/>
						<TextareaControl
							label={ __( 'Title', 'brndle' ) }
							value={ attributes.title }
							onChange={ ( v ) => setAttributes( { title: v } ) }
							help={ __( 'Supports HTML for styling (e.g., <span class=\'gradient-text\'>)', 'brndle' ) }
						/>
						<TextareaControl
							label={ __( 'Subtitle', 'brndle' ) }
							value={ attributes.subtitle }
							onChange={ ( v ) => setAttributes( { subtitle: v } ) }
						/>
					</PanelBody>

					<PanelBody title={ __( 'Call to Action', 'brndle' ) } initialOpen={ false }>
						<TextControl
							label={ __( 'Primary Button Text', 'brndle' ) }
							value={ attributes.cta_primary }
							onChange={ ( v ) => setAttributes( { cta_primary: v } ) }
						/>
						<TextControl
							label={ __( 'Primary Button URL', 'brndle' ) }
							value={ attributes.cta_primary_url }
							onChange={ ( v ) =>
								setAttributes( { cta_primary_url: v } )
							}
						/>
						<TextControl
							label={ __( 'Secondary Button Text', 'brndle' ) }
							value={ attributes.cta_secondary }
							onChange={ ( v ) =>
								setAttributes( { cta_secondary: v } )
							}
						/>
						<TextControl
							label={ __( 'Secondary Button URL', 'brndle' ) }
							value={ attributes.cta_secondary_url }
							onChange={ ( v ) =>
								setAttributes( { cta_secondary_url: v } )
							}
						/>
					</PanelBody>

					<PanelBody title={ __( 'Settings', 'brndle' ) } initialOpen={ false }>
						<TextControl
							label={ __( 'Image URL', 'brndle' ) }
							value={ attributes.image }
							onChange={ ( v ) => setAttributes( { image: v } ) }
						/>
						<SelectControl
							label={ __( 'Variant', 'brndle' ) }
							value={ attributes.variant }
							options={ [
								{ label: __( 'Dark', 'brndle' ), value: 'dark' },
								{ label: __( 'Light', 'brndle' ), value: 'light' },
								{ label: __( 'Gradient', 'brndle' ), value: 'gradient' },
							] }
							onChange={ ( v ) => setAttributes( { variant: v } ) }
						/>
					</PanelBody>

					<PanelBody title={ __( 'Logo Strip', 'brndle' ) } initialOpen={ false }>
						<TextareaControl
							label={ __( 'Logos', 'brndle' ) }
							value={ ( attributes.logos || [] ).join( '\n' ) }
							onChange={ ( v ) =>
								setAttributes( {
									logos: v
										.split( '\n' )
										.filter( ( l ) => l.trim() ),
								} )
							}
							help={ __( 'One company name per line', 'brndle' ) }
						/>
					</PanelBody>
				</InspectorControls>

				<div { ...blockProps }>
					<ServerSideRender
						block="brndle/hero"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
