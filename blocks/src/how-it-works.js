import { registerBlockType } from '@wordpress/blocks';
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
					<PanelBody title={ __( 'Section Header', 'brndle' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'Eyebrow', 'brndle' ) }
							value={ attributes.eyebrow }
							onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
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
							onChange={ ( v ) => setAttributes( { subtitle: v } ) }
						/>
						<SelectControl
							label={ __( 'Layout', 'brndle' ) }
							value={ attributes.layout }
							options={ [
								{ label: __( 'Horizontal (cards)', 'brndle' ), value: 'horizontal' },
								{ label: __( 'Vertical (timeline)', 'brndle' ), value: 'vertical' },
							] }
							onChange={ ( v ) => setAttributes( { layout: v } ) }
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
					{ steps.map( ( step, i ) => (
						<PanelBody
							key={ i }
							title={ `Step ${ i + 1 }${
								step.title ? `: ${ step.title }` : ''
							}` }
							initialOpen={ false }
						>
							<TextControl
								label={ __( 'Title', 'brndle' ) }
								value={ step.title }
								onChange={ ( v ) => updateStep( i, 'title', v ) }
							/>
							<TextareaControl
								label={ __( 'Description', 'brndle' ) }
								value={ step.description }
								onChange={ ( v ) =>
									updateStep( i, 'description', v )
								}
							/>
							<TextControl
								label={ __( 'Icon (emoji or text)', 'brndle' ) }
								value={ step.icon }
								onChange={ ( v ) => updateStep( i, 'icon', v ) }
								help={ __( 'Leave empty to show step number', 'brndle' ) }
							/>
							<Button
								isDestructive
								isSmall
								onClick={ () => removeStep( i ) }
							>
								{ __( 'Remove Step', 'brndle' ) }
							</Button>
						</PanelBody>
					) ) }
					<PanelBody title={ __( 'Add Step', 'brndle' ) } initialOpen={ true }>
						<Button variant="secondary" onClick={ addStep }>
							{ __( 'Add Step', 'brndle' ) }
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
