import { registerBlockType } from '@wordpress/blocks';
import { image } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	SelectControl,
	Button,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { ImageControl } from './components/image-control';

/**
 * Normalise the companies array — older posts stored an array of strings
 * ("Acme") while the new format stores objects ({ name, url, id, alt }).
 * The Blade template still accepts both, but the editor needs objects so
 * the controls can bind.
 */
function normaliseCompanies( companies ) {
	return ( companies || [] ).map( ( c ) =>
		typeof c === 'string'
			? { name: c, url: '', id: 0, alt: '' }
			: { name: '', url: '', id: 0, alt: '', ...c }
	);
}

registerBlockType( 'brndle/logos', {
	icon: image,

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		const companies = normaliseCompanies( attributes.companies );

		const updateCompany = ( index, patch ) => {
			const next = [ ...companies ];
			next[ index ] = { ...next[ index ], ...patch };
			setAttributes( { companies: next } );
		};

		const addCompany = () => {
			setAttributes( {
				companies: [
					...companies,
					{ name: '', url: '', id: 0, alt: '' },
				],
			} );
		};

		const removeCompany = ( index ) => {
			setAttributes( {
				companies: companies.filter( ( _, i ) => i !== index ),
			} );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Section Header', 'brndle' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'Title', 'brndle' ) }
							value={ attributes.title }
							onChange={ ( v ) => setAttributes( { title: v } ) }
							help={ __(
								'Small caption shown above the logo strip.',
								'brndle'
							) }
						/>
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

					{ companies.map( ( company, i ) => (
						<PanelBody
							key={ i }
							title={ `${ __( 'Logo', 'brndle' ) } ${ i + 1 }${
								company.name ? `: ${ company.name }` : ''
							}` }
							initialOpen={ false }
						>
							<TextControl
								label={ __( 'Company name', 'brndle' ) }
								value={ company.name || '' }
								onChange={ ( v ) =>
									updateCompany( i, { name: v } )
								}
								help={ __(
									'Falls back to bold text if no image is selected.',
									'brndle'
								) }
							/>
							<ImageControl
								label={ __( 'Logo image (optional)', 'brndle' ) }
								image={ company.url }
								imageId={ company.id }
								imageAlt={ company.alt || company.name }
								onChange={ ( {
									image,
									imageId,
									imageAlt,
								} ) =>
									updateCompany( i, {
										url: image,
										id: imageId,
										alt: imageAlt,
									} )
								}
							/>
							<Button
								isDestructive
								isSmall
								onClick={ () => removeCompany( i ) }
							>
								{ __( 'Remove logo', 'brndle' ) }
							</Button>
						</PanelBody>
					) ) }

					<PanelBody title={ __( 'Add Logo', 'brndle' ) } initialOpen={ true }>
						<Button variant="secondary" onClick={ addCompany }>
							{ __( 'Add logo entry', 'brndle' ) }
						</Button>
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
