import { RangeControl } from '@wordpress/components';
import LayoutSelector from '../components/LayoutSelector';
import ToggleRow from '../components/ToggleRow';

const ARCHIVE_LAYOUTS = [
	{
		key: 'grid',
		name: 'Grid',
		description: 'Card grid layout',
		icon: (
			<svg width="48" height="40" viewBox="0 0 48 40" fill="none">
				<rect x="1" y="1" width="14" height="18" rx="2" fill="#e0e0e0" />
				<rect x="17" y="1" width="14" height="18" rx="2" fill="#e0e0e0" />
				<rect x="33" y="1" width="14" height="18" rx="2" fill="#e0e0e0" />
				<rect x="1" y="22" width="14" height="18" rx="2" fill="#e0e0e0" />
				<rect x="17" y="22" width="14" height="18" rx="2" fill="#e0e0e0" />
				<rect x="33" y="22" width="14" height="18" rx="2" fill="#e0e0e0" />
			</svg>
		),
	},
	{
		key: 'list',
		name: 'List',
		description: 'Horizontal rows',
		icon: (
			<svg width="48" height="40" viewBox="0 0 48 40" fill="none">
				<rect x="1" y="2" width="16" height="10" rx="2" fill="#e0e0e0" />
				<rect x="20" y="3" width="20" height="3" rx="1" fill="#ccc" />
				<rect x="20" y="8" width="26" height="2" rx="1" fill="#e0e0e0" />
				<rect x="1" y="16" width="16" height="10" rx="2" fill="#e0e0e0" />
				<rect x="20" y="17" width="20" height="3" rx="1" fill="#ccc" />
				<rect x="20" y="22" width="26" height="2" rx="1" fill="#e0e0e0" />
				<rect x="1" y="30" width="16" height="10" rx="2" fill="#e0e0e0" />
				<rect x="20" y="31" width="20" height="3" rx="1" fill="#ccc" />
				<rect x="20" y="36" width="26" height="2" rx="1" fill="#e0e0e0" />
			</svg>
		),
	},
	{
		key: 'magazine',
		name: 'Magazine',
		description: 'Featured + grid',
		icon: (
			<svg width="48" height="40" viewBox="0 0 48 40" fill="none">
				<rect x="1" y="1" width="30" height="24" rx="2" fill="#e0e0e0" />
				<rect x="33" y="1" width="14" height="11" rx="2" fill="#e0e0e0" />
				<rect x="33" y="14" width="14" height="11" rx="2" fill="#e0e0e0" />
				<rect x="1" y="28" width="14" height="12" rx="2" fill="#e0e0e0" />
				<rect x="17" y="28" width="14" height="12" rx="2" fill="#e0e0e0" />
				<rect x="33" y="28" width="14" height="12" rx="2" fill="#e0e0e0" />
			</svg>
		),
	},
	{
		key: 'editorial',
		name: 'Editorial',
		description: 'Text-focused',
		icon: (
			<svg width="48" height="40" viewBox="0 0 48 40" fill="none">
				<rect x="8" y="2" width="32" height="4" rx="1" fill="#ccc" />
				<rect x="12" y="8" width="24" height="2" rx="1" fill="#e0e0e0" />
				<line x1="4" y1="14" x2="44" y2="14" stroke="#f0f0f0" strokeWidth="1" />
				<rect x="8" y="18" width="32" height="4" rx="1" fill="#ccc" />
				<rect x="12" y="24" width="24" height="2" rx="1" fill="#e0e0e0" />
				<line x1="4" y1="30" x2="44" y2="30" stroke="#f0f0f0" strokeWidth="1" />
				<rect x="8" y="34" width="32" height="4" rx="1" fill="#ccc" />
			</svg>
		),
	},
	{
		key: 'minimal',
		name: 'Minimal',
		description: 'Clean and simple',
		icon: (
			<svg width="48" height="40" viewBox="0 0 48 40" fill="none">
				<rect x="6" y="4" width="36" height="3" rx="1" fill="#ccc" />
				<rect x="6" y="9" width="28" height="2" rx="1" fill="#e0e0e0" />
				<rect x="6" y="16" width="36" height="3" rx="1" fill="#ccc" />
				<rect x="6" y="21" width="28" height="2" rx="1" fill="#e0e0e0" />
				<rect x="6" y="28" width="36" height="3" rx="1" fill="#ccc" />
				<rect x="6" y="33" width="28" height="2" rx="1" fill="#e0e0e0" />
			</svg>
		),
	},
];

export default function BlogArchive( { settings, onChange } ) {
	return (
		<div className="brndle-blog-archive">
			<h3 className="brndle-section-title">Archive Layout</h3>

			<LayoutSelector
				options={ ARCHIVE_LAYOUTS }
				selected={ settings.archive_layout || 'grid' }
				onChange={ ( v ) => onChange( 'archive_layout', v ) }
				columns={ 5 }
			/>

			<h3 className="brndle-section-title">Display Options</h3>

			<RangeControl
				label="Posts Per Page"
				value={ settings.archive_posts_per_page || 12 }
				onChange={ ( v ) => onChange( 'archive_posts_per_page', v ) }
				min={ 6 }
				max={ 24 }
				step={ 3 }
				__nextHasNoMarginBottom
			/>

			<ToggleRow
				label="Show Sidebar"
				description="Display a sidebar on archive pages"
				checked={ !! settings.archive_show_sidebar }
				onChange={ ( v ) => onChange( 'archive_show_sidebar', v ) }
			/>

			<ToggleRow
				label="Show Category Filter"
				description="Display category filter bar above posts"
				checked={ !! settings.archive_show_category_filter }
				onChange={ ( v ) =>
					onChange( 'archive_show_category_filter', v )
				}
			/>
		</div>
	);
}
