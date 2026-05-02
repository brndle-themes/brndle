import { useState, useEffect, useCallback, useRef } from '@wordpress/element';
import { Button, Notice } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';
import apiFetch from '@wordpress/api-fetch';
import ToggleRow from '../components/ToggleRow';
import SectionRow from '../components/SectionRow';

/**
 * Blog Homepage Sections tab.
 *
 * Lets the admin opt into the news-portal layout (only takes effect
 * when the blog index IS the front page) and configure an ordered
 * list of category sections, each with its own visual style.
 *
 * 1.5.3: when the toggle is flipped on and sections is still empty,
 * we auto-populate with the top 5 most-populated top-level categories
 * in a magazine-flow style order. Mirrors the server-side fallback so
 * what the admin sees is exactly what the frontend will render. The
 * auto-fill only fires once per mount; if the user clears the list
 * and saves, it stays cleared (the server-side fallback still applies
 * on the frontend until they save again).
 *
 * See plans/2026-05-02-blog-homepage-sections.md for the design.
 */

const STYLE_OPTIONS = [
	{ value: 'featured-hero', label: __( 'Featured hero — 1 large + 2 stacked', 'brndle' ) },
	{ value: 'grid-3col', label: __( 'Grid (3 columns)', 'brndle' ) },
	{ value: 'magazine-strip', label: __( 'Magazine strip — 1 feature + 4 small', 'brndle' ) },
	{ value: 'list-with-thumb', label: __( 'List with thumbnail', 'brndle' ) },
	{ value: 'mixed-2x2', label: __( 'Mixed — 1 hero + 3 small', 'brndle' ) },
	{ value: 'ticker', label: __( 'Ticker (horizontal scroll)', 'brndle' ) },
	{ value: 'editorial-pair', label: __( 'Editorial pair (2 large)', 'brndle' ) },
];

// The magazine-flow composition. Mirrors the server-side fallback in
// resources/views/partials/archive/sections.blade.php so the admin
// preview is the same shape as the frontend render.
const MAGAZINE_FLOW = [
	{ style: 'featured-hero', count: 3 },   // 01 lead
	{ style: 'grid-3col', count: 6 },       // 02 recap
	{ style: 'magazine-strip', count: 5 },  // 03 long-read + numbered list
	{ style: 'editorial-pair', count: 2 },  // 04 spread
	{ style: 'ticker', count: 8 },          // 05 trending strip
];

const NEW_SECTION = () => ( {
	category_id: 0,
	style: 'grid-3col',
	count: 4,
	show_title: true,
	show_view_all: true,
} );

const buildSuggestedDefaults = ( topCategoriesByCount ) => {
	return MAGAZINE_FLOW.map( ( slot, i ) => {
		const cat = topCategoriesByCount[ i ];
		return {
			category_id: cat ? cat.id : 0,
			style: slot.style,
			count: slot.count,
			show_title: true,
			show_view_all: true,
		};
	} ).filter( ( s ) => s.category_id > 0 );
};

