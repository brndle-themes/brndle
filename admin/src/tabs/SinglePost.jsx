import LayoutSelector from '../components/LayoutSelector';
import ToggleRow from '../components/ToggleRow';

const SINGLE_LAYOUTS = [
	{
		key: 'standard',
		name: 'Standard',
		description: 'Clean Vercel-style',
		icon: (
			<svg width="48" height="40" viewBox="0 0 48 40" fill="none">
				<rect x="8" y="2" width="32" height="10" rx="2" fill="#e0e0e0" />
				<rect x="8" y="15" width="24" height="3" rx="1" fill="#ccc" />
				<rect x="8" y="20" width="32" height="2" rx="1" fill="#e0e0e0" />
				<rect x="8" y="24" width="30" height="2" rx="1" fill="#e0e0e0" />
				<rect x="8" y="28" width="32" height="2" rx="1" fill="#e0e0e0" />
				<rect x="8" y="32" width="26" height="2" rx="1" fill="#e0e0e0" />
			</svg>
		),
	},
	{
		key: 'hero-immersive',
		name: 'Hero Immersive',
		description: 'Apple Newsroom style',
		icon: (
			<svg width="48" height="40" viewBox="0 0 48 40" fill="none">
				<rect x="0" y="0" width="48" height="18" fill="#333" />
				<rect x="10" y="8" width="28" height="4" rx="1" fill="#999" />
				<rect x="8" y="22" width="32" height="2" rx="1" fill="#e0e0e0" />
				<rect x="8" y="26" width="30" height="2" rx="1" fill="#e0e0e0" />
				<rect x="8" y="30" width="32" height="2" rx="1" fill="#e0e0e0" />
			</svg>
		),
	},
	{
		key: 'sidebar',
		name: 'Sidebar',
		description: 'Content + TOC sidebar',
		icon: (
			<svg width="48" height="40" viewBox="0 0 48 40" fill="none">
				<rect x="2" y="2" width="30" height="8" rx="2" fill="#e0e0e0" />
				<rect x="2" y="13" width="30" height="2" rx="1" fill="#e0e0e0" />
				<rect x="2" y="17" width="28" height="2" rx="1" fill="#e0e0e0" />
				<rect x="2" y="21" width="30" height="2" rx="1" fill="#e0e0e0" />
				<rect x="34" y="2" width="12" height="26" rx="2" fill="#f0f0f0" stroke="#e0e0e0" />
				<rect x="36" y="6" width="8" height="2" rx="1" fill="#e0e0e0" />
				<rect x="36" y="12" width="8" height="2" rx="1" fill="#e0e0e0" />
			</svg>
		),
	},
	{
		key: 'editorial',
		name: 'Editorial',
		description: 'NYT long-read style',
		icon: (
			<svg width="48" height="40" viewBox="0 0 48 40" fill="none">
				<rect x="6" y="2" width="6" height="12" rx="1" fill="#ccc" />
				<rect x="14" y="2" width="28" height="2" rx="1" fill="#e0e0e0" />
				<rect x="14" y="6" width="26" height="2" rx="1" fill="#e0e0e0" />
				<rect x="14" y="10" width="28" height="2" rx="1" fill="#e0e0e0" />
				<rect x="6" y="16" width="36" height="2" rx="1" fill="#e0e0e0" />
				<rect x="2" y="22" width="44" height="8" rx="2" fill="#f5f5f5" />
				<rect x="6" y="32" width="36" height="2" rx="1" fill="#e0e0e0" />
				<rect x="6" y="36" width="32" height="2" rx="1" fill="#e0e0e0" />
			</svg>
		),
	},
	{
		key: 'cinematic',
		name: 'Cinematic',
		description: 'Figma blog style',
		icon: (
			<svg width="48" height="40" viewBox="0 0 48 40" fill="none">
				<rect x="0" y="0" width="48" height="12" fill="#e0e0e0" />
				<rect x="12" y="15" width="24" height="4" rx="1" fill="#ccc" />
				<rect x="8" y="22" width="32" height="2" rx="1" fill="#e0e0e0" />
				<rect x="0" y="28" width="48" height="8" fill="#f0f0f0" />
				<rect x="8" y="38" width="32" height="2" rx="1" fill="#e0e0e0" />
			</svg>
		),
	},
	{
		key: 'presentation',
		name: 'Presentation',
		description: 'Linear slide style',
		icon: (
			<svg width="48" height="40" viewBox="0 0 48 40" fill="none">
				<rect x="0" y="0" width="48" height="18" fill="#f5f5f5" />
				<rect x="14" y="6" width="20" height="4" rx="1" fill="#ccc" />
				<line x1="0" y1="19" x2="48" y2="19" stroke="#e0e0e0" />
				<rect x="0" y="20" width="48" height="20" fill="#fafafa" />
				<rect x="4" y="22" width="2" height="8" rx="1" fill="#ddd" />
				<rect x="8" y="24" width="32" height="2" rx="1" fill="#e0e0e0" />
				<rect x="8" y="28" width="28" height="2" rx="1" fill="#e0e0e0" />
			</svg>
		),
	},
	{
		key: 'split',
		name: 'Split',
		description: 'Stripe two-column',
		icon: (
			<svg width="48" height="40" viewBox="0 0 48 40" fill="none">
				<rect x="0" y="0" width="22" height="40" fill="#f5f5f5" />
				<rect x="4" y="10" width="14" height="4" rx="1" fill="#ccc" />
				<rect x="4" y="16" width="12" height="2" rx="1" fill="#e0e0e0" />
				<line x1="23" y1="0" x2="23" y2="40" stroke="#e0e0e0" />
				<rect x="26" y="4" width="18" height="2" rx="1" fill="#e0e0e0" />
				<rect x="26" y="8" width="16" height="2" rx="1" fill="#e0e0e0" />
				<rect x="26" y="12" width="18" height="2" rx="1" fill="#e0e0e0" />
				<rect x="26" y="16" width="14" height="2" rx="1" fill="#e0e0e0" />
				<rect x="26" y="20" width="18" height="2" rx="1" fill="#e0e0e0" />
			</svg>
		),
	},
	{
		key: 'minimal-dark',
		name: 'Minimal Dark',
		description: 'Mercury/Linear dark',
		icon: (
			<svg width="48" height="40" viewBox="0 0 48 40" fill="none">
				<rect x="0" y="0" width="48" height="40" fill="#1a1a1a" />
				<rect x="12" y="8" width="24" height="3" rx="1" fill="#555" />
				<rect x="12" y="14" width="24" height="2" rx="1" fill="#444" />
				<rect x="12" y="18" width="22" height="2" rx="1" fill="#444" />
				<rect x="12" y="22" width="24" height="2" rx="1" fill="#444" />
				<rect x="12" y="26" width="20" height="2" rx="1" fill="#444" />
				<rect x="12" y="30" width="24" height="2" rx="1" fill="#444" />
			</svg>
		),
	},
];

