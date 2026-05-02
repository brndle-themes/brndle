import { Button, SelectControl, RangeControl, ToggleControl, Spinner } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Single homepage-section row (admin).
 *
 * Inputs:
 *   - section: { category_id, style, count, show_title, show_view_all }
 *   - categories: [{ value, label }] (top-level categories from REST)
 *   - styleOptions: [{ value, label }]
 *   - onUpdate(patch), onMoveUp, onMoveDown, onRemove
 *   - index, total
 */
export default function SectionRow( {
	index,
	total,
	section,
	categories,
	loadingCats,
	styleOptions,
	onUpdate,
	onMoveUp,
	onMoveDown,
	onRemove,
} ) {
	const categoryChoices = [
		{ value: '0', label: __( '— Select category —', 'brndle' ) },
		...categories,
	];

	return (
		<li
			className="brndle-section-row"
			style={ {
				border: '1px solid var(--wp-admin-theme-color, #ddd)',
				borderRadius: 8,
				padding: 16,
				marginBottom: 12,
				background: '#fff',
			} }
		>
			<div
				style={ {
					display: 'flex',
					alignItems: 'center',
					justifyContent: 'space-between',
					marginBottom: 12,
					gap: 8,
				} }
			>
				<strong style={ { fontSize: 14 } }>
					{ sprintf(
						/* translators: %d is the section number */
						__( 'Section %d', 'brndle' ),
						index + 1
					) }
				</strong>
				<div style={ { display: 'flex', gap: 4 } }>
					<Button
						size="small"
						variant="tertiary"
						disabled={ index === 0 }
						onClick={ onMoveUp }
						aria-label={ __( 'Move section up', 'brndle' ) }
					>
						↑
					</Button>
					<Button
						size="small"
						variant="tertiary"
						disabled={ index === total - 1 }
						onClick={ onMoveDown }
						aria-label={ __( 'Move section down', 'brndle' ) }
					>
						↓
					</Button>
					<Button
						size="small"
						variant="tertiary"
						isDestructive
						onClick={ onRemove }
						aria-label={ __( 'Remove section', 'brndle' ) }
					>
						{ __( 'Remove', 'brndle' ) }
					</Button>
				</div>
			</div>

			<div
				style={ {
					display: 'grid',
					gridTemplateColumns: 'repeat(2, minmax(0, 1fr))',
					gap: 16,
					alignItems: 'start',
				} }
			>
				<div>
					{ loadingCats ? (
						<div style={ { display: 'flex', alignItems: 'center', gap: 6 } }>
							<Spinner /> { __( 'Loading categories…', 'brndle' ) }
						</div>
					) : (
						<SelectControl
							label={ __( 'Category', 'brndle' ) }
							value={ String( section.category_id || 0 ) }
							options={ categoryChoices }
							onChange={ ( v ) =>
								onUpdate( { category_id: parseInt( v, 10 ) || 0 } )
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					) }
				</div>
				<div>
					<SelectControl
						label={ __( 'Style', 'brndle' ) }
						value={ section.style || 'grid-3col' }
						options={ styleOptions }
						onChange={ ( v ) => onUpdate( { style: v } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</div>
			</div>

			<div style={ { marginTop: 12 } }>
				<RangeControl
					label={ __( 'Posts to show', 'brndle' ) }
					value={ section.count || 4 }
					onChange={ ( v ) => onUpdate( { count: v } ) }
					min={ 1 }
					max={ 10 }
					step={ 1 }
					__nextHasNoMarginBottom
				/>
			</div>

			<div
				style={ {
					display: 'grid',
					gridTemplateColumns: 'repeat(2, minmax(0, 1fr))',
					gap: 12,
					marginTop: 12,
				} }
			>
				<ToggleControl
					label={ __( 'Show category title', 'brndle' ) }
					checked={ !! section.show_title }
					onChange={ ( v ) => onUpdate( { show_title: v } ) }
					__nextHasNoMarginBottom
				/>
				<ToggleControl
					label={ __( 'Show "view all" link', 'brndle' ) }
					checked={ !! section.show_view_all }
					onChange={ ( v ) => onUpdate( { show_view_all: v } ) }
					__nextHasNoMarginBottom
				/>
			</div>
		</li>
	);
}
