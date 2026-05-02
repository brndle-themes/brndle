import { registerBlockType } from '@wordpress/blocks';
import { tag } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	SelectControl,
	ToggleControl,
	Button,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'brndle/pricing', {
	icon: tag,

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		const plans = attributes.plans || [];

		const updatePlan = ( index, key, value ) => {
			const newPlans = [ ...plans ];
			newPlans[ index ] = { ...newPlans[ index ], [ key ]: value };
			setAttributes( { plans: newPlans } );
		};

		const addPlan = () => {
			setAttributes( {
				plans: [
					...plans,
					{
						name: '',
						description: '',
						price: '',
						period: '',
						features: [],
						cta_text: '',
						cta_url: '#',
						featured: false,
						badge: '',
					},
				],
			} );
		};

		const removePlan = ( index ) => {
			setAttributes( {
				plans: plans.filter( ( _, i ) => i !== index ),
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
							] }
							onChange={ ( v ) => setAttributes( { variant: v } ) }
						/>
					</PanelBody>

					{ plans.map( ( plan, i ) => (
						<PanelBody
							key={ i }
							title={ `Plan ${ i + 1 }${
								plan.name ? `: ${ plan.name }` : ''
							}` }
							initialOpen={ false }
						>
							<TextControl
								label={ __( 'Plan Name', 'brndle' ) }
								value={ plan.name }
								onChange={ ( v ) =>
									updatePlan( i, 'name', v )
								}
							/>
							<TextareaControl
								label={ __( 'Description', 'brndle' ) }
								value={ plan.description }
								onChange={ ( v ) =>
									updatePlan( i, 'description', v )
								}
							/>
							<TextControl
								label={ __( 'Price', 'brndle' ) }
								value={ plan.price }
								onChange={ ( v ) =>
									updatePlan( i, 'price', v )
								}
								help={ __( 'e.g., $29', 'brndle' ) }
							/>
							<TextControl
								label={ __( 'Period', 'brndle' ) }
								value={ plan.period }
								onChange={ ( v ) =>
									updatePlan( i, 'period', v )
								}
								help={ __( 'e.g., /month', 'brndle' ) }
							/>
							<TextareaControl
								label={ __( 'Features', 'brndle' ) }
								value={ (
									plan.features || []
								).join( '\n' ) }
								onChange={ ( v ) =>
									updatePlan(
										i,
										'features',
										v
											.split( '\n' )
											.filter( ( l ) => l.trim() )
									)
								}
								help={ __( 'One feature per line', 'brndle' ) }
							/>
							<TextControl
								label={ __( 'CTA Text', 'brndle' ) }
								value={ plan.cta_text }
								onChange={ ( v ) =>
									updatePlan( i, 'cta_text', v )
								}
							/>
							<TextControl
								label={ __( 'CTA URL', 'brndle' ) }
								value={ plan.cta_url }
								onChange={ ( v ) =>
									updatePlan( i, 'cta_url', v )
								}
							/>
							<ToggleControl
								label={ __( 'Featured Plan', 'brndle' ) }
								checked={ !! plan.featured }
								onChange={ ( v ) =>
									updatePlan( i, 'featured', v )
								}
							/>
							{ plan.featured && (
								<TextControl
									label={ __( 'Badge Text', 'brndle' ) }
									value={ plan.badge }
									onChange={ ( v ) =>
										updatePlan( i, 'badge', v )
									}
									help={ __( 'e.g., Most Popular', 'brndle' ) }
								/>
							) }
							<Button
								isDestructive
								isSmall
								onClick={ () => removePlan( i ) }
							>
								{ __( 'Remove Plan', 'brndle' ) }
							</Button>
						</PanelBody>
					) ) }

					<PanelBody title={ __( 'Add Plan', 'brndle' ) } initialOpen={ true }>
						<Button variant="secondary" onClick={ addPlan }>
							{ __( 'Add Plan', 'brndle' ) }
						</Button>
					</PanelBody>
				</InspectorControls>

				<div { ...blockProps }>
					<ServerSideRender
						block="brndle/pricing"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
