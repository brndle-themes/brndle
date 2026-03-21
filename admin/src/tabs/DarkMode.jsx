import { SelectControl } from '@wordpress/components';
import ToggleRow from '../components/ToggleRow';

export default function DarkMode( { settings, onChange } ) {
	return (
		<div className="brndle-dark-mode">
			<h3 className="brndle-section-title">Dark Mode</h3>

			<SelectControl
				label="Default Mode"
				value={ settings.dark_mode_default || 'light' }
				options={ [
					{ label: 'Light', value: 'light' },
					{ label: 'Dark', value: 'dark' },
					{ label: 'System (auto)', value: 'system' },
				] }
				onChange={ ( v ) => onChange( 'dark_mode_default', v ) }
				__nextHasNoMarginBottom
			/>

			<ToggleRow
				label="Show Dark Mode Toggle"
				description="Let visitors switch between light and dark mode"
				checked={ !! settings.dark_mode_toggle }
				onChange={ ( v ) => onChange( 'dark_mode_toggle', v ) }
			/>

			{ settings.dark_mode_toggle && (
				<SelectControl
					label="Toggle Position"
					value={
						settings.dark_mode_toggle_position || 'bottom-right'
					}
					options={ [
						{ label: 'Bottom Right', value: 'bottom-right' },
						{ label: 'Bottom Left', value: 'bottom-left' },
						{ label: 'Header', value: 'header' },
					] }
					onChange={ ( v ) =>
						onChange( 'dark_mode_toggle_position', v )
					}
					__nextHasNoMarginBottom
				/>
			) }
		</div>
	);
}
