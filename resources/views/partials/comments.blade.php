@if(! post_password_required())
  <section id="comments" class="max-w-3xl mx-auto px-6 py-12">
    @if(have_comments())
      <h2 class="text-2xl font-bold text-text-primary mb-8">
        {{-- translators: %s: number of comments --}}
        {{ sprintf(_n('%s Comment', '%s Comments', get_comments_number(), 'brndle'), number_format_i18n(get_comments_number())) }}
      </h2>

      <ol class="space-y-6 comment-list">
        {!! wp_list_comments(['style' => 'ol', 'short_ping' => true, 'echo' => false]) !!}
      </ol>
    @endif

    @if(! comments_open() && get_comments_number() != '0' && post_type_supports(get_post_type(), 'comments'))
      <p class="text-sm text-text-tertiary">{{ __('Comments are closed.', 'brndle') }}</p>
    @endif

    @php(comment_form())
  </section>
@endif
