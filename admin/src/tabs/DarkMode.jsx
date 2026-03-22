import { SelectControl, Notice } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ToggleRow from '../components/ToggleRow';

export default function DarkMode( { settings, onChange } ) {
	const toggle = !! settings.dark_mode_toggle;
	const defaultMode = settings.dark_mode_default || 'light';
	const darkModeDisabled = ! toggle && defaultMode === 'light';

	return (
		<div className="brndle-dark-mode">
			<h3 className="brndle-section-title">
				{ __( 'Dark Mode', 'brndle' ) }
			</h3>

			<ToggleRow
				label={ __( 'Show Dark Mode Toggle', 'brndle' ) }
				description={ __(
					'Let visitors switch between light and dark mode',
					'brndle'
				) }
				checked={ toggle }
				onChange={ ( v ) => onChange( 'dark_mode_toggle', v ) }
			/>

			{ toggle && (
				<SelectControl
					label={ __( 'Toggle Position', 'brndle' ) }
					value={
						settings.dark_mode_toggle_position || 'bottom-right'
					}
					options={ [
						{ label: __( 'Bottom Right', 'brndle' ), value: 'bottom-right' },
						{ label: __( 'Bottom Left', 'brndle' ), value: 'bottom-left' },
						{ label: __( 'Header', 'brndle' ), value: 'header' },
					] }
					onChange={ ( v ) =>
						onChange( 'dark_mode_toggle_position', v )
					}
					__nextHasNoMarginBottom
				/>
			) }

			<SelectControl
				label={ __( 'Default Mode', 'brndle' ) }
				value={ defaultMode }
				options={ [
					{ label: __( 'Light', 'brndle' ), value: 'light' },
					{ label: __( 'Dark', 'brndle' ), value: 'dark' },
					{ label: __( 'System (auto)', 'brndle' ), value: 'system' },
				] }
				onChange={ ( v ) => onChange( 'dark_mode_default', v ) }
				__nextHasNoMarginBottom
			/>

			{ darkModeDisabled && (
				<Notice status="info" isDismissible={ false }>
					{ __(
						'Dark mode is fully disabled. The site will always display in light mode. No dark mode CSS is loaded — saving bandwidth.',
						'brndle'
					) }
				</Notice>
			) }
		</div>
	);
}
