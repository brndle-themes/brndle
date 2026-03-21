import { ToggleControl } from '@wordpress/components';

export default function ToggleRow( { label, description, checked, onChange } ) {
	return (
		<div className="brndle-toggle-row">
			<div className="brndle-toggle-info">
				<span className="brndle-toggle-label">{ label }</span>
				{ description && (
					<span className="brndle-toggle-desc">
						{ description }
					</span>
				) }
			</div>
			<ToggleControl
				checked={ checked }
				onChange={ onChange }
				__nextHasNoMarginBottom
			/>
		</div>
	);
}
