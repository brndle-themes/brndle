<form role="search" method="get" class="search-form max-w-md" action="{{ home_url('/') }}">
  <label class="sr-only" for="s">{{ __('Search', 'brndle') }}</label>
  <div class="flex gap-2">
    <input
      type="search"
      id="s"
      name="s"
      value="{{ get_search_query() }}"
      placeholder="{{ __('Search...', 'brndle') }}"
      class="flex-1 px-4 py-3 text-sm rounded-xl border border-slate-200 bg-white text-text-primary placeholder-text-tertiary focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-colors"
    >
    <button type="submit" class="px-5 py-3 text-sm font-semibold rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition-colors">
      {{ __('Search', 'brndle') }}
    </button>
  </div>
</form>
