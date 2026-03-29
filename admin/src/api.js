const { restUrl, cacheUrl, nonce } = window.brndleAdmin || {};

if ( ! restUrl || ! nonce ) {
	// eslint-disable-next-line no-console
	console.error( 'Brndle: Missing REST API configuration. Admin scripts may not be loaded correctly.' );
}

export async function fetchSettings() {
	const res = await fetch( restUrl, {
		headers: { 'X-WP-Nonce': nonce },
	} );
	if ( ! res.ok ) {
		if ( res.status === 403 ) throw new Error( 'Permission denied. Please refresh and try again.' );
		if ( res.status === 404 ) throw new Error( 'Settings endpoint not found. Theme may need reactivation.' );
		if ( res.status >= 500 ) throw new Error( 'Server error. Please try again later.' );
		throw new Error( 'Failed to load settings' );
	}
	return res.json();
}

export async function saveSettings( settings ) {
	const res = await fetch( restUrl, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-WP-Nonce': nonce,
		},
		body: JSON.stringify( settings ),
	} );
	if ( ! res.ok ) {
		if ( res.status === 403 )
			throw new Error(
				'Session expired. Please refresh the page.'
			);
		if ( res.status >= 500 ) throw new Error( 'Server error. Please try again later.' );
		throw new Error( 'Save failed: ' + res.statusText );
	}
	return res.json();
}

export async function resetSettings() {
	const res = await fetch( restUrl, {
		method: 'DELETE',
		headers: { 'X-WP-Nonce': nonce },
	} );
	if ( ! res.ok ) {
		if ( res.status === 403 ) throw new Error( 'Session expired. Please refresh the page.' );
		throw new Error( 'Reset failed' );
	}
	return res.json();
}

export async function purgeCache() {
	const res = await fetch( cacheUrl, {
		method: 'POST',
		headers: { 'X-WP-Nonce': nonce },
	} );
	if ( ! res.ok ) {
		if ( res.status === 403 ) throw new Error( 'Session expired. Please refresh the page.' );
		throw new Error( 'Cache purge failed' );
	}
	return res.json();
}
