import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	RangeControl,
	Button,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'brndle/testimonials', {
	icon: (
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
			<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
		</svg>
	),

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
								label="Quote"
								value={ item.quote }
								onChange={ ( v ) =>
									updateItem( i, 'quote', v )
								}
							/>
							<TextControl
								label="Name"
								value={ item.name }
								onChange={ ( v ) =>
									updateItem( i, 'name', v )
								}
							/>
							<TextControl
								label="Role"
								value={ item.role }
								onChange={ ( v ) =>
									updateItem( i, 'role', v )
								}
							/>
							<TextControl
								label="Avatar URL"
								value={ item.avatar }
								onChange={ ( v ) =>
									updateItem( i, 'avatar', v )
								}
							/>
							<RangeControl
								label="Stars"
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
								Remove Testimonial
							</Button>
						</PanelBody>
					) ) }

					<PanelBody title="Add Testimonial" initialOpen={ true }>
						<Button variant="secondary" onClick={ addItem }>
							Add Testimonial
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
