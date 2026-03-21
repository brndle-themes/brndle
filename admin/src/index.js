import { createRoot } from '@wordpress/element';
import App from './App';
import './admin.css';

const root = document.getElementById( 'brndle-settings-root' );
if ( root ) {
	createRoot( root ).render( <App /> );
}
