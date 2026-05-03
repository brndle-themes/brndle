const path = require( 'path' );
const defaults = require( '@wordpress/scripts/config/webpack.config' );

const defaultEntry = typeof defaults.entry === 'function' ? defaults.entry() : defaults.entry;

module.exports = {
	...defaults,
	entry: {
		...defaultEntry,
		'page-meta-sidebar': path.resolve( __dirname, 'src/page-meta-sidebar.js' ),
		'lead-form-view': path.resolve( __dirname, 'src/lead-form-view.js' ),
		'code-view': path.resolve( __dirname, 'src/code-view.js' ),
		'timeline-view': path.resolve( __dirname, 'src/timeline-view.js' ),
		'tabs-accordion-view': path.resolve( __dirname, 'src/tabs-accordion-view.js' ),
	},
	resolve: {
		...defaults.resolve,
		extensions: [ '.js', '.jsx', '.ts', '.tsx', '.json' ],
		fullySpecified: false,
	},
	module: {
		...defaults.module,
		rules: [
			...( defaults.module?.rules || [] ).map( ( rule ) => {
				if ( rule.test?.toString().includes( 'mjs' ) ) {
					return {
						...rule,
						resolve: { ...( rule.resolve || {} ), fullySpecified: false },
					};
				}
				return rule;
			} ),
			{
				test: /\.jsx?$/,
				resolve: { fullySpecified: false },
			},
		],
	},
};
