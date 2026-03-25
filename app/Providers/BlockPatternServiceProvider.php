<?php

namespace Brndle\Providers;

class BlockPatternServiceProvider
{
    protected string $category = 'brndle-pages';

    public function boot(): void
    {
        add_action('init', [$this, 'registerCategory']);
        add_action('init', [$this, 'registerPatterns']);
    }

    public function registerCategory(): void
    {
        register_block_pattern_category($this->category, [
            'label' => __('Brndle Page Templates', 'brndle'),
        ]);
    }

    public function registerPatterns(): void
    {
        $patterns = [
            'saas-product'         => 'SaaS Product Page',
            'professional-services'=> 'Professional Services',
            'lead-generation'      => 'Lead Generation',
            'product-launch'       => 'Product Launch',
            'promotional-campaign' => 'Promotional Campaign',
            'ecommerce-product'    => 'E-commerce Product',
            'portfolio-agency'     => 'Portfolio / Agency',
        ];

        foreach ($patterns as $slug => $title) {
            $method = 'pattern' . str_replace(' ', '', ucwords(str_replace('-', ' ', $slug)));
            if (method_exists($this, $method)) {
                register_block_pattern("brndle/{$slug}", [
                    'title'      => __($title, 'brndle'),
                    'categories' => [$this->category],
                    'blockTypes' => ['brndle/hero'],
                    'content'    => $this->{$method}(),
                ]);
            }
        }
    }

