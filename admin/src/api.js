const { restUrl, nonce } = window.brndleAdmin || {};

export async function fetchSettings() {
	const res = await fetch( restUrl, {
		headers: { 'X-WP-Nonce': nonce },
	} );
	if ( ! res.ok ) throw new Error( 'Failed to load settings' );
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
		throw new Error( 'Save failed: ' + res.statusText );
	}
	return res.json();
}

export async function resetSettings() {
	const res = await fetch( restUrl, {
		method: 'DELETE',
		headers: { 'X-WP-Nonce': nonce },
	} );
	if ( ! res.ok ) throw new Error( 'Reset failed' );
	return res.json();
}
