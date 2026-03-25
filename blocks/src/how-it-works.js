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

registerBlockType( 'brndle/how-it-works', {
	icon: (
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
			<circle cx="6" cy="5" r="3" />
			<circle cx="18" cy="5" r="3" />
			<circle cx="6" cy="19" r="3" />
			<circle cx="18" cy="19" r="3" />
			<line x1="9" y1="5" x2="15" y2="5" />
			<line x1="6" y1="8" x2="6" y2="16" />
			<line x1="18" y1="8" x2="18" y2="16" />
		</svg>
	),

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		const steps = attributes.steps || [];

		const updateStep = ( index, key, value ) => {
			const newSteps = [ ...steps ];
			newSteps[ index ] = { ...newSteps[ index ], [ key ]: value };
			setAttributes( { steps: newSteps } );
		};

		const addStep = () => {
			setAttributes( {
				steps: [ ...steps, { title: '', description: '', icon: '' } ],
			} );
		};

		const removeStep = ( index ) => {
			setAttributes( {
				steps: steps.filter( ( _, i ) => i !== index ),
			} );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title="Section Header" initialOpen={ true }>
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
							label="Subtitle"
							value={ attributes.subtitle }
							onChange={ ( v ) => setAttributes( { subtitle: v } ) }
						/>
						<SelectControl
							label="Layout"
							value={ attributes.layout }
							options={ [
								{ label: 'Horizontal (cards)', value: 'horizontal' },
								{ label: 'Vertical (timeline)', value: 'vertical' },
							] }
							onChange={ ( v ) => setAttributes( { layout: v } ) }
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
					{ steps.map( ( step, i ) => (
						<PanelBody
							key={ i }
							title={ `Step ${ i + 1 }${
								step.title ? `: ${ step.title }` : ''
							}` }
							initialOpen={ false }
						>
							<TextControl
								label="Title"
								value={ step.title }
								onChange={ ( v ) => updateStep( i, 'title', v ) }
							/>
							<TextareaControl
								label="Description"
								value={ step.description }
								onChange={ ( v ) =>
									updateStep( i, 'description', v )
								}
							/>
							<TextControl
								label="Icon (emoji or text)"
								value={ step.icon }
								onChange={ ( v ) => updateStep( i, 'icon', v ) }
								help="Leave empty to show step number"
							/>
							<Button
								isDestructive
								isSmall
								onClick={ () => removeStep( i ) }
							>
								Remove Step
							</Button>
						</PanelBody>
					) ) }
					<PanelBody title="Add Step" initialOpen={ true }>
						<Button variant="secondary" onClick={ addStep }>
							Add Step
						</Button>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<ServerSideRender
						block="brndle/how-it-works"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
