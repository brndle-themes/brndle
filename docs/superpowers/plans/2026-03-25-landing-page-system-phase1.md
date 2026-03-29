# Brndle Landing Page System — Phase 1 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add 3 high-impact blocks (content-image-split, how-it-works, lead-form), per-page meta box, theme.json upgrade, editor CSS fixes, and align-wide support — enabling 6/7 landing page archetypes.

**Architecture:** Server-side rendered blocks via Blade (matching existing 8 blocks). Page meta box via `register_post_meta` + block editor sidebar panel. Theme.json enhanced for full editor experience. All CSS/JS scoped — no inline styles.

**Tech Stack:** PHP 8.2+, Roots Acorn 5, Blade, Tailwind CSS v4, Vite 7, @wordpress/scripts (webpack), React (block editor)

**Theme path:** `/Users/varundubey/Local Sites/roots/app/public/wp-content/themes/brndle`

**Key skills:** @wp-plugin-development, @wordpress-gutenberg-blocks, @wp-block-themes, @wp-security-review

---

## File Map

### New Files

| File | Purpose |
|------|---------|
| `blocks/content-image-split/block.json` | Attributes schema for content+image split block |
| `blocks/src/content-image-split.js` | Editor React component |
| `resources/views/blocks/content-image-split.blade.php` | Blade frontend view |
| `blocks/how-it-works/block.json` | Attributes schema for how-it-works block |
| `blocks/src/how-it-works.js` | Editor React component |
| `resources/views/blocks/how-it-works.blade.php` | Blade frontend view |
| `blocks/lead-form/block.json` | Attributes schema for lead-form block |
| `blocks/src/lead-form.js` | Editor React component |
| `resources/views/blocks/lead-form.blade.php` | Blade frontend view |
| `app/Providers/PageMetaServiceProvider.php` | Register page-level meta box + editor sidebar panel |
| `blocks/src/page-meta-sidebar.js` | Editor sidebar panel for per-page overrides |

### Modified Files

| File | Change |
|------|--------|
| `app/Providers/BlockServiceProvider.php:13-22` | Add 3 new block slugs to `$blocks` array |
| `blocks/src/index.js` | Add 3 new block imports + page-meta-sidebar import |
| `blocks/src/editor.css` | Add CSS for 3 new block wrappers |
| `app/Providers/ThemeServiceProvider.php` | Boot `PageMetaServiceProvider` |
| `app/View/Composers/Theme.php` | Check page meta before global settings |
| `theme.json` | Add `appearanceTools`, spacing presets |
| `app/setup.php` | Add `align-wide` support |
| `resources/css/app.css` | Add `.alignfull`/`.alignwide` CSS rules |
| `resources/css/editor.css` | Import custom classes for editor fidelity |

---

## Task 1: Theme Infrastructure — align-wide + theme.json + editor CSS

**Files:**
- Modify: `app/setup.php:81` (add theme support)
- Modify: `theme.json` (add appearanceTools, spacing)
- Modify: `resources/css/app.css` (add alignment CSS)
- Modify: `resources/css/editor.css` (import custom classes)

- [ ] **Step 1: Add align-wide theme support**

In `app/setup.php`, inside the `after_setup_theme` callback (after line 84), add:

```php
add_theme_support('align-wide');
```

- [ ] **Step 2: Add alignment CSS to app.css**

In `resources/css/app.css`, after the RTL Support section (before the Custom scrollbar section), add:

```css
/* ========================================================================
   Block Alignment Support
   ======================================================================== */

.alignwide {
  max-width: 80rem;
  margin-left: auto;
  margin-right: auto;
}

.alignfull {
  max-width: none;
  width: 100vw;
  position: relative;
  left: 50%;
  right: 50%;
  margin-left: -50vw;
  margin-right: -50vw;
}

/* Constrained layouts already handle alignment — avoid double offset */
.is-layout-constrained > .alignfull {
  left: auto;
  right: auto;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}
```

- [ ] **Step 3: Upgrade theme.json**

Replace the entire `theme.json` content with:

```json
{
  "__preprocessed__": "This file is used to build the theme.json file in the public/build/assets directory. The built artifact includes Tailwind colors, fonts, and font sizes.",
  "$schema": "https://schemas.wp.org/trunk/theme.json",
  "version": 3,
  "settings": {
    "appearanceTools": true,
    "layout": {
      "contentSize": "48rem",
      "wideSize": "80rem"
    },
    "background": {
      "backgroundImage": true
    },
    "color": {
      "custom": false,
      "customDuotone": false,
      "customGradient": false,
      "defaultDuotone": false,
      "defaultGradients": false,
      "defaultPalette": false,
      "duotone": []
    },
    "spacing": {
      "padding": true,
      "margin": true,
      "blockGap": true,
      "units": ["px", "%", "em", "rem", "vw", "vh"],
      "spacingSizes": [
        { "slug": "10", "size": "0.25rem", "name": "3XS" },
        { "slug": "20", "size": "0.5rem", "name": "2XS" },
        { "slug": "30", "size": "1rem", "name": "XS" },
        { "slug": "40", "size": "1.5rem", "name": "S" },
        { "slug": "50", "size": "2rem", "name": "M" },
        { "slug": "60", "size": "3rem", "name": "L" },
        { "slug": "70", "size": "5rem", "name": "XL" },
        { "slug": "80", "size": "8rem", "name": "2XL" }
      ]
    },
    "typography": {
      "defaultFontSizes": false,
      "customFontSize": true,
      "fluid": true,
      "fontSizes": [
        { "slug": "small", "size": "0.875rem", "name": "Small" },
        { "slug": "medium", "size": "1rem", "name": "Medium" },
        { "slug": "large", "size": "1.25rem", "name": "Large" },
        { "slug": "x-large", "size": "1.75rem", "name": "X-Large" },
        { "slug": "xx-large", "size": "2.5rem", "name": "2X-Large" },
        { "slug": "hero", "size": "clamp(2.5rem, 5vw, 4.5rem)", "name": "Hero" }
      ]
    }
  }
}
```

