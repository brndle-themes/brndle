export default function LayoutSelector( {
	options,
	selected,
	onChange,
	columns = 4,
} ) {
	return (
		<div className="brndle-layout-selector">
			<div
				className="brndle-layout-grid"
				style={ {
					gridTemplateColumns: `repeat(${ columns }, 1fr)`,
				} }
			>
				{ options.map( ( option ) => (
					<button
						key={ option.key }
						type="button"
						aria-label={ `Select ${ option.name } layout` }
						aria-pressed={ selected === option.key }
						className={ `brndle-layout-card${
							selected === option.key ? ' selected' : ''
						}` }
						onClick={ () => onChange( option.key ) }
					>
						<div className="brndle-layout-icon">
							{ option.icon }
						</div>
						<div className="brndle-layout-name">
							{ option.name }
						</div>
						{ option.description && (
							<div className="brndle-layout-desc">
								{ option.description }
							</div>
						) }
						{ selected === option.key && (
							<span className="brndle-layout-check">
								&#10003;
							</span>
						) }
					</button>
				) ) }
			</div>
		</div>
	);
}
