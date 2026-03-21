import { useState } from '@wordpress/element';
import { CheckboxControl, ColorPicker } from '@wordpress/components';

const PRESETS = [
	{ key: 'neutral', name: 'Neutral', accent: '#18181b' },
	{ key: 'sapphire', name: 'Sapphire', accent: '#0070F3' },
	{ key: 'indigo', name: 'Indigo', accent: '#635BFF' },
	{ key: 'cobalt', name: 'Cobalt', accent: '#0C66E4' },
	{ key: 'trust', name: 'Trust', accent: '#0530AD' },
	{ key: 'commerce', name: 'Commerce', accent: '#2a6e3f' },
	{ key: 'signal', name: 'Signal', accent: '#F22F46' },
	{ key: 'coral', name: 'Coral', accent: '#FF7A59' },
	{ key: 'aubergine', name: 'Aubergine', accent: '#4A154B' },
	{ key: 'midnight', name: 'Midnight', accent: '#1e3a5f' },
	{ key: 'stone', name: 'Stone', accent: '#57534e' },
	{ key: 'carbon', name: 'Carbon', accent: '#09090b' },
];

function hexToHSL( hex ) {
	let r = parseInt( hex.slice( 1, 3 ), 16 ) / 255;
	let g = parseInt( hex.slice( 3, 5 ), 16 ) / 255;
	let b = parseInt( hex.slice( 5, 7 ), 16 ) / 255;

	const max = Math.max( r, g, b );
	const min = Math.min( r, g, b );
	let h = 0;
	let s = 0;
	const l = ( max + min ) / 2;

	if ( max !== min ) {
		const d = max - min;
		s = l > 0.5 ? d / ( 2 - max - min ) : d / ( max + min );
		switch ( max ) {
			case r:
				h = ( ( g - b ) / d + ( g < b ? 6 : 0 ) ) / 6;
				break;
			case g:
				h = ( ( b - r ) / d + 2 ) / 6;
				break;
			case b:
				h = ( ( r - g ) / d + 4 ) / 6;
				break;
		}
	}

	return { h: Math.round( h * 360 ), s: Math.round( s * 100 ), l: Math.round( l * 100 ) };
}

function generateVariants( hex ) {
	const { h, s } = hexToHSL( hex );
	return {
		accent: hex,
		hover: `hsl(${ h }, ${ s }%, 35%)`,
		light: `hsl(${ h }, ${ Math.min( s, 80 ) }%, 95%)`,
		subtle: `hsl(${ h }, ${ Math.min( s, 60 ) }%, 98%)`,
	};
}

export default function ColorSchemeSelector( {
	selectedScheme,
	customAccent,
	onSchemeChange,
	onCustomAccentChange,
} ) {
	const [ useCustom, setUseCustom ] = useState( !! customAccent );

	const activeAccent = useCustom && customAccent
		? customAccent
		: ( PRESETS.find( ( p ) => p.key === selectedScheme )?.accent || '#0070F3' );

	const variants = generateVariants( activeAccent );

	const handleUseCustomToggle = ( checked ) => {
		setUseCustom( checked );
		if ( ! checked ) {
			onCustomAccentChange( '' );
		}
	};

	return (
		<div className="brndle-color-scheme-selector">
			<h3 className="brndle-section-title">Color Scheme</h3>
			<div className="brndle-scheme-grid">
				{ PRESETS.map( ( preset ) => (
					<button
						key={ preset.key }
						type="button"
						className={ `brndle-scheme-card${
							! useCustom && selectedScheme === preset.key
								? ' selected'
								: ''
						}` }
						onClick={ () => {
							onSchemeChange( preset.key );
							setUseCustom( false );
							onCustomAccentChange( '' );
						} }
					>
						<div className="brndle-scheme-preview">
							<div
								className="brndle-scheme-preview-header"
								style={ { backgroundColor: preset.accent } }
							/>
							<div className="brndle-scheme-preview-body">
								<div
									className="brndle-scheme-preview-cta"
									style={ {
										backgroundColor: preset.accent,
									} }
								/>
							</div>
						</div>
						<span className="brndle-scheme-name">
							{ preset.name }
						</span>
						{ ! useCustom && selectedScheme === preset.key && (
							<span className="brndle-scheme-check">&#10003;</span>
						) }
					</button>
				) ) }
			</div>

			<CheckboxControl
				label="Use custom brand color"
				checked={ useCustom }
				onChange={ handleUseCustomToggle }
				__nextHasNoMarginBottom
			/>

			{ useCustom && (
				<div className="brndle-custom-color-picker">
					<ColorPicker
						color={ customAccent || '#0070F3' }
						onChange={ ( color ) => onCustomAccentChange( color ) }
						enableAlpha={ false }
					/>
				</div>
			) }

			<h3 className="brndle-section-title">Preview</h3>
			<div className="brndle-swatch-bar">
				<div
					className="brndle-swatch"
					style={ { backgroundColor: variants.accent } }
					title="Accent"
				/>
				<div
					className="brndle-swatch"
					style={ { backgroundColor: variants.hover } }
					title="Hover"
				/>
				<div
					className="brndle-swatch"
					style={ { backgroundColor: variants.light } }
					title="Light"
				/>
				<div
					className="brndle-swatch"
					style={ { backgroundColor: variants.subtle } }
					title="Subtle"
				/>
			</div>
		</div>
	);
}