- [ ] **Step 4: Fix editor CSS for custom class fidelity**

Replace `resources/css/editor.css` content with:

```css
@import "tailwindcss";

/* Brndle custom classes for editor fidelity */
.brndle-section-dark {
  background-color: #080B16;
  color: #ffffff;
}

.brndle-section-dark .text-text-secondary,
.brndle-section-dark .text-text-tertiary {
  color: rgba(255, 255, 255, 0.6);
}

.gradient-text {
  background: linear-gradient(135deg, var(--color-accent) 0%, #a855f7 50%, #06b6d4 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.noise::after {
  display: none; /* Disable in editor to avoid overlay issues */
}

.reveal {
  opacity: 1;
  transform: none;
}
```

- [ ] **Step 5: Build frontend + verify**

```bash
cd /Users/varundubey/Local\ Sites/roots/app/public/wp-content/themes/brndle
npm run build
```

Expected: Build succeeds with no errors.

- [ ] **Step 6: Commit**

```bash
git add app/setup.php theme.json resources/css/app.css resources/css/editor.css
git commit -m "feat: add align-wide support, upgrade theme.json, fix editor CSS fidelity"
```

---

## Task 2: Page Meta Box — Per-Page Header/Footer/Color Overrides

**Files:**
- Create: `app/Providers/PageMetaServiceProvider.php`
- Create: `blocks/src/page-meta-sidebar.js`
- Modify: `app/Providers/ThemeServiceProvider.php`
- Modify: `app/View/Composers/Theme.php`
- Modify: `blocks/src/index.js` (add import)

- [ ] **Step 1: Create PageMetaServiceProvider**

Create `app/Providers/PageMetaServiceProvider.php`:

```php
<?php

namespace Brndle\Providers;

class PageMetaServiceProvider
{
    public function boot(): void
    {
        add_action('init', [$this, 'registerMeta']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueueEditorAssets']);
    }

    public function registerMeta(): void
    {
        $meta_fields = [
            '_brndle_header_style' => [
                'type' => 'string',
                'default' => '',
            ],
            '_brndle_footer_style' => [
                'type' => 'string',
                'default' => '',
            ],
            '_brndle_hide_header' => [
                'type' => 'boolean',
                'default' => false,
            ],
            '_brndle_hide_footer' => [
                'type' => 'boolean',
                'default' => false,
            ],
            '_brndle_color_scheme' => [
                'type' => 'string',
                'default' => '',
            ],
            '_brndle_body_class' => [
                'type' => 'string',
                'default' => '',
            ],
        ];

        foreach ($meta_fields as $key => $args) {
            register_post_meta('page', $key, [
                'show_in_rest' => true,
                'single' => true,
                'type' => $args['type'],
                'default' => $args['default'],
                'auth_callback' => fn () => current_user_can('edit_posts'),
            ]);
        }
    }

    public function enqueueEditorAssets(): void
    {
        // The sidebar panel JS is bundled into blocks/build/index.js via index.js import
        // No separate enqueue needed — it piggybacks on the blocks editor script
    }
}
```

- [ ] **Step 2: Create editor sidebar panel**

Create `blocks/src/page-meta-sidebar.js`:

```js
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
```

- [ ] **Step 3: Add import to blocks/src/index.js**

Add at the end of `blocks/src/index.js`:

```js
import './page-meta-sidebar';
```

- [ ] **Step 4: Boot PageMetaServiceProvider from ThemeServiceProvider**

In `app/Providers/ThemeServiceProvider.php`, in the `boot()` method, add after the existing `BlockServiceProvider` boot line:

```php
$this->app->make(\Brndle\Providers\PageMetaServiceProvider::class)->boot();
```

- [ ] **Step 5: Update Theme Composer to check page meta**

In `app/View/Composers/Theme.php`, at the beginning of the `override()` method (after the static cache check), add a helper to read page meta:

```php
$pageMeta = fn (string $key, string $default) => (is_singular('page') && ($v = get_post_meta(get_the_ID(), $key, true)) !== '') ? $v : $default;
$pageMetaBool = fn (string $key) => is_singular('page') && (bool) get_post_meta(get_the_ID(), $key, true);
```

Then update these lines in the return array:

```php
// Header — check page meta first
'headerStyle' => $pageMeta('_brndle_header_style', Settings::get('header_style', 'sticky')),
'hideHeader' => $pageMetaBool('_brndle_hide_header'),

// Footer — check page meta first
'footerStyle' => $pageMeta('_brndle_footer_style', Settings::get('footer_style', 'dark')),
'hideFooter' => $pageMetaBool('_brndle_hide_footer'),
```