    private function serializeBlock(string $name, array $attrs = []): string
    {
        if (empty($attrs)) {
            return "<!-- wp:{$name} /-->\n\n";
        }
        $json = json_encode($attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return "<!-- wp:{$name} {$json} /-->\n\n";
    }

    // ─── Pattern 1: SaaS Product Page ───────────────────

    private function patternSaasProduct(): string
    {
        return
            $this->serializeBlock('brndle/hero', [
                'eyebrow' => 'Now in Public Beta',
                'title' => 'Ship better software,<br>10x faster',
                'subtitle' => 'Streamline your entire development workflow — from planning to deployment — in one unified platform trusted by 5,000+ engineering teams.',
                'cta_primary' => 'Start Free Trial',
                'cta_primary_url' => '#',
                'cta_secondary' => 'Watch Demo',
                'cta_secondary_url' => '#',
                'variant' => 'dark',
                'logos' => ['Stripe', 'Notion', 'Vercel', 'Linear', 'Figma'],
            ]) .
            $this->serializeBlock('brndle/stats', [
                'variant' => 'dark',
                'items' => [
                    ['value' => '5,000+', 'label' => 'Engineering Teams'],
                    ['value' => '99.9%', 'label' => 'Uptime SLA'],
                    ['value' => '4.2s', 'label' => 'Avg Deploy Time'],
                    ['value' => '$0', 'label' => 'Setup Cost'],
                ],
            ]) .
            $this->serializeBlock('brndle/features', [
                'eyebrow' => 'Core Platform',
                'title' => 'Everything your team needs to ship faster',
                'variant' => 'light',
                'features' => [
                    ['title' => 'Smart CI/CD Pipelines', 'description' => 'Automatic parallel builds, intelligent caching, and zero-config deploys that just work.', 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => 'Real-time Collaboration', 'description' => 'Code reviews, pair programming, and shared environments — built for distributed teams.', 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => 'One-click Environments', 'description' => 'Spin up production-identical preview environments for every branch and pull request.', 'bullets' => [], 'image' => '', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/how-it-works', [
                'eyebrow' => 'Get Started in Minutes',
                'title' => 'From signup to first deploy in under 10 minutes',
                'layout' => 'horizontal',
                'variant' => 'light',
                'steps' => [
                    ['title' => 'Connect Your Repo', 'description' => 'Link your GitHub, GitLab, or Bitbucket in one click.', 'icon' => ''],
                    ['title' => 'Configure Pipeline', 'description' => 'Use our visual editor or drop in your existing YAML.', 'icon' => ''],
                    ['title' => 'Deploy with Confidence', 'description' => 'Push to main and watch your pipeline run automatically.', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/testimonials', [
                'eyebrow' => 'Loved by engineers',
                'title' => 'Teams that switched never looked back',
                'items' => [
                    ['name' => 'Sarah Chen', 'role' => 'VP Engineering, Acme Corp', 'quote' => 'We cut our deploy time from 45 minutes to under 5. The ROI was obvious within the first week.', 'avatar' => '', 'stars' => 5],
                    ['name' => 'Marcus Rodriguez', 'role' => 'CTO, ScaleUp', 'quote' => 'Finally a platform that does not fight you. Our team adopted it in two days with zero training.', 'avatar' => '', 'stars' => 5],
                    ['name' => 'Priya Patel', 'role' => 'Lead DevOps, TechFlow', 'quote' => 'The preview environments alone justified the switch. QA cycles dropped by 60%.', 'avatar' => '', 'stars' => 5],
                ],
            ]) .
            $this->serializeBlock('brndle/pricing', [
                'eyebrow' => 'Simple Pricing',
                'title' => 'Start free, scale as you grow',
                'variant' => 'light',
                'plans' => [
                    ['name' => 'Starter', 'description' => 'For small teams getting started', 'price' => '$0', 'period' => '/mo', 'features' => ['3 team members', '500 CI minutes/mo', 'Community support'], 'cta_text' => 'Get Started Free', 'cta_url' => '#', 'featured' => false, 'badge' => ''],
                    ['name' => 'Pro', 'description' => 'For growing engineering teams', 'price' => '$49', 'period' => '/mo', 'features' => ['Unlimited members', '5,000 CI minutes/mo', 'Preview environments', 'Priority support'], 'cta_text' => 'Start Free Trial', 'cta_url' => '#', 'featured' => true, 'badge' => 'Most Popular'],
                    ['name' => 'Enterprise', 'description' => 'For large organizations', 'price' => 'Custom', 'period' => '', 'features' => ['Everything in Pro', 'Unlimited CI minutes', 'SSO/SAML', 'SLA guarantee', 'Dedicated support'], 'cta_text' => 'Contact Sales', 'cta_url' => '#', 'featured' => false, 'badge' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/faq', [
                'title' => 'Frequently asked questions',
                'items' => [
                    ['question' => 'Is there really a free tier?', 'answer' => 'Yes. The Starter plan is free forever with no credit card required. It includes 3 team members and 500 CI minutes per month.'],
                    ['question' => 'Can I migrate from my current CI/CD?', 'answer' => 'Absolutely. We support importing from GitHub Actions, GitLab CI, CircleCI, and Jenkins. Most teams migrate in under an hour.'],
                    ['question' => 'What happens if I exceed my plan limits?', 'answer' => 'We will notify you before you hit your limit. You can upgrade at any time, and we prorate the difference.'],
                    ['question' => 'Do you offer annual billing?', 'answer' => 'Yes — save 20% with annual billing on all paid plans.'],
                    ['question' => 'What is your cancellation policy?', 'answer' => 'Cancel anytime with no penalties. Your data is retained for 30 days after cancellation.'],
                ],
            ]) .
            $this->serializeBlock('brndle/cta', [
                'title' => 'Ready to ship 10x faster?',
                'subtitle' => 'Join 5,000 engineering teams already using the platform. Free forever, no credit card required.',
                'cta_primary' => 'Start Free Trial',
                'cta_primary_url' => '#',
                'cta_secondary' => 'Talk to Sales',
                'cta_secondary_url' => '#',
                'variant' => 'dark',
            ]);
    }

    // ─── Pattern 2: Professional Services ───────────────

    private function patternProfessionalServices(): string
    {
        return
            $this->serializeBlock('brndle/hero', [
                'eyebrow' => 'Management Consulting',
                'title' => 'We solve the problems that slow your business',
                'subtitle' => 'Strategic consulting for mid-market companies. We embed with your team, diagnose the root cause, and build the systems to fix it permanently.',
                'cta_primary' => 'Book a Discovery Call',
                'cta_primary_url' => '#',
                'cta_secondary' => 'See Our Work',
                'cta_secondary_url' => '#',
                'variant' => 'dark',
            ]) .
            $this->serializeBlock('brndle/stats', [
                'variant' => 'light',
                'items' => [
                    ['value' => '14 years', 'label' => 'In Business'],
                    ['value' => '$2.4B', 'label' => 'Revenue Impacted'],
                    ['value' => '97%', 'label' => 'Client Retention'],
                    ['value' => '300+', 'label' => 'Engagements'],
                ],
            ]) .
            $this->serializeBlock('brndle/content-image-split', [
                'eyebrow' => 'Our Approach',
                'title' => 'Diagnosis before prescription',
                'description' => 'Most consultants arrive with a predetermined playbook. We spend the first two weeks listening, mapping, and measuring before recommending a single change.',
                'bullets' => ['Cross-functional interviews with 15-40 stakeholders', 'Quantitative analysis of your operational data', 'Benchmarking against 50+ comparable businesses', 'Written diagnosis report before any engagement begins'],
                'image' => '',
                'image_position' => 'right',
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/features', [
                'eyebrow' => 'Service Lines',
                'title' => 'We specialize in three areas',
                'variant' => 'light',
                'features' => [
                    ['title' => 'Operations & Supply Chain', 'description' => 'Reduce waste, increase throughput, and build resilient operations that scale.', 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => 'Revenue Growth Strategy', 'description' => 'Find and fix the bottlenecks between your product and your customers.', 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => 'Organizational Design', 'description' => 'Build the team structure, processes, and culture that match your growth stage.', 'bullets' => [], 'image' => '', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/team', [
                'eyebrow' => 'Your Team',
                'title' => 'Senior practitioners, not junior analysts',
                'subtitle' => 'Every engagement is led by a principal with 15+ years of operating experience.',
                'columns' => '3',
                'variant' => 'light',
                'members' => [
                    ['name' => 'David Kim', 'role' => 'Managing Partner', 'bio' => 'Former COO at a $500M logistics company. 20 years in operations.', 'photo' => '', 'linkedin' => '#', 'twitter' => ''],
                    ['name' => 'Rachel Torres', 'role' => 'Revenue Strategy Lead', 'bio' => 'Built and scaled sales orgs from $5M to $100M ARR at three SaaS companies.', 'photo' => '', 'linkedin' => '#', 'twitter' => ''],
                    ['name' => 'James Okonkwo', 'role' => 'Org Design Principal', 'bio' => 'Former CHRO. Specializes in post-acquisition integration and culture transformation.', 'photo' => '', 'linkedin' => '#', 'twitter' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/testimonials', [
                'eyebrow' => 'Client Results',
                'title' => 'Measurable outcomes, not just advice',
                'items' => [
                    ['name' => 'Lisa Chang', 'role' => 'CEO, Meridian Logistics', 'quote' => 'They found $4.2M in annual savings we did not know existed. The diagnosis alone was worth the engagement fee.', 'avatar' => '', 'stars' => 5],
                    ['name' => 'Robert Hayes', 'role' => 'CFO, Apex Manufacturing', 'quote' => 'Revenue grew 40% in the first year after implementing their recommendations. No consultant has ever delivered like this.', 'avatar' => '', 'stars' => 5],
                    ['name' => 'Anika Desai', 'role' => 'COO, BrightPath Health', 'quote' => 'They embedded with our team for 12 weeks and left us with systems that still work three years later.', 'avatar' => '', 'stars' => 5],
                ],
            ]) .
            $this->serializeBlock('brndle/how-it-works', [
                'title' => 'How an engagement works',
                'layout' => 'vertical',
                'variant' => 'light',
                'steps' => [
                    ['title' => 'Discovery Call', 'description' => 'A 30-minute conversation to understand your situation and determine if we are a good fit.', 'icon' => ''],
                    ['title' => 'Diagnostic Phase', 'description' => 'Two weeks of interviews, data analysis, and benchmarking. You receive a written report.', 'icon' => ''],
                    ['title' => 'Solution Design', 'description' => 'We present 2-3 options with projected ROI and implementation timelines.', 'icon' => ''],
                    ['title' => 'Implementation', 'description' => 'Our team embeds with yours to execute the plan and transfer knowledge.', 'icon' => ''],
                    ['title' => 'Handover', 'description' => 'Documentation, training, and a 90-day support period to ensure sustainability.', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/cta', [
                'title' => 'Let us diagnose your biggest constraint',
                'subtitle' => 'The discovery call is free. If we can not help, we will tell you who can.',
                'cta_primary' => 'Book Discovery Call',
                'cta_primary_url' => '#',
                'cta_secondary' => 'Download Case Study',
                'cta_secondary_url' => '#',
                'variant' => 'dark',
            ]);
    }

    // ─── Pattern 3: Lead Generation ─────────────────────

    private function patternLeadGeneration(): string
    {
        return
            $this->serializeBlock('brndle/hero', [
                'eyebrow' => 'Free Report',
                'title' => 'The 7 sales mistakes costing you $500K in lost revenue',
                'subtitle' => 'Download our data-backed report based on interviews with 1,200 B2B sales leaders. Includes benchmarks, playbooks, and a 30-day action plan.',
                'cta_primary' => 'Download Free Report',
                'cta_primary_url' => '#lead-form',
                'variant' => 'dark',
            ]) .
            $this->serializeBlock('brndle/logos', [
                'title' => 'Featured in',
                'companies' => ['Forbes', 'Harvard Business Review', 'Sales Hacker', 'G2', 'Gartner'],
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/stats', [
                'variant' => 'light',
                'items' => [
                    ['value' => '1,200+', 'label' => 'Sales Leaders Surveyed'],
                    ['value' => '$1.2M', 'label' => 'Avg Revenue Impact'],
                    ['value' => '47%', 'label' => 'Pipeline Increase'],
                    ['value' => '3.1x', 'label' => 'ROI Within 90 Days'],
                ],
            ]) .
            $this->serializeBlock('brndle/content-image-split', [
                'eyebrow' => 'What is Inside',
                'title' => '93 pages of data your competitors do not have',
                'bullets' => ['The 7 pipeline killers and how to fix each one', 'Benchmark data segmented by company size and industry', 'Step-by-step outbound playbook used by top performers', 'Tool stack recommendations with vendor comparisons'],
                'image' => '',
                'image_position' => 'right',
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/testimonials', [
                'title' => 'What sales leaders are saying',
                'items' => [
                    ['name' => 'Mark Sullivan', 'role' => 'VP Sales, CloudScale', 'quote' => 'This report changed how we approach outbound. Pipeline is up 52% since we implemented the framework.', 'avatar' => '', 'stars' => 5],
                    ['name' => 'Jennifer Wu', 'role' => 'CRO, DataBridge', 'quote' => 'The benchmarks alone saved us months of guessing. Best free resource I have found in B2B sales.', 'avatar' => '', 'stars' => 5],
                    ['name' => 'Alex Petrov', 'role' => 'Head of Sales, NovaTech', 'quote' => 'We went from 12 to 19 SQLs per month using the playbook in chapter 4. Concrete, actionable advice.', 'avatar' => '', 'stars' => 5],
                ],
            ]) .
            $this->serializeBlock('brndle/lead-form', [
                'eyebrow' => 'Get Instant Access',
                'title' => 'Download the free report',
                'subtitle' => 'Join 12,000 sales leaders who have already used this framework.',
                'layout' => 'split',
                'variant' => 'dark',
                'fields' => [
                    ['label' => 'Full Name', 'type' => 'text', 'required' => true, 'placeholder' => 'Jane Smith'],
                    ['label' => 'Work Email', 'type' => 'email', 'required' => true, 'placeholder' => 'jane@company.com'],
                    ['label' => 'Company', 'type' => 'text', 'required' => true, 'placeholder' => 'Acme Inc'],
                ],
                'button_text' => 'Send Me the Report',
                'success_message' => 'Check your inbox — the report is on its way!',
            ]);
    }

    // ─── Pattern 4: Product Launch ──────────────────────

    private function patternProductLaunch(): string
    {
        return
            $this->serializeBlock('brndle/hero', [
                'eyebrow' => 'Launching Soon',
                'title' => 'Introducing Pulse — the AI analytics co-pilot',
                'subtitle' => 'Pulse connects to every data source in your stack and answers your business questions in plain English. No SQL. No dashboards. Just answers.',
                'cta_primary' => 'Join the Waitlist',
                'cta_primary_url' => '#waitlist',
                'cta_secondary' => 'Watch the Demo',
                'cta_secondary_url' => '#demo',
                'variant' => 'dark',
            ]) .
            $this->serializeBlock('brndle/video-embed', [
                'eyebrow' => 'See It In Action',
                'title' => 'Three minutes is all it takes',
                'video_type' => 'youtube',
                'video_url' => '',
                'aspect_ratio' => '16/9',
                'max_width' => 'medium',
                'variant' => 'dark',
            ]) .
            $this->serializeBlock('brndle/features', [
                'eyebrow' => 'Built Different',
                'title' => 'Analytics that actually answers your questions',
                'variant' => 'light',
                'features' => [
                    ['title' => 'Natural Language Queries', 'description' => 'Ask questions in plain English. No SQL required. Get answers in seconds, not hours.', 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => 'Automated Insight Reports', 'description' => 'Wake up to a daily briefing of what changed in your metrics and why.', 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => 'Proactive Anomaly Alerts', 'description' => 'Get notified before small dips become big problems. AI-powered anomaly detection.', 'bullets' => [], 'image' => '', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/stats', [
                'variant' => 'dark',
                'items' => [
                    ['value' => '< 2min', 'label' => 'Time to First Insight'],
                    ['value' => '200+', 'label' => 'Native Integrations'],
                    ['value' => '99.5%', 'label' => 'Query Accuracy'],
                    ['value' => '5,000', 'label' => 'Beta Waitlist'],
                ],
            ]) .
            $this->serializeBlock('brndle/how-it-works', [
                'title' => 'Up and running in one afternoon',
                'layout' => 'horizontal',
                'variant' => 'light',
                'steps' => [
                    ['title' => 'Connect Your Data', 'description' => 'One-click integrations with Postgres, BigQuery, Snowflake, and 200+ more.', 'icon' => ''],
                    ['title' => 'Ask Your First Question', 'description' => 'Type a question like you would ask a colleague. Get an answer with charts.', 'icon' => ''],
                    ['title' => 'Share with Your Team', 'description' => 'Save answers as dashboards, schedule email reports, or share via Slack.', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/testimonials', [
                'title' => 'What beta users are saying',
                'items' => [
                    ['name' => 'Emily Park', 'role' => 'Head of Analytics, Revamp', 'quote' => 'I replaced three BI tools with Pulse. My team now spends time on decisions, not dashboards.', 'avatar' => '', 'stars' => 5],
                    ['name' => 'Dan Foster', 'role' => 'CEO, MetricLab', 'quote' => 'The anomaly alerts caught a pricing bug that would have cost us $80K. Paid for itself day one.', 'avatar' => '', 'stars' => 5],
                ],
            ]) .
            $this->serializeBlock('brndle/lead-form', [
                'eyebrow' => 'Limited Beta Access',
                'title' => 'Join 5,000 teams on the waitlist',
                'layout' => 'stacked',
                'variant' => 'dark',
                'fields' => [
                    ['label' => 'Work Email', 'type' => 'email', 'required' => true, 'placeholder' => 'you@company.com'],
                ],
                'button_text' => 'Claim My Spot',
                'success_message' => 'You are on the list! We will reach out within 48 hours.',
            ]);
    }

    // ─── Pattern 5: Promotional Campaign ────────────────

    private function patternPromotionalCampaign(): string
    {
        return
            $this->serializeBlock('brndle/hero', [
                'eyebrow' => 'Black Friday — 60% Off Ends Sunday',
                'title' => 'Professional tools at startup prices',
                'subtitle' => 'One weekend only: get lifetime access to our complete design system, component library, and Figma kit for a one-time payment.',
                'cta_primary' => 'Claim 60% Off — $79',
                'cta_primary_url' => '#',
                'cta_secondary' => 'See What is Included',
                'cta_secondary_url' => '#features',
                'variant' => 'dark',
            ]) .
            $this->serializeBlock('brndle/stats', [
                'variant' => 'dark',
                'items' => [
                    ['value' => '8,000+', 'label' => 'Designers'],
                    ['value' => '600+', 'label' => 'Components'],
                    ['value' => '4.9/5', 'label' => 'Average Rating'],
                    ['value' => '$79', 'label' => 'One-Time Price'],
                ],
            ]) .
            $this->serializeBlock('brndle/features', [
                'eyebrow' => 'Everything Included',
                'title' => 'Not a subscription. Own it forever.',
                'variant' => 'light',
                'features' => [
                    ['title' => 'Complete Design System', 'description' => '600+ production-ready components with variants, states, and responsive breakpoints.', 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => 'Figma Component Kit', 'description' => 'Auto-layout components with design tokens that sync with your codebase.', 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => 'Developer Handoff Files', 'description' => 'React, Vue, and Tailwind code for every component. Copy-paste ready.', 'bullets' => [], 'image' => '', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/content-image-split', [
                'eyebrow' => 'The Detail',
                'title' => 'Production-ready, not just pretty',
                'description' => 'Every component is built to WCAG 2.1 AA, ships with dark mode variants, and includes responsive breakpoints from 320px to 1920px.',
                'bullets' => ['WCAG 2.1 AA accessible', 'Dark mode for every component', '5 color palette variations', 'Tailwind, CSS, and Figma tokens'],
                'image' => '',
                'image_position' => 'left',
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/testimonials', [
                'title' => 'Trusted by 8,000+ designers',
                'items' => [
                    ['name' => 'Sarah Kim', 'role' => 'Product Designer, Loom', 'quote' => 'This kit saved me 200+ hours on my last project. The quality is better than anything I could build myself.', 'avatar' => '', 'stars' => 5],
                    ['name' => 'Tom Bryant', 'role' => 'Frontend Lead, Series B Startup', 'quote' => 'The code quality is exceptional. Auto-layout, accessible, responsive — it just works.', 'avatar' => '', 'stars' => 5],
                    ['name' => 'Nina Volkov', 'role' => 'Freelance Designer', 'quote' => 'Paid for itself on my first client project. Now I use it for everything.', 'avatar' => '', 'stars' => 5],
                ],
            ]) .
            $this->serializeBlock('brndle/pricing', [
                'title' => 'One plan. Everything included.',
                'variant' => 'light',
                'plans' => [
                    ['name' => 'Complete Bundle', 'description' => 'Everything you need', 'price' => '$79', 'period' => 'one-time (was $199)', 'features' => ['All 600+ components', 'Figma source files', 'React + Vue code', 'Lifetime updates', 'Commercial license', '30-day refund guarantee'], 'cta_text' => 'Get Lifetime Access', 'cta_url' => '#', 'featured' => true, 'badge' => '60% Off Today'],
                ],
            ]) .
            $this->serializeBlock('brndle/faq', [
                'title' => 'Common questions',
                'items' => [
                    ['question' => 'What does lifetime access mean?', 'answer' => 'You pay once and receive all current components plus every future update. No recurring fees, ever.'],
                    ['question' => 'What is the refund policy?', 'answer' => 'Full refund within 30 days, no questions asked. We want you to be completely satisfied.'],
                    ['question' => 'Can I use this for client projects?', 'answer' => 'Yes. The commercial license covers unlimited personal and client projects.'],
                    ['question' => 'When does the sale end?', 'answer' => 'Sunday at midnight EST. The price returns to $199 on Monday — no exceptions.'],
                ],
            ]) .
            $this->serializeBlock('brndle/cta', [
                'title' => 'Offer ends Sunday at midnight',
                'subtitle' => 'Price returns to $199 on Monday. No exceptions, no extensions.',
                'cta_primary' => 'Get Lifetime Access — $79',
                'cta_primary_url' => '#',
                'variant' => 'dark',
            ]);
    }

    // ─── Pattern 6: E-commerce Product ──────────────────

    private function patternEcommerceProduct(): string
    {
        return
            $this->serializeBlock('brndle/hero', [
                'eyebrow' => 'New: Cold Brew Collection',
                'title' => 'Coffee that earns its morning',
                'subtitle' => 'Single-origin beans, small-batch roasted within 48 hours of your order. Shipped fresh to your door with a satisfaction guarantee.',
                'cta_primary' => 'Shop the Collection',
                'cta_primary_url' => '#',
                'cta_secondary' => 'Take the Quiz',
                'cta_secondary_url' => '#',
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/logos', [
                'title' => 'As seen in',
                'companies' => ['The New York Times', 'Bon Appetit', 'Wirecutter', 'Food & Wine', 'Eater'],
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/content-image-split', [
                'eyebrow' => 'The Difference',
                'title' => 'From farm to your cup in 72 hours',
                'description' => 'We work directly with 12 farms across Ethiopia, Colombia, and Guatemala. No middlemen, no commodity brokers.',
                'bullets' => ['Roasted to order, never pre-roasted stock', 'Carbon-neutral shipping on every order', 'Compostable packaging, no plastic', 'Full traceability — scan the bag to meet your farmer'],
                'image' => '',
                'image_position' => 'right',
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/how-it-works', [
                'title' => 'Freshness by design',
                'layout' => 'horizontal',
                'variant' => 'light',
                'steps' => [
                    ['title' => 'Pick Your Roast', 'description' => 'Light, medium, or dark — plus single-origin and seasonal blends.', 'icon' => ''],
                    ['title' => 'We Roast to Order', 'description' => 'Your beans are roasted the day after you order. Never from inventory.', 'icon' => ''],
                    ['title' => 'Ships in 24 Hours', 'description' => 'Delivered to your door in 2-3 days. Free shipping over $35.', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/features', [
                'eyebrow' => 'Our Commitment',
                'title' => 'What makes this coffee different',
                'variant' => 'light',
                'features' => [
                    ['title' => 'Direct Trade Partnership', 'description' => 'We pay farmers 40% above fair trade prices and visit every farm annually.', 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => 'Roasted to Order', 'description' => 'No warehouse inventory. Every bag is roasted specifically for your order.', 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => 'Carbon-Neutral Delivery', 'description' => 'We offset 100% of shipping emissions through verified carbon credit programs.', 'bullets' => [], 'image' => '', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/stats', [
                'variant' => 'dark',
                'items' => [
                    ['value' => '12', 'label' => 'Partner Farms'],
                    ['value' => '48hrs', 'label' => 'Roast to Ship'],
                    ['value' => '50,000+', 'label' => 'Happy Customers'],
                    ['value' => '4.8/5', 'label' => 'Average Rating'],
                ],
            ]) .
            $this->serializeBlock('brndle/testimonials', [
                'title' => 'What our customers say',
                'items' => [
                    ['name' => 'Michael Torres', 'role' => 'Home barista', 'quote' => 'I have tried every subscription box. This is the only one where I can taste the difference freshness makes.', 'avatar' => '', 'stars' => 5],
                    ['name' => 'Sophie Laurent', 'role' => 'Cafe owner, Brooklyn', 'quote' => 'We switched our house blend to their Colombian single-origin. Customers noticed immediately.', 'avatar' => '', 'stars' => 5],
                    ['name' => 'Chris Park', 'role' => 'Coffee enthusiast', 'quote' => 'The traceability QR code is a game-changer. I know exactly which farm grew my beans.', 'avatar' => '', 'stars' => 5],
                ],
            ]) .
            $this->serializeBlock('brndle/faq', [
                'title' => 'Common questions',
                'items' => [
                    ['question' => 'What grind options do you offer?', 'answer' => 'Whole bean, espresso, drip, pour-over, French press, and cold brew grinds. Select at checkout.'],
                    ['question' => 'How fast is shipping?', 'answer' => 'Orders placed before 2pm ship same day. Standard delivery is 2-3 business days.'],
                    ['question' => 'Can I pause or cancel my subscription?', 'answer' => 'Yes, anytime. No commitment, no cancellation fees. Manage everything from your account.'],
                    ['question' => 'What is the freshness guarantee?', 'answer' => 'If you are not satisfied with the freshness, we will replace your order or refund you. No questions asked.'],
                ],
            ]) .
            $this->serializeBlock('brndle/cta', [
                'title' => 'Your first bag ships tomorrow',
                'subtitle' => 'Free shipping on orders over $35. Pause or cancel any time.',
                'cta_primary' => 'Start Your Order',
                'cta_primary_url' => '#',
                'cta_secondary' => 'Browse All Roasts',
                'cta_secondary_url' => '#',
                'variant' => 'dark',
            ]);
    }

    // ─── Pattern 7: Portfolio / Agency ───────────────────

    private function patternPortfolioAgency(): string
    {
        return
            $this->serializeBlock('brndle/hero', [
                'eyebrow' => 'Brand & Digital Agency',
                'title' => 'We build the brands people cannot stop talking about',
                'subtitle' => 'Strategic branding, product design, and web development for ambitious companies. Based in New York. Working everywhere.',
                'cta_primary' => 'Start a Project',
                'cta_primary_url' => '#',
                'cta_secondary' => 'See Our Work',
                'cta_secondary_url' => '#',
                'variant' => 'dark',
            ]) .
            $this->serializeBlock('brndle/logos', [
                'title' => 'Clients we have worked with',
                'companies' => ['Shopify', 'Airbnb', 'Stripe', 'Duolingo', 'Loom', 'Notion'],
                'variant' => 'dark',
            ]) .
            $this->serializeBlock('brndle/stats', [
                'variant' => 'dark',
                'items' => [
                    ['value' => '180+', 'label' => 'Projects Launched'],
                    ['value' => '12yrs', 'label' => 'In Business'],
                    ['value' => '94%', 'label' => 'Repeat Clients'],
                    ['value' => '22', 'label' => 'Awwwards'],
                ],
            ]) .
            $this->serializeBlock('brndle/features', [
                'eyebrow' => 'Services',
                'title' => 'End-to-end creative execution',
                'variant' => 'light',
                'features' => [
                    ['title' => 'Brand Strategy & Identity', 'description' => 'Positioning, naming, visual identity, and brand guidelines that scale from startup to enterprise.', 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => 'Product Design & UX', 'description' => 'User research, wireframing, prototyping, and high-fidelity UI for web and mobile products.', 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => 'Web Development & CMS', 'description' => 'Performant, accessible websites built on modern stacks. WordPress, headless, or custom.', 'bullets' => [], 'image' => '', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/team', [
                'eyebrow' => 'The People',
                'title' => 'Small team. Massive output.',
                'subtitle' => 'We stay deliberately small so every client gets senior attention.',
                'columns' => '4',
                'variant' => 'light',
                'members' => [
                    ['name' => 'Alex Rivera', 'role' => 'Creative Director', 'bio' => 'Former lead designer at Pentagram. 15 years in brand identity.', 'photo' => '', 'linkedin' => '#', 'twitter' => '#'],
                    ['name' => 'Maya Johnson', 'role' => 'Strategy Lead', 'bio' => 'Ex-McKinsey. Translates business goals into creative briefs.', 'photo' => '', 'linkedin' => '#', 'twitter' => ''],
                    ['name' => 'Kai Nakamura', 'role' => 'Lead Designer', 'bio' => 'Awwwards judge. Obsessed with typography and motion.', 'photo' => '', 'linkedin' => '', 'twitter' => '#'],
                    ['name' => 'Sam Chen', 'role' => 'Engineering Lead', 'bio' => 'Full-stack. Builds the performant frontends designers dream up.', 'photo' => '', 'linkedin' => '#', 'twitter' => '#'],
                ],
            ]) .
            $this->serializeBlock('brndle/content-image-split', [
                'eyebrow' => 'Case Study',
                'title' => 'How we helped Meridian grow 3x in 18 months',
                'description' => 'Meridian came to us with a commoditized brand and no digital presence. We rebuilt their identity, redesigned their product, and launched a new site — in 12 weeks.',
                'image' => '',
                'image_position' => 'left',
                'cta_text' => 'Read the Case Study',
                'cta_url' => '#',
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/testimonials', [
                'title' => 'What clients say',
                'items' => [
                    ['name' => 'Lauren Miller', 'role' => 'Founder, Meridian', 'quote' => 'They turned our brand from forgettable to magnetic. Inbound leads tripled within 3 months of launch.', 'avatar' => '', 'stars' => 5],
                    ['name' => 'James Park', 'role' => 'CMO, Elevate Health', 'quote' => 'Best agency experience I have had in 20 years. They deliver on time, on budget, and above expectations.', 'avatar' => '', 'stars' => 5],
                    ['name' => 'Olivia Zhang', 'role' => 'VP Product, DataSync', 'quote' => 'Our product redesign increased activation by 45%. The UX work alone justified the engagement.', 'avatar' => '', 'stars' => 5],
                ],
            ]) .
            $this->serializeBlock('brndle/how-it-works', [
                'title' => 'How we work together',
                'layout' => 'vertical',
                'variant' => 'light',
                'steps' => [
                    ['title' => 'Discovery & Brief', 'description' => 'We learn your business, audience, and goals. You get a detailed creative brief.', 'icon' => ''],
                    ['title' => 'Strategy & Concept', 'description' => 'We present 2-3 strategic directions with moodboards and rationale.', 'icon' => ''],
                    ['title' => 'Design & Build', 'description' => '4-8 weeks of focused execution with weekly check-ins.', 'icon' => ''],
                    ['title' => 'Launch & Handover', 'description' => 'We launch, train your team, and provide 30 days of post-launch support.', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/lead-form', [
                'eyebrow' => 'Work With Us',
                'title' => 'Tell us about your project',
                'subtitle' => 'We respond within one business day.',
                'layout' => 'split',
                'variant' => 'dark',
                'fields' => [
                    ['label' => 'Name', 'type' => 'text', 'required' => true, 'placeholder' => 'Your name'],
                    ['label' => 'Email', 'type' => 'email', 'required' => true, 'placeholder' => 'you@company.com'],
                    ['label' => 'Company', 'type' => 'text', 'required' => false, 'placeholder' => 'Company name'],
                    ['label' => 'Tell us about your project', 'type' => 'textarea', 'required' => false, 'placeholder' => 'What are you building? What is the timeline?'],
                ],
                'button_text' => 'Send Message',
                'success_message' => 'Got it — we will be in touch within 24 hours.',
            ]);
    }
}
