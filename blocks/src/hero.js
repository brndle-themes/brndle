import { registerBlockType } from '@wordpress/blocks';
import { cover } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	SelectControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { ImageControl } from './components/image-control';

registerBlockType( 'brndle/hero', {
	icon: cover,

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

					<PanelBody title={ __( 'Image', 'brndle' ) } initialOpen={ false }>
						<ImageControl
							label={ __( 'Hero image', 'brndle' ) }
							image={ attributes.image }
							imageId={ attributes.image_id }
							imageAlt={ attributes.image_alt }
							onChange={ ( {
								image,
								imageId,
								imageAlt,
							} ) =>
								setAttributes( {
									image,
									image_id: imageId,
									image_alt: imageAlt,
								} )
							}
						/>
					</PanelBody>

					<PanelBody title={ __( 'Settings', 'brndle' ) } initialOpen={ false }>
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