Add these new keys to the return array:

```php
'hideHeader' => $pageMetaBool('_brndle_hide_header'),
'hideFooter' => $pageMetaBool('_brndle_hide_footer'),
'pageBodyClass' => is_singular('page') ? get_post_meta(get_the_ID(), '_brndle_body_class', true) : '',
```

- [ ] **Step 6: Update header/footer includes to respect hide flags**

In `resources/views/layouts/app.blade.php` and `resources/views/layouts/landing.blade.php`, wrap the header and footer includes:

```blade
@unless($hideHeader ?? false)
  @include('sections.header')
@endunless
```

```blade
@unless($hideFooter ?? false)
  @include('sections.footer')
@endunless
```

- [ ] **Step 7: PHP syntax check**

```bash
php -l app/Providers/PageMetaServiceProvider.php
php -l app/View/Composers/Theme.php
php -l app/Providers/ThemeServiceProvider.php
```

Expected: No syntax errors in all 3 files.

- [ ] **Step 8: Build blocks**

```bash
npm run blocks:build
```

Expected: Build succeeds with the new `page-meta-sidebar.js` bundled in.

- [ ] **Step 9: Commit**

```bash
git add app/Providers/PageMetaServiceProvider.php blocks/src/page-meta-sidebar.js app/Providers/ThemeServiceProvider.php app/View/Composers/Theme.php blocks/src/index.js resources/views/layouts/app.blade.php resources/views/layouts/landing.blade.php
git commit -m "feat: add per-page meta box for header/footer/color overrides"
```

---

## Task 3: Block — Content Image Split

**Files:**
- Create: `blocks/content-image-split/block.json`
- Create: `blocks/src/content-image-split.js`
- Create: `resources/views/blocks/content-image-split.blade.php`
- Modify: `app/Providers/BlockServiceProvider.php:13-22`
- Modify: `blocks/src/index.js`
- Modify: `blocks/src/editor.css`

- [ ] **Step 1: Create block.json**

Create `blocks/content-image-split/block.json`:

```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "brndle/content-image-split",
  "title": "Content + Image",
  "category": "brndle-sections",
  "description": "Side-by-side content and image section with flexible layout options.",
  "keywords": ["split", "image", "content", "media", "text"],
  "textdomain": "brndle",
  "attributes": {
    "eyebrow": { "type": "string", "default": "" },
    "title": { "type": "string", "default": "" },
    "description": { "type": "string", "default": "" },
    "bullets": { "type": "array", "default": [] },
    "image": { "type": "string", "default": "" },
    "image_alt": { "type": "string", "default": "" },
    "image_position": { "type": "string", "default": "right", "enum": ["left", "right"] },
    "cta_text": { "type": "string", "default": "" },
    "cta_url": { "type": "string", "default": "#" },
    "variant": { "type": "string", "default": "light", "enum": ["light", "dark"] }
  },
  "supports": { "align": ["full", "wide"], "html": false, "anchor": true }
}
```

- [ ] **Step 2: Create editor component**

Create `blocks/src/content-image-split.js`:

```js
import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
    PanelBody,
    TextControl,
    TextareaControl,
    SelectControl,
    Button,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'brndle/content-image-split', {
    icon: (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
            <rect x="1" y="3" width="10" height="18" rx="1" />
            <rect x="13" y="3" width="10" height="18" rx="1" />
        </svg>
    ),

    edit: ( { attributes, setAttributes } ) => {
        const blockProps = useBlockProps();
        const bullets = attributes.bullets || [];

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
                            help="Supports HTML for styling"
                        />
                        <TextareaControl
                            label="Description"
                            value={ attributes.description }
                            onChange={ ( v ) => setAttributes( { description: v } ) }
                        />
                        <TextareaControl
                            label="Bullet Points"
                            value={ bullets.join( '\n' ) }
                            onChange={ ( v ) =>
                                setAttributes( {
                                    bullets: v.split( '\n' ).filter( ( l ) => l.trim() ),
                                } )
                            }
                            help="One bullet per line"
                        />
                    </PanelBody>
                    <PanelBody title="Image" initialOpen={ true }>
                        <TextControl
                            label="Image URL"
                            value={ attributes.image }
                            onChange={ ( v ) => setAttributes( { image: v } ) }
                        />
                        <TextControl
                            label="Image Alt Text"
                            value={ attributes.image_alt }
                            onChange={ ( v ) => setAttributes( { image_alt: v } ) }
                        />
                        <SelectControl
                            label="Image Position"
                            value={ attributes.image_position }
                            options={ [
                                { label: 'Right', value: 'right' },
                                { label: 'Left', value: 'left' },
                            ] }
                            onChange={ ( v ) => setAttributes( { image_position: v } ) }
                        />
                    </PanelBody>
                    <PanelBody title="Call to Action" initialOpen={ false }>
                        <TextControl
                            label="Button Text"
                            value={ attributes.cta_text }
                            onChange={ ( v ) => setAttributes( { cta_text: v } ) }
                        />
                        <TextControl
                            label="Button URL"
                            value={ attributes.cta_url }
                            onChange={ ( v ) => setAttributes( { cta_url: v } ) }
                        />
                    </PanelBody>
                    <PanelBody title="Settings" initialOpen={ false }>
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
                        block="brndle/content-image-split"
                        attributes={ attributes }
                    />
                </div>
            </>
        );
    },

    save: () => null,
} );
```

