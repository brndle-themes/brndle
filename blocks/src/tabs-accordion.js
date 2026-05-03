/**
 * brndle/tabs-accordion — combined tabs + accordion editorial block.
 */

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { layout as tabsIcon } from '@wordpress/icons';
import {
	BlockControls,
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	Button,
	RadioControl,
	ToolbarGroup,
	ToolbarButton,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useUniqueId } from './components/use-unique-id';

registerBlockType( 'brndle/tabs-accordion', {
	icon: tabsIcon,

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
				items: [ ...items, { label: '', content: '' } ],
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

		const isTabs = ( attributes.displayMode || 'tabs' ) === 'tabs';

		return (
			<>
				<BlockControls>
					<ToolbarGroup>
						<ToolbarButton
							icon={ tabsIcon }
							label={ __( 'Toggle tabs / accordion', 'brndle' ) }
							onClick={ () =>
								setAttributes( {
									displayMode: isTabs ? 'accordion' : 'tabs',
								} )
							}
						>
							{ isTabs
								? __( 'Tabs', 'brndle' )
								: __( 'Accordion', 'brndle' ) }
						</ToolbarButton>
					</ToolbarGroup>
				</BlockControls>

				<InspectorControls>
					<PanelBody title={ __( 'Display', 'brndle' ) } initialOpen={ true }>
						<RadioControl
							label={ __( 'Display mode', 'brndle' ) }
							selected={ attributes.displayMode || 'tabs' }
							options={ [
								{ value: 'tabs', label: __( 'Tabs', 'brndle' ) },
								{ value: 'accordion', label: __( 'Accordion', 'brndle' ) },
							] }
							onChange={ ( v ) => setAttributes( { displayMode: v } ) }
						/>
						<TextControl
							label={ __( 'Section title (optional)', 'brndle' ) }
							value={ attributes.title || '' }
							onChange={ ( v ) => setAttributes( { title: v } ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</PanelBody>

					{ isTabs ? (
						<PanelBody title={ __( 'Tabs', 'brndle' ) } initialOpen={ false }>
							<RadioControl
								label={ __( 'Tab strip alignment', 'brndle' ) }
								selected={ attributes.tabsAlignment || 'start' }
								options={ [
									{ value: 'start', label: __( 'Start', 'brndle' ) },
									{ value: 'center', label: __( 'Center', 'brndle' ) },
									{ value: 'end', label: __( 'End', 'brndle' ) },
								] }
								onChange={ ( v ) =>
									setAttributes( { tabsAlignment: v } )
								}
							/>
						</PanelBody>
					) : (
						<PanelBody title={ __( 'Accordion', 'brndle' ) } initialOpen={ false }>
							<RadioControl
								label={ __( 'Mode', 'brndle' ) }
								selected={ attributes.accordionMode || 'single' }
								options={ [
									{ value: 'single', label: __( 'Single — opening one closes the others', 'brndle' ) },
									{ value: 'multiple', label: __( 'Multiple — any number can be open', 'brndle' ) },
								] }
								onChange={ ( v ) =>
									setAttributes( { accordionMode: v } )
								}
							/>
							<RadioControl
								label={ __( 'Default state', 'brndle' ) }
								selected={ attributes.accordionDefault || 'closed' }
								options={ [
									{ value: 'closed', label: __( 'All closed', 'brndle' ) },
									{ value: 'first', label: __( 'First open', 'brndle' ) },
									{ value: 'all', label: __( 'All open (multiple only)', 'brndle' ) },
								] }
								onChange={ ( v ) =>
									setAttributes( { accordionDefault: v } )
								}
							/>
						</PanelBody>
					) }

					{ items.map( ( item, i ) => (
						<PanelBody
							key={ i }
							title={ `${ i + 1 }. ${ item.label || __( 'Untitled', 'brndle' ) }` }
							initialOpen={ false }
						>
							<TextControl
								label={ __( 'Label', 'brndle' ) }
								value={ item.label || '' }
								onChange={ ( v ) => updateItem( i, 'label', v ) }
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<TextareaControl
								label={ __( 'Content', 'brndle' ) }
								help={ __( 'Limited HTML allowed: bold, italic, links, line breaks, inline code.', 'brndle' ) }
								value={ item.content || '' }
								onChange={ ( v ) => updateItem( i, 'content', v ) }
								rows={ 4 }
								__nextHasNoMarginBottom
							/>
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
					<PanelBody title={ __( 'Add item', 'brndle' ) } initialOpen={ true }>
						<Button variant="secondary" onClick={ addItem }>
							{ __( 'Add panel', 'brndle' ) }
						</Button>
					</PanelBody>
				</InspectorControls>

				<div { ...blockProps }>
					<ServerSideRender
						block="brndle/tabs-accordion"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
