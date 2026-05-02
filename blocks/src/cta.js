import { registerBlockType } from '@wordpress/blocks';
import { megaphone } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	SelectControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'brndle/cta', {
	icon: megaphone,

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Content', 'brndle' ) } initialOpen={ true }>
						<TextareaControl
							label={ __( 'Title', 'brndle' ) }
							value={ attributes.title }
							onChange={ ( v ) => setAttributes( { title: v } ) }
							help={ __( 'Supports HTML for styling', 'brndle' ) }
						/>
						<TextareaControl
							label={ __( 'Subtitle', 'brndle' ) }
							value={ attributes.subtitle }
							onChange={ ( v ) =>
								setAttributes( { subtitle: v } )
							}
						/>
					</PanelBody>

					<PanelBody title={ __( 'Buttons', 'brndle' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'Primary Button Text', 'brndle' ) }
							value={ attributes.cta_primary }
							onChange={ ( v ) =>
								setAttributes( { cta_primary: v } )
							}
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
						<SelectControl
							label={ __( 'Variant', 'brndle' ) }
							value={ attributes.variant }
							options={ [
								{ label: __( 'Dark', 'brndle' ), value: 'dark' },
								{ label: __( 'Light', 'brndle' ), value: 'light' },
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