- [ ] **Step 3: Create Blade view**

Create `resources/views/blocks/content-image-split.blade.php`:

```blade
@php($a = $attributes)

<section class="py-24 md:py-32 {{ ($a['variant'] ?? 'light') === 'dark' ? 'brndle-section-dark' : 'bg-surface-primary' }}">
  <div class="max-w-7xl mx-auto px-6">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center {{ ($a['image_position'] ?? 'right') === 'left' ? 'lg:[direction:rtl] lg:[&>*]:[direction:ltr]' : '' }}">
      {{-- Content --}}
      <div class="reveal">
        @if($a['eyebrow'])
          <p class="text-sm font-semibold text-accent uppercase tracking-[0.15em] mb-3">{{ $a['eyebrow'] }}</p>
        @endif

        @if($a['title'])
          <h2 class="text-3xl sm:text-4xl font-bold tracking-tight">{!! wp_kses_post($a['title']) !!}</h2>
        @endif

        @if($a['description'])
          <p class="mt-4 text-lg {{ ($a['variant'] ?? 'light') === 'dark' ? 'text-white/70' : 'text-text-secondary' }} leading-relaxed">{{ $a['description'] }}</p>
        @endif

        @if(!empty($a['bullets']))
          <ul class="mt-6 space-y-3">
            @foreach($a['bullets'] as $bullet)
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"/></svg>
                <span class="{{ ($a['variant'] ?? 'light') === 'dark' ? 'text-white/70' : 'text-text-secondary' }}">{{ $bullet }}</span>
              </li>
            @endforeach
          </ul>
        @endif

        @if($a['cta_text'])
          <div class="mt-8">
            <a href="{{ esc_url($a['cta_url']) }}" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold rounded-xl bg-accent text-white hover:opacity-90 transition-all focus:outline-2 focus:outline-offset-2 focus:outline-accent">
              {{ $a['cta_text'] }}
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
          </div>
        @endif
      </div>

      {{-- Image --}}
      <div class="reveal">
        @if($a['image'])
          <div class="rounded-2xl {{ ($a['variant'] ?? 'light') === 'dark' ? 'border border-white/10' : 'bg-surface-secondary border border-surface-tertiary shadow-lg' }} overflow-hidden">
            <img src="{{ esc_url($a['image']) }}" alt="{{ esc_attr($a['image_alt'] ?? '') }}" class="w-full" loading="lazy" decoding="async">
          </div>
        @else
          <div class="aspect-[4/3] rounded-2xl {{ ($a['variant'] ?? 'light') === 'dark' ? 'bg-white/5 border border-white/10' : 'bg-surface-secondary border border-surface-tertiary' }} flex items-center justify-center">
            <span class="text-text-tertiary text-sm">{{ __('Add an image URL', 'brndle') }}</span>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>
```

- [ ] **Step 4: Register in BlockServiceProvider**

In `app/Providers/BlockServiceProvider.php`, add `'content-image-split'` to the `$blocks` array.

- [ ] **Step 5: Add import in index.js**

In `blocks/src/index.js`, add:

```js
import './content-image-split';
```

- [ ] **Step 6: Add editor CSS**

In `blocks/src/editor.css`, add:

```css
.wp-block-brndle-content-image-split {
    max-width: 100%;
    margin: 0;
}
```

- [ ] **Step 7: Build and verify**

```bash
npm run blocks:build && npm run build
php -l app/Providers/BlockServiceProvider.php
```

- [ ] **Step 8: Commit**

```bash
git add blocks/content-image-split/ blocks/src/content-image-split.js resources/views/blocks/content-image-split.blade.php app/Providers/BlockServiceProvider.php blocks/src/index.js blocks/src/editor.css
git commit -m "feat: add content-image-split block for side-by-side sections"
```

---

## Task 4: Block — How It Works

**Files:**
- Create: `blocks/how-it-works/block.json`
- Create: `blocks/src/how-it-works.js`
- Create: `resources/views/blocks/how-it-works.blade.php`
- Modify: `app/Providers/BlockServiceProvider.php`
- Modify: `blocks/src/index.js`
- Modify: `blocks/src/editor.css`

- [ ] **Step 1: Create block.json**

Create `blocks/how-it-works/block.json`:

```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "brndle/how-it-works",
  "title": "How It Works",
  "category": "brndle-sections",
  "description": "Numbered process steps with optional icons and descriptions.",
  "keywords": ["steps", "process", "how", "workflow"],
  "textdomain": "brndle",
  "attributes": {
    "eyebrow": { "type": "string", "default": "" },
    "title": { "type": "string", "default": "" },
    "subtitle": { "type": "string", "default": "" },
    "steps": { "type": "array", "default": [] },
    "layout": { "type": "string", "default": "horizontal", "enum": ["horizontal", "vertical"] },
    "variant": { "type": "string", "default": "light", "enum": ["light", "dark"] }
  },
  "supports": { "align": ["full", "wide"], "html": false, "anchor": true }
}
```

- [ ] **Step 2: Create editor component**

Create `blocks/src/how-it-works.js`:

