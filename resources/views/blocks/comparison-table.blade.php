@php
  $a = $attributes;
  $columns = $a['columns'] ?? [];
  $rows = $a['rows'] ?? [];
  $isDark = ($a['variant'] ?? 'light') === 'dark';
  $highlight = (int) ($a['highlight_column'] ?? -1);
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

    @if(!empty($columns) && !empty($rows))
      <div class="overflow-x-auto reveal">
        <table class="w-full border-collapse text-sm">
          <thead>
            <tr>
              <th class="text-left p-4 font-semibold {{ $isDark ? 'text-white/50' : 'text-text-tertiary' }} w-56 min-w-[14rem]"></th>
              @foreach($columns as $ci => $col)
                @php($isHL = $ci === $highlight)
                <th class="p-4 text-center font-bold {{ $isHL ? 'bg-accent text-white rounded-t-2xl' : ($isDark ? 'text-white' : 'text-text-primary') }}">
                  <div class="text-base font-bold">{{ $col['label'] ?? '' }}</div>
                  @if(!empty($col['sublabel']))
                    <div class="text-xs {{ $isHL ? 'text-white/70' : ($isDark ? 'text-white/50' : 'text-text-tertiary') }} font-normal mt-0.5">{{ $col['sublabel'] }}</div>
                  @endif
                </th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $ri => $row)
              @php($isLast = $ri === count($rows) - 1)
              <tr class="{{ $isDark ? 'border-b border-white/[0.06]' : 'border-b border-surface-tertiary' }} {{ $isLast ? 'border-b-0' : '' }}">
                <td class="p-4 {{ $isDark ? 'text-white/80' : 'text-text-primary' }} font-medium">{{ $row['feature'] ?? '' }}</td>
                @foreach($columns as $ci => $col)
                  @php
                    $isHL = $ci === $highlight;
                    $val = $row['values'][$ci] ?? false;
                  @endphp
                  <td class="p-4 text-center {{ $isHL ? ($isDark ? 'bg-white/[0.08]' : 'bg-accent/[0.04]') : '' }} {{ ($isLast && $isHL) ? 'rounded-b-2xl' : '' }}">
                    @if($val === true)
                      <svg class="w-5 h-5 mx-auto text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"/></svg>
                    @elseif($val === false)
                      <svg class="w-4 h-4 mx-auto {{ $isDark ? 'text-white/20' : 'text-text-tertiary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    @else
                      <span class="{{ $isHL ? ($isDark ? 'text-white/80' : 'text-accent font-medium') : ($isDark ? 'text-white/70' : 'text-text-secondary') }}">{{ $val }}</span>
                    @endif
                  </td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</section>
