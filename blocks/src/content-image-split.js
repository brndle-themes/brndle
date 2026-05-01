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
import { ImageControl } from './components/image-control';

registerBlockType( 'brndle/content-image-split', {
	icon: (
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
			<rect x="1" y="3" width="10" height="18" rx="1" />
			<rect x="13" y="3" width="10" height="18" rx="1" />
		</svg>
	),

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		const bullets = attributes.bullets || [];

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
							help={ __( 'Supports HTML for styling', 'brndle' ) }
						/>
						<TextareaControl
							label={ __( 'Description', 'brndle' ) }
							value={ attributes.description }
							onChange={ ( v ) => setAttributes( { description: v } ) }
						/>
						<TextareaControl
							label={ __( 'Bullet Points', 'brndle' ) }
							value={ bullets.join( '\n' ) }
							onChange={ ( v ) =>
								setAttributes( {
									bullets: v.split( '\n' ).filter( ( l ) => l.trim() ),
								} )
							}
							help={ __( 'One bullet per line', 'brndle' ) }
						/>
					</PanelBody>
					<PanelBody title={ __( 'Image', 'brndle' ) } initialOpen={ true }>
						<ImageControl
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
						<SelectControl
							label={ __( 'Image Position', 'brndle' ) }
							value={ attributes.image_position }
							options={ [
								{ label: __( 'Right', 'brndle' ), value: 'right' },
								{ label: __( 'Left', 'brndle' ), value: 'left' },
							] }
							onChange={ ( v ) =>
								setAttributes( { image_position: v } )
							}
						/>
					</PanelBody>
					<PanelBody title={ __( 'Call to Action', 'brndle' ) } initialOpen={ false }>
						<TextControl
							label={ __( 'Button Text', 'brndle' ) }
							value={ attributes.cta_text }
							onChange={ ( v ) => setAttributes( { cta_text: v } ) }
						/>
						<TextControl
							label={ __( 'Button URL', 'brndle' ) }
							value={ attributes.cta_url }
							onChange={ ( v ) => setAttributes( { cta_url: v } ) }
						/>
					</PanelBody>
					<PanelBody title={ __( 'Settings', 'brndle' ) } initialOpen={ false }>
						<SelectControl
							label={ __( 'Variant', 'brndle' ) }
							value={ attributes.variant }
							options={ [
								{ label: __( 'Light', 'brndle' ), value: 'light' },
								{ label: __( 'Dark', 'brndle' ), value: 'dark' },
							] }
							onChange={ ( v ) => setAttributes( { variant: v } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<ServerSideRender
						block="brndle/content-image-split"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
