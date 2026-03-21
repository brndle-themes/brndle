import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	SelectControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'brndle/cta', {
	icon: (
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
			<rect x="3" y="5" width="18" height="14" rx="2" />
			<path d="M8 15h8M10 11h4" />
		</svg>
	),

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();

		return (
			<>
				<InspectorControls>
					<PanelBody title="Content" initialOpen={ true }>
						<TextareaControl
							label="Title"
							value={ attributes.title }
							onChange={ ( v ) => setAttributes( { title: v } ) }
							help="Supports HTML for styling"
						/>
						<TextareaControl
							label="Subtitle"
							value={ attributes.subtitle }
							onChange={ ( v ) =>
								setAttributes( { subtitle: v } )
							}
						/>
					</PanelBody>

					<PanelBody title="Buttons" initialOpen={ true }>
						<TextControl
							label="Primary Button Text"
							value={ attributes.cta_primary }
							onChange={ ( v ) =>
								setAttributes( { cta_primary: v } )
							}
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
						<SelectControl
							label="Variant"
							value={ attributes.variant }
							options={ [
								{ label: 'Dark', value: 'dark' },
								{ label: 'Light', value: 'light' },
							] }
							onChange={ ( v ) => setAttributes( { variant: v } ) }
						/>
					</PanelBody>
				</InspectorControls>

				<div { ...blockProps }>
					<ServerSideRender
						block="brndle/cta"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
