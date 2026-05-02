import { registerBlockType } from '@wordpress/blocks';
import { gallery } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	SelectControl,
	Button,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'brndle/features', {
	icon: gallery,

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		const features = attributes.features || [];

		const updateFeature = ( index, key, value ) => {
			const newFeatures = [ ...features ];
			newFeatures[ index ] = { ...newFeatures[ index ], [ key ]: value };
			setAttributes( { features: newFeatures } );
		};

		const addFeature = () => {
			setAttributes( {
				features: [
					...features,
					{
						title: '',
						description: '',
						bullets: [],
						image: '',
						icon: '',
					},
				],
			} );
		};

		const removeFeature = ( index ) => {
			setAttributes( {
				features: features.filter( ( _, i ) => i !== index ),
			} );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Section Header', 'brndle' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'Eyebrow', 'brndle' ) }
							value={ attributes.eyebrow }
							onChange={ ( v ) =>
								setAttributes( { eyebrow: v } )
							}
						/>
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
						<SelectControl
							label={ __( 'Variant', 'brndle' ) }
							value={ attributes.variant }
							options={ [
								{ label: __( 'Light', 'brndle' ), value: 'light' },
								{ label: __( 'Dark', 'brndle' ), value: 'dark' },
								{ label: __( 'Subtle', 'brndle' ), value: 'subtle' },
							] }
							onChange={ ( v ) => setAttributes( { variant: v } ) }
						/>
					</PanelBody>

					{ features.map( ( feature, i ) => (
						<PanelBody
							key={ i }
							title={ `Feature ${ i + 1 }${
								feature.title ? `: ${ feature.title }` : ''
							}` }
							initialOpen={ false }
						>
							<TextControl
								label={ __( 'Title', 'brndle' ) }
								value={ feature.title }
								onChange={ ( v ) =>
									updateFeature( i, 'title', v )
								}
							/>
							<TextareaControl
								label={ __( 'Description', 'brndle' ) }
								value={ feature.description }
								onChange={ ( v ) =>
									updateFeature( i, 'description', v )
								}
							/>
							<TextareaControl
								label={ __( 'Bullet Points', 'brndle' ) }
								value={ (
									feature.bullets || []
								).join( '\n' ) }
								onChange={ ( v ) =>
									updateFeature(
										i,
										'bullets',
										v
											.split( '\n' )
											.filter( ( l ) => l.trim() )
									)
								}
								help={ __( 'One bullet point per line', 'brndle' ) }
							/>
							<TextControl
								label={ __( 'Image URL', 'brndle' ) }
								value={ feature.image }
								onChange={ ( v ) =>
									updateFeature( i, 'image', v )
								}
							/>
							<SelectControl
								label={ __( 'Icon', 'brndle' ) }
								value={ feature.icon }
								options={ [
									{ label: __( '— None —', 'brndle' ), value: '' },
									{ label: __( 'Globe', 'brndle' ), value: 'globe-alt' },
									{ label: __( 'Academic Cap', 'brndle' ), value: 'academic-cap' },
									{ label: __( 'Dollar', 'brndle' ), value: 'currency-dollar' },
									{ label: __( 'Shopping Cart', 'brndle' ), value: 'shopping-cart' },
									{ label: __( 'Search', 'brndle' ), value: 'magnifying-glass' },
									{ label: __( 'Target', 'brndle' ), value: 'cursor-arrow-rays' },
									{ label: __( 'Map Pin', 'brndle' ), value: 'map-pin' },
									{ label: __( 'Chat', 'brndle' ), value: 'chat-bubble-left-right' },
									{ label: __( 'Video', 'brndle' ), value: 'video-camera' },
									{ label: __( 'Book', 'brndle' ), value: 'book-open' },
									{ label: __( 'Chart Bar', 'brndle' ), value: 'chart-bar' },
									{ label: __( 'Trending Up', 'brndle' ), value: 'arrow-trending-up' },
									{ label: __( 'Lock', 'brndle' ), value: 'lock-closed' },
									{ label: __( 'Star', 'brndle' ), value: 'star' },
									{ label: __( 'Sparkles', 'brndle' ), value: 'sparkles' },
									{ label: __( 'Rocket', 'brndle' ), value: 'rocket-launch' },
									{ label: __( 'Document', 'brndle' ), value: 'document-text' },
									{ label: __( 'Pencil', 'brndle' ), value: 'pencil-square' },
									{ label: __( 'Office', 'brndle' ), value: 'building-office' },
									{ label: __( 'Storefront', 'brndle' ), value: 'building-storefront' },
									{ label: __( 'Users', 'brndle' ), value: 'users' },
									{ label: __( 'Heart', 'brndle' ), value: 'heart' },
									{ label: __( 'Shield', 'brndle' ), value: 'shield-check' },
									{ label: __( 'Wrench', 'brndle' ), value: 'wrench' },
									{ label: __( 'Light Bulb', 'brndle' ), value: 'light-bulb' },
									{ label: __( 'Phone', 'brndle' ), value: 'device-phone-mobile' },
									{ label: __( 'Paint Brush', 'brndle' ), value: 'paint-brush' },
									{ label: __( 'Trophy', 'brndle' ), value: 'trophy' },
									{ label: __( 'Ticket', 'brndle' ), value: 'ticket' },
									{ label: __( 'Check Circle', 'brndle' ), value: 'check-circle' },
									{ label: __( 'Settings', 'brndle' ), value: 'cog-6-tooth' },
								] }
								onChange={ ( v ) =>
									updateFeature( i, 'icon', v )
								}
							/>
							<Button
								isDestructive
								isSmall
								onClick={ () => removeFeature( i ) }
							>
								{ __( 'Remove Feature', 'brndle' ) }
							</Button>
						</PanelBody>
					) ) }

					<PanelBody title={ __( 'Add Feature', 'brndle' ) } initialOpen={ true }>
						<Button variant="secondary" onClick={ addFeature }>
							{ __( 'Add Feature', 'brndle' ) }
						</Button>
					</PanelBody>
				</InspectorControls>

				<div { ...blockProps }>
					<ServerSideRender
						block="brndle/features"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
