/**
 * brndle/code — syntax-highlighted code block.
 *
 * Editor canvas keeps it simple: a monospaced TextareaControl, language
 * picker in BlockControls, and inspector toggles for line numbers,
 * copy button, theme, and caption. Frontend rendering happens via
 * ServerSideRender against the Blade template.
 */

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { code as codeIcon } from '@wordpress/icons';
import {
	BlockControls,
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextareaControl,
	TextControl,
	ToggleControl,
	RadioControl,
	SelectControl,
	ToolbarGroup,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useUniqueId } from './components/use-unique-id';

const LANGUAGE_OPTIONS = [
	{ label: __( 'Plain text', 'brndle' ), value: 'plain' },
	{ label: 'Bash / Shell', value: 'bash' },
	{ label: 'CSS', value: 'css' },
	{ label: 'Diff', value: 'diff' },
	{ label: 'Dockerfile', value: 'dockerfile' },
	{ label: 'HTML', value: 'html' },
	{ label: 'JavaScript', value: 'js' },
	{ label: 'JSON', value: 'json' },
	{ label: 'JSX', value: 'jsx' },
	{ label: 'Markdown', value: 'markdown' },
	{ label: 'Nginx', value: 'nginx' },
	{ label: 'PHP', value: 'php' },
	{ label: 'Python', value: 'python' },
	{ label: 'SCSS', value: 'scss' },
	{ label: 'SQL', value: 'sql' },
	{ label: 'TypeScript', value: 'ts' },
	{ label: 'TSX', value: 'tsx' },
	{ label: 'YAML', value: 'yaml' },
];

registerBlockType( 'brndle/code', {
	icon: codeIcon,

	edit: ( { attributes, setAttributes, clientId } ) => {
		const blockProps = useBlockProps();
		useUniqueId( clientId, attributes, setAttributes );

		return (
			<>
				<BlockControls>
					<ToolbarGroup>
						<SelectControl
							label={ __( 'Language', 'brndle' ) }
							hideLabelFromVision
							value={ attributes.language }
							options={ LANGUAGE_OPTIONS }
							onChange={ ( v ) => setAttributes( { language: v } ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</ToolbarGroup>
				</BlockControls>

				<InspectorControls>
					<PanelBody title={ __( 'Code', 'brndle' ) } initialOpen={ true }>
						<SelectControl
							label={ __( 'Language', 'brndle' ) }
							value={ attributes.language }
							options={ LANGUAGE_OPTIONS }
							onChange={ ( v ) => setAttributes( { language: v } ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						<TextControl
							label={ __( 'Caption', 'brndle' ) }
							help={ __( 'One-line attribution under the code (file path / commit / source URL).', 'brndle' ) }
							value={ attributes.caption || '' }
							onChange={ ( v ) => setAttributes( { caption: v } ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						<ToggleControl
							label={ __( 'Show line numbers', 'brndle' ) }
							checked={ !! attributes.showLineNumbers }
							onChange={ ( v ) => setAttributes( { showLineNumbers: v } ) }
							__nextHasNoMarginBottom
						/>
						<ToggleControl
							label={ __( 'Show copy button', 'brndle' ) }
							checked={ !! attributes.showCopy }
							onChange={ ( v ) => setAttributes( { showCopy: v } ) }
							__nextHasNoMarginBottom
						/>
					</PanelBody>
					<PanelBody title={ __( 'Theme', 'brndle' ) } initialOpen={ false }>
						<RadioControl
							label={ __( 'Color theme', 'brndle' ) }
							help={ __( 'Auto follows the page dark-mode setting. Override only when a specific snippet reads better one way.', 'brndle' ) }
							selected={ attributes.theme || 'auto' }
							options={ [
								{ label: __( 'Auto (follows page)', 'brndle' ), value: 'auto' },
								{ label: __( 'Light', 'brndle' ), value: 'light' },
								{ label: __( 'Dark', 'brndle' ), value: 'dark' },
							] }
							onChange={ ( v ) => setAttributes( { theme: v } ) }
						/>
					</PanelBody>
				</InspectorControls>

				<div { ...blockProps }>
					{ attributes.code ? (
						<ServerSideRender
							block="brndle/code"
							attributes={ attributes }
						/>
					) : (
						<TextareaControl
							label={ __( 'Code', 'brndle' ) }
							help={ __( 'Paste or type the snippet. Switch language in the toolbar above.', 'brndle' ) }
							value={ attributes.code || '' }
							onChange={ ( v ) => setAttributes( { code: v } ) }
							rows={ 8 }
							className="brndle-code-editor-textarea"
							__nextHasNoMarginBottom
						/>
					) }
					{ attributes.code ? (
						<TextareaControl
							label={ __( 'Edit code', 'brndle' ) }
							value={ attributes.code }
							onChange={ ( v ) => setAttributes( { code: v } ) }
							rows={ 6 }
							className="brndle-code-editor-textarea"
							__nextHasNoMarginBottom
						/>
					) : null }
				</div>
			</>
		);
	},

	save: () => null,
} );