```js
import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
    PanelBody,
    TextControl,
    TextareaControl,
    SelectControl,
    Button,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'brndle/how-it-works', {
    icon: (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
            <circle cx="5" cy="6" r="2" />
            <line x1="7" y1="6" x2="11" y2="6" />
            <circle cx="13" cy="6" r="2" />
            <line x1="15" y1="6" x2="19" y2="6" />
            <circle cx="21" cy="6" r="2" />
            <line x1="5" y1="10" x2="5" y2="20" />
            <line x1="13" y1="10" x2="13" y2="20" />
            <line x1="21" y1="10" x2="21" y2="20" />
        </svg>
    ),

    edit: ( { attributes, setAttributes } ) => {
        const blockProps = useBlockProps();
        const steps = attributes.steps || [];

        const updateStep = ( index, key, value ) => {
            const newSteps = [ ...steps ];
            newSteps[ index ] = { ...newSteps[ index ], [ key ]: value };
            setAttributes( { steps: newSteps } );
        };

        const addStep = () => {
            setAttributes( {
                steps: [ ...steps, { title: '', description: '', icon: '' } ],
            } );
        };

        const removeStep = ( index ) => {
            setAttributes( { steps: steps.filter( ( _, i ) => i !== index ) } );
        };

        return (
            <>
                <InspectorControls>
                    <PanelBody title="Section Header" initialOpen={ true }>
                        <TextControl label="Eyebrow" value={ attributes.eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } />
                        <TextareaControl label="Title" value={ attributes.title } onChange={ ( v ) => setAttributes( { title: v } ) } help="Supports HTML" />
                        <TextareaControl label="Subtitle" value={ attributes.subtitle } onChange={ ( v ) => setAttributes( { subtitle: v } ) } />
                        <SelectControl label="Layout" value={ attributes.layout }
                            options={ [ { label: 'Horizontal (row)', value: 'horizontal' }, { label: 'Vertical (stacked)', value: 'vertical' } ] }
                            onChange={ ( v ) => setAttributes( { layout: v } ) } />
                        <SelectControl label="Variant" value={ attributes.variant }
                            options={ [ { label: 'Light', value: 'light' }, { label: 'Dark', value: 'dark' } ] }
                            onChange={ ( v ) => setAttributes( { variant: v } ) } />
                    </PanelBody>
                    { steps.map( ( step, i ) => (
                        <PanelBody key={ i } title={ `Step ${ i + 1 }${ step.title ? `: ${ step.title }` : '' }` } initialOpen={ false }>
                            <TextControl label="Title" value={ step.title } onChange={ ( v ) => updateStep( i, 'title', v ) } />
                            <TextareaControl label="Description" value={ step.description } onChange={ ( v ) => updateStep( i, 'description', v ) } />
                            <TextControl label="Icon (emoji or text)" value={ step.icon } onChange={ ( v ) => updateStep( i, 'icon', v ) } />
                            <Button isDestructive isSmall onClick={ () => removeStep( i ) }>Remove Step</Button>
                        </PanelBody>
                    ) ) }
                    <PanelBody title="Add Step" initialOpen={ true }>
                        <Button variant="secondary" onClick={ addStep }>Add Step</Button>
                    </PanelBody>
                </InspectorControls>
                <div { ...blockProps }>
                    <ServerSideRender block="brndle/how-it-works" attributes={ attributes } />
                </div>
            </>
        );
    },

    save: () => null,
} );
```

- [ ] **Step 3: Create Blade view**

Create `resources/views/blocks/how-it-works.blade.php`:

```blade
@php
  $a = $attributes;
  $steps = $a['steps'] ?? [];
  $isDark = ($a['variant'] ?? 'light') === 'dark';
  $isVertical = ($a['layout'] ?? 'horizontal') === 'vertical';
  $stepCols = ['md:grid-cols-1', 'md:grid-cols-2', 'md:grid-cols-3', 'md:grid-cols-4'];
@endphp

<section class="py-24 md:py-32 {{ $isDark ? 'brndle-section-dark' : 'bg-surface-secondary' }}">
  <div class="max-w-7xl mx-auto px-6">
    @if($a['title'])
      <div class="max-w-3xl mx-auto text-center mb-16 reveal">
        @if($a['eyebrow'])
          <p class="text-sm font-semibold text-accent uppercase tracking-[0.15em] mb-3">{{ $a['eyebrow'] }}</p>
        @endif
        <h2 class="text-4xl sm:text-5xl font-bold tracking-tight">{!! wp_kses_post($a['title']) !!}</h2>
        @if($a['subtitle'])
          <p class="mt-4 text-lg {{ $isDark ? 'text-white/70' : 'text-text-secondary' }}">{{ $a['subtitle'] }}</p>
        @endif
      </div>
    @endif

    @if($isVertical)
      {{-- Vertical timeline layout --}}
      <div class="max-w-2xl mx-auto space-y-0">
        @foreach($steps as $i => $step)
          <div class="relative flex gap-6 pb-12 {{ $i === count($steps) - 1 ? '' : '' }} reveal">
            {{-- Timeline line --}}
            @if($i < count($steps) - 1)
              <div class="absolute left-5 top-12 w-px h-full {{ $isDark ? 'bg-white/10' : 'bg-surface-tertiary' }}"></div>
            @endif
            {{-- Step number --}}
            <div class="relative z-10 flex-shrink-0 w-10 h-10 rounded-full bg-accent text-white flex items-center justify-center text-sm font-bold">
              @if(!empty($step['icon']))
                {{ $step['icon'] }}
              @else
                {{ $i + 1 }}
              @endif
            </div>
            <div class="pt-1">
              <h3 class="text-lg font-bold">{{ $step['title'] ?? '' }}</h3>
              @if(!empty($step['description']))
                <p class="mt-2 {{ $isDark ? 'text-white/70' : 'text-text-secondary' }} leading-relaxed">{{ $step['description'] }}</p>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @else
      {{-- Horizontal card layout --}}
      <div class="grid {{ $stepCols[min(count($steps), 4) - 1] ?? 'md:grid-cols-3' }} gap-8">
        @foreach($steps as $i => $step)
          <div class="relative text-center reveal">
            {{-- Connector line --}}
            @if($i < count($steps) - 1)
              <div class="hidden md:block absolute top-5 left-[calc(50%+24px)] right-[-calc(50%-24px)] w-[calc(100%-48px)] h-px {{ $isDark ? 'bg-white/10' : 'bg-surface-tertiary' }}" style="left: calc(50% + 24px); width: calc(100% - 48px);"></div>
            @endif
            {{-- Step circle --}}
            <div class="relative z-10 w-12 h-12 rounded-full bg-accent text-white flex items-center justify-center text-lg font-bold mx-auto mb-4">
              @if(!empty($step['icon']))
                {{ $step['icon'] }}
              @else
                {{ $i + 1 }}
              @endif
            </div>
            <h3 class="text-lg font-bold mb-2">{{ $step['title'] ?? '' }}</h3>
            @if(!empty($step['description']))
              <p class="{{ $isDark ? 'text-white/70' : 'text-text-secondary' }} leading-relaxed">{{ $step['description'] }}</p>
            @endif
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>
```

