import { SelectControl, TextControl } from '@wordpress/components';

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
				] }
				onChange={ ( v ) => onChange( 'header_style', v ) }
				__nextHasNoMarginBottom
			/>

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
