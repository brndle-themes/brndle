import { SelectControl, TextControl } from '@wordpress/components';
import ToggleRow from '../components/ToggleRow';

export default function Header( { settings, onChange } ) {
	return (
		<div className="brndle-header">
			<h3 className="brndle-section-title">Header</h3>

			<SelectControl
				label="Header Style"
				value={ settings.header_style || 'sticky' }
				options={ [
					{ label: 'Sticky', value: 'sticky' },
					{ label: 'Solid', value: 'solid' },
					{ label: 'Transparent', value: 'transparent' },
					{ label: 'Centered', value: 'centered' },
					{ label: 'Minimal', value: 'minimal' },
					{ label: 'Split', value: 'split' },
					{ label: 'Banner', value: 'banner' },
					{ label: 'Glass', value: 'glass' },
				] }
				onChange={ ( v ) => onChange( 'header_style', v ) }
				__nextHasNoMarginBottom
			/>

			{ settings.header_style === 'banner' && (
				<TextControl
					label="Banner Text"
					value={ settings.header_banner_text || '' }
					onChange={ ( v ) => onChange( 'header_banner_text', v ) }
					placeholder="Free shipping on all orders"
					help="Announcement text displayed in the top banner bar."
					__nextHasNoMarginBottom
				/>
			) }

			<h3 className="brndle-section-title">Call to Action</h3>

			<TextControl
				label="CTA Button Text"
				value={ settings.header_cta_text || '' }
				onChange={ ( v ) => onChange( 'header_cta_text', v ) }
				placeholder="Get Started"
				__nextHasNoMarginBottom
			/>

			<TextControl
				label="CTA Button URL"
				value={ settings.header_cta_url || '' }
				onChange={ ( v ) => onChange( 'header_cta_url', v ) }
				placeholder="https://example.com/signup"
				__nextHasNoMarginBottom
			/>

			<h3 className="brndle-section-title">Behavior</h3>

			<SelectControl
				label="Sticky mode"
				help="How the header behaves on scroll. Static = no sticky. Sticky-fixed = always visible. Sticky-fade = sticky with subtle fade-in on scroll. Hide-on-scroll = scroll down hides, scroll up reveals."
				value={ settings.header_sticky_mode || 'sticky-fixed' }
				options={ [
					{ label: 'Static (no sticky)', value: 'static' },
					{ label: 'Sticky (always visible)', value: 'sticky-fixed' },
					{ label: 'Sticky with fade-on-scroll', value: 'sticky-fade' },
					{ label: 'Hide on scroll down, reveal on scroll up', value: 'sticky-hide-on-scroll' },
				] }
				onChange={ ( v ) => onChange( 'header_sticky_mode', v ) }
				__nextHasNoMarginBottom
			/>

			<ToggleRow
				label="Show header search"
				description="Adds a search icon to the header that opens a popover with the WordPress search form."
				checked={ !! settings.header_search_enabled }
				onChange={ ( v ) => onChange( 'header_search_enabled', v ) }
			/>

			<h3 className="brndle-section-title">Mobile</h3>

			<SelectControl
				label="Mobile Menu Style"
				value={ settings.header_mobile_style || 'slide' }
				options={ [
					{ label: 'Slide', value: 'slide' },
					{ label: 'Fullscreen', value: 'fullscreen' },
				] }
				onChange={ ( v ) => onChange( 'header_mobile_style', v ) }
				__nextHasNoMarginBottom
			/>
		</div>
	);
}