- [ ] **Step 4: Register + import + editor CSS**

Same pattern as Task 3: add `'how-it-works'` to `$blocks`, add `import './how-it-works';` to index.js, add `.wp-block-brndle-how-it-works { max-width: 100%; margin: 0; }` to editor.css.

- [ ] **Step 5: Build and verify**

```bash
npm run blocks:build && npm run build
```

- [ ] **Step 6: Commit**

```bash
git add blocks/how-it-works/ blocks/src/how-it-works.js resources/views/blocks/how-it-works.blade.php app/Providers/BlockServiceProvider.php blocks/src/index.js blocks/src/editor.css
git commit -m "feat: add how-it-works block with horizontal and vertical layouts"
```

---

## Task 5: Block — Lead Form

**Files:**
- Create: `blocks/lead-form/block.json`
- Create: `blocks/src/lead-form.js`
- Create: `resources/views/blocks/lead-form.blade.php`
- Modify: `app/Providers/BlockServiceProvider.php`
- Modify: `blocks/src/index.js`
- Modify: `blocks/src/editor.css`

- [ ] **Step 1: Create block.json**

Create `blocks/lead-form/block.json`:

```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "brndle/lead-form",
  "title": "Lead Form",
  "category": "brndle-sections",
  "description": "Email capture section with headline, description, and customizable form fields.",
  "keywords": ["form", "email", "signup", "lead", "capture", "newsletter"],
  "textdomain": "brndle",
  "attributes": {
    "eyebrow": { "type": "string", "default": "" },
    "title": { "type": "string", "default": "" },
    "subtitle": { "type": "string", "default": "" },
    "fields": { "type": "array", "default": [{"label": "Email", "type": "email", "required": true, "placeholder": "you@company.com"}] },
    "button_text": { "type": "string", "default": "Get Started" },
    "success_message": { "type": "string", "default": "Thanks! We'll be in touch." },
    "form_action": { "type": "string", "default": "" },
    "layout": { "type": "string", "default": "stacked", "enum": ["stacked", "inline", "split"] },
    "variant": { "type": "string", "default": "light", "enum": ["light", "dark", "accent"] }
  },
  "supports": { "align": ["full", "wide"], "html": false, "anchor": true }
}
```

- [ ] **Step 2: Create editor component**

Create `blocks/src/lead-form.js`:

