@php
  $content = get_post()->post_content ?? '';
  preg_match_all('/<h([23])[^>]*id=["\']([^"\']+)["\'][^>]*>(.*?)<\/h[23]>/i', $content, $matches, PREG_SET_ORDER);
  // If no IDs, generate from text
  if (empty($matches)) {
    preg_match_all('/<h([23])[^>]*>(.*?)<\/h[23]>/i', $content, $rawMatches, PREG_SET_ORDER);
    $matches = [];
    foreach ($rawMatches as $m) {
      $text = wp_strip_all_tags($m[2]);
      $slug = sanitize_title($text);
      $matches[] = [0 => $m[0], 1 => $m[1], 2 => $slug, 3 => $text];
    }
  }
@endphp

@if(!empty($matches))
  <nav class="brndle-toc" aria-label="{{ __('Table of Contents', 'brndle') }}">
    <h2 class="text-sm font-bold uppercase tracking-widest text-text-tertiary mb-4">{{ __('On this page', 'brndle') }}</h2>
    <ol class="space-y-2 text-sm text-text-secondary" id="brndle-toc-list">
      @php($inH3 = false)
      @foreach($matches as $heading)
        @if($heading[1] == '2')
          @if($inH3)
            </ol></li>
            @php($inH3 = false)
          @endif
          <li>
            <a href="#{{ esc_attr($heading[2]) }}" class="brndle-toc-link block py-1 hover:text-accent transition-colors" data-target="{{ esc_attr($heading[2]) }}">
              {{ wp_strip_all_tags($heading[3]) }}
            </a>
        @elseif($heading[1] == '3')
          @if(!$inH3)
            <ol class="ml-4 mt-1 space-y-1">
            @php($inH3 = true)
          @endif
            <li>
              <a href="#{{ esc_attr($heading[2]) }}" class="brndle-toc-link block py-0.5 hover:text-accent transition-colors" data-target="{{ esc_attr($heading[2]) }}">
                {{ wp_strip_all_tags($heading[3]) }}
              </a>
            </li>
        @endif
        @if($heading[1] == '2' && !($loop->last))
          {{-- leave li open for potential nested h3s --}}
        @elseif($heading[1] == '2' && $loop->last)
          </li>
        @endif
      @endforeach
      @if($inH3)
        </ol></li>
      @endif
    </ol>
  </nav>

  <script>
  (function(){
    var links=document.querySelectorAll('.brndle-toc-link');
    if(!links.length)return;
    var ids=[];links.forEach(function(l){ids.push(document.getElementById(l.dataset.target));});
    ids=ids.filter(Boolean);
    var obs=new IntersectionObserver(function(entries){
      entries.forEach(function(e){
        if(e.isIntersecting){
          links.forEach(function(l){l.classList.remove('text-accent','font-medium');});
          var active=document.querySelector('.brndle-toc-link[data-target="'+e.target.id+'"]');
          if(active){active.classList.add('text-accent','font-medium');}
        }
      });
    },{rootMargin:'-80px 0px -60% 0px',threshold:0});
    ids.forEach(function(el){obs.observe(el);});
  })();
  </script>
@endif
