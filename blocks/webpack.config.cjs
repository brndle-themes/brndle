const defaults = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaults,
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
