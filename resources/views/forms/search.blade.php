<form role="search" method="get" class="search-form max-w-md" action="{{ home_url('/') }}">
  <label class="sr-only" for="s">{{ __('Search', 'brndle') }}</label>
  <div class="flex gap-2">
    <input
      type="search"
      id="s"
      name="s"
      value="{{ get_search_query() }}"
      placeholder="{{ __('Search...', 'brndle') }}"
      class="flex-1 px-4 py-3 text-sm rounded-xl border border-surface-tertiary bg-surface-primary text-text-primary placeholder-text-tertiary focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-colors"
      required
    >
    <button type="submit" class="px-5 py-3 text-sm font-semibold rounded-xl bg-text-primary text-white hover:opacity-90 transition-colors">
      {{ __('Search', 'brndle') }}
    </button>
  </div>
</form>
