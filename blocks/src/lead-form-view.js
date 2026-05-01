/**
 * Frontend handler for the brndle/lead-form block.
 *
 * Submits via fetch to the configured REST endpoint, swaps the form for a
 * success message announced through an aria-live region, and restores the
 * submit button on failure with an inline error message.
 */

const SELECTOR = '[data-brndle-lead-form][data-brndle-endpoint]';
const BOUND = '_brndleBound';

function decodeEntities( text ) {
	if ( ! text ) {
		return '';
	}
	// Server-side `esc_attr()` encodes &, <, >, " — parse to text safely.
	return new DOMParser().parseFromString( text, 'text/html' ).body
		.textContent;
}

function announce( form, message ) {
	const live = form.querySelector( '[data-brndle-status]' );
	if ( live ) {
		live.textContent = message;
	}
}

function setError( form, message ) {
	let err = form.querySelector( '.brndle-form-error' );
	if ( ! err ) {
		err = document.createElement( 'p' );
		err.className = 'brndle-form-error text-sm text-red-400 mt-2';
		err.setAttribute( 'role', 'alert' );
		form.appendChild( err );
	}
	err.textContent = message;
}

function clearError( form ) {
	const err = form.querySelector( '.brndle-form-error' );
	if ( err ) {
		err.remove();
	}
}

async function submit( form, event ) {
	event.preventDefault();
	const button = form.querySelector( '[type="submit"]' );
	const originalLabel = button ? button.textContent : '';
	if ( button ) {
		button.disabled = true;
		button.textContent =
			form.dataset.sendingLabel || 'Sending…';
	}
	clearError( form );

	const data = {};
	new FormData( form ).forEach( ( value, key ) => {
		data[ key ] = value;
	} );
	data._source_url = window.location.href;
	if ( form.dataset.mailchimpList ) {
		data._mailchimp_list = form.dataset.mailchimpList;
	}

	try {
		const response = await fetch( form.dataset.brndleEndpoint, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': form.dataset.restNonce || '',
			},
			body: JSON.stringify( data ),
		} );
		const result = await response.json();

		if ( result && result.success ) {
			const message = decodeEntities( form.dataset.success );
			while ( form.firstChild ) {
				form.removeChild( form.firstChild );
			}
			const heading = document.createElement( 'p' );
			heading.className = 'text-center py-6 text-lg font-medium';
			heading.setAttribute( 'role', 'status' );
			heading.setAttribute( 'aria-live', 'polite' );
			heading.tabIndex = -1;
			heading.textContent = message;
			form.appendChild( heading );
			heading.focus?.();
			return;
		}

		if ( button ) {
			button.disabled = false;
			button.textContent = originalLabel;
		}
		const message =
			( result && result.message ) ||
			decodeEntities( form.dataset.errorMessage ) ||
			'Something went wrong.';
		setError( form, message );
		announce( form, message );
	} catch ( _err ) {
		if ( button ) {
			button.disabled = false;
			button.textContent = originalLabel;
		}
		const message =
			decodeEntities( form.dataset.errorMessage ) ||
			'Network error. Please try again.';
		setError( form, message );
		announce( form, message );
	}
}

function bind( form ) {
	if ( form[ BOUND ] ) {
		return;
	}
	form[ BOUND ] = true;
	form.addEventListener( 'submit', ( event ) => submit( form, event ) );
}

function init() {
	document.querySelectorAll( SELECTOR ).forEach( bind );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', init );
} else {
	init();
}
