@php
  $a = $attributes;
  $fields = $a['fields'] ?? [];
  $isDark = ($a['variant'] ?? 'light') === 'dark';
  $isAccent = ($a['variant'] ?? 'light') === 'accent';
  $isSplit = ($a['layout'] ?? 'stacked') === 'split';
  $isInline = ($a['layout'] ?? 'stacked') === 'inline';
  $sectionClass = $isDark ? 'brndle-section-dark' : ($isAccent ? 'bg-accent text-white' : 'bg-surface-secondary');
  $textClass = $isDark ? 'text-white/70' : ($isAccent ? 'text-white/80' : 'text-text-secondary');
  $inputClass = $isDark || $isAccent
    ? 'bg-white/10 border-white/20 text-white placeholder-white/40'
    : 'bg-surface-primary border-surface-tertiary text-text-primary placeholder-text-tertiary';
@endphp

<section class="py-24 md:py-32 {{ $sectionClass }}">
  <div class="max-w-7xl mx-auto px-6">
    <div class="{{ $isSplit ? 'grid lg:grid-cols-2 gap-12 lg:gap-20 items-center' : 'max-w-2xl mx-auto text-center' }}">
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

      <div class="reveal">
        <form
          @if(!empty($a['form_action']))
            action="{{ $a['form_action'] }}"
          @endif
          method="post"
          class="{{ $isInline ? 'flex flex-wrap gap-3 items-end' : 'space-y-4' }}"
          data-brndle-lead-form
          data-success="{{ esc_attr($a['success_message'] ?: __('Thank you! Your submission has been received.', 'brndle')) }}"
          @if(empty($a['form_action']))
            data-brndle-endpoint="{{ rest_url('brndle/v1/forms/submit') }}"
            data-rest-nonce="{{ wp_create_nonce('wp_rest') }}"
            data-mailchimp-list="{{ esc_attr($a['mailchimp_list_id'] ?? '') }}"
          @endif
        >
          @if(empty($a['form_action']))
            <input type="hidden" name="_brndle_nonce" value="{{ wp_create_nonce('brndle_form_submit') }}">
            <div style="position:absolute;left:-9999px" aria-hidden="true">
              <input type="text" name="_brndle_hp" tabindex="-1" autocomplete="off">
            </div>
          @endif
          @foreach($fields as $loop_index => $field)
            @php($fieldId = 'brndle-field-' . sanitize_title($field['label'] ?? 'field') . '-' . $loop_index)
            <div class="{{ $isInline ? 'flex-1 min-w-[200px]' : '' }}">
              @if(!$isInline)
                <label for="{{ $fieldId }}" class="block text-sm font-medium mb-1.5 {{ $isDark || $isAccent ? 'text-white/90' : 'text-text-primary' }}">
                  {{ $field['label'] ?? '' }}
                  @if($field['required'] ?? false) <span class="text-red-400">*</span> @endif
                </label>
              @endif

              @if(($field['type'] ?? 'text') === 'textarea')
                <textarea
                  id="{{ $fieldId }}"
                  name="{{ sanitize_title($field['label'] ?? 'field') }}"
                  placeholder="{{ esc_attr($field['placeholder'] ?? '') }}"
                  {{ ($field['required'] ?? false) ? 'required' : '' }}
                  rows="4"
                  class="w-full px-4 py-3 rounded-xl border {{ $inputClass }} focus:outline-2 focus:outline-accent transition-colors"
                  @if($isInline) aria-label="{{ $field['label'] ?? '' }}" @endif
                ></textarea>
              @else
                <input
                  id="{{ $fieldId }}"
                  type="{{ esc_attr($field['type'] ?? 'text') }}"
                  name="{{ sanitize_title($field['label'] ?? 'field') }}"
                  placeholder="{{ esc_attr($field['placeholder'] ?? ($isInline ? ($field['label'] ?? '') : '')) }}"
                  {{ ($field['required'] ?? false) ? 'required' : '' }}
                  class="w-full px-4 py-3 rounded-xl border {{ $inputClass }} focus:outline-2 focus:outline-accent transition-colors"
                  @if($isInline) aria-label="{{ $field['label'] ?? '' }}" @endif
                >
              @endif
            </div>
          @endforeach

          <div class="{{ $isInline ? '' : 'pt-2' }}">
            <button type="submit" class="{{ $isInline ? '' : 'w-full' }} px-8 py-3 text-sm font-semibold rounded-xl {{ $isAccent ? 'bg-white text-accent hover:bg-white/90' : 'bg-accent text-on-accent hover:opacity-90' }} transition-all focus:outline-2 focus:outline-offset-2 focus:outline-accent">
              {{ $a['button_text'] ?? __('Get Started', 'brndle') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @if(empty($a['form_action']))
  <script>
  (function(){
    document.querySelectorAll('[data-brndle-lead-form][data-brndle-endpoint]').forEach(function(form){
      if(form._brndleBound)return;
      form._brndleBound=true;
      form.addEventListener('submit',function(e){
        e.preventDefault();
        var btn=form.querySelector('[type="submit"]');
        var orig=btn.textContent;
        btn.disabled=true;
        btn.textContent='Sending...';
        var data={};
        new FormData(form).forEach(function(v,k){data[k]=v});
        data._source_url=window.location.href;
        if(form.dataset.mailchimpList)data._mailchimp_list=form.dataset.mailchimpList;
        fetch(form.dataset.brndleEndpoint,{
          method:'POST',
          headers:{'Content-Type':'application/json','X-WP-Nonce':form.dataset.restNonce||''},
          body:JSON.stringify(data)
        })
        .then(function(r){return r.json()})
        .then(function(res){
          if(res.success){
            while(form.firstChild)form.removeChild(form.firstChild);
            var p=document.createElement('p');
            p.className='text-center py-6 text-lg font-medium';
            var tmp=document.createElement('span');tmp.innerHTML=form.dataset.success;p.textContent=tmp.textContent;
            form.appendChild(p);
          }else{
            btn.disabled=false;
            btn.textContent=orig;
            var err=form.querySelector('.brndle-form-error');
            if(!err){err=document.createElement('p');err.className='brndle-form-error text-sm text-red-400 mt-2';form.appendChild(err)}
            err.textContent=res.message||'Something went wrong.';
          }
        })
        .catch(function(){
          btn.disabled=false;
          btn.textContent=orig;
        });
      });
    });
  })();
  </script>
  @endif
</section>
