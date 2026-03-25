import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'brndle/video-embed', {
	icon: (
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
			<rect x="2" y="4" width="20" height="16" rx="2" />
			<polygon points="10,8 16,12 10,16" fill="currentColor" stroke="none" />
		</svg>
	),

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();

		return (
			<>
				<InspectorControls>
					<PanelBody title="Content" initialOpen={ true }>
						<TextControl
							label="Eyebrow"
							value={ attributes.eyebrow }
							onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
						/>
						<TextareaControl
							label="Title"
							value={ attributes.title }
							onChange={ ( v ) => setAttributes( { title: v } ) }
							help="Supports HTML"
						/>
						<TextareaControl
							label="Subtitle"
							value={ attributes.subtitle }
							onChange={ ( v ) => setAttributes( { subtitle: v } ) }
						/>
					</PanelBody>
					<PanelBody title="Video" initialOpen={ true }>
						<TextControl
							label="Video URL"
							value={ attributes.video_url }
							onChange={ ( v ) => setAttributes( { video_url: v } ) }
							help="YouTube, Vimeo, or direct video URL"
						/>
						<SelectControl
							label="Video Source"
							value={ attributes.video_type }
							options={ [
								{ label: 'YouTube', value: 'youtube' },
								{ label: 'Vimeo', value: 'vimeo' },
								{ label: 'Self-hosted', value: 'self' },
							] }
							onChange={ ( v ) => setAttributes( { video_type: v } ) }
						/>
						{ attributes.video_type === 'self' && (
							<TextControl
								label="Poster Image URL"
								value={ attributes.poster }
								onChange={ ( v ) => setAttributes( { poster: v } ) }
								help="Thumbnail shown before video plays"
							/>
						) }
						<ToggleControl
							label="Autoplay"
							checked={ !! attributes.autoplay }
							onChange={ ( v ) => setAttributes( { autoplay: v } ) }
							help="Video will be muted when autoplaying"
						/>
						<ToggleControl
							label="Show Controls"
							checked={ !! attributes.show_controls }
							onChange={ ( v ) => setAttributes( { show_controls: v } ) }
						/>
					</PanelBody>
					<PanelBody title="Layout" initialOpen={ false }>
						<SelectControl
							label="Aspect Ratio"
							value={ attributes.aspect_ratio }
							options={ [
								{ label: '16:9 (Widescreen)', value: '16/9' },
								{ label: '4:3 (Classic)', value: '4/3' },
								{ label: '1:1 (Square)', value: '1/1' },
								{ label: '21:9 (Ultrawide)', value: '21/9' },
							] }
							onChange={ ( v ) => setAttributes( { aspect_ratio: v } ) }
						/>
						<SelectControl
							label="Max Width"
							value={ attributes.max_width }
							options={ [
								{ label: 'Full', value: 'full' },
								{ label: 'Medium', value: 'medium' },
								{ label: 'Narrow', value: 'narrow' },
							] }
							onChange={ ( v ) => setAttributes( { max_width: v } ) }
						/>
						<SelectControl
							label="Variant"
							value={ attributes.variant }
							options={ [
								{ label: 'Light', value: 'light' },
								{ label: 'Dark', value: 'dark' },
							] }
							onChange={ ( v ) => setAttributes( { variant: v } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<ServerSideRender
						block="brndle/video-embed"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
