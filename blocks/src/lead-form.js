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

registerBlockType( 'brndle/lead-form', {
	icon: (
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
			<rect x="3" y="4" width="18" height="16" rx="2" />
			<line x1="7" y1="9" x2="17" y2="9" />
			<line x1="7" y1="13" x2="13" y2="13" />
			<rect x="14" y="15" width="4" height="2" rx="1" fill="currentColor" stroke="none" />
		</svg>
	),

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		const fields = attributes.fields || [];

		const updateField = ( index, key, value ) => {
			const newFields = [ ...fields ];
			newFields[ index ] = { ...newFields[ index ], [ key ]: value };
			setAttributes( { fields: newFields } );
		};

		const addField = () => {
			setAttributes( {
				fields: [
					...fields,
					{ label: '', type: 'text', required: false, placeholder: '' },
				],
			} );
		};

		const removeField = ( index ) => {
			setAttributes( {
				fields: fields.filter( ( _, i ) => i !== index ),
			} );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title="Content" initialOpen={ true }>
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
							onChange={ ( v ) =>
								setAttributes( { subtitle: v } )
							}
						/>
					</PanelBody>
					<PanelBody title="Form Settings" initialOpen={ true }>
						<TextControl
							label="Button Text"
							value={ attributes.button_text }
							onChange={ ( v ) =>
								setAttributes( { button_text: v } )
							}
						/>
						<TextControl
							label="Success Message"
							value={ attributes.success_message }
							onChange={ ( v ) =>
								setAttributes( { success_message: v } )
							}
						/>
						<TextControl
							label="Form Action URL (optional)"
							value={ attributes.form_action }
							onChange={ ( v ) =>
								setAttributes( { form_action: v } )
							}
							help="Leave empty to use built-in form handler with Mailchimp, webhook, and email notifications. Only set this if you want to POST to an external endpoint."
						/>
						<TextControl
							label="Mailchimp List ID (optional)"
							value={ attributes.mailchimp_list_id }
							onChange={ ( v ) =>
								setAttributes( { mailchimp_list_id: v } )
							}
							help="Override the global Mailchimp list for this form. Leave empty to use the list configured in Brndle > Forms settings."
						/>
						<SelectControl
							label="Layout"
							value={ attributes.layout }
							options={ [
								{
									label: 'Stacked (full width)',
									value: 'stacked',
								},
								{
									label: 'Inline (single row)',
									value: 'inline',
								},
								{
									label: 'Split (text + form)',
									value: 'split',
								},
							] }
							onChange={ ( v ) =>
								setAttributes( { layout: v } )
							}
						/>
						<SelectControl
							label="Variant"
							value={ attributes.variant }
							options={ [
								{ label: 'Light', value: 'light' },
								{ label: 'Dark', value: 'dark' },
								{ label: 'Accent', value: 'accent' },
							] }
							onChange={ ( v ) =>
								setAttributes( { variant: v } )
							}
						/>
					</PanelBody>
					{ fields.map( ( field, i ) => (
						<PanelBody
							key={ i }
							title={ `Field ${ i + 1 }${
								field.label ? `: ${ field.label }` : ''
							}` }
							initialOpen={ false }
						>
							<TextControl
								label="Label"
								value={ field.label }
								onChange={ ( v ) =>
									updateField( i, 'label', v )
								}
							/>
							<SelectControl
								label="Type"
								value={ field.type }
								options={ [
									{ label: 'Text', value: 'text' },
									{ label: 'Email', value: 'email' },
									{ label: 'Phone', value: 'tel' },
									{ label: 'URL', value: 'url' },
									{ label: 'Textarea', value: 'textarea' },
								] }
								onChange={ ( v ) =>
									updateField( i, 'type', v )
								}
							/>
							<TextControl
								label="Placeholder"
								value={ field.placeholder }
								onChange={ ( v ) =>
									updateField( i, 'placeholder', v )
								}
							/>
							<ToggleControl
								label="Required"
								checked={ !! field.required }
								onChange={ ( v ) =>
									updateField( i, 'required', v )
								}
							/>
							<Button
								isDestructive
								isSmall
								onClick={ () => removeField( i ) }
							>
								Remove Field
							</Button>
						</PanelBody>
					) ) }
					<PanelBody title="Add Field" initialOpen={ true }>
						<Button variant="secondary" onClick={ addField }>
							Add Field
						</Button>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<ServerSideRender
						block="brndle/lead-form"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