export default function SinglePost( { settings, onChange } ) {
	return (
		<div className="brndle-single-post">
			<h3 className="brndle-section-title">Post Layout</h3>

			<LayoutSelector
				options={ SINGLE_LAYOUTS }
				selected={ settings.single_layout || 'standard' }
				onChange={ ( v ) => onChange( 'single_layout', v ) }
				columns={ 4 }
			/>

			<h3 className="brndle-section-title">Post Features</h3>

			<ToggleRow
				label="Reading Progress Bar"
				description="Show a progress bar at the top as visitors scroll"
				checked={ !! settings.single_show_progress_bar }
				onChange={ ( v ) => onChange( 'single_show_progress_bar', v ) }
			/>

			<ToggleRow
				label="Reading Time"
				description="Display estimated reading time in the post header"
				checked={ !! settings.single_show_reading_time }
				onChange={ ( v ) => onChange( 'single_show_reading_time', v ) }
			/>

			<ToggleRow
				label="Author Box"
				description="Show author bio at the end of posts"
				checked={ !! settings.single_show_author_box }
				onChange={ ( v ) => onChange( 'single_show_author_box', v ) }
			/>

			<ToggleRow
				label="Social Share Buttons"
				description="Display share buttons for Twitter, LinkedIn, and more"
				checked={ !! settings.single_show_social_share }
				onChange={ ( v ) => onChange( 'single_show_social_share', v ) }
			/>

			<ToggleRow
				label="Related Posts"
				description="Show related posts at the bottom of articles"
				checked={ !! settings.single_show_related_posts }
				onChange={ ( v ) => onChange( 'single_show_related_posts', v ) }
			/>

			<ToggleRow
				label="Table of Contents"
				description="Auto-generate a table of contents from headings"
				checked={ !! settings.single_show_toc }
				onChange={ ( v ) => onChange( 'single_show_toc', v ) }
			/>

			<ToggleRow
				label="Post Navigation"
				description="Show previous/next post links"
				checked={ !! settings.single_show_post_nav }
				onChange={ ( v ) => onChange( 'single_show_post_nav', v ) }
			/>
		</div>
	);
}
