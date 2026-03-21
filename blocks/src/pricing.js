import { registerBlockType } from '@wordpress/blocks';
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
	icon: (
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
			<path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
		</svg>
	),

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
								label="Plan Name"
								value={ plan.name }
								onChange={ ( v ) =>
									updatePlan( i, 'name', v )
								}
							/>
							<TextareaControl
								label="Description"
								value={ plan.description }
								onChange={ ( v ) =>
									updatePlan( i, 'description', v )
								}
							/>
							<TextControl
								label="Price"
								value={ plan.price }
								onChange={ ( v ) =>
									updatePlan( i, 'price', v )
								}
								help="e.g., $29"
							/>
							<TextControl
								label="Period"
								value={ plan.period }
								onChange={ ( v ) =>
									updatePlan( i, 'period', v )
								}
								help="e.g., /month"
							/>
							<TextareaControl
								label="Features"
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
								help="One feature per line"
							/>
							<TextControl
								label="CTA Text"
								value={ plan.cta_text }
								onChange={ ( v ) =>
									updatePlan( i, 'cta_text', v )
								}
							/>
							<TextControl
								label="CTA URL"
								value={ plan.cta_url }
								onChange={ ( v ) =>
									updatePlan( i, 'cta_url', v )
								}
							/>
							<ToggleControl
								label="Featured Plan"
								checked={ !! plan.featured }
								onChange={ ( v ) =>
									updatePlan( i, 'featured', v )
								}
							/>
							{ plan.featured && (
								<TextControl
									label="Badge Text"
									value={ plan.badge }
									onChange={ ( v ) =>
										updatePlan( i, 'badge', v )
									}
									help="e.g., Most Popular"
								/>
							) }
							<Button
								isDestructive
								isSmall
								onClick={ () => removePlan( i ) }
							>
								Remove Plan
							</Button>
						</PanelBody>
					) ) }

					<PanelBody title="Add Plan" initialOpen={ true }>
						<Button variant="secondary" onClick={ addPlan }>
							Add Plan
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
