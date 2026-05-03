/**
 * Hook: ensures every block instance has a stable, short unique-id we can
 * scope CSS / JS controllers to.
 *
 * The block's `attributes.uniqueId` is set on first edit using the first 8
 * characters of the editor `clientId`. Re-renders preserve the value;
 * duplicating a block (which gets a new clientId) re-fires the effect and
 * generates a fresh id, so duplicates don't share scope state.
 */

import { useEffect } from '@wordpress/element';

export function useUniqueId( clientId, attributes, setAttributes ) {
	useEffect( () => {
		if ( ! clientId ) {
			return;
		}
		const expected = clientId.replace( /-/g, '' ).slice( 0, 8 );
		if ( attributes.uniqueId !== expected ) {
			setAttributes( { uniqueId: expected } );
		}
	}, [ clientId ] );
}
