import ColorSchemeSelector from '../components/ColorSchemeSelector';

export default function Colors( { settings, onChange } ) {
	return (
		<div className="brndle-colors">
			<ColorSchemeSelector
				selectedScheme={ settings.color_scheme || 'sapphire' }
				customAccent={ settings.custom_accent || '' }
				onSchemeChange={ ( scheme ) =>
					onChange( 'color_scheme', scheme )
				}
				onCustomAccentChange={ ( color ) =>
					onChange( 'custom_accent', color )
				}
			/>
		</div>
	);
}
