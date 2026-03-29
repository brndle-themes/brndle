{{--
  Template Name: Homepage Canvas
  Description: Full canvas personal brand homepage with custom design. Uses wp_head/wp_footer for plugin compatibility.
--}}

<!doctype html>
<html @php(language_attributes()) class="scroll-smooth" data-theme="light">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php(do_action('get_header'))
    @php(wp_head())
    @vite(['resources/css/app.css'])
    <style>
      body { font-family: var(--font-family-body); background: var(--color-surface-inverse, #0a0a0a); color: #f5f5f5; }
      h1, h2, h3, h4, nav { font-family: var(--font-family-heading); }

      /* Gradient text */
      .vp-gradient { background: linear-gradient(135deg, var(--color-accent), color-mix(in srgb, var(--color-accent) 70%, #60a5fa)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

      /* Cursor blink */
      @keyframes blink { 0%,50%{opacity:1} 51%,100%{opacity:0} }
      .vp-cursor { animation: blink 1s infinite; color: var(--color-accent); }

      /* Mesh bg */
      .vp-mesh { position:absolute;inset:0;overflow:hidden;pointer-events:none }
      .vp-mesh::before { content:'';position:absolute;width:700px;height:700px;border-radius:50%;background:radial-gradient(circle,color-mix(in srgb, var(--color-accent) 7%, transparent) 0%,transparent 70%);top:-300px;right:-200px }
      .vp-mesh::after { content:'';position:absolute;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,color-mix(in srgb, var(--color-accent) 5%, transparent) 0%,transparent 70%);bottom:-200px;left:-150px }

      /* Section divider */
      .vp-divider { height:1px;background:linear-gradient(90deg,transparent,color-mix(in srgb, var(--color-accent) 25%, transparent),transparent) }

      /* Scroll reveal */
      .reveal { opacity:0;transform:translateY(40px);transition:opacity 0.8s cubic-bezier(0.16,1,0.3,1),transform 0.8s cubic-bezier(0.16,1,0.3,1) }
      .reveal.visible { opacity:1;transform:translateY(0) }

      /* Buttons */
      .btn-green { display:inline-flex;align-items:center;gap:0.5rem;padding:0.85rem 1.75rem;border-radius:9999px;font-size:0.925rem;font-weight:600;background:var(--color-accent);color:var(--color-on-accent);transition:all 0.3s;text-decoration:none }
      .btn-green:hover { filter:brightness(0.9);box-shadow:0 0 30px color-mix(in srgb, var(--color-accent) 20%, transparent) }
      .btn-green svg { width:1rem;height:1rem;transition:transform 0.3s }
      .btn-green:hover svg { transform:translateX(4px) }
      .btn-ghost { display:inline-flex;align-items:center;padding:0.85rem 1.75rem;border-radius:9999px;font-size:0.925rem;font-weight:500;border:1px solid rgba(255,255,255,0.12);color:rgba(255,255,255,0.8);transition:all 0.3s;text-decoration:none }
      .btn-ghost:hover { background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.2) }

      /* Cards */
      .vp-card { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:1rem;padding:2rem;transition:all 0.4s cubic-bezier(0.16,1,0.3,1) }
      .vp-card:hover { border-color:color-mix(in srgb, var(--color-accent) 20%, transparent);box-shadow:0 0 40px color-mix(in srgb, var(--color-accent) 6%, transparent);transform:translateY(-4px) }

      /* Nav */
      .vp-nav { position:sticky;top:0;z-index:50;padding:1rem 0;background:color-mix(in srgb, var(--color-surface-inverse, #0a0a0a) 80%, transparent);backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,0.06) }
      .vp-nav a { color:rgba(255,255,255,0.6);font-size:0.875rem;text-decoration:none;transition:color 0.3s }
      .vp-nav a:hover { color:#fff }
      .vp-nav-cta { padding:0.5rem 1.25rem;border-radius:9999px;border:1px solid rgba(255,255,255,0.12);font-weight:600 }
      .vp-nav-cta:hover { border-color:var(--color-accent);color:var(--color-accent) }

      /* Footer link row */
      .vp-link-row a { color:rgba(255,255,255,0.3);transition:color 0.3s;text-decoration:none }
      .vp-link-row a:hover { color:rgba(255,255,255,0.7) }

      /* Featured posts */
      .vp-post-link { display:flex;justify-content:space-between;align-items:center;padding:1.25rem 0;border-bottom:1px solid rgba(255,255,255,0.06);text-decoration:none;transition:all 0.2s }
      .vp-post-link:hover { padding-left:0.5rem }
      .vp-post-link h3 { color:#f5f5f5;font-size:1.05rem;font-weight:500 }
      .vp-post-link:hover h3 { color:var(--color-accent) }
      .vp-post-link time { color:rgba(255,255,255,0.3);font-size:0.8rem;white-space:nowrap }

      @media (prefers-reduced-motion:reduce) { .reveal{opacity:1;transform:none;transition:none} .vp-card:hover{transform:none} .vp-cursor{animation:none} }
      @media (max-width:768px) { .vp-nav-links{display:none} .hero-grid{grid-template-columns:1fr} }
    </style>
  </head>

  <body class="antialiased">
    @php(wp_body_open())

    {{-- Nav --}}
    <nav class="vp-nav">
      <div style="max-width:min(90vw,1200px);margin:0 auto;padding:0 1.5rem;display:flex;align-items:center;justify-content:space-between">
        <a href="/" style="font-weight:700;font-size:1.25rem;color:#fff"><span style="color:var(--color-accent)">v</span>apvarun</a>
        <div class="vp-nav-links" style="display:flex;gap:2rem;align-items:center">
          <a href="/open-source/">Open Source</a>
          <a href="/collaborate/">Collaborate</a>
          <a href="/blog/">Blog</a>
          <a href="/about/">About</a>
          <a href="/contact/" class="vp-nav-cta">Let's Talk</a>
        </div>
      </div>
    </nav>

    <main>
      {{-- Hero --}}
      <section style="padding:clamp(5rem,12vw,10rem) 0;position:relative;overflow:hidden">
        <div class="vp-mesh"></div>
        <div style="max-width:min(90vw,1200px);margin:0 auto;padding:0 1.5rem;position:relative;z-index:1">
          <div class="hero-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center">
            {{-- Photo --}}
            <div style="position:relative" class="reveal">
              <div style="position:absolute;inset:-1rem;background:linear-gradient(135deg,rgba(0,255,136,0.15),transparent,rgba(96,165,250,0.08));border-radius:1.5rem;filter:blur(30px)"></div>
              <div style="position:relative;border-radius:1rem;overflow:hidden;border:2px solid rgba(255,255,255,0.08);box-shadow:0 25px 50px rgba(0,0,0,0.5)">
                @php($photo = \Brndle\Settings\Settings::get('site_logo_light', ''))
                @php($photoUrl = 'https://wordpress-1412975-6298996.cloudwaysapps.com/wp-content/uploads/2026/03/varun-hero.jpg')
                <img src="{{ $photoUrl }}" alt="Varun Dubey" style="width:100%;aspect-ratio:4/3;object-fit:cover;object-position:top;display:block" loading="eager" fetchpriority="high">
                <div style="position:absolute;inset:0;background:linear-gradient(to top,#0a0a0a,transparent 50%);opacity:0.5"></div>
                <div style="position:absolute;bottom:1rem;left:1rem;display:inline-flex;align-items:center;gap:0.5rem;padding:0.4rem 0.75rem;border-radius:9999px;background:rgba(0,0,0,0.6);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.08);font-size:0.75rem;color:rgba(255,255,255,0.8)">
                  <span style="width:0.5rem;height:0.5rem;border-radius:50%;background:var(--color-accent);display:inline-block" class="animate-pulse"></span>
                  Wbcom Designs Founder
                </div>
              </div>
            </div>

            {{-- Content --}}
            <div class="reveal">
              <p style="font-size:0.85rem;color:rgba(255,255,255,0.3);font-family:'Space Grotesk',monospace;margin-bottom:1.5rem;letter-spacing:0.15em"><span style="color:var(--color-accent)">$</span> whoami</p>
              <h1 style="font-size:clamp(2.2rem,5vw,3.5rem);font-weight:700;letter-spacing:-0.02em;line-height:1.1">
                Building the bridge between <span class="vp-gradient">AI and WordPress.</span>
              </h1>
              <p style="margin-top:1.5rem;font-size:clamp(1rem,2vw,1.15rem);color:rgba(255,255,255,0.5);line-height:1.7;max-width:32rem">
                I make MCP servers, open source tools, and community platforms. Founder of Wbcom Designs. BuddyPress core contributor. 125+ repos on GitHub.
              </p>
              <div style="margin-top:2.5rem;display:flex;flex-wrap:wrap;gap:1rem">
                <a href="/open-source/" class="btn-green">
                  View My Work
                  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="/contact/" class="btn-ghost">Let's Talk</a>
              </div>
              <div style="margin-top:2.5rem;font-family:'Space Grotesk',monospace;font-size:0.85rem;color:rgba(255,255,255,0.3)">
                <p><span style="color:var(--color-accent)">repos:</span> <span style="color:#f5f5f5">125+</span></p>
                <p><span style="color:var(--color-accent)">since:</span> <span style="color:#f5f5f5">2010</span></p>
                <p><span style="color:var(--color-accent)">badges:</span> <span style="color:#f5f5f5">7</span> on WordPress.org</p>
                <p><span style="color:var(--color-accent)">status:</span> <span style="color:#f5f5f5">shipping<span class="vp-cursor">_</span></span></p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <div class="vp-divider"></div>

      {{-- Featured Writing --}}
      <section style="padding:clamp(4rem,10vw,8rem) 0">
        <div style="max-width:min(90vw,800px);margin:0 auto;padding:0 1.5rem">
          <div class="reveal">
            <p style="font-size:0.8rem;font-family:'Space Grotesk',monospace;color:rgba(0,255,136,0.6);letter-spacing:0.15em;margin-bottom:1rem">// recent_writing</p>
            <h2 style="font-size:clamp(1.5rem,3vw,2rem);font-weight:700">Featured posts</h2>
          </div>
          <div style="margin-top:2rem" class="reveal">
            @php
              $featured = get_posts([
                'post_type' => 'post',
                'posts_per_page' => 6,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',
                'no_found_rows' => true,
                'update_post_term_cache' => false,
                'update_post_meta_cache' => false,
              ]);
            @endphp
            @foreach($featured as $post)
              <a href="{{ get_permalink($post) }}" class="vp-post-link">
                <h3>{{ get_the_title($post) }}</h3>
                <time datetime="{{ get_post_time('c', true, $post) }}">{{ get_the_date('M j, Y', $post) }}</time>
              </a>
            @endforeach
          </div>
          <div style="margin-top:2rem" class="reveal">
            <a href="/blog/" class="btn-ghost">View all posts &rarr;</a>
          </div>
        </div>
      </section>

      <div class="vp-divider"></div>

      {{-- Links / Connect --}}
      <section style="padding:clamp(4rem,10vw,8rem) 0;text-align:center">
        <div style="max-width:min(90vw,600px);margin:0 auto;padding:0 1.5rem" class="reveal">
          <p style="font-size:0.8rem;font-family:'Space Grotesk',monospace;color:rgba(0,255,136,0.6);letter-spacing:0.15em;margin-bottom:1rem">// connect</p>
          <h2 style="font-size:clamp(1.5rem,3vw,2rem);font-weight:700">Let's talk about what's next</h2>
          <p style="color:rgba(255,255,255,0.5);margin-top:1rem;font-size:1.05rem">Whether it's AI tooling, WordPress architecture, or an idea you're exploring.</p>
          <div style="margin-top:2.5rem;display:flex;justify-content:center;flex-wrap:wrap;gap:1rem">
            <a href="/contact/" class="btn-green">Get in Touch</a>
            <a href="https://github.com/vapvarun" target="_blank" rel="noopener" class="btn-ghost">GitHub</a>
            <a href="/blog/" class="btn-ghost">Blog</a>
          </div>
          <div class="vp-link-row" style="display:flex;justify-content:center;gap:1.5rem;margin-top:3rem">
            <a href="https://x.com/vapvarun" target="_blank" rel="noopener" aria-label="X"><svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
            <a href="https://github.com/vapvarun" target="_blank" rel="noopener" aria-label="GitHub"><svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg></a>
            <a href="https://linkedin.com/in/vapvarun" target="_blank" rel="noopener" aria-label="LinkedIn"><svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
          </div>
        </div>
      </section>
    </main>

    @include('sections.footer')

    @php(do_action('get_footer'))
    @php(wp_footer())

    <script>
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    </script>
  </body>
</html>
