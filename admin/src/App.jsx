import { useState, useEffect, useCallback } from '@wordpress/element';
import { Button, Spinner, TabPanel, Snackbar } from '@wordpress/components';
import { fetchSettings, saveSettings, resetSettings } from './api';
import SiteIdentity from './tabs/SiteIdentity';
import Colors from './tabs/Colors';
import DarkMode from './tabs/DarkMode';
import Typography from './tabs/Typography';
import Header from './tabs/Header';
import Footer from './tabs/Footer';
import BlogArchive from './tabs/BlogArchive';
import SinglePost from './tabs/SinglePost';
import Performance from './tabs/Performance';

const TAB_CONFIG = [
	{ name: 'site-identity', title: 'Site Identity', Component: SiteIdentity },
	{ name: 'colors', title: 'Colors', Component: Colors },
	{ name: 'dark-mode', title: 'Dark Mode', Component: DarkMode },
	{ name: 'typography', title: 'Typography', Component: Typography },
	{ name: 'header', title: 'Header', Component: Header },
	{ name: 'footer', title: 'Footer', Component: Footer },
	{ name: 'blog-archive', title: 'Blog Archive', Component: BlogArchive },
	{ name: 'single-post', title: 'Single Post', Component: SinglePost },
	{ name: 'performance', title: 'Performance', Component: Performance },
];

export default function App() {
	const [ settings, setSettings ] = useState( {} );
	const [ loading, setLoading ] = useState( true );
	const [ saving, setSaving ] = useState( false );
	const [ dirty, setDirty ] = useState( false );
	const [ notice, setNotice ] = useState( null );

	useEffect( () => {
		fetchSettings()
			.then( ( s ) => {
				setSettings( s );
				setLoading( false );
			} )
			.catch( () => {
				setNotice( {
					status: 'error',
					message: 'Failed to load settings.',
				} );
				setLoading( false );
			} );
	}, [] );

	useEffect( () => {
		const handler = ( e ) => {
			if ( dirty ) {
				e.preventDefault();
				e.returnValue = '';
			}
		};
		window.addEventListener( 'beforeunload', handler );
		return () => window.removeEventListener( 'beforeunload', handler );
	}, [ dirty ] );

	const updateSetting = useCallback( ( key, value ) => {
		setSettings( ( prev ) => ( { ...prev, [ key ]: value } ) );
		setDirty( true );
	}, [] );

	const handleSave = async () => {
		setSaving( true );
		try {
			const result = await saveSettings( settings );
			setSettings( result.settings );
			setDirty( false );
			setNotice( { status: 'success', message: 'Settings saved.' } );
		} catch ( err ) {
			setNotice( { status: 'error', message: err.message } );
		}
		setSaving( false );
	};

	const handleReset = async () => {
		if (
			! window.confirm(
				'Reset all settings to defaults? This cannot be undone.'
			)
		)
			return;
		setSaving( true );
		try {
			const result = await resetSettings();
			setSettings( result.settings );
			setDirty( false );
			setNotice( {
				status: 'success',
				message: 'Settings reset to defaults.',
			} );
		} catch ( err ) {
			setNotice( { status: 'error', message: err.message } );
		}
		setSaving( false );
	};

	if ( loading ) {
		return (
			<div className="brndle-loading">
				<Spinner />
			</div>
		);
	}

	return (
		<div className="brndle-admin">
			<div className="brndle-admin-header">
				<div className="brndle-admin-title">
					<div className="brndle-logo">B</div>
					<h1>Brndle Settings</h1>
				</div>
				<div className="brndle-admin-actions">
					<Button
						variant="tertiary"
						onClick={ handleReset }
						disabled={ saving }
					>
						Reset to Defaults
					</Button>
					<Button
						variant="primary"
						onClick={ handleSave }
						disabled={ ! dirty || saving }
						isBusy={ saving }
					>
						{ saving ? 'Saving...' : 'Save Changes' }
					</Button>
				</div>
			</div>

			<TabPanel
				tabs={ TAB_CONFIG.map( ( t ) => ( {
					name: t.name,
					title: t.title,
				} ) ) }
			>
				{ ( tab ) => {
					const cfg = TAB_CONFIG.find(
						( t ) => t.name === tab.name
					);
					if ( ! cfg ) return null;
					const { Component } = cfg;
					return (
						<div className="brndle-tab-content">
							<Component
								settings={ settings }
								onChange={ updateSetting }
							/>
						</div>
					);
				} }
			</TabPanel>

			{ notice && (
				<Snackbar
					onRemove={ () => setNotice( null ) }
					status={ notice.status }
				>
					{ notice.message }
				</Snackbar>
			) }
		</div>
	);
}
