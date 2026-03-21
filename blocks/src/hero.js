import { registerBlockType } from '@wordpress/blocks';
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
							help="Supports HTML for styling (e.g., <span class='gradient-text'>)"
						/>
						<TextareaControl
							label="Subtitle"
							value={ attributes.subtitle }
							onChange={ ( v ) => setAttributes( { subtitle: v } ) }
						/>
					</PanelBody>

					<PanelBody title="Call to Action" initialOpen={ false }>
						<TextControl
							label="Primary Button Text"
							value={ attributes.cta_primary }
							onChange={ ( v ) => setAttributes( { cta_primary: v } ) }
						/>
						<TextControl
							label="Primary Button URL"
							value={ attributes.cta_primary_url }
							onChange={ ( v ) =>
								setAttributes( { cta_primary_url: v } )
							}
						/>
						<TextControl
							label="Secondary Button Text"
							value={ attributes.cta_secondary }
							onChange={ ( v ) =>
								setAttributes( { cta_secondary: v } )
							}
						/>
						<TextControl
							label="Secondary Button URL"
							value={ attributes.cta_secondary_url }
							onChange={ ( v ) =>
								setAttributes( { cta_secondary_url: v } )
							}
						/>
					</PanelBody>

					<PanelBody title="Settings" initialOpen={ false }>
						<TextControl
							label="Image URL"
							value={ attributes.image }
							onChange={ ( v ) => setAttributes( { image: v } ) }
						/>
						<SelectControl
							label="Variant"
							value={ attributes.variant }
							options={ [
								{ label: 'Dark', value: 'dark' },
								{ label: 'Light', value: 'light' },
								{ label: 'Gradient', value: 'gradient' },
							] }
							onChange={ ( v ) => setAttributes( { variant: v } ) }
						/>
					</PanelBody>

					<PanelBody title="Logo Strip" initialOpen={ false }>
						<TextareaControl
							label="Logos"
							value={ ( attributes.logos || [] ).join( '\n' ) }
							onChange={ ( v ) =>
								setAttributes( {
									logos: v
										.split( '\n' )
										.filter( ( l ) => l.trim() ),
								} )
							}
							help="One company name per line"
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