```js
import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
    PanelBody,
    TextControl,
    TextareaControl,
    SelectControl,
    ToggleControl,
    Button,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'brndle/lead-form', {
    icon: (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
            <rect x="3" y="4" width="18" height="16" rx="2" />
            <line x1="7" y1="9" x2="17" y2="9" />
            <line x1="7" y1="13" x2="13" y2="13" />
            <rect x="14" y="15" width="4" height="2" rx="1" fill="currentColor" stroke="none" />
        </svg>
    ),

    edit: ( { attributes, setAttributes } ) => {
        const blockProps = useBlockProps();
        const fields = attributes.fields || [];

        const updateField = ( index, key, value ) => {
            const newFields = [ ...fields ];
            newFields[ index ] = { ...newFields[ index ], [ key ]: value };
            setAttributes( { fields: newFields } );
        };

        const addField = () => {
            setAttributes( {
                fields: [ ...fields, { label: '', type: 'text', required: false, placeholder: '' } ],
            } );
        };

        const removeField = ( index ) => {
            setAttributes( { fields: fields.filter( ( _, i ) => i !== index ) } );
        };

        return (
            <>
                <InspectorControls>
                    <PanelBody title="Content" initialOpen={ true }>
                        <TextControl label="Eyebrow" value={ attributes.eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } />
                        <TextareaControl label="Title" value={ attributes.title } onChange={ ( v ) => setAttributes( { title: v } ) } help="Supports HTML" />
                        <TextareaControl label="Subtitle" value={ attributes.subtitle } onChange={ ( v ) => setAttributes( { subtitle: v } ) } />
                    </PanelBody>
                    <PanelBody title="Form Settings" initialOpen={ true }>
                        <TextControl label="Button Text" value={ attributes.button_text } onChange={ ( v ) => setAttributes( { button_text: v } ) } />
                        <TextControl label="Success Message" value={ attributes.success_message } onChange={ ( v ) => setAttributes( { success_message: v } ) } />
                        <TextControl label="Form Action URL" value={ attributes.form_action } onChange={ ( v ) => setAttributes( { form_action: v } ) } help="Leave empty for default WordPress handling" />
                        <SelectControl label="Layout" value={ attributes.layout }
                            options={ [
                                { label: 'Stacked (full width)', value: 'stacked' },
                                { label: 'Inline (single row)', value: 'inline' },
                                { label: 'Split (text + form)', value: 'split' },
                            ] }
                            onChange={ ( v ) => setAttributes( { layout: v } ) } />
                        <SelectControl label="Variant" value={ attributes.variant }
                            options={ [
                                { label: 'Light', value: 'light' },
                                { label: 'Dark', value: 'dark' },
                                { label: 'Accent', value: 'accent' },
                            ] }
                            onChange={ ( v ) => setAttributes( { variant: v } ) } />
                    </PanelBody>
                    { fields.map( ( field, i ) => (
                        <PanelBody key={ i } title={ `Field ${ i + 1 }${ field.label ? `: ${ field.label }` : '' }` } initialOpen={ false }>
                            <TextControl label="Label" value={ field.label } onChange={ ( v ) => updateField( i, 'label', v ) } />
                            <SelectControl label="Type" value={ field.type }
                                options={ [
                                    { label: 'Text', value: 'text' },
                                    { label: 'Email', value: 'email' },
                                    { label: 'Phone', value: 'tel' },
                                    { label: 'URL', value: 'url' },
                                    { label: 'Textarea', value: 'textarea' },
                                ] }
                                onChange={ ( v ) => updateField( i, 'type', v ) } />
                            <TextControl label="Placeholder" value={ field.placeholder } onChange={ ( v ) => updateField( i, 'placeholder', v ) } />
                            <ToggleControl label="Required" checked={ !! field.required } onChange={ ( v ) => updateField( i, 'required', v ) } />
                            <Button isDestructive isSmall onClick={ () => removeField( i ) }>Remove Field</Button>
                        </PanelBody>
                    ) ) }
                    <PanelBody title="Add Field" initialOpen={ true }>
                        <Button variant="secondary" onClick={ addField }>Add Field</Button>
                    </PanelBody>
                </InspectorControls>
                <div { ...blockProps }>
                    <ServerSideRender block="brndle/lead-form" attributes={ attributes } />
                </div>
            </>
        );
    },

    save: () => null,
} );
```

- [ ] **Step 3: Create Blade view**

Create `resources/views/blocks/lead-form.blade.php`:

```blade
@php
  $a = $attributes;
  $fields = $a['fields'] ?? [];
  $isDark = ($a['variant'] ?? 'light') === 'dark';
  $isAccent = ($a['variant'] ?? 'light') === 'accent';
  $isSplit = ($a['layout'] ?? 'stacked') === 'split';
  $isInline = ($a['layout'] ?? 'stacked') === 'inline';
  $sectionClass = $isDark ? 'brndle-section-dark' : ($isAccent ? 'bg-accent text-white' : 'bg-surface-secondary');
  $textClass = $isDark ? 'text-white/70' : ($isAccent ? 'text-white/80' : 'text-text-secondary');
@endphp

<section class="py-24 md:py-32 {{ $sectionClass }}">
  <div class="max-w-7xl mx-auto px-6">
    <div class="{{ $isSplit ? 'grid lg:grid-cols-2 gap-12 lg:gap-20 items-center' : 'max-w-2xl mx-auto text-center' }}">
      {{-- Content --}}
      <div class="reveal {{ $isSplit ? '' : 'mb-10' }}">
        @if($a['eyebrow'])
          <p class="text-sm font-semibold {{ $isAccent ? 'text-white/70' : 'text-accent' }} uppercase tracking-[0.15em] mb-3">{{ $a['eyebrow'] }}</p>
        @endif
        @if($a['title'])
          <h2 class="text-3xl sm:text-4xl font-bold tracking-tight">{!! wp_kses_post($a['title']) !!}</h2>
        @endif
        @if($a['subtitle'])
          <p class="mt-4 text-lg {{ $textClass }}">{{ $a['subtitle'] }}</p>
        @endif
      </div>

      {{-- Form --}}
      <div class="reveal">
        <form
          action="{{ esc_url($a['form_action'] ?: '#') }}"
          method="post"
          class="{{ $isInline ? 'flex flex-wrap gap-3 items-end' : 'space-y-4' }}"
          data-brndle-lead-form
          data-success="{{ esc_attr($a['success_message'] ?? '') }}"
        >
          @foreach($fields as $field)
            <div class="{{ $isInline ? 'flex-1 min-w-[200px]' : '' }}">
              @if(!$isInline)
                <label class="block text-sm font-medium mb-1.5 {{ $isDark || $isAccent ? 'text-white/90' : 'text-text-primary' }}">
                  {{ $field['label'] ?? '' }}
                  @if($field['required'] ?? false) <span class="text-red-400">*</span> @endif
                </label>
              @endif

              @if(($field['type'] ?? 'text') === 'textarea')
                <textarea
                  name="{{ sanitize_title($field['label'] ?? 'field') }}"
                  placeholder="{{ esc_attr($field['placeholder'] ?? '') }}"
                  {{ ($field['required'] ?? false) ? 'required' : '' }}
                  rows="4"
                  class="w-full px-4 py-3 rounded-xl border {{ $isDark || $isAccent ? 'bg-white/10 border-white/20 text-white placeholder-white/40' : 'bg-surface-primary border-surface-tertiary text-text-primary placeholder-text-tertiary' }} focus:outline-2 focus:outline-accent transition-colors"
                ></textarea>
              @else
                <input
                  type="{{ esc_attr($field['type'] ?? 'text') }}"
                  name="{{ sanitize_title($field['label'] ?? 'field') }}"
                  placeholder="{{ esc_attr($field['placeholder'] ?? ($isInline ? ($field['label'] ?? '') : '')) }}"
                  {{ ($field['required'] ?? false) ? 'required' : '' }}
                  class="w-full px-4 py-3 rounded-xl border {{ $isDark || $isAccent ? 'bg-white/10 border-white/20 text-white placeholder-white/40' : 'bg-surface-primary border-surface-tertiary text-text-primary placeholder-text-tertiary' }} focus:outline-2 focus:outline-accent transition-colors"
                >
              @endif
            </div>
          @endforeach

          <div class="{{ $isInline ? '' : 'pt-2' }}">
            <button type="submit" class="w-full {{ $isInline ? 'w-auto' : '' }} px-8 py-3 text-sm font-semibold rounded-xl {{ $isAccent ? 'bg-white text-accent hover:bg-white/90' : 'bg-accent text-white hover:opacity-90' }} transition-all focus:outline-2 focus:outline-offset-2 focus:outline-accent">
              {{ $a['button_text'] ?? __('Get Started', 'brndle') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
```

