import { Button, TextControl } from '@wordpress/components';

function LogoUpload( { label, value, onSelect, onRemove } ) {
	const openMediaLibrary = () => {
		const frame = wp.media( {
			title: `Select ${ label }`,
			button: { text: 'Use this image' },
			multiple: false,
			library: { type: 'image' },
		} );
		frame.on( 'select', () => {
			const attachment = frame.state().get( 'selection' ).first().toJSON();
			onSelect( attachment.url );
		} );
		frame.open();
	};

	return (
		<div className="brndle-logo-upload">
			<label className="brndle-toggle-label">{ label }</label>
			<div className="brndle-logo-upload-area">
				{ value ? (
					<div className="brndle-logo-preview">
						<img src={ value } alt={ label } style={ { maxHeight: 60, maxWidth: 200 } } />
						<div className="brndle-logo-actions">
							<Button variant="secondary" size="small" onClick={ openMediaLibrary }>
								Replace
							</Button>
							<Button variant="tertiary" size="small" isDestructive onClick={ onRemove }>
								Remove
							</Button>
						</div>
					</div>
				) : (
					<Button variant="secondary" onClick={ openMediaLibrary }>
						Upload Image
					</Button>
				) }
			</div>
		</div>
	);
}

export default function SiteIdentity( { settings, onChange } ) {
	const socialLinks = settings.social_links || {};

	const updateSocialLink = ( key, value ) => {
		onChange( 'social_links', {
			...socialLinks,
			[ key ]: value,
		} );
	};

	return (
		<div className="brndle-site-identity">
			<h3 className="brndle-section-title">Logo</h3>

			<LogoUpload
				label="Logo (Light Background)"
				value={ settings.site_logo_light }
				onSelect={ ( url ) => onChange( 'site_logo_light', url ) }
				onRemove={ () => onChange( 'site_logo_light', '' ) }
			/>

			<LogoUpload
				label="Logo (Dark Background)"
				value={ settings.site_logo_dark }
				onSelect={ ( url ) => onChange( 'site_logo_dark', url ) }
				onRemove={ () => onChange( 'site_logo_dark', '' ) }
			/>

			<h3 className="brndle-section-title">Social Links</h3>

			<TextControl
				label="Twitter / X"
				value={ socialLinks.twitter || '' }
				onChange={ ( v ) => updateSocialLink( 'twitter', v ) }
				placeholder="https://twitter.com/yourhandle"
				__nextHasNoMarginBottom
			/>

			<TextControl
				label="LinkedIn"
				value={ socialLinks.linkedin || '' }
				onChange={ ( v ) => updateSocialLink( 'linkedin', v ) }
				placeholder="https://linkedin.com/in/yourprofile"
				__nextHasNoMarginBottom
			/>

			<TextControl
				label="GitHub"
				value={ socialLinks.github || '' }
				onChange={ ( v ) => updateSocialLink( 'github', v ) }
				placeholder="https://github.com/yourusername"
				__nextHasNoMarginBottom
			/>

			<TextControl
				label="Instagram"
				value={ socialLinks.instagram || '' }
				onChange={ ( v ) => updateSocialLink( 'instagram', v ) }
				placeholder="https://instagram.com/yourhandle"
				__nextHasNoMarginBottom
			/>
		</div>
	);
}
