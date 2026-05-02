import { registerBlockType } from '@wordpress/blocks';
import { envelope } from '@wordpress/icons';
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

registerBlockType( 'brndle/lead-form', {
	icon: envelope,

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
					<PanelBody title={ __( 'Content', 'brndle' ) } initialOpen={ true }>
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
							onChange={ ( v ) =>
								setAttributes( { subtitle: v } )
							}
						/>
					</PanelBody>
					<PanelBody title={ __( 'Form Settings', 'brndle' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'Button Text', 'brndle' ) }
							value={ attributes.button_text }
							onChange={ ( v ) =>
								setAttributes( { button_text: v } )
							}
						/>
						<TextControl
							label={ __( 'Success Message', 'brndle' ) }
							value={ attributes.success_message }
							onChange={ ( v ) =>
								setAttributes( { success_message: v } )
							}
						/>
						<TextControl
							label={ __( 'Form Action URL (optional)', 'brndle' ) }
							value={ attributes.form_action }
							onChange={ ( v ) =>
								setAttributes( { form_action: v } )
							}
							help={ __( 'Leave empty to use built-in form handler with Mailchimp, webhook, and email notifications. Only set this if you want to POST to an external endpoint.', 'brndle' ) }
						/>
						<TextControl
							label={ __( 'Mailchimp List ID (optional)', 'brndle' ) }
							value={ attributes.mailchimp_list_id }
							onChange={ ( v ) =>
								setAttributes( { mailchimp_list_id: v } )
							}
							help={ __( 'Override the global Mailchimp list for this form. Leave empty to use the list configured in Brndle > Forms settings.', 'brndle' ) }
						/>
						<SelectControl
							label={ __( 'Layout', 'brndle' ) }
							value={ attributes.layout }
							options={ [
								{
									label: __( 'Stacked (full width)', 'brndle' ),
									value: 'stacked',
								},
								{
									label: __( 'Inline (single row)', 'brndle' ),
									value: 'inline',
								},
								{
									label: __( 'Split (text + form)', 'brndle' ),
									value: 'split',
								},
							] }
							onChange={ ( v ) =>
								setAttributes( { layout: v } )
							}
						/>
						<SelectControl
							label={ __( 'Variant', 'brndle' ) }
							value={ attributes.variant }
							options={ [
								{ label: __( 'Light', 'brndle' ), value: 'light' },
								{ label: __( 'Dark', 'brndle' ), value: 'dark' },
								{ label: __( 'Accent', 'brndle' ), value: 'accent' },
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
								label={ __( 'Label', 'brndle' ) }
								value={ field.label }
								onChange={ ( v ) =>
									updateField( i, 'label', v )
								}
							/>
							<SelectControl
								label={ __( 'Type', 'brndle' ) }
								value={ field.type }
								options={ [
									{ label: __( 'Text', 'brndle' ), value: 'text' },
									{ label: __( 'Email', 'brndle' ), value: 'email' },
									{ label: __( 'Phone', 'brndle' ), value: 'tel' },
									{ label: __( 'URL', 'brndle' ), value: 'url' },
									{ label: __( 'Textarea', 'brndle' ), value: 'textarea' },
								] }
								onChange={ ( v ) =>
									updateField( i, 'type', v )
								}
							/>
							<TextControl
								label={ __( 'Placeholder', 'brndle' ) }
								value={ field.placeholder }
								onChange={ ( v ) =>
									updateField( i, 'placeholder', v )
								}
							/>
							<ToggleControl
								label={ __( 'Required', 'brndle' ) }
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
								{ __( 'Remove Field', 'brndle' ) }
							</Button>
						</PanelBody>
					) ) }
					<PanelBody title={ __( 'Add Field', 'brndle' ) } initialOpen={ true }>
						<Button variant="secondary" onClick={ addField }>
							{ __( 'Add Field', 'brndle' ) }
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
