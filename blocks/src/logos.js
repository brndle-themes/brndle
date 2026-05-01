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

registerBlockType( 'brndle/logos', {
	icon: (
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
			<rect x="2" y="7" width="6" height="6" rx="1" />
			<rect x="9" y="7" width="6" height="6" rx="1" />
			<rect x="16" y="7" width="6" height="6" rx="1" />
		</svg>
	),

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Content', 'brndle' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'Title', 'brndle' ) }
							value={ attributes.title }
							onChange={ ( v ) => setAttributes( { title: v } ) }
						/>
						<TextareaControl
							label={ __( 'Companies', 'brndle' ) }
							value={ ( attributes.companies || [] )
								.map( ( c ) =>
									typeof c === 'string' ? c : c.name || ''
								)
								.join( '\n' ) }
							onChange={ ( v ) =>
								setAttributes( {
									companies: v
										.split( '\n' )
										.filter( ( l ) => l.trim() ),
								} )
							}
							help={ __( 'One company name per line', 'brndle' ) }
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
						block="brndle/logos"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
