import { useState } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ToggleRow from '../components/ToggleRow';
import { purgeCache } from '../api';

export default function Performance( { settings, onChange } ) {
	const [ purging, setPurging ] = useState( false );
	const [ purgeResult, setPurgeResult ] = useState( null );

	const handlePurge = async () => {
		setPurging( true );
		setPurgeResult( null );
		try {
			const result = await purgeCache();
			const views = result.cleared?.blade_views ?? 0;
			setPurgeResult( {
				status: 'success',
				message: `Cache purged — ${ views } compiled view${ views !== 1 ? 's' : '' } cleared.`,
			} );
		} catch ( err ) {
			setPurgeResult( { status: 'error', message: err.message } );
		}
		setPurging( false );
	};

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

			<div className="brndle-cache-section" style={ { marginTop: '2rem', paddingTop: '1.5rem', borderTop: '1px solid #e0e0e0' } }>
				<h3 className="brndle-section-title">{ __( 'Cache Management', 'brndle' ) }</h3>
				<p style={ { color: '#757575', fontSize: '13px', marginBottom: '1rem' } }>
					{ __( 'Clears compiled Blade views, settings cache, and WordPress object cache. Use after modifying templates or when changes are not reflecting on the frontend.', 'brndle' ) }
				</p>
				<Button
					variant="secondary"
					isDestructive
					onClick={ handlePurge }
					isBusy={ purging }
					disabled={ purging }
				>
					{ purging ? __( 'Purging...', 'brndle' ) : __( 'Purge All Caches', 'brndle' ) }
				</Button>
				{ purgeResult && (
					<p style={ {
						marginTop: '0.75rem',
						fontSize: '13px',
						color: purgeResult.status === 'success' ? '#00a32a' : '#d63638',
					} }>
						{ purgeResult.message }
					</p>
				) }
			</div>
		</div>
	);
}
