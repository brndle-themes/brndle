import { registerBlockType } from '@wordpress/blocks';
import { grid } from '@wordpress/icons';
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

registerBlockType( 'brndle/card-grid', {
	icon: grid,

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		const items = attributes.items || [];

		const update = ( index, key, value ) => {
			const next = [ ...items ];
			next[ index ] = { ...next[ index ], [ key ]: value };
			setAttributes( { items: next } );
		};
		const add = () =>
			setAttributes( {
				items: [ ...items, { title: '', description: '', link_text: '', link_url: '' } ],
			} );
		const remove = ( index ) =>
			setAttributes( { items: items.filter( ( _, i ) => i !== index ) } );
		const move = ( index, delta ) => {
			const next = [ ...items ];
			const target = index + delta;
			if ( target < 0 || target >= next.length ) return;
			[ next[ index ], next[ target ] ] = [ next[ target ], next[ index ] ];
			setAttributes( { items: next } );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Section header', 'brndle' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'Eyebrow', 'brndle' ) }
							value={ attributes.eyebrow }
							onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
						/>
						<TextControl
							label={ __( 'Title', 'brndle' ) }
							value={ attributes.title }
							onChange={ ( v ) => setAttributes( { title: v } ) }
						/>
						<TextareaControl
							label={ __( 'Subtitle', 'brndle' ) }
							value={ attributes.subtitle }
							onChange={ ( v ) => setAttributes( { subtitle: v } ) }
						/>
					</PanelBody>

					<PanelBody title={ __( 'Layout', 'brndle' ) } initialOpen={ true }>
						<SelectControl
							label={ __( 'Columns', 'brndle' ) }
							value={ String( attributes.columns ) }
							options={ [
								{ value: '2', label: __( 'Two', 'brndle' ) },
								{ value: '3', label: __( 'Three', 'brndle' ) },
							] }
							onChange={ ( v ) => setAttributes( { columns: parseInt( v, 10 ) } ) }
						/>
						<ToggleControl
							label={ __( 'Promote the first card', 'brndle' ) }
							checked={ !! attributes.featureFirst }
							onChange={ ( v ) => setAttributes( { featureFirst: v } ) }
							help={ __( 'Spans the first card across the row so the grid has a focal point.', 'brndle' ) }
						/>
						<SelectControl
							label={ __( 'Variant', 'brndle' ) }
							value={ attributes.variant }
							options={ [
								{ value: 'light', label: __( 'Light', 'brndle' ) },
								{ value: 'dark', label: __( 'Dark', 'brndle' ) },
							] }
							onChange={ ( v ) => setAttributes( { variant: v } ) }
						/>
					</PanelBody>

					<PanelBody title={ __( 'Cards', 'brndle' ) } initialOpen={ true }>
						{ items.map( ( item, index ) => (
							<div key={ index } style={ { marginBottom: 20, paddingBottom: 16, borderBottom: '1px solid #e0e0e0' } }>
								<TextControl
									label={ __( 'Title', 'brndle' ) }
									value={ item.title || '' }
									onChange={ ( v ) => update( index, 'title', v ) }
								/>
								<TextareaControl
									label={ __( 'Description', 'brndle' ) }
									value={ item.description || '' }
									onChange={ ( v ) => update( index, 'description', v ) }
								/>
								<TextControl
									label={ __( 'Link text', 'brndle' ) }
									value={ item.link_text || '' }
									onChange={ ( v ) => update( index, 'link_text', v ) }
								/>
								<TextControl
									label={ __( 'Link URL', 'brndle' ) }
									value={ item.link_url || '' }
									onChange={ ( v ) => update( index, 'link_url', v ) }
								/>
								<div style={ { display: 'flex', gap: 8 } }>
									<Button size="small" onClick={ () => move( index, -1 ) } disabled={ index === 0 }>
										{ __( 'Up', 'brndle' ) }
									</Button>
									<Button size="small" onClick={ () => move( index, 1 ) } disabled={ index === items.length - 1 }>
										{ __( 'Down', 'brndle' ) }
									</Button>
									<Button size="small" isDestructive onClick={ () => remove( index ) }>
										{ __( 'Remove', 'brndle' ) }
									</Button>
								</div>
							</div>
						) ) }
						<Button variant="secondary" onClick={ add }>
							{ __( 'Add card', 'brndle' ) }
						</Button>
					</PanelBody>
				</InspectorControls>

				<div { ...blockProps }>
					<ServerSideRender block="brndle/card-grid" attributes={ attributes } />
				</div>
			</>
		);
	},

	save: () => null,
} );