export default function BlogHomepage( { settings, onChange } ) {
	const [ rawCategories, setRawCategories ] = useState( [] );
	const [ loadingCats, setLoadingCats ] = useState( true );
	// Track the toggle's previous value so auto-fill triggers ONLY on
	// the off→on transition. Initial value is `null` so the first render
	// (when previousToggle is sync'd from the loaded settings) doesn't
	// count as a transition. This respects users who deliberately
	// cleared the list and saved an empty state.
	const previousToggle = useRef( null );

	const sections = Array.isArray( settings.homepage_sections )
		? settings.homepage_sections
		: [];

	useEffect( () => {
		// `_fields` trims the REST payload from ~2KB/cat to ~80 bytes/cat —
		// matters on sites with 100+ categories. `hide_empty=true` excludes
		// categories that would never be picked anyway. On a 7k+ post site
		// these two flags drop the admin fetch from ~250KB to ~20KB.
		apiFetch( {
			path: '/wp/v2/categories?parent=0&per_page=100&hide_empty=true&orderby=count&order=desc&_fields=id,name,count,slug',
		} )
			.then( ( cats ) => {
				setRawCategories( cats );
				setLoadingCats( false );
			} )
			.catch( () => setLoadingCats( false ) );
	}, [] );

	// Dropdown options: same data, sorted alphabetically for predictable
	// browsing. Decode the &amp; etc. that REST returns encoded.
	const categoryOptions = [ ...rawCategories ]
		.sort( ( a, b ) => a.name.localeCompare( b.name ) )
		.map( ( c ) => ( {
			value: String( c.id ),
			label: `${ decodeEntities( c.name ) } (${ c.count })`,
		} ) );

	// Top 5 by post count, hide empty cats.
	const topByCount = rawCategories
		.filter( ( c ) => c.count > 0 )
		.slice( 0, 5 );

	const applySuggestedDefaults = useCallback( () => {
		const defaults = buildSuggestedDefaults( topByCount );
		if ( defaults.length > 0 ) {
			onChange( 'homepage_sections', defaults );
		}
	}, [ topByCount, onChange ] );

	// Auto-fill on toggle off→on transition (and only then). Initial mount
	// just records the current toggle value so a tab that opens already-on
	// does NOT trigger auto-fill — we respect users who deliberately
	// cleared sections and saved an empty state.
	const toggleOn = !! settings.homepage_sections_enabled;
	useEffect( () => {
		const prev = previousToggle.current;
		previousToggle.current = toggleOn;

		if ( prev === null ) return;             // first render, just sync
		if ( prev === toggleOn ) return;         // no transition
		if ( ! toggleOn ) return;                // on → off, nothing to do
		if ( sections.length > 0 ) return;       // user already has sections
		if ( loadingCats ) return;               // wait for category data
		if ( topByCount.length === 0 ) return;   // no categories with posts
		applySuggestedDefaults();
	}, [
		toggleOn,
		sections.length,
		loadingCats,
		topByCount.length,
		applySuggestedDefaults,
	] );

	const updateSection = useCallback(
		( index, patch ) => {
			const next = sections.map( ( s, i ) =>
				i === index ? { ...s, ...patch } : s
			);
			onChange( 'homepage_sections', next );
		},
		[ sections, onChange ]
	);

	const removeSection = useCallback(
		( index ) => {
			onChange(
				'homepage_sections',
				sections.filter( ( _, i ) => i !== index )
			);
		},
		[ sections, onChange ]
	);

	const moveSection = useCallback(
		( index, delta ) => {
			const target = index + delta;
			if ( target < 0 || target >= sections.length ) return;
			const next = sections.slice();
			[ next[ index ], next[ target ] ] = [ next[ target ], next[ index ] ];
			onChange( 'homepage_sections', next );
		},
		[ sections, onChange ]
	);

	const addSection = useCallback( () => {
		onChange( 'homepage_sections', [ ...sections, NEW_SECTION() ] );
	}, [ sections, onChange ] );

	return (
		<div className="brndle-blog-homepage">
			<h3 className="brndle-section-title">{ __( 'Sections Layout', 'brndle' ) }</h3>

			<ToggleRow
				label={ __( 'Use sections layout when blog is the homepage', 'brndle' ) }
				description={ __(
					'When the blog index is set as your front page, render stacked category sections (news-portal style) instead of the regular archive layout. No effect when a static page is the front page.',
					'brndle'
				) }
				checked={ !! settings.homepage_sections_enabled }
				onChange={ ( v ) => onChange( 'homepage_sections_enabled', v ) }
			/>

			{ !! settings.homepage_sections_enabled && (
				<>
					<h3 className="brndle-section-title" style={ { marginTop: 32 } }>
						{ __( 'Homepage Sections', 'brndle' ) }
					</h3>
					<p className="brndle-section-description">
						{ __(
							'Each section renders posts from one top-level category in the chosen visual style. Sections render top-to-bottom in the order shown below.',
							'brndle'
						) }
					</p>

					{ sections.length === 0 && (
						<Notice status="info" isDismissible={ false }>
							<div style={ { display: 'flex', alignItems: 'center', gap: 12, flexWrap: 'wrap' } }>
								<span>
									{ loadingCats
										? __( 'Loading category suggestions…', 'brndle' )
										: topByCount.length === 0
										? __(
												'No categories with posts found. Publish at least one post in a top-level category before configuring sections.',
												'brndle'
										  )
										: __(
												'Tip: start from a magazine-flow preset built from your top 5 most-populated categories.',
												'brndle'
										  ) }
								</span>
								{ ! loadingCats && topByCount.length > 0 && (
									<Button
										variant="primary"
										size="small"
										onClick={ applySuggestedDefaults }
									>
										{ __( 'Apply suggested defaults', 'brndle' ) }
									</Button>
								) }
							</div>
						</Notice>
					) }

					<ol
						className="brndle-section-list"
						style={ { listStyle: 'none', padding: 0, margin: '16px 0' } }
					>
						{ sections.map( ( section, index ) => (
							<SectionRow
								key={ index }
								index={ index }
								total={ sections.length }
								section={ section }
								categories={ categoryOptions }
								loadingCats={ loadingCats }
								styleOptions={ STYLE_OPTIONS }
								onUpdate={ ( patch ) => updateSection( index, patch ) }
								onMoveUp={ () => moveSection( index, -1 ) }
								onMoveDown={ () => moveSection( index, 1 ) }
								onRemove={ () => removeSection( index ) }
							/>
						) ) }
					</ol>

					<Button
						variant="secondary"
						onClick={ addSection }
						style={ { marginTop: 8 } }
					>
						+ { __( 'Add section', 'brndle' ) }
					</Button>
				</>
			) }
		</div>
	);
}
