import { registerBlockType } from '@wordpress/blocks';
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
	icon: (
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
			<rect x="3" y="3" width="8" height="8" rx="1" />
			<rect x="13" y="3" width="8" height="8" rx="1" />
			<rect x="3" y="13" width="8" height="8" rx="1" />
			<rect x="13" y="13" width="8" height="8" rx="1" />
		</svg>
	),

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
					<PanelBody title="Section Header" initialOpen={ true }>
						<TextControl
							label="Eyebrow"
							value={ attributes.eyebrow }
							onChange={ ( v ) =>
								setAttributes( { eyebrow: v } )
							}
						/>
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
						<SelectControl
							label="Variant"
							value={ attributes.variant }
							options={ [
								{ label: 'Light', value: 'light' },
								{ label: 'Dark', value: 'dark' },
								{ label: 'Subtle', value: 'subtle' },
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
								label="Title"
								value={ feature.title }
								onChange={ ( v ) =>
									updateFeature( i, 'title', v )
								}
							/>
							<TextareaControl
								label="Description"
								value={ feature.description }
								onChange={ ( v ) =>
									updateFeature( i, 'description', v )
								}
							/>
							<TextareaControl
								label="Bullet Points"
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
								help="One bullet point per line"
							/>
							<TextControl
								label="Image URL"
								value={ feature.image }
								onChange={ ( v ) =>
									updateFeature( i, 'image', v )
								}
							/>
							<SelectControl
								label="Icon"
								value={ feature.icon }
								options={ [
									{ label: '— None —', value: '' },
									{ label: 'Globe', value: 'globe-alt' },
									{ label: 'Academic Cap', value: 'academic-cap' },
									{ label: 'Dollar', value: 'currency-dollar' },
									{ label: 'Shopping Cart', value: 'shopping-cart' },
									{ label: 'Search', value: 'magnifying-glass' },
									{ label: 'Target', value: 'cursor-arrow-rays' },
									{ label: 'Map Pin', value: 'map-pin' },
									{ label: 'Chat', value: 'chat-bubble-left-right' },
									{ label: 'Video', value: 'video-camera' },
									{ label: 'Book', value: 'book-open' },
									{ label: 'Chart Bar', value: 'chart-bar' },
									{ label: 'Trending Up', value: 'arrow-trending-up' },
									{ label: 'Lock', value: 'lock-closed' },
									{ label: 'Star', value: 'star' },
									{ label: 'Sparkles', value: 'sparkles' },
									{ label: 'Rocket', value: 'rocket-launch' },
									{ label: 'Document', value: 'document-text' },
									{ label: 'Pencil', value: 'pencil-square' },
									{ label: 'Office', value: 'building-office' },
									{ label: 'Storefront', value: 'building-storefront' },
									{ label: 'Users', value: 'users' },
									{ label: 'Heart', value: 'heart' },
									{ label: 'Shield', value: 'shield-check' },
									{ label: 'Wrench', value: 'wrench' },
									{ label: 'Light Bulb', value: 'light-bulb' },
									{ label: 'Phone', value: 'device-phone-mobile' },
									{ label: 'Paint Brush', value: 'paint-brush' },
									{ label: 'Trophy', value: 'trophy' },
									{ label: 'Ticket', value: 'ticket' },
									{ label: 'Check Circle', value: 'check-circle' },
									{ label: 'Settings', value: 'cog-6-tooth' },
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
								Remove Feature
							</Button>
						</PanelBody>
					) ) }

					<PanelBody title="Add Feature" initialOpen={ true }>
						<Button variant="secondary" onClick={ addFeature }>
							Add Feature
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
