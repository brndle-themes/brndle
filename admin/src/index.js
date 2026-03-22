import { createRoot, Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import App from './App';
import './admin.css';

class ErrorBoundary extends Component {
	constructor( props ) {
		super( props );
		this.state = { hasError: false };
	}

	static getDerivedStateFromError() {
		return { hasError: true };
	}

	render() {
		if ( this.state.hasError ) {
			return (
				<div className="brndle-admin" style={ { padding: '2rem', textAlign: 'center' } }>
					<h2>{ __( 'Something went wrong loading the settings panel.', 'brndle' ) }</h2>
					<p>{ __( 'Try refreshing the page. If the problem persists, check the browser console for errors.', 'brndle' ) }</p>
					<button
						type="button"
						className="components-button is-primary"
						onClick={ () => window.location.reload() }
					>
						{ __( 'Refresh Page', 'brndle' ) }
					</button>
				</div>
			);
		}
		return this.props.children;
	}
}

const root = document.getElementById( 'brndle-settings-root' );
if ( root ) {
	createRoot( root ).render(
		<ErrorBoundary>
			<App />
		</ErrorBoundary>
	);
}
