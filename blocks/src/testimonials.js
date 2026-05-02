import { registerBlockType } from '@wordpress/blocks';
import { quote } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	RangeControl,
	Button,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { ImageControl } from './components/image-control';

registerBlockType( 'brndle/testimonials', {
	icon: quote,

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
				items: [
					...items,
					{
						quote: '',
						name: '',
						role: '',
						avatar: '',
						stars: 5,
					},
				],
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
					</PanelBody>

					{ items.map( ( item, i ) => (
						<PanelBody
							key={ i }
							title={ `Testimonial ${ i + 1 }${
								item.name ? `: ${ item.name }` : ''
							}` }
							initialOpen={ false }
						>
							<TextareaControl
								label={ __( 'Quote', 'brndle' ) }
								value={ item.quote }
								onChange={ ( v ) =>
									updateItem( i, 'quote', v )
								}
							/>
							<TextControl
								label={ __( 'Name', 'brndle' ) }
								value={ item.name }
								onChange={ ( v ) =>
									updateItem( i, 'name', v )
								}
							/>
							<TextControl
								label={ __( 'Role', 'brndle' ) }
								value={ item.role }
								onChange={ ( v ) =>
									updateItem( i, 'role', v )
								}
							/>
							<ImageControl
								label={ __( 'Avatar', 'brndle' ) }
								image={ item.avatar }
								imageId={ item.avatar_id }
								imageAlt={ item.avatar_alt || item.name }
								onChange={ ( {
									image,
									imageId,
									imageAlt,
								} ) => {
									const newItems = [ ...items ];
									newItems[ i ] = {
										...newItems[ i ],
										avatar: image,
										avatar_id: imageId,
										avatar_alt: imageAlt,
									};
									setAttributes( { items: newItems } );
								} }
							/>
							<RangeControl
								label={ __( 'Stars', 'brndle' ) }
								value={ item.stars ?? 5 }
								onChange={ ( v ) =>
									updateItem( i, 'stars', v )
								}
								min={ 0 }
								max={ 5 }
							/>
							<Button
								isDestructive
								isSmall
								onClick={ () => removeItem( i ) }
							>
								{ __( 'Remove Testimonial', 'brndle' ) }
							</Button>
						</PanelBody>
					) ) }

					<PanelBody title={ __( 'Add Testimonial', 'brndle' ) } initialOpen={ true }>
						<Button variant="secondary" onClick={ addItem }>
							{ __( 'Add Testimonial', 'brndle' ) }
						</Button>
					</PanelBody>
				</InspectorControls>

				<div { ...blockProps }>
					<ServerSideRender
						block="brndle/testimonials"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
