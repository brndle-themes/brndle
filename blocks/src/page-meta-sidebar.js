import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { SelectControl, ToggleControl, TextControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { registerPlugin } from '@wordpress/plugins';

function BrndlePageSettings() {
    const meta = useSelect(
        ( select ) => select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {},
        []
    );
    const { editPost } = useDispatch( 'core/editor' );
    const postType = useSelect( ( select ) => select( 'core/editor' ).getCurrentPostType(), [] );

    if ( postType !== 'page' ) {
        return null;
    }

    const setMeta = ( key, value ) => {
        editPost( { meta: { ...meta, [ key ]: value } } );
    };

    return (
        <PluginDocumentSettingPanel
            name="brndle-page-settings"
            title="Brndle Page Settings"
            className="brndle-page-settings"
        >
            <SelectControl
                label="Header Style"
                value={ meta._brndle_header_style || '' }
                options={ [
                    { label: 'Use Global Setting', value: '' },
                    { label: 'Sticky', value: 'sticky' },
                    { label: 'Solid', value: 'solid' },
                    { label: 'Transparent', value: 'transparent' },
                    { label: 'Centered', value: 'centered' },
                    { label: 'Minimal', value: 'minimal' },
                    { label: 'Split', value: 'split' },
                    { label: 'Banner', value: 'banner' },
                    { label: 'Glass', value: 'glass' },
                ] }
                onChange={ ( v ) => setMeta( '_brndle_header_style', v ) }
                __nextHasNoMarginBottom
            />

            <ToggleControl
                label="Hide Header"
                checked={ !! meta._brndle_hide_header }
                onChange={ ( v ) => setMeta( '_brndle_hide_header', v ) }
                __nextHasNoMarginBottom
            />

            <SelectControl
                label="Footer Style"
                value={ meta._brndle_footer_style || '' }
                options={ [
                    { label: 'Use Global Setting', value: '' },
                    { label: 'Dark', value: 'dark' },
                    { label: 'Light', value: 'light' },
                    { label: 'Columns', value: 'columns' },
                    { label: 'Minimal', value: 'minimal' },
                    { label: 'Big', value: 'big' },
                    { label: 'Stacked', value: 'stacked' },
                ] }
                onChange={ ( v ) => setMeta( '_brndle_footer_style', v ) }
                __nextHasNoMarginBottom
            />

            <ToggleControl
                label="Hide Footer"
                checked={ !! meta._brndle_hide_footer }
                onChange={ ( v ) => setMeta( '_brndle_hide_footer', v ) }
                __nextHasNoMarginBottom
            />

            <SelectControl
                label="Color Scheme Override"
                value={ meta._brndle_color_scheme || '' }
                options={ [
                    { label: 'Use Global Setting', value: '' },
                    { label: 'Sapphire', value: 'sapphire' },
                    { label: 'Indigo', value: 'indigo' },
                    { label: 'Cobalt', value: 'cobalt' },
                    { label: 'Trust', value: 'trust' },
                    { label: 'Commerce', value: 'commerce' },
                    { label: 'Signal', value: 'signal' },
                    { label: 'Coral', value: 'coral' },
                    { label: 'Aubergine', value: 'aubergine' },
                    { label: 'Midnight', value: 'midnight' },
                    { label: 'Stone', value: 'stone' },
                    { label: 'Carbon', value: 'carbon' },
                    { label: 'Neutral', value: 'neutral' },
                ] }
                onChange={ ( v ) => setMeta( '_brndle_color_scheme', v ) }
                __nextHasNoMarginBottom
            />

            <TextControl
                label="Extra Body Class"
                value={ meta._brndle_body_class || '' }
                onChange={ ( v ) => setMeta( '_brndle_body_class', v ) }
                help="Space-separated CSS classes added to the body tag"
                __nextHasNoMarginBottom
            />
        </PluginDocumentSettingPanel>
    );
}

registerPlugin( 'brndle-page-settings', {
    render: BrndlePageSettings,
    icon: null,
} );
