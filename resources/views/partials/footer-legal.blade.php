{{--
  Legal row for the footer bottom bar.

  The `footer_navigation` location used to render in the `stacked` style only,
  so every other footer style silently dropped privacy / terms / refund - links
  a site is generally obliged to surface. Included from each bottom bar.
--}}
@if(has_nav_menu('footer_navigation'))
  <nav aria-label="{{ esc_attr__('Legal', 'brndle') }}">
    {!! wp_nav_menu([
      'theme_location' => 'footer_navigation',
      'menu_class' => 'flex flex-wrap items-center gap-x-6 gap-y-1',
      'container' => false,
      'echo' => false,
      'depth' => 1,
      'link_before' => '<span class="text-sm text-text-tertiary hover:text-text-primary transition-colors">',
      'link_after' => '</span>',
    ]) !!}
  </nav>
@endif
