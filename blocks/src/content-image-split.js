import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	SelectControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

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
					<PanelBody title="Content" initialOpen={ true }>
						<TextControl
							label="Eyebrow"
							value={ attributes.eyebrow }
							onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
						/>
						<TextareaControl
							label="Title"
							value={ attributes.title }
							onChange={ ( v ) => setAttributes( { title: v } ) }
							help="Supports HTML for styling"
						/>
						<TextareaControl
							label="Description"
							value={ attributes.description }
							onChange={ ( v ) => setAttributes( { description: v } ) }
						/>
						<TextareaControl
							label="Bullet Points"
							value={ bullets.join( '\n' ) }
							onChange={ ( v ) =>
								setAttributes( {
									bullets: v.split( '\n' ).filter( ( l ) => l.trim() ),
								} )
							}
							help="One bullet per line"
						/>
					</PanelBody>
					<PanelBody title="Image" initialOpen={ true }>
						<TextControl
							label="Image URL"
							value={ attributes.image }
							onChange={ ( v ) => setAttributes( { image: v } ) }
						/>
						<TextControl
							label="Image Alt Text"
							value={ attributes.image_alt }
							onChange={ ( v ) => setAttributes( { image_alt: v } ) }
						/>
						<SelectControl
							label="Image Position"
							value={ attributes.image_position }
							options={ [
								{ label: 'Right', value: 'right' },
								{ label: 'Left', value: 'left' },
							] }
							onChange={ ( v ) =>
								setAttributes( { image_position: v } )
							}
						/>
					</PanelBody>
					<PanelBody title="Call to Action" initialOpen={ false }>
						<TextControl
							label="Button Text"
							value={ attributes.cta_text }
							onChange={ ( v ) => setAttributes( { cta_text: v } ) }
						/>
						<TextControl
							label="Button URL"
							value={ attributes.cta_url }
							onChange={ ( v ) => setAttributes( { cta_url: v } ) }
						/>
					</PanelBody>
					<PanelBody title="Settings" initialOpen={ false }>
						<SelectControl
							label="Variant"
							value={ attributes.variant }
							options={ [
								{ label: 'Light', value: 'light' },
								{ label: 'Dark', value: 'dark' },
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
