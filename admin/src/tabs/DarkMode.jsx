import { SelectControl, Notice } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ToggleRow from '../components/ToggleRow';

export default function DarkMode( { settings, onChange } ) {
	const toggle = !! settings.dark_mode_toggle;
	const defaultMode = settings.dark_mode_default || 'light';
	const fixedMode = ! toggle;

	const fixedModeNotice = ! toggle && {
		light: __(
			'Fixed mode: the site always displays in light. No theme-switching JS runs, and any stored visitor preference is cleared on next visit.',
			'brndle'
		),
		dark: __(
			'Fixed mode: the site always displays in dark. No theme-switching JS runs, and any stored visitor preference is cleared on next visit.',
			'brndle'
		),
		system: __(
			'Fixed mode: the site follows each visitor\'s operating system preference automatically. No toggle is shown.',
			'brndle'
		),
	}[ defaultMode ];

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

			{ fixedMode && fixedModeNotice && (
				<Notice status="info" isDismissible={ false }>
					{ fixedModeNotice }
				</Notice>
			) }
		</div>
	);
}
