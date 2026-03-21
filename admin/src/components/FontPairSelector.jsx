import { useEffect, useRef } from '@wordpress/element';

const PAIRS = [
	{
		key: 'system',
		name: 'System',
		heading: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
		body: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
		source: 'GitHub',
		google: null,
	},
	{
		key: 'inter',
		name: 'Inter',
		heading: 'Inter, sans-serif',
		body: 'Inter, sans-serif',
		source: 'Linear, Notion, Figma',
		google: 'Inter:wght@400;500;600;700;800',
	},
	{
		key: 'geist',
		name: 'Geist',
		heading: 'Geist Sans, sans-serif',
		body: 'Geist Sans, sans-serif',
		source: 'Vercel',
		google: null,
	},
	{
		key: 'plex',
		name: 'IBM Plex',
		heading: 'IBM Plex Sans, sans-serif',
		body: 'IBM Plex Sans, sans-serif',
		source: 'IBM',
		google: 'IBM+Plex+Sans:wght@400;500;600;700',
	},
	{
		key: 'dm-sans',
		name: 'DM Sans',
		heading: 'DM Sans, sans-serif',
		body: 'DM Sans, sans-serif',
		source: 'Google Design',
		google: 'DM+Sans:wght@400;500;600;700',
	},
	{
		key: 'editorial',
		name: 'Editorial',
		heading: 'Playfair Display, serif',
		body: 'Source Serif 4, serif',
		source: 'NYT / Intercom',
		google: 'Playfair+Display:wght@400;600;700;800&family=Source+Serif+4:wght@400;500;600',
	},
	{
		key: 'magazine',
		name: 'Magazine',
		heading: 'Fraunces, serif',
		body: 'Libre Franklin, sans-serif',
		source: 'Premium editorial',
		google: 'Fraunces:wght@400;600;700;800&family=Libre+Franklin:wght@400;500;600',
	},
	{
		key: 'humanist',
		name: 'Humanist',
		heading: 'Merriweather, serif',
		body: 'Source Sans 3, sans-serif',
		source: 'Publishing',
		google: 'Merriweather:wght@400;700;900&family=Source+Sans+3:wght@400;500;600',
	},
];

export default function FontPairSelector( { selected, onChange } ) {
	const loadedRef = useRef( new Set() );

	useEffect( () => {
		const googlePairs = PAIRS.filter(
			( p ) => p.google && ! loadedRef.current.has( p.key )
		);
		if ( googlePairs.length === 0 ) return;

		const families = googlePairs.map( ( p ) => p.google ).join( '&family=' );
		const link = document.createElement( 'link' );
		link.rel = 'stylesheet';
		link.href = `https://fonts.googleapis.com/css2?family=${ families }&display=swap`;
		document.head.appendChild( link );

		googlePairs.forEach( ( p ) => loadedRef.current.add( p.key ) );
	}, [] );

	return (
		<div className="brndle-font-pair-selector">
			<h3 className="brndle-section-title">Font Pair</h3>
			<div className="brndle-font-grid">
				{ PAIRS.map( ( pair ) => (
					<button
						key={ pair.key }
						type="button"
						className={ `brndle-font-card${
							selected === pair.key ? ' selected' : ''
						}` }
						onClick={ () => onChange( pair.key ) }
					>
						<div
							className="brndle-font-heading-sample"
							style={ { fontFamily: pair.heading } }
						>
							The quick brown fox
						</div>
						<div
							className="brndle-font-body-sample"
							style={ { fontFamily: pair.body } }
						>
							Jumps over the lazy dog. Perfect typography
							creates a visual harmony that draws readers in.
						</div>
						<div className="brndle-font-meta">
							<strong>{ pair.name }</strong>
							{ ' ' }&middot; { pair.source }
						</div>
						{ selected === pair.key && (
							<span className="brndle-font-check">&#10003;</span>
						) }
					</button>
				) ) }
			</div>
		</div>
	);
}
