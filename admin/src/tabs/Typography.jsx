import { RangeControl } from '@wordpress/components';
import FontPairSelector from '../components/FontPairSelector';

export default function Typography( { settings, onChange } ) {
	return (
		<div className="brndle-typography">
			<FontPairSelector
				selected={ settings.font_pair || 'inter' }
				onChange={ ( pair ) => onChange( 'font_pair', pair ) }
			/>

			<h3 className="brndle-section-title">Size &amp; Scale</h3>

			<RangeControl
				label="Base Font Size (px)"
				value={ settings.font_size_base || 16 }
				onChange={ ( v ) => onChange( 'font_size_base', v ) }
				min={ 12 }
				max={ 24 }
				step={ 1 }
				__nextHasNoMarginBottom
			/>

			<RangeControl
				label="Heading Scale Ratio"
				help="Controls how much larger each heading level is relative to the previous. 1.25 is the Major Third scale."
				value={ settings.heading_scale || 1.25 }
				onChange={ ( v ) => onChange( 'heading_scale', v ) }
				min={ 1.1 }
				max={ 1.5 }
				step={ 0.05 }
				__nextHasNoMarginBottom
			/>
		</div>
	);
}
