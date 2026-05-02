import { registerBlockType } from '@wordpress/blocks';
import { chartBar } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	SelectControl,
	Button,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'brndle/stats', {
	icon: chartBar,

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
				items: [ ...items, { value: '', label: '' } ],
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
					<PanelBody title={ __( 'Stats', 'brndle' ) } initialOpen={ true }>
						{ items.map( ( item, i ) => (
							<div
								key={ i }
								style={ {
									marginBottom: '16px',
									paddingBottom: '16px',
									borderBottom: '1px solid #e0e0e0',
								} }
							>
								<TextControl
									label={ `Value ${ i + 1 }` }
									value={ item.value }
									onChange={ ( v ) =>
										updateItem( i, 'value', v )
									}
								/>
								<TextControl
									label={ `Label ${ i + 1 }` }
									value={ item.label }
									onChange={ ( v ) =>
										updateItem( i, 'label', v )
									}
								/>
								<Button
									isDestructive
									isSmall
									onClick={ () => removeItem( i ) }
								>
									{ __( 'Remove', 'brndle' ) }
								</Button>
							</div>
						) ) }
						<Button variant="secondary" onClick={ addItem }>
							{ __( 'Add Stat', 'brndle' ) }
						</Button>
					</PanelBody>

					<PanelBody title={ __( 'Settings', 'brndle' ) } initialOpen={ false }>
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
				</InspectorControls>

				<div { ...blockProps }>
					<ServerSideRender
						block="brndle/stats"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
