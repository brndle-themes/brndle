/**
 * brndle/timeline — vertical milestones block.
 */

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { tableOfContents as timelineIcon } from '@wordpress/icons';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	Button,
	RadioControl,
	SelectControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useUniqueId } from './components/use-unique-id';

registerBlockType( 'brndle/timeline', {
	icon: timelineIcon,

	edit: ( { attributes, setAttributes, clientId } ) => {
		const items = attributes.items || [];
		const blockProps = useBlockProps();
		useUniqueId( clientId, attributes, setAttributes );

		const updateItem = ( index, key, value ) => {
			const next = items.map( ( item, i ) =>
				i === index ? { ...item, [ key ]: value } : item
			);
			setAttributes( { items: next } );
		};

		const addItem = () =>
			setAttributes( {
				items: [
					...items,
					{ date: '', title: '', description: '', icon: '' },
				],
			} );

		const removeItem = ( index ) =>
			setAttributes( {
				items: items.filter( ( _, i ) => i !== index ),
			} );

		const moveItem = ( index, dir ) => {
			const target = index + dir;
			if ( target < 0 || target >= items.length ) return;
			const next = [ ...items ];
			[ next[ index ], next[ target ] ] = [ next[ target ], next[ index ] ];
			setAttributes( { items: next } );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Section', 'brndle' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'Title', 'brndle' ) }
							value={ attributes.title || '' }
							onChange={ ( v ) => setAttributes( { title: v } ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</PanelBody>
					<PanelBody title={ __( 'Style', 'brndle' ) } initialOpen={ true }>
						<SelectControl
							label={ __( 'Icon style', 'brndle' ) }
							value={ attributes.iconStyle || 'dot' }
							options={ [
								{ value: 'dot', label: __( 'Solid dot', 'brndle' ) },
								{ value: 'numbered', label: __( 'Numbered (01, 02 …)', 'brndle' ) },
								{ value: 'lucide', label: __( 'Lucide icon (per item)', 'brndle' ) },
							] }
							onChange={ ( v ) => setAttributes( { iconStyle: v } ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						<RadioControl
							label={ __( 'Connector line', 'brndle' ) }
							selected={ attributes.connector || 'solid' }
							options={ [
								{ value: 'solid', label: __( 'Solid', 'brndle' ) },
								{ value: 'dashed', label: __( 'Dashed', 'brndle' ) },
								{ value: 'none', label: __( 'None', 'brndle' ) },
							] }
							onChange={ ( v ) => setAttributes( { connector: v } ) }
						/>
						<RadioControl
							label={ __( 'Density', 'brndle' ) }
							selected={ attributes.density || 'comfortable' }
							options={ [
								{ value: 'comfortable', label: __( 'Comfortable', 'brndle' ) },
								{ value: 'compact', label: __( 'Compact', 'brndle' ) },
							] }
							onChange={ ( v ) => setAttributes( { density: v } ) }
						/>
					</PanelBody>
					{ items.map( ( item, i ) => (
						<PanelBody
							key={ i }
							title={ `${ i + 1 }. ${ item.title || __( 'Untitled', 'brndle' ) }` }
							initialOpen={ false }
						>
							<TextControl
								label={ __( 'Date / label', 'brndle' ) }
								value={ item.date || '' }
								onChange={ ( v ) => updateItem( i, 'date', v ) }
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<TextControl
								label={ __( 'Title', 'brndle' ) }
								value={ item.title || '' }
								onChange={ ( v ) => updateItem( i, 'title', v ) }
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<TextareaControl
								label={ __( 'Description', 'brndle' ) }
								value={ item.description || '' }
								onChange={ ( v ) => updateItem( i, 'description', v ) }
								__nextHasNoMarginBottom
							/>
							{ ( attributes.iconStyle || 'dot' ) === 'lucide' && (
								<TextControl
									label={ __( 'Icon (Lucide name)', 'brndle' ) }
									help={ __( 'Lucide icon name (kebab-case). Must be in the Brndle icon set.', 'brndle' ) }
									value={ item.icon || '' }
									onChange={ ( v ) => updateItem( i, 'icon', v ) }
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
							) }
							<div style={ { display: 'flex', gap: 6, marginTop: 12 } }>
								<Button
									size="small"
									variant="tertiary"
									disabled={ i === 0 }
									onClick={ () => moveItem( i, -1 ) }
								>
									{ __( 'Up', 'brndle' ) }
								</Button>
								<Button
									size="small"
									variant="tertiary"
									disabled={ i === items.length - 1 }
									onClick={ () => moveItem( i, 1 ) }
								>
									{ __( 'Down', 'brndle' ) }
								</Button>
								<Button
									size="small"
									variant="tertiary"
									isDestructive
									onClick={ () => removeItem( i ) }
								>
									{ __( 'Remove', 'brndle' ) }
								</Button>
							</div>
						</PanelBody>
					) ) }
					<PanelBody title={ __( 'Add milestone', 'brndle' ) } initialOpen={ true }>
						<Button variant="secondary" onClick={ addItem }>
							{ __( 'Add timeline item', 'brndle' ) }
						</Button>
					</PanelBody>
				</InspectorControls>

				<div { ...blockProps }>
					<ServerSideRender block="brndle/timeline" attributes={ attributes } />
				</div>
			</>
		);
	},

	save: () => null,
} );
