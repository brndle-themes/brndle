import ToggleRow from '../components/ToggleRow';

export default function Performance( { settings, onChange } ) {
	return (
		<div className="brndle-performance">
			<h3 className="brndle-section-title">Performance Optimizations</h3>

			<ToggleRow
				label="Remove Emoji Scripts"
				description="Remove WordPress emoji detection scripts to improve page load speed"
				checked={ !! settings.perf_remove_emoji }
				onChange={ ( v ) => onChange( 'perf_remove_emoji', v ) }
			/>

			<ToggleRow
				label="Remove wp-embed"
				description="Remove the wp-embed script used for embedding WordPress posts"
				checked={ !! settings.perf_remove_embed }
				onChange={ ( v ) => onChange( 'perf_remove_embed', v ) }
			/>

			<ToggleRow
				label="Lazy Load Images"
				description="Defer off-screen images until they enter the viewport"
				checked={ !! settings.perf_lazy_images }
				onChange={ ( v ) => onChange( 'perf_lazy_images', v ) }
			/>

			<ToggleRow
				label="Preload Fonts"
				description="Preload the selected font pair for faster text rendering"
				checked={ !! settings.perf_preload_fonts }
				onChange={ ( v ) => onChange( 'perf_preload_fonts', v ) }
			/>

			<ToggleRow
				label="Remove Global Styles"
				description="Remove WordPress core block styles (~110KB). Enable only if your content relies on Brndle's Tailwind styling rather than default Gutenberg block styles."
				checked={ !! settings.perf_remove_global_styles }
				onChange={ ( v ) => onChange( 'perf_remove_global_styles', v ) }
			/>
		</div>
	);
}
