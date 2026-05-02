import { registerBlockType } from '@wordpress/blocks';
import { video } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
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
	icon: video,

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Content', 'brndle' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'Eyebrow', 'brndle' ) }
							value={ attributes.eyebrow }
							onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
						/>
						<TextareaControl
							label={ __( 'Title', 'brndle' ) }
							value={ attributes.title }
							onChange={ ( v ) => setAttributes( { title: v } ) }
							help={ __( 'Supports HTML', 'brndle' ) }
						/>
						<TextareaControl
							label={ __( 'Subtitle', 'brndle' ) }
							value={ attributes.subtitle }
							onChange={ ( v ) => setAttributes( { subtitle: v } ) }
						/>
					</PanelBody>
					<PanelBody title={ __( 'Video', 'brndle' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'Video URL', 'brndle' ) }
							value={ attributes.video_url }
							onChange={ ( v ) => setAttributes( { video_url: v } ) }
							help={ __( 'YouTube, Vimeo, or direct video URL', 'brndle' ) }
						/>
						<SelectControl
							label={ __( 'Video Source', 'brndle' ) }
							value={ attributes.video_type }
							options={ [
								{ label: __( 'YouTube', 'brndle' ), value: 'youtube' },
								{ label: __( 'Vimeo', 'brndle' ), value: 'vimeo' },
								{ label: __( 'Self-hosted', 'brndle' ), value: 'self' },
							] }
							onChange={ ( v ) => setAttributes( { video_type: v } ) }
						/>
						{ attributes.video_type === 'self' && (
							<TextControl
								label={ __( 'Poster Image URL', 'brndle' ) }
								value={ attributes.poster }
								onChange={ ( v ) => setAttributes( { poster: v } ) }
								help={ __( 'Thumbnail shown before video plays', 'brndle' ) }
							/>
						) }
						<ToggleControl
							label={ __( 'Autoplay', 'brndle' ) }
							checked={ !! attributes.autoplay }
							onChange={ ( v ) => setAttributes( { autoplay: v } ) }
							help={ __( 'Video will be muted when autoplaying', 'brndle' ) }
						/>
						<ToggleControl
							label={ __( 'Show Controls', 'brndle' ) }
							checked={ !! attributes.show_controls }
							onChange={ ( v ) => setAttributes( { show_controls: v } ) }
						/>
					</PanelBody>
					<PanelBody title={ __( 'Layout', 'brndle' ) } initialOpen={ false }>
						<SelectControl
							label={ __( 'Aspect Ratio', 'brndle' ) }
							value={ attributes.aspect_ratio }
							options={ [
								{ label: __( '16:9 (Widescreen)', 'brndle' ), value: '16/9' },
								{ label: __( '4:3 (Classic)', 'brndle' ), value: '4/3' },
								{ label: __( '1:1 (Square)', 'brndle' ), value: '1/1' },
								{ label: __( '21:9 (Ultrawide)', 'brndle' ), value: '21/9' },
							] }
							onChange={ ( v ) => setAttributes( { aspect_ratio: v } ) }
						/>
						<SelectControl
							label={ __( 'Max Width', 'brndle' ) }
							value={ attributes.max_width }
							options={ [
								{ label: __( 'Full', 'brndle' ), value: 'full' },
								{ label: __( 'Medium', 'brndle' ), value: 'medium' },
								{ label: __( 'Narrow', 'brndle' ), value: 'narrow' },
							] }
							onChange={ ( v ) => setAttributes( { max_width: v } ) }
						/>
						<SelectControl
							label={ __( 'Variant', 'brndle' ) }
							value={ attributes.variant }
							options={ [
								{ label: __( 'Light', 'brndle' ), value: 'light' },
								{ label: __( 'Dark', 'brndle' ), value: 'dark' },
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
