import { __ } from '@wordpress/i18n';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, BaseControl, TextControl } from '@wordpress/components';

/**
 * Inspector image picker.
 *
 * Backed by core MediaUpload, falls back to a URL string when no attachment
 * is selected so existing posts that store a raw URL keep rendering. The
 * caller passes the three correlated attribute values (url, id, alt) and
 * receives a single `onChange({ image, imageId, imageAlt })` callback.
 */
export function ImageControl( {
	label,
	image,
	imageId,
	imageAlt,
	onChange,
	help,
	allowedTypes = [ 'image' ],
} ) {
	const handleSelect = ( media ) => {
		onChange( {
			image: media?.url || '',
			imageId: media?.id || 0,
			imageAlt: media?.alt || imageAlt || '',
		} );
	};

	const handleRemove = () => {
		onChange( { image: '', imageId: 0, imageAlt: '' } );
	};

	const handleAltChange = ( value ) => {
		onChange( { image, imageId, imageAlt: value } );
	};

	const handleUrlChange = ( value ) => {
		// Allow direct URL editing when no attachment is selected (e.g. CDN-hosted assets).
		onChange( { image: value, imageId: 0, imageAlt } );
	};

	return (
		<BaseControl
			__nextHasNoMarginBottom
			label={ label || __( 'Image', 'brndle' ) }
			help={ help }
		>
			<MediaUploadCheck>
				<MediaUpload
					onSelect={ handleSelect }
					allowedTypes={ allowedTypes }
					value={ imageId }
					render={ ( { open } ) => (
						<>
							{ image ? (
								<div
									style={ {
										marginBottom: 8,
										border: '1px solid #ddd',
										borderRadius: 4,
										overflow: 'hidden',
									} }
								>
									<img
										src={ image }
										alt=""
										style={ {
											display: 'block',
											maxWidth: '100%',
											height: 'auto',
										} }
									/>
								</div>
							) : null }
							<div
								style={ {
									display: 'flex',
									gap: 8,
									marginBottom: 12,
								} }
							>
								<Button onClick={ open } variant="secondary">
									{ image
										? __( 'Replace', 'brndle' )
										: __( 'Select image', 'brndle' ) }
								</Button>
								{ image ? (
									<Button
										onClick={ handleRemove }
										variant="tertiary"
										isDestructive
									>
										{ __( 'Remove', 'brndle' ) }
									</Button>
								) : null }
							</div>
						</>
					) }
				/>
			</MediaUploadCheck>
			<TextControl
				__nextHasNoMarginBottom
				label={ __( 'Image URL', 'brndle' ) }
				value={ image || '' }
				onChange={ handleUrlChange }
				help={ __(
					'Paste an external URL or use the picker above.',
					'brndle'
				) }
			/>
			<TextControl
				__nextHasNoMarginBottom
				label={ __( 'Alt text', 'brndle' ) }
				value={ imageAlt || '' }
				onChange={ handleAltChange }
				help={ __(
					'Describe the image for screen readers and SEO.',
					'brndle'
				) }
			/>
		</BaseControl>
	);
}
