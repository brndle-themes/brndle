import { registerBlockType } from '@wordpress/blocks';
import { table } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	SelectControl,
	Button,
	__experimentalNumberControl as NumberControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'brndle/comparison-table', {
	icon: table,

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		const columns = attributes.columns || [];
		const rows = attributes.rows || [];

		// --- Column helpers ---
		const addColumn = () => {
			const newCol = { label: '', sublabel: '' };
			const newColumns = [ ...columns, newCol ];
			// Append a false value to every existing row's values array
			const newRows = rows.map( ( row ) => ( {
				...row,
				values: [ ...( row.values || [] ), false ],
			} ) );
			setAttributes( { columns: newColumns, rows: newRows } );
		};

		const removeColumn = ( ci ) => {
			const newColumns = columns.filter( ( _, i ) => i !== ci );
			const newRows = rows.map( ( row ) => ( {
				...row,
				values: ( row.values || [] ).filter( ( _, i ) => i !== ci ),
			} ) );
			// Adjust highlight_column if needed
			let hl = attributes.highlight_column;
			if ( hl === ci ) hl = -1;
			else if ( hl > ci ) hl = hl - 1;
			setAttributes( { columns: newColumns, rows: newRows, highlight_column: hl } );
		};

		const updateColumn = ( ci, field, value ) => {
			const newColumns = columns.map( ( col, i ) =>
				i === ci ? { ...col, [ field ]: value } : col
			);
			setAttributes( { columns: newColumns } );
		};

		// --- Row helpers ---
		const addRow = () => {
			const newRow = {
				feature: '',
				values: columns.map( () => false ),
			};
			setAttributes( { rows: [ ...rows, newRow ] } );
		};

		const removeRow = ( ri ) => {
			setAttributes( { rows: rows.filter( ( _, i ) => i !== ri ) } );
		};

		const updateRowFeature = ( ri, value ) => {
			const newRows = rows.map( ( row, i ) =>
				i === ri ? { ...row, feature: value } : row
			);
			setAttributes( { rows: newRows } );
		};

		// Encode a cell's SelectControl value to the stored type
		// Stored values: true (boolean), false (boolean), or string
		const getCellSelectValue = ( val ) => {
			if ( val === true ) return 'check';
			if ( val === false ) return 'cross';
			return 'custom';
		};

		const updateRowValue = ( ri, ci, selectVal, customText ) => {
			const newRows = rows.map( ( row, i ) => {
				if ( i !== ri ) return row;
				const newValues = ( row.values || [] ).map( ( v, j ) => {
					if ( j !== ci ) return v;
					if ( selectVal === 'check' ) return true;
					if ( selectVal === 'cross' ) return false;
					return customText !== undefined ? customText : ( typeof v === 'string' ? v : '' );
				} );
				return { ...row, values: newValues };
			} );
			setAttributes( { rows: newRows } );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Header', 'brndle' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'Eyebrow', 'brndle' ) }
							value={ attributes.eyebrow }
							onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
						/>
						<TextControl
							label={ __( 'Title', 'brndle' ) }
							value={ attributes.title }
							onChange={ ( v ) => setAttributes( { title: v } ) }
							help={ __( 'Supports HTML for styling', 'brndle' ) }
						/>
						<TextControl
							label={ __( 'Subtitle', 'brndle' ) }
							value={ attributes.subtitle }
							onChange={ ( v ) => setAttributes( { subtitle: v } ) }
						/>
					</PanelBody>

					<PanelBody title={ `Columns (${ columns.length })` } initialOpen={ true }>
						{ columns.map( ( col, ci ) => (
							<div
								key={ ci }
								style={ {
									marginBottom: '16px',
									paddingBottom: '16px',
									borderBottom: '1px solid #e0e0e0',
								} }
							>
								<div
									style={ {
										display: 'flex',
										justifyContent: 'space-between',
										alignItems: 'center',
										marginBottom: '8px',
									} }
								>
									<strong>Column { ci + 1 }</strong>
									<Button
										isDestructive
										variant="tertiary"
										onClick={ () => removeColumn( ci ) }
									>
										{ __( 'Remove', 'brndle' ) }
									</Button>
								</div>
								<TextControl
									label={ __( 'Label', 'brndle' ) }
									value={ col.label || '' }
									onChange={ ( v ) => updateColumn( ci, 'label', v ) }
								/>
								<TextControl
									label={ __( 'Sublabel (e.g. price)', 'brndle' ) }
									value={ col.sublabel || '' }
									onChange={ ( v ) => updateColumn( ci, 'sublabel', v ) }
								/>
							</div>
						) ) }
						<Button variant="secondary" onClick={ addColumn }>
							+ Add Column
						</Button>
					</PanelBody>

					<PanelBody title={ `Rows (${ rows.length })` } initialOpen={ false }>
						{ rows.map( ( row, ri ) => (
							<div
								key={ ri }
								style={ {
									marginBottom: '16px',
									paddingBottom: '16px',
									borderBottom: '1px solid #e0e0e0',
								} }
							>
								<div
									style={ {
										display: 'flex',
										justifyContent: 'space-between',
										alignItems: 'center',
										marginBottom: '8px',
									} }
								>
									<strong>Row { ri + 1 }</strong>
									<Button
										isDestructive
										variant="tertiary"
										onClick={ () => removeRow( ri ) }
									>
										{ __( 'Remove', 'brndle' ) }
									</Button>
								</div>
								<TextControl
									label={ __( 'Feature name', 'brndle' ) }
									value={ row.feature || '' }
									onChange={ ( v ) => updateRowFeature( ri, v ) }
								/>
								{ columns.map( ( col, ci ) => {
									const cellVal = ( row.values || [] )[ ci ];
									const selectVal = getCellSelectValue( cellVal );
									return (
										<div key={ ci } style={ { marginTop: '8px' } }>
											<SelectControl
												label={ `"${ col.label || `Col ${ ci + 1 }` }" value` }
												value={ selectVal }
												options={ [
													{ label: __( 'Check', 'brndle' ), value: 'check' },
													{ label: __( 'Cross', 'brndle' ), value: 'cross' },
													{ label: __( 'Custom text', 'brndle' ), value: 'custom' },
												] }
												onChange={ ( v ) =>
													updateRowValue( ri, ci, v, undefined )
												}
											/>
											{ selectVal === 'custom' && (
												<TextControl
													label={ __( 'Custom text', 'brndle' ) }
													value={ typeof cellVal === 'string' ? cellVal : '' }
													onChange={ ( v ) =>
														updateRowValue( ri, ci, 'custom', v )
													}
												/>
											) }
										</div>
									);
								} ) }
							</div>
						) ) }
						<Button variant="secondary" onClick={ addRow }>
							+ Add Row
						</Button>
					</PanelBody>

					<PanelBody title={ __( 'Settings', 'brndle' ) } initialOpen={ false }>
						<NumberControl
							label={ __( 'Highlight column (0-based, -1 = none)', 'brndle' ) }
							value={ attributes.highlight_column }
							min={ -1 }
							max={ columns.length - 1 }
							onChange={ ( v ) =>
								setAttributes( { highlight_column: parseInt( v, 10 ) } )
							}
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
				</InspectorControls>
				<div { ...blockProps }>
					<ServerSideRender
						block="brndle/comparison-table"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