- [ ] **Step 4: Register + import + editor CSS**

Add `'lead-form'` to `$blocks`, `import './lead-form';` to index.js, `.wp-block-brndle-lead-form { max-width: 100%; margin: 0; }` to editor.css.

- [ ] **Step 5: Build and verify**

```bash
npm run blocks:build && npm run build
```

- [ ] **Step 6: Commit**

```bash
git add blocks/lead-form/ blocks/src/lead-form.js resources/views/blocks/lead-form.blade.php app/Providers/BlockServiceProvider.php blocks/src/index.js blocks/src/editor.css
git commit -m "feat: add lead-form block with stacked, inline, and split layouts"
```

---

## Task 6: Final Build + Smoke Test

- [ ] **Step 1: Full rebuild all assets**

```bash
cd /Users/varundubey/Local\ Sites/roots/app/public/wp-content/themes/brndle
npm run build && npm run admin:build && npm run blocks:build
```

- [ ] **Step 2: Clear Blade cache**

```bash
rm -rf wp-content/cache/acorn/framework/views/*
```

(Run from the `public/` directory)

- [ ] **Step 3: PHP syntax check on all modified files**

```bash
php -l app/Providers/BlockServiceProvider.php
php -l app/Providers/PageMetaServiceProvider.php
php -l app/Providers/ThemeServiceProvider.php
php -l app/View/Composers/Theme.php
php -l app/setup.php
```

All must report "No syntax errors detected."

- [ ] **Step 4: Smoke test — visit homepage**

Navigate to `https://roots.local/` — verify no PHP errors, header/footer render, CSS loads.

- [ ] **Step 5: Smoke test — block editor**

Navigate to `https://roots.local/wp-admin/post-new.php?post_type=page` — verify:
- Block inserter shows "Brndle Sections" category with 11 blocks (8 existing + 3 new)
- Content Image Split block can be inserted and configured
- How It Works block can be inserted and configured
- Lead Form block can be inserted and configured
- "Brndle Page Settings" sidebar panel appears with header/footer/color controls

- [ ] **Step 6: Smoke test — create test landing page**

Create a page using "Landing Page" template with:
1. Hero block (dark variant)
2. Content Image Split block
3. How It Works block (horizontal layout)
4. Lead Form block (split layout, accent variant)
5. CTA block

Verify all render correctly on the frontend.

- [ ] **Step 7: Commit final state**

```bash
git add -A
git commit -m "chore: Phase 1 complete — 3 new blocks, page meta box, theme infrastructure"
```

---

## Summary

| Task | What | Files Changed |
|------|------|---------------|
| 1 | Theme infrastructure (align-wide, theme.json, editor CSS) | 4 files |
| 2 | Page meta box (per-page header/footer/color) | 7 files |
| 3 | Content Image Split block | 6 files |
| 4 | How It Works block | 6 files |
| 5 | Lead Form block | 6 files |
| 6 | Final build + smoke test | 0 files (verification only) |

**Total new files:** 11
**Total modified files:** 9
**New blocks:** content-image-split, how-it-works, lead-form
**Next:** Phase 2 plan (comparison-table, team, video-embed + 7 archetype patterns)
