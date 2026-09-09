import { registerBlockType } from '@wordpress/blocks';
import { postList } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	RangeControl,
	TextControl,
	ToggleControl,
	Notice,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import ServerSideRender from '@wordpress/server-side-render';

// Mirrors Defaults::homepageSectionStyles(). Kept in the editor only as
// labels; the render path re-validates and falls back to grid-3col.
const STYLES = [
	{ value: 'featured-hero', label: __( 'Featured hero', 'brndle' ) },
	{ value: 'editorial-pair', label: __( 'Editorial pair', 'brndle' ) },
	{ value: 'grid-3col', label: __( 'Grid, 3 columns', 'brndle' ) },
	{ value: 'list-with-thumb', label: __( 'List with thumbnails', 'brndle' ) },
	{ value: 'magazine-strip', label: __( 'Magazine strip', 'brndle' ) },
	{ value: 'mixed-2x2', label: __( 'Mixed 2x2', 'brndle' ) },
	{ value: 'ticker', label: __( 'Ticker', 'brndle' ) },
];

registerBlockType( 'brndle/post-feed', {
	icon: postList,

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();

		// Only categories that actually hold posts are offered. Picking an
		// empty one renders nothing, which reads as a broken block.
		const categories = useSelect(
			( select ) =>
				select( coreStore ).getEntityRecords( 'taxonomy', 'category', {
					per_page: 100,
					orderby: 'count',
					order: 'desc',
					hide_empty: true,
					_fields: 'id,name,count',
				} ),
			[]
		);

		const options = [
			{ value: 0, label: __( 'Select a category', 'brndle' ) },
			...( categories || [] ).map( ( c ) => ( {
				value: c.id,
				label: `${ c.name } (${ c.count })`,
			} ) ),
		];

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Source', 'brndle' ) } initialOpen={ true }>
						<SelectControl
							label={ __( 'Category', 'brndle' ) }
							value={ attributes.categoryId }
							options={ options }
							onChange={ ( v ) => setAttributes( { categoryId: parseInt( v, 10 ) || 0 } ) }
							help={ __( 'Only categories that currently have posts are listed.', 'brndle' ) }
						/>
						<RangeControl
							label={ __( 'Posts to show', 'brndle' ) }
							value={ attributes.count }
							onChange={ ( v ) => setAttributes( { count: v } ) }
							min={ 1 }
							max={ 10 }
						/>
					</PanelBody>

					<PanelBody title={ __( 'Layout', 'brndle' ) } initialOpen={ true }>
						<SelectControl
							label={ __( 'Style', 'brndle' ) }
							value={ attributes.style }
							options={ STYLES }
							onChange={ ( v ) => setAttributes( { style: v } ) }
						/>
						<TextControl
							label={ __( 'Heading override', 'brndle' ) }
							value={ attributes.heading }
							onChange={ ( v ) => setAttributes( { heading: v } ) }
							help={ __( 'Leave empty to use the category name.', 'brndle' ) }
						/>
						<ToggleControl
							label={ __( 'Show heading', 'brndle' ) }
							checked={ !! attributes.showTitle }
							onChange={ ( v ) => setAttributes( { showTitle: v } ) }
						/>
						<ToggleControl
							label={ __( 'Show "view all" link', 'brndle' ) }
							checked={ !! attributes.showViewAll }
							onChange={ ( v ) => setAttributes( { showViewAll: v } ) }
						/>
					</PanelBody>
				</InspectorControls>

				<div { ...blockProps }>
					{ ! attributes.categoryId ? (
						<Notice status="info" isDismissible={ false }>
							{ __( 'Pick a category in the sidebar to show posts here.', 'brndle' ) }
						</Notice>
					) : (
						<ServerSideRender
							block="brndle/post-feed"
							attributes={ attributes }
						/>
					) }
				</div>
			</>
		);
	},

	save: () => null,
} );
