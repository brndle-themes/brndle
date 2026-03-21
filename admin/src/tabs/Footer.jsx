import { SelectControl, TextControl } from '@wordpress/components';
import ToggleRow from '../components/ToggleRow';

export default function Footer( { settings, onChange } ) {
	return (
		<div className="brndle-footer">
			<h3 className="brndle-section-title">Footer</h3>

			<SelectControl
				label="Footer Style"
				value={ settings.footer_style || 'dark' }
				options={ [
					{ label: 'Dark', value: 'dark' },
					{ label: 'Light', value: 'light' },
				] }
				onChange={ ( v ) => onChange( 'footer_style', v ) }
				__nextHasNoMarginBottom
			/>

			<SelectControl
				label="Widget Columns"
				value={ String( settings.footer_columns || 3 ) }
				options={ [
					{ label: '2 Columns', value: '2' },
					{ label: '3 Columns', value: '3' },
					{ label: '4 Columns', value: '4' },
				] }
				onChange={ ( v ) =>
					onChange( 'footer_columns', parseInt( v, 10 ) )
				}
				__nextHasNoMarginBottom
			/>

			<TextControl
				label="Copyright Text"
				value={ settings.footer_copyright || '' }
				onChange={ ( v ) => onChange( 'footer_copyright', v ) }
				placeholder="Auto-generated if left empty"
				help="Leave empty to auto-generate copyright text with the current year and site name."
				__nextHasNoMarginBottom
			/>

			<ToggleRow
				label="Show Social Icons"
				description="Display social media links in the footer"
				checked={ !! settings.footer_show_social }
				onChange={ ( v ) => onChange( 'footer_show_social', v ) }
			/>
		</div>
	);
}
