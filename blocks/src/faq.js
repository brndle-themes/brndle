import { registerBlockType } from '@wordpress/blocks';
import { help } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	Button,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'brndle/faq', {
	icon: help,

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		const items = attributes.items || [];

		const updateItem = ( index, key, value ) => {
			const newItems = [ ...items ];
			newItems[ index ] = { ...newItems[ index ], [ key ]: value };
			setAttributes( { items: newItems } );
		};

		const addItem = () => {
			setAttributes( {
				items: [ ...items, { question: '', answer: '' } ],
			} );
		};

		const removeItem = ( index ) => {
			setAttributes( {
				items: items.filter( ( _, i ) => i !== index ),
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
						/>
					</PanelBody>

					{ items.map( ( item, i ) => (
						<PanelBody
							key={ i }
							title={ `FAQ ${ i + 1 }${
								item.question
									? `: ${ item.question.substring(
											0,
											30
									  ) }...`
									: ''
							}` }
							initialOpen={ false }
						>
							<TextControl
								label={ __( 'Question', 'brndle' ) }
								value={ item.question }
								onChange={ ( v ) =>
									updateItem( i, 'question', v )
								}
							/>
							<TextareaControl
								label={ __( 'Answer', 'brndle' ) }
								value={ item.answer }
								onChange={ ( v ) =>
									updateItem( i, 'answer', v )
								}
							/>
							<Button
								isDestructive
								isSmall
								onClick={ () => removeItem( i ) }
							>
								{ __( 'Remove FAQ', 'brndle' ) }
							</Button>
						</PanelBody>
					) ) }

					<PanelBody title={ __( 'Add FAQ', 'brndle' ) } initialOpen={ true }>
						<Button variant="secondary" onClick={ addItem }>
							{ __( 'Add FAQ Item', 'brndle' ) }
						</Button>
					</PanelBody>
				</InspectorControls>

				<div { ...blockProps }>
					<ServerSideRender
						block="brndle/faq"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
