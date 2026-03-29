@php
  $a = $attributes;
  $plans = $a['plans'] ?? [];
  $isDark = ($a['variant'] ?? 'light') === 'dark';
  $hasGroups = false;
  $groups = [];
  foreach ($plans as $plan) {
      if (!empty($plan['billing_group'])) {
          $hasGroups = true;
          $groups[$plan['billing_group']] = true;
      }
  }
  $groupKeys = array_keys($groups);
  $toggleId = 'pricing-toggle-' . wp_unique_id();
@endphp

<section class="py-24 md:py-32 {{ $isDark ? 'brndle-section-dark' : 'bg-surface-secondary' }}">
  <div class="max-w-7xl mx-auto px-6">
    <div class="max-w-3xl mx-auto text-center mb-16 reveal">
      @if($a['eyebrow'])
        <p class="text-sm font-semibold text-accent uppercase tracking-[0.15em] mb-3">{{ $a['eyebrow'] }}</p>
      @endif
      @if($a['title'])
        <h2 class="text-4xl sm:text-5xl font-bold tracking-tight">{!! wp_kses_post($a['title']) !!}</h2>
      @endif
      @if($a['subtitle'])
        <p class="mt-5 text-lg {{ $isDark ? 'text-white/70' : 'text-text-secondary' }}">{{ $a['subtitle'] }}</p>
      @endif

      @if($hasGroups && count($groupKeys) === 2)
        <div class="mt-8 inline-flex items-center gap-3 p-1 rounded-full {{ $isDark ? 'bg-white/[0.06] border border-white/[0.08]' : 'bg-surface-primary border border-surface-tertiary' }}" role="radiogroup" aria-label="{{ __('Billing period', 'brndle') }}">
          @foreach($groupKeys as $i => $group)
            <button
              type="button"
              role="radio"
              aria-checked="{{ $i === 0 ? 'true' : 'false' }}"
              data-toggle="{{ $toggleId }}"
              data-group="{{ esc_attr($group) }}"
              class="brndle-billing-btn px-6 py-2 text-sm font-semibold rounded-full transition-all duration-200 {{ $i === 0 ? 'bg-accent text-on-accent shadow-sm' : ($isDark ? 'text-white/60 hover:text-white/80' : 'text-text-secondary hover:text-text-primary') }}"
            >
              {{ ucfirst($group) }}
              @if($group === 'lifetime')
                <span class="ml-1 text-xs {{ $i === 0 ? 'text-on-accent/70' : 'text-accent' }}">{{ __('Save more', 'brndle') }}</span>
              @endif
            </button>
          @endforeach
        </div>
        <div class="sr-only" aria-live="polite" data-billing-announce="{{ $toggleId }}"></div>
      @endif
    </div>

    @php
      if ($hasGroups) {
          $defaultGroup = $groupKeys[0] ?? '';
          $visibleCount = 0;
          foreach ($plans as $plan) {
              $pg = $plan['billing_group'] ?? '';
              if ($pg === '' || $pg === $defaultGroup) {
                  $visibleCount++;
              }
          }
      } else {
          $visibleCount = count($plans);
      }
      $gridCols = ['md:grid-cols-1', 'md:grid-cols-2', 'md:grid-cols-3'];
    @endphp

    <div class="grid {{ $gridCols[min(max($visibleCount, 1), 3) - 1] ?? 'md:grid-cols-3' }} gap-6 max-w-5xl mx-auto" @if($hasGroups) data-pricing-grid="{{ $toggleId }}" @endif>
      @foreach($plans as $plan)
        @php
          $featured = $plan['featured'] ?? false;
          $billingGroup = $plan['billing_group'] ?? '';
          $isHidden = $hasGroups && $billingGroup && $billingGroup !== ($groupKeys[0] ?? '');
        @endphp
        <div
          class="p-8 rounded-2xl reveal transition-all duration-300 hover:shadow-lg {{ $featured ? 'brndle-section-dark border-2 border-accent relative' : 'bg-surface-primary border border-surface-tertiary hover:border-text-tertiary' }} {{ $isHidden ? 'hidden' : '' }}"
          {!! $featured ? 'aria-label="' . esc_attr($plan['badge'] ?? __('Most Popular', 'brndle')) . ' plan"' : '' !!}
          @if($billingGroup) data-billing-group="{{ esc_attr($billingGroup) }}" @endif
        >
          @if($featured)
            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-accent text-on-accent text-xs font-bold" aria-hidden="true">{{ $plan['badge'] ?? __('Most Popular', 'brndle') }}</div>
          @endif

          <h3 class="text-lg font-bold">{{ $plan['name'] ?? '' }}</h3>
          @if(isset($plan['description']))
            <p class="text-sm {{ $featured ? 'text-white/70' : 'text-text-secondary' }} mt-1">{{ $plan['description'] }}</p>
          @endif

          <div class="mt-6 mb-8">
            @if(!empty($plan['original_price']))
              <span class="text-xl {{ $featured ? 'text-white/40' : 'text-text-tertiary' }} line-through mr-1">{{ $plan['original_price'] }}</span>
            @endif
            <span class="text-5xl font-bold">{{ $plan['price'] ?? '' }}</span>
            @if(isset($plan['period']))
              <span class="{{ $featured ? 'text-white/50' : 'text-text-tertiary' }} ml-1">{{ $plan['period'] }}</span>
            @endif
          </div>

          @if(isset($plan['features']) && is_array($plan['features']))
            <ul class="space-y-3 mb-8">
              @foreach($plan['features'] as $feature)
                <li class="flex items-center gap-3 text-sm {{ $featured ? 'text-white/70' : 'text-text-secondary' }}">
                  <svg class="w-5 h-5 text-accent shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"/></svg>
                  {{ $feature }}
                </li>
              @endforeach
            </ul>
          @endif

          <a href="{{ $plan['cta_url'] ?? '#' }}" {!! $featured ? 'style="color:var(--color-on-accent)"' : '' !!} class="block w-full text-center px-6 py-3 text-sm font-semibold rounded-xl transition-all focus:outline-2 focus:outline-offset-2 focus:outline-accent {{ $featured ? 'bg-accent hover:opacity-90 font-bold hover:-translate-y-px hover:shadow-lg hover:shadow-glow' : 'border border-surface-tertiary text-text-secondary hover:bg-surface-secondary hover:border-text-tertiary' }}">
            {{ $plan['cta_text'] ?? __('Get Started', 'brndle') }}
          </a>
        </div>
      @endforeach
    </div>

    @if($hasGroups)
    <script>
    (function(){
      var tid={!! json_encode($toggleId) !!};
      var btns=document.querySelectorAll('[data-toggle="'+tid+'"]');
      var grid=document.querySelector('[data-pricing-grid="'+tid+'"]');
      if(!btns.length||!grid) return;
      var isDark={{ $isDark ? 'true' : 'false' }};
      btns.forEach(function(btn){
        btn.addEventListener('click',function(){
          var group=btn.getAttribute('data-group');
          btns.forEach(function(b){
            var isActive=b===btn;
            b.setAttribute('aria-checked',isActive?'true':'false');
            if(isActive){
              b.className=b.className.replace(/text-white\/60|text-white\/80|text-text-secondary|text-text-primary|hover:text-white\/80|hover:text-text-primary/g,'');
              b.classList.add('bg-accent','text-on-accent','shadow-sm');
            } else {
              b.classList.remove('bg-accent','text-on-accent','shadow-sm');
              if(isDark){b.classList.add('text-white/60')}else{b.classList.add('text-text-secondary')}
            }
          });
          var cards=grid.querySelectorAll('[data-billing-group]');
          var visible=0;
          cards.forEach(function(c){
            var cg=c.getAttribute('data-billing-group');
            if(cg===group){c.classList.remove('hidden');visible++}else{c.classList.add('hidden')}
          });
          // Also count cards without billing group (always visible)
          grid.querySelectorAll(':scope > div:not([data-billing-group])').forEach(function(){visible++});
          // Update grid columns
          var cols=Math.min(Math.max(visible,1),3);
          grid.classList.remove('md:grid-cols-1','md:grid-cols-2','md:grid-cols-3');
          grid.classList.add('md:grid-cols-'+cols);
          var announce=document.querySelector('[data-billing-announce="'+tid+'"]');
          if(announce){announce.textContent='Showing '+group+' plans';}
        });
      });
    })();
    </script>
    @endif
  </div>
</section>
