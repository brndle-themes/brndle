import './page-meta-sidebar.css';
import { __ } from '@wordpress/i18n';
import { createRoot, createElement, useState } from '@wordpress/element';
import {
	SelectControl,
	ToggleControl,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';

/* ── Inline SVG icon helper ────────────────────────────────────────── */
const svg = ( paths, vb = '0 0 24 24' ) =>
	createElement(
		'svg',
		{
			xmlns: 'http://www.w3.org/2000/svg',
			viewBox: vb,
			width: 18,
			height: 18,
			fill: 'none',
			stroke: 'currentColor',
			strokeWidth: 1.5,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		},
		...( Array.isArray( paths ) ? paths : [ paths ] ).map( ( d ) =>
			createElement( 'path', { d } )
		)
	);

const icons = {
	layout: svg( 'M4 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5ZM4 9h16M9 9v11' ),
	header: svg( [
		'M4 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5Z',
		'M4 12h16M4 16h10',
	] ),
	palette: svg( 'M12 2a10 10 0 0 0 0 20 2 2 0 0 0 2-2v-.5a2 2 0 0 1 2-2h1.5A2.5 2.5 0 0 0 20 15v-3a10 10 0 0 0-8-10ZM8.5 9a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3ZM12.5 7a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3ZM16 11a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3ZM9 15a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z' ),
	code: svg( 'M16 18l6-6-6-6M8 6l-6 6 6 6' ),
};

/* ── Card Component ────────────────────────────────────────────────── */
function SettingsCard( { icon, title, description, children, defaultOpen = false } ) {
	const [ open, setOpen ] = useState( defaultOpen );

	return createElement(
		'div',
		{ className: 'brndle-meta-card' + ( open ? ' is-open' : '' ) },
		createElement(
			'button',
			{
				className: 'brndle-meta-card__header',
				type: 'button',
				onClick: () => setOpen( ! open ),
				'aria-expanded': open,
			},
			createElement(
				'div',
				{ className: 'brndle-meta-card__header-left' },
				createElement( 'span', { className: 'brndle-meta-card__icon' }, icon ),
				createElement(
					'div',
					null,
					createElement( 'span', { className: 'brndle-meta-card__title' }, title ),
					description &&
						createElement( 'span', { className: 'brndle-meta-card__desc' }, description )
				)
			),
			createElement(
				'svg',
				{
					className: 'brndle-meta-card__chevron',
					width: 20,
					height: 20,
					viewBox: '0 0 20 20',
					fill: 'currentColor',
				},
				createElement( 'path', {
					d: open
						? 'M5.293 12.707a1 1 0 0 1 0-1.414L10 6.586l4.707 4.707a1 1 0 0 1-1.414 1.414L10 9.414l-3.293 3.293a1 1 0 0 1-1.414 0Z'
						: 'M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414L10 13.414l-4.707-4.707a1 1 0 0 1 0-1.414Z',
				} )
			)
		),
		open &&
			createElement( 'div', { className: 'brndle-meta-card__body' }, children )
	);
}

/* ── Main Meta Box Component ───────────────────────────────────────── */
function BrndlePageMetaBox() {
	const meta = useSelect(
		( select ) =>
			select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {},
		[]
	);
	const { editPost } = useDispatch( 'core/editor' );

	const setMeta = ( key, value ) => {
		editPost( { meta: { ...meta, [ key ]: value } } );
	};

	return createElement(
		'div',
		{ className: 'brndle-meta-box' },

		/* ── Grid of 4 cards ──────────────────────────── */
		createElement(
			'div',
			{ className: 'brndle-meta-grid' },

			/* Card 1: Layout */
			createElement(
				SettingsCard,
				{
					icon: icons.layout,
					title: 'Layout & Visibility',
					description: 'Page structure and content display',
					defaultOpen: true,
				},
				createElement( ToggleControl, {
					label: __( 'Hide Page Title', 'brndle' ),
					help: meta._brndle_hide_title
						? 'Title is hidden on the frontend'
						: 'Title is visible on the frontend',
					checked: !! meta._brndle_hide_title,
					onChange: ( v ) => setMeta( '_brndle_hide_title', v ),
					__nextHasNoMarginBottom: true,
				} ),
				createElement( 'div', { className: 'brndle-meta-spacer' } ),
				createElement( SelectControl, {
					label: __( 'Content Width', 'brndle' ),
					value: meta._brndle_content_width || '',
					options: [
						{ label: __( 'Default (max-w-4xl)', 'brndle' ), value: '' },
						{ label: __( 'Narrow \u2014 640px', 'brndle' ), value: 'narrow' },
						{ label: __( 'Wide \u2014 1280px', 'brndle' ), value: 'wide' },
						{ label: __( 'Full Width', 'brndle' ), value: 'full' },
					],
					onChange: ( v ) => setMeta( '_brndle_content_width', v ),
					help: 'Override content container width',
					__nextHasNoMarginBottom: true,
				} )
			),

			/* Card 2: Header & Footer */
			createElement(
				SettingsCard,
				{
					icon: icons.header,
					title: 'Header & Footer',
					description: 'Per-page header and footer overrides',
				},
				createElement( SelectControl, {
					label: __( 'Header Style', 'brndle' ),
					value: meta._brndle_header_style || '',
					options: [
						{ label: __( 'Use Global Setting', 'brndle' ), value: '' },
						{ label: __( 'Sticky', 'brndle' ), value: 'sticky' },
						{ label: __( 'Solid', 'brndle' ), value: 'solid' },
						{ label: __( 'Transparent', 'brndle' ), value: 'transparent' },
						{ label: __( 'Centered', 'brndle' ), value: 'centered' },
						{ label: __( 'Minimal', 'brndle' ), value: 'minimal' },
						{ label: __( 'Split', 'brndle' ), value: 'split' },
						{ label: __( 'Banner', 'brndle' ), value: 'banner' },
						{ label: __( 'Glass', 'brndle' ), value: 'glass' },
					],
					onChange: ( v ) => setMeta( '_brndle_header_style', v ),
					__nextHasNoMarginBottom: true,
				} ),
				createElement( 'div', { className: 'brndle-meta-spacer' } ),
				createElement( ToggleControl, {
					label: __( 'Hide Header', 'brndle' ),
					checked: !! meta._brndle_hide_header,
					onChange: ( v ) => setMeta( '_brndle_hide_header', v ),
					__nextHasNoMarginBottom: true,
				} ),
				createElement( 'div', { className: 'brndle-meta-divider' } ),
				createElement( SelectControl, {
					label: __( 'Footer Style', 'brndle' ),
					value: meta._brndle_footer_style || '',
					options: [
						{ label: __( 'Use Global Setting', 'brndle' ), value: '' },
						{ label: __( 'Dark', 'brndle' ), value: 'dark' },
						{ label: __( 'Light', 'brndle' ), value: 'light' },
						{ label: __( 'Columns', 'brndle' ), value: 'columns' },
						{ label: __( 'Minimal', 'brndle' ), value: 'minimal' },
						{ label: __( 'Big', 'brndle' ), value: 'big' },
						{ label: __( 'Stacked', 'brndle' ), value: 'stacked' },
					],
					onChange: ( v ) => setMeta( '_brndle_footer_style', v ),
					__nextHasNoMarginBottom: true,
				} ),
				createElement( 'div', { className: 'brndle-meta-spacer' } ),
				createElement( ToggleControl, {
					label: __( 'Hide Footer', 'brndle' ),
					checked: !! meta._brndle_hide_footer,
					onChange: ( v ) => setMeta( '_brndle_hide_footer', v ),
					__nextHasNoMarginBottom: true,
				} )
			),

			/* Card 3: Appearance */
			createElement(
				SettingsCard,
				{
					icon: icons.palette,
					title: 'Appearance',
					description: 'Color scheme and visual overrides',
				},
				createElement( SelectControl, {
					label: __( 'Color Scheme', 'brndle' ),
					value: meta._brndle_color_scheme || '',
					options: [
						{ label: __( 'Use Global Setting', 'brndle' ), value: '' },
						{ label: __( 'Sapphire', 'brndle' ), value: 'sapphire' },
						{ label: __( 'Indigo', 'brndle' ), value: 'indigo' },
						{ label: __( 'Cobalt', 'brndle' ), value: 'cobalt' },
						{ label: __( 'Trust', 'brndle' ), value: 'trust' },
						{ label: __( 'Commerce', 'brndle' ), value: 'commerce' },
						{ label: __( 'Signal', 'brndle' ), value: 'signal' },
						{ label: __( 'Coral', 'brndle' ), value: 'coral' },
						{ label: __( 'Aubergine', 'brndle' ), value: 'aubergine' },
						{ label: __( 'Midnight', 'brndle' ), value: 'midnight' },
						{ label: __( 'Stone', 'brndle' ), value: 'stone' },
						{ label: __( 'Carbon', 'brndle' ), value: 'carbon' },
						{ label: __( 'Neutral', 'brndle' ), value: 'neutral' },
					],
					onChange: ( v ) => setMeta( '_brndle_color_scheme', v ),
					help: 'Override accent color for this page only',
					__nextHasNoMarginBottom: true,
				} ),
				createElement( 'div', { className: 'brndle-meta-spacer' } ),
				createElement( TextControl, {
					label: __( 'Extra Body Classes', 'brndle' ),
					value: meta._brndle_body_class || '',
					onChange: ( v ) => setMeta( '_brndle_body_class', v ),
					placeholder: 'my-class another-class',
					help: 'Space-separated classes added to <body>',
					__nextHasNoMarginBottom: true,
				} )
			),

			/* Card 4: Custom CSS */
			createElement(
				SettingsCard,
				{
					icon: icons.code,
					title: 'Custom CSS',
					description: 'Page-specific styles',
				},
				createElement( TextareaControl, {
					value: meta._brndle_custom_css || '',
					onChange: ( v ) => setMeta( '_brndle_custom_css', v ),
					help: 'Injected in <head> for this page only. No <style> tags needed.',
					rows: 8,
					className: 'brndle-css-editor',
					placeholder: '.my-class {\n  color: var(--color-accent);\n}',
					__nextHasNoMarginBottom: true,
				} )
			)
		)
	);
}

/* ── Mount into PHP meta box container ─────────────────────────────── */
wp.domReady( () => {
	const container = document.getElementById( 'brndle-page-settings-root' );
	if ( ! container ) {
		return;
	}

	const root = createRoot( container );
	root.render( createElement( BrndlePageMetaBox ) );
} );
