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
            'saas-product'         => __('SaaS Product Page', 'brndle'),
            'professional-services'=> __('Professional Services', 'brndle'),
            'lead-generation'      => __('Lead Generation', 'brndle'),
            'product-launch'       => __('Product Launch', 'brndle'),
            'promotional-campaign' => __('Promotional Campaign', 'brndle'),
            'ecommerce-product'    => __('E-commerce Product', 'brndle'),
            'portfolio-agency'     => __('Portfolio / Agency', 'brndle'),
        ];

        foreach ($patterns as $slug => $title) {
            $method = 'pattern' . str_replace(' ', '', ucwords(str_replace('-', ' ', $slug)));
            if (method_exists($this, $method)) {
                register_block_pattern("brndle/{$slug}", [
                    'title'      => $title,
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
                'eyebrow' => __('Now in Public Beta', 'brndle'),
                'title' => __('Ship better software,<br>10x faster', 'brndle'),
                'subtitle' => __('Streamline your entire development workflow — from planning to deployment — in one unified platform trusted by 5,000+ engineering teams.', 'brndle'),
                'cta_primary' => __('Start Free Trial', 'brndle'),
                'cta_primary_url' => '#',
                'cta_secondary' => __('Watch Demo', 'brndle'),
                'cta_secondary_url' => '#',
                'variant' => 'dark',
                'logos' => ['Stripe', 'Notion', 'Vercel', 'Linear', 'Figma'],
            ]) .
            $this->serializeBlock('brndle/stats', [
                'variant' => 'dark',
                'items' => [
                    ['value' => '5,000+', 'label' => __('Engineering Teams', 'brndle')],
                    ['value' => '99.9%', 'label' => __('Uptime SLA', 'brndle')],
                    ['value' => '4.2s', 'label' => __('Avg Deploy Time', 'brndle')],
                    ['value' => '$0', 'label' => __('Setup Cost', 'brndle')],
                ],
            ]) .
            $this->serializeBlock('brndle/features', [
                'eyebrow' => __('Core Platform', 'brndle'),
                'title' => __('Everything your team needs to ship faster', 'brndle'),
                'variant' => 'light',
                'features' => [
                    ['title' => __('Smart CI/CD Pipelines', 'brndle'), 'description' => __('Automatic parallel builds, intelligent caching, and zero-config deploys that just work.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => __('Real-time Collaboration', 'brndle'), 'description' => __('Code reviews, pair programming, and shared environments — built for distributed teams.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => __('One-click Environments', 'brndle'), 'description' => __('Spin up production-identical preview environments for every branch and pull request.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/how-it-works', [
                'eyebrow' => __('Get Started in Minutes', 'brndle'),
                'title' => __('From signup to first deploy in under 10 minutes', 'brndle'),
                'layout' => 'horizontal',
                'variant' => 'light',
                'steps' => [
                    ['title' => __('Connect Your Repo', 'brndle'), 'description' => __('Link your GitHub, GitLab, or Bitbucket in one click.', 'brndle'), 'icon' => ''],
                    ['title' => __('Configure Pipeline', 'brndle'), 'description' => __('Use our visual editor or drop in your existing YAML.', 'brndle'), 'icon' => ''],
                    ['title' => __('Deploy with Confidence', 'brndle'), 'description' => __('Push to main and watch your pipeline run automatically.', 'brndle'), 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/testimonials', [
                'eyebrow' => __('Loved by engineers', 'brndle'),
                'title' => __('Teams that switched never looked back', 'brndle'),
                'items' => [
                    ['name' => __('Sarah Chen', 'brndle'), 'role' => __('VP Engineering, Acme Corp', 'brndle'), 'quote' => __('We cut our deploy time from 45 minutes to under 5. The ROI was obvious within the first week.', 'brndle'), 'avatar' => '', 'stars' => 5],
                    ['name' => __('Marcus Rodriguez', 'brndle'), 'role' => __('CTO, ScaleUp', 'brndle'), 'quote' => __('Finally a platform that does not fight you. Our team adopted it in two days with zero training.', 'brndle'), 'avatar' => '', 'stars' => 5],
                    ['name' => __('Priya Patel', 'brndle'), 'role' => __('Lead DevOps, TechFlow', 'brndle'), 'quote' => __('The preview environments alone justified the switch. QA cycles dropped by 60%.', 'brndle'), 'avatar' => '', 'stars' => 5],
                ],
            ]) .
            $this->serializeBlock('brndle/pricing', [
                'eyebrow' => __('Simple Pricing', 'brndle'),
                'title' => __('Start free, scale as you grow', 'brndle'),
                'variant' => 'light',
                'plans' => [
                    ['name' => __('Starter', 'brndle'), 'description' => __('For small teams getting started', 'brndle'), 'price' => '$0', 'period' => __('/mo', 'brndle'), 'features' => [__('3 team members', 'brndle'), __('500 CI minutes/mo', 'brndle'), __('Community support', 'brndle')], 'cta_text' => __('Get Started Free', 'brndle'), 'cta_url' => '#', 'featured' => false, 'badge' => ''],
                    ['name' => __('Pro', 'brndle'), 'description' => __('For growing engineering teams', 'brndle'), 'price' => '$49', 'period' => __('/mo', 'brndle'), 'features' => [__('Unlimited members', 'brndle'), __('5,000 CI minutes/mo', 'brndle'), __('Preview environments', 'brndle'), __('Priority support', 'brndle')], 'cta_text' => __('Start Free Trial', 'brndle'), 'cta_url' => '#', 'featured' => true, 'badge' => __('Most Popular', 'brndle')],
                    ['name' => __('Enterprise', 'brndle'), 'description' => __('For large organizations', 'brndle'), 'price' => __('Custom', 'brndle'), 'period' => '', 'features' => [__('Everything in Pro', 'brndle'), __('Unlimited CI minutes', 'brndle'), __('SSO/SAML', 'brndle'), __('SLA guarantee', 'brndle'), __('Dedicated support', 'brndle')], 'cta_text' => __('Contact Sales', 'brndle'), 'cta_url' => '#', 'featured' => false, 'badge' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/faq', [
                'title' => __('Frequently asked questions', 'brndle'),
                'items' => [
                    ['question' => __('Is there really a free tier?', 'brndle'), 'answer' => __('Yes. The Starter plan is free forever with no credit card required. It includes 3 team members and 500 CI minutes per month.', 'brndle')],
                    ['question' => __('Can I migrate from my current CI/CD?', 'brndle'), 'answer' => __('Absolutely. We support importing from GitHub Actions, GitLab CI, CircleCI, and Jenkins. Most teams migrate in under an hour.', 'brndle')],
                    ['question' => __('What happens if I exceed my plan limits?', 'brndle'), 'answer' => __('We will notify you before you hit your limit. You can upgrade at any time, and we prorate the difference.', 'brndle')],
                    ['question' => __('Do you offer annual billing?', 'brndle'), 'answer' => __('Yes — save 20% with annual billing on all paid plans.', 'brndle')],
                    ['question' => __('What is your cancellation policy?', 'brndle'), 'answer' => __('Cancel anytime with no penalties. Your data is retained for 30 days after cancellation.', 'brndle')],
                ],
            ]) .
            $this->serializeBlock('brndle/cta', [
                'title' => __('Ready to ship 10x faster?', 'brndle'),
                'subtitle' => __('Join 5,000 engineering teams already using the platform. Free forever, no credit card required.', 'brndle'),
                'cta_primary' => __('Start Free Trial', 'brndle'),
                'cta_primary_url' => '#',
                'cta_secondary' => __('Talk to Sales', 'brndle'),
                'cta_secondary_url' => '#',
                'variant' => 'dark',
            ]);
    }

    // ─── Pattern 2: Professional Services ───────────────

    private function patternProfessionalServices(): string
    {
        return
            $this->serializeBlock('brndle/hero', [
                'eyebrow' => __('Management Consulting', 'brndle'),
                'title' => __('We solve the problems that slow your business', 'brndle'),
                'subtitle' => __('Strategic consulting for mid-market companies. We embed with your team, diagnose the root cause, and build the systems to fix it permanently.', 'brndle'),
                'cta_primary' => __('Book a Discovery Call', 'brndle'),
                'cta_primary_url' => '#',
                'cta_secondary' => __('See Our Work', 'brndle'),
                'cta_secondary_url' => '#',
                'variant' => 'dark',
            ]) .
            $this->serializeBlock('brndle/stats', [
                'variant' => 'light',
                'items' => [
                    ['value' => '14 years', 'label' => __('In Business', 'brndle')],
                    ['value' => '$2.4B', 'label' => __('Revenue Impacted', 'brndle')],
                    ['value' => '97%', 'label' => __('Client Retention', 'brndle')],
                    ['value' => '300+', 'label' => __('Engagements', 'brndle')],
                ],
            ]) .
            $this->serializeBlock('brndle/content-image-split', [
                'eyebrow' => __('Our Approach', 'brndle'),
                'title' => __('Diagnosis before prescription', 'brndle'),
                'description' => __('Most consultants arrive with a predetermined playbook. We spend the first two weeks listening, mapping, and measuring before recommending a single change.', 'brndle'),
                'bullets' => [__('Cross-functional interviews with 15-40 stakeholders', 'brndle'), __('Quantitative analysis of your operational data', 'brndle'), __('Benchmarking against 50+ comparable businesses', 'brndle'), __('Written diagnosis report before any engagement begins', 'brndle')],
                'image' => '',
                'image_position' => 'right',
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/features', [
                'eyebrow' => __('Service Lines', 'brndle'),
                'title' => __('We specialize in three areas', 'brndle'),
                'variant' => 'light',
                'features' => [
                    ['title' => __('Operations & Supply Chain', 'brndle'), 'description' => __('Reduce waste, increase throughput, and build resilient operations that scale.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => __('Revenue Growth Strategy', 'brndle'), 'description' => __('Find and fix the bottlenecks between your product and your customers.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => __('Organizational Design', 'brndle'), 'description' => __('Build the team structure, processes, and culture that match your growth stage.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/team', [
                'eyebrow' => __('Your Team', 'brndle'),
                'title' => __('Senior practitioners, not junior analysts', 'brndle'),
                'subtitle' => __('Every engagement is led by a principal with 15+ years of operating experience.', 'brndle'),
                'columns' => '3',
                'variant' => 'light',
                'members' => [
                    ['name' => __('David Kim', 'brndle'), 'role' => __('Managing Partner', 'brndle'), 'bio' => __('Former COO at a $500M logistics company. 20 years in operations.', 'brndle'), 'photo' => '', 'linkedin' => '#', 'twitter' => ''],
                    ['name' => __('Rachel Torres', 'brndle'), 'role' => __('Revenue Strategy Lead', 'brndle'), 'bio' => __('Built and scaled sales orgs from $5M to $100M ARR at three SaaS companies.', 'brndle'), 'photo' => '', 'linkedin' => '#', 'twitter' => ''],
                    ['name' => __('James Okonkwo', 'brndle'), 'role' => __('Org Design Principal', 'brndle'), 'bio' => __('Former CHRO. Specializes in post-acquisition integration and culture transformation.', 'brndle'), 'photo' => '', 'linkedin' => '#', 'twitter' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/testimonials', [
                'eyebrow' => __('Client Results', 'brndle'),
                'title' => __('Measurable outcomes, not just advice', 'brndle'),
                'items' => [
                    ['name' => __('Lisa Chang', 'brndle'), 'role' => __('CEO, Meridian Logistics', 'brndle'), 'quote' => __('They found $4.2M in annual savings we did not know existed. The diagnosis alone was worth the engagement fee.', 'brndle'), 'avatar' => '', 'stars' => 5],
                    ['name' => __('Robert Hayes', 'brndle'), 'role' => __('CFO, Apex Manufacturing', 'brndle'), 'quote' => __('Revenue grew 40% in the first year after implementing their recommendations. No consultant has ever delivered like this.', 'brndle'), 'avatar' => '', 'stars' => 5],
                    ['name' => __('Anika Desai', 'brndle'), 'role' => __('COO, BrightPath Health', 'brndle'), 'quote' => __('They embedded with our team for 12 weeks and left us with systems that still work three years later.', 'brndle'), 'avatar' => '', 'stars' => 5],
                ],
            ]) .
            $this->serializeBlock('brndle/how-it-works', [
                'title' => __('How an engagement works', 'brndle'),
                'layout' => 'vertical',
                'variant' => 'light',
                'steps' => [
                    ['title' => __('Discovery Call', 'brndle'), 'description' => __('A 30-minute conversation to understand your situation and determine if we are a good fit.', 'brndle'), 'icon' => ''],
                    ['title' => __('Diagnostic Phase', 'brndle'), 'description' => __('Two weeks of interviews, data analysis, and benchmarking. You receive a written report.', 'brndle'), 'icon' => ''],
                    ['title' => __('Solution Design', 'brndle'), 'description' => __('We present 2-3 options with projected ROI and implementation timelines.', 'brndle'), 'icon' => ''],
                    ['title' => __('Implementation', 'brndle'), 'description' => __('Our team embeds with yours to execute the plan and transfer knowledge.', 'brndle'), 'icon' => ''],
                    ['title' => __('Handover', 'brndle'), 'description' => __('Documentation, training, and a 90-day support period to ensure sustainability.', 'brndle'), 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/cta', [
                'title' => __('Let us diagnose your biggest constraint', 'brndle'),
                'subtitle' => __('The discovery call is free. If we can not help, we will tell you who can.', 'brndle'),
                'cta_primary' => __('Book Discovery Call', 'brndle'),
                'cta_primary_url' => '#',
                'cta_secondary' => __('Download Case Study', 'brndle'),
                'cta_secondary_url' => '#',
                'variant' => 'dark',
            ]);
    }

    // ─── Pattern 3: Lead Generation ─────────────────────

    private function patternLeadGeneration(): string
    {
        return
            $this->serializeBlock('brndle/hero', [
                'eyebrow' => __('Free Report', 'brndle'),
                'title' => __('The 7 sales mistakes costing you $500K in lost revenue', 'brndle'),
                'subtitle' => __('Download our data-backed report based on interviews with 1,200 B2B sales leaders. Includes benchmarks, playbooks, and a 30-day action plan.', 'brndle'),
                'cta_primary' => __('Download Free Report', 'brndle'),
                'cta_primary_url' => '#lead-form',
                'variant' => 'dark',
            ]) .
            $this->serializeBlock('brndle/logos', [
                'title' => __('Featured in', 'brndle'),
                'companies' => ['Forbes', 'Harvard Business Review', 'Sales Hacker', 'G2', 'Gartner'],
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/stats', [
                'variant' => 'light',
                'items' => [
                    ['value' => '1,200+', 'label' => __('Sales Leaders Surveyed', 'brndle')],
                    ['value' => '$1.2M', 'label' => __('Avg Revenue Impact', 'brndle')],
                    ['value' => '47%', 'label' => __('Pipeline Increase', 'brndle')],
                    ['value' => '3.1x', 'label' => __('ROI Within 90 Days', 'brndle')],
                ],
            ]) .
            $this->serializeBlock('brndle/content-image-split', [
                'eyebrow' => __('What is Inside', 'brndle'),
                'title' => __('93 pages of data your competitors do not have', 'brndle'),
                'bullets' => [__('The 7 pipeline killers and how to fix each one', 'brndle'), __('Benchmark data segmented by company size and industry', 'brndle'), __('Step-by-step outbound playbook used by top performers', 'brndle'), __('Tool stack recommendations with vendor comparisons', 'brndle')],
                'image' => '',
                'image_position' => 'right',
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/testimonials', [
                'title' => __('What sales leaders are saying', 'brndle'),
                'items' => [
                    ['name' => __('Mark Sullivan', 'brndle'), 'role' => __('VP Sales, CloudScale', 'brndle'), 'quote' => __('This report changed how we approach outbound. Pipeline is up 52% since we implemented the framework.', 'brndle'), 'avatar' => '', 'stars' => 5],
                    ['name' => __('Jennifer Wu', 'brndle'), 'role' => __('CRO, DataBridge', 'brndle'), 'quote' => __('The benchmarks alone saved us months of guessing. Best free resource I have found in B2B sales.', 'brndle'), 'avatar' => '', 'stars' => 5],
                    ['name' => __('Alex Petrov', 'brndle'), 'role' => __('Head of Sales, NovaTech', 'brndle'), 'quote' => __('We went from 12 to 19 SQLs per month using the playbook in chapter 4. Concrete, actionable advice.', 'brndle'), 'avatar' => '', 'stars' => 5],
                ],
            ]) .
            $this->serializeBlock('brndle/lead-form', [
                'eyebrow' => __('Get Instant Access', 'brndle'),
                'title' => __('Download the free report', 'brndle'),
                'subtitle' => __('Join 12,000 sales leaders who have already used this framework.', 'brndle'),
                'layout' => 'split',
                'variant' => 'dark',
                'fields' => [
                    ['label' => __('Full Name', 'brndle'), 'type' => 'text', 'required' => true, 'placeholder' => __('Jane Smith', 'brndle')],
                    ['label' => __('Work Email', 'brndle'), 'type' => 'email', 'required' => true, 'placeholder' => 'jane@company.com'],
                    ['label' => __('Company', 'brndle'), 'type' => 'text', 'required' => true, 'placeholder' => __('Acme Inc', 'brndle')],
                ],
                'button_text' => __('Send Me the Report', 'brndle'),
                'success_message' => __('Check your inbox — the report is on its way!', 'brndle'),
            ]);
    }

    // ─── Pattern 4: Product Launch ──────────────────────

    private function patternProductLaunch(): string
    {
        return
            $this->serializeBlock('brndle/hero', [
                'eyebrow' => __('Launching Soon', 'brndle'),
                'title' => __('Introducing Pulse — the AI analytics co-pilot', 'brndle'),
                'subtitle' => __('Pulse connects to every data source in your stack and answers your business questions in plain English. No SQL. No dashboards. Just answers.', 'brndle'),
                'cta_primary' => __('Join the Waitlist', 'brndle'),
                'cta_primary_url' => '#waitlist',
                'cta_secondary' => __('Watch the Demo', 'brndle'),
                'cta_secondary_url' => '#demo',
                'variant' => 'dark',
            ]) .
            $this->serializeBlock('brndle/video-embed', [
                'eyebrow' => __('See It In Action', 'brndle'),
                'title' => __('Three minutes is all it takes', 'brndle'),
                'video_type' => 'youtube',
                'video_url' => '',
                'aspect_ratio' => '16/9',
                'max_width' => 'medium',
                'variant' => 'dark',
            ]) .
            $this->serializeBlock('brndle/features', [
                'eyebrow' => __('Built Different', 'brndle'),
                'title' => __('Analytics that actually answers your questions', 'brndle'),
                'variant' => 'light',
                'features' => [
                    ['title' => __('Natural Language Queries', 'brndle'), 'description' => __('Ask questions in plain English. No SQL required. Get answers in seconds, not hours.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => __('Automated Insight Reports', 'brndle'), 'description' => __('Wake up to a daily briefing of what changed in your metrics and why.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => __('Proactive Anomaly Alerts', 'brndle'), 'description' => __('Get notified before small dips become big problems. AI-powered anomaly detection.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/stats', [
                'variant' => 'dark',
                'items' => [
                    ['value' => '< 2min', 'label' => __('Time to First Insight', 'brndle')],
                    ['value' => '200+', 'label' => __('Native Integrations', 'brndle')],
                    ['value' => '99.5%', 'label' => __('Query Accuracy', 'brndle')],
                    ['value' => '5,000', 'label' => __('Beta Waitlist', 'brndle')],
                ],
            ]) .
            $this->serializeBlock('brndle/how-it-works', [
                'title' => __('Up and running in one afternoon', 'brndle'),
                'layout' => 'horizontal',
                'variant' => 'light',
                'steps' => [
                    ['title' => __('Connect Your Data', 'brndle'), 'description' => __('One-click integrations with Postgres, BigQuery, Snowflake, and 200+ more.', 'brndle'), 'icon' => ''],
                    ['title' => __('Ask Your First Question', 'brndle'), 'description' => __('Type a question like you would ask a colleague. Get an answer with charts.', 'brndle'), 'icon' => ''],
                    ['title' => __('Share with Your Team', 'brndle'), 'description' => __('Save answers as dashboards, schedule email reports, or share via Slack.', 'brndle'), 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/testimonials', [
                'title' => __('What beta users are saying', 'brndle'),
                'items' => [
                    ['name' => __('Emily Park', 'brndle'), 'role' => __('Head of Analytics, Revamp', 'brndle'), 'quote' => __('I replaced three BI tools with Pulse. My team now spends time on decisions, not dashboards.', 'brndle'), 'avatar' => '', 'stars' => 5],
                    ['name' => __('Dan Foster', 'brndle'), 'role' => __('CEO, MetricLab', 'brndle'), 'quote' => __('The anomaly alerts caught a pricing bug that would have cost us $80K. Paid for itself day one.', 'brndle'), 'avatar' => '', 'stars' => 5],
                ],
            ]) .
            $this->serializeBlock('brndle/lead-form', [
                'eyebrow' => __('Limited Beta Access', 'brndle'),
                'title' => __('Join 5,000 teams on the waitlist', 'brndle'),
                'layout' => 'stacked',
                'variant' => 'dark',
                'fields' => [
                    ['label' => __('Work Email', 'brndle'), 'type' => 'email', 'required' => true, 'placeholder' => 'you@company.com'],
                ],
                'button_text' => __('Claim My Spot', 'brndle'),
                'success_message' => __('You are on the list! We will reach out within 48 hours.', 'brndle'),
            ]);
    }

    // ─── Pattern 5: Promotional Campaign ────────────────

    private function patternPromotionalCampaign(): string
    {
        return
            $this->serializeBlock('brndle/hero', [
                'eyebrow' => __('Black Friday — 60% Off Ends Sunday', 'brndle'),
                'title' => __('Professional tools at startup prices', 'brndle'),
                'subtitle' => __('One weekend only: get lifetime access to our complete design system, component library, and Figma kit for a one-time payment.', 'brndle'),
                'cta_primary' => __('Claim 60% Off — $79', 'brndle'),
                'cta_primary_url' => '#',
                'cta_secondary' => __('See What is Included', 'brndle'),
                'cta_secondary_url' => '#features',
                'variant' => 'dark',
            ]) .
            $this->serializeBlock('brndle/stats', [
                'variant' => 'dark',
                'items' => [
                    ['value' => '8,000+', 'label' => __('Designers', 'brndle')],
                    ['value' => '600+', 'label' => __('Components', 'brndle')],
                    ['value' => '4.9/5', 'label' => __('Average Rating', 'brndle')],
                    ['value' => '$79', 'label' => __('One-Time Price', 'brndle')],
                ],
            ]) .
            $this->serializeBlock('brndle/features', [
                'eyebrow' => __('Everything Included', 'brndle'),
                'title' => __('Not a subscription. Own it forever.', 'brndle'),
                'variant' => 'light',
                'features' => [
                    ['title' => __('Complete Design System', 'brndle'), 'description' => __('600+ production-ready components with variants, states, and responsive breakpoints.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => __('Figma Component Kit', 'brndle'), 'description' => __('Auto-layout components with design tokens that sync with your codebase.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => __('Developer Handoff Files', 'brndle'), 'description' => __('React, Vue, and Tailwind code for every component. Copy-paste ready.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/content-image-split', [
                'eyebrow' => __('The Detail', 'brndle'),
                'title' => __('Production-ready, not just pretty', 'brndle'),
                'description' => __('Every component is built to WCAG 2.1 AA, ships with dark mode variants, and includes responsive breakpoints from 320px to 1920px.', 'brndle'),
                'bullets' => [__('WCAG 2.1 AA accessible', 'brndle'), __('Dark mode for every component', 'brndle'), __('5 color palette variations', 'brndle'), __('Tailwind, CSS, and Figma tokens', 'brndle')],
                'image' => '',
                'image_position' => 'left',
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/testimonials', [
                'title' => __('Trusted by 8,000+ designers', 'brndle'),
                'items' => [
                    ['name' => __('Sarah Kim', 'brndle'), 'role' => __('Product Designer, Loom', 'brndle'), 'quote' => __('This kit saved me 200+ hours on my last project. The quality is better than anything I could build myself.', 'brndle'), 'avatar' => '', 'stars' => 5],
                    ['name' => __('Tom Bryant', 'brndle'), 'role' => __('Frontend Lead, Series B Startup', 'brndle'), 'quote' => __('The code quality is exceptional. Auto-layout, accessible, responsive — it just works.', 'brndle'), 'avatar' => '', 'stars' => 5],
                    ['name' => __('Nina Volkov', 'brndle'), 'role' => __('Freelance Designer', 'brndle'), 'quote' => __('Paid for itself on my first client project. Now I use it for everything.', 'brndle'), 'avatar' => '', 'stars' => 5],
                ],
            ]) .
            $this->serializeBlock('brndle/pricing', [
                'title' => __('One plan. Everything included.', 'brndle'),
                'variant' => 'light',
                'plans' => [
                    ['name' => __('Complete Bundle', 'brndle'), 'description' => __('Everything you need', 'brndle'), 'price' => '$79', 'period' => __('one-time (was $199)', 'brndle'), 'features' => [__('All 600+ components', 'brndle'), __('Figma source files', 'brndle'), __('React + Vue code', 'brndle'), __('Lifetime updates', 'brndle'), __('Commercial license', 'brndle'), __('30-day refund guarantee', 'brndle')], 'cta_text' => __('Get Lifetime Access', 'brndle'), 'cta_url' => '#', 'featured' => true, 'badge' => __('60% Off Today', 'brndle')],
                ],
            ]) .
            $this->serializeBlock('brndle/faq', [
                'title' => __('Common questions', 'brndle'),
                'items' => [
                    ['question' => __('What does lifetime access mean?', 'brndle'), 'answer' => __('You pay once and receive all current components plus every future update. No recurring fees, ever.', 'brndle')],
                    ['question' => __('What is the refund policy?', 'brndle'), 'answer' => __('Full refund within 30 days, no questions asked. We want you to be completely satisfied.', 'brndle')],
                    ['question' => __('Can I use this for client projects?', 'brndle'), 'answer' => __('Yes. The commercial license covers unlimited personal and client projects.', 'brndle')],
                    ['question' => __('When does the sale end?', 'brndle'), 'answer' => __('Sunday at midnight EST. The price returns to $199 on Monday — no exceptions.', 'brndle')],
                ],
            ]) .
            $this->serializeBlock('brndle/cta', [
                'title' => __('Offer ends Sunday at midnight', 'brndle'),
                'subtitle' => __('Price returns to $199 on Monday. No exceptions, no extensions.', 'brndle'),
                'cta_primary' => __('Get Lifetime Access — $79', 'brndle'),
                'cta_primary_url' => '#',
                'variant' => 'dark',
            ]);
    }

    // ─── Pattern 6: E-commerce Product ──────────────────

    private function patternEcommerceProduct(): string
    {
        return
            $this->serializeBlock('brndle/hero', [
                'eyebrow' => __('New: Cold Brew Collection', 'brndle'),
                'title' => __('Coffee that earns its morning', 'brndle'),
                'subtitle' => __('Single-origin beans, small-batch roasted within 48 hours of your order. Shipped fresh to your door with a satisfaction guarantee.', 'brndle'),
                'cta_primary' => __('Shop the Collection', 'brndle'),
                'cta_primary_url' => '#',
                'cta_secondary' => __('Take the Quiz', 'brndle'),
                'cta_secondary_url' => '#',
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/logos', [
                'title' => __('As seen in', 'brndle'),
                'companies' => ['The New York Times', 'Bon Appetit', 'Wirecutter', 'Food & Wine', 'Eater'],
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/content-image-split', [
                'eyebrow' => __('The Difference', 'brndle'),
                'title' => __('From farm to your cup in 72 hours', 'brndle'),
                'description' => __('We work directly with 12 farms across Ethiopia, Colombia, and Guatemala. No middlemen, no commodity brokers.', 'brndle'),
                'bullets' => [__('Roasted to order, never pre-roasted stock', 'brndle'), __('Carbon-neutral shipping on every order', 'brndle'), __('Compostable packaging, no plastic', 'brndle'), __('Full traceability — scan the bag to meet your farmer', 'brndle')],
                'image' => '',
                'image_position' => 'right',
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/how-it-works', [
                'title' => __('Freshness by design', 'brndle'),
                'layout' => 'horizontal',
                'variant' => 'light',
                'steps' => [
                    ['title' => __('Pick Your Roast', 'brndle'), 'description' => __('Light, medium, or dark — plus single-origin and seasonal blends.', 'brndle'), 'icon' => ''],
                    ['title' => __('We Roast to Order', 'brndle'), 'description' => __('Your beans are roasted the day after you order. Never from inventory.', 'brndle'), 'icon' => ''],
                    ['title' => __('Ships in 24 Hours', 'brndle'), 'description' => __('Delivered to your door in 2-3 days. Free shipping over $35.', 'brndle'), 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/features', [
                'eyebrow' => __('Our Commitment', 'brndle'),
                'title' => __('What makes this coffee different', 'brndle'),
                'variant' => 'light',
                'features' => [
                    ['title' => __('Direct Trade Partnership', 'brndle'), 'description' => __('We pay farmers 40% above fair trade prices and visit every farm annually.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => __('Roasted to Order', 'brndle'), 'description' => __('No warehouse inventory. Every bag is roasted specifically for your order.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => __('Carbon-Neutral Delivery', 'brndle'), 'description' => __('We offset 100% of shipping emissions through verified carbon credit programs.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/stats', [
                'variant' => 'dark',
                'items' => [
                    ['value' => '12', 'label' => __('Partner Farms', 'brndle')],
                    ['value' => '48hrs', 'label' => __('Roast to Ship', 'brndle')],
                    ['value' => '50,000+', 'label' => __('Happy Customers', 'brndle')],
                    ['value' => '4.8/5', 'label' => __('Average Rating', 'brndle')],
                ],
            ]) .
            $this->serializeBlock('brndle/testimonials', [
                'title' => __('What our customers say', 'brndle'),
                'items' => [
                    ['name' => __('Michael Torres', 'brndle'), 'role' => __('Home barista', 'brndle'), 'quote' => __('I have tried every subscription box. This is the only one where I can taste the difference freshness makes.', 'brndle'), 'avatar' => '', 'stars' => 5],
                    ['name' => __('Sophie Laurent', 'brndle'), 'role' => __('Cafe owner, Brooklyn', 'brndle'), 'quote' => __('We switched our house blend to their Colombian single-origin. Customers noticed immediately.', 'brndle'), 'avatar' => '', 'stars' => 5],
                    ['name' => __('Chris Park', 'brndle'), 'role' => __('Coffee enthusiast', 'brndle'), 'quote' => __('The traceability QR code is a game-changer. I know exactly which farm grew my beans.', 'brndle'), 'avatar' => '', 'stars' => 5],
                ],
            ]) .
            $this->serializeBlock('brndle/faq', [
                'title' => __('Common questions', 'brndle'),
                'items' => [
                    ['question' => __('What grind options do you offer?', 'brndle'), 'answer' => __('Whole bean, espresso, drip, pour-over, French press, and cold brew grinds. Select at checkout.', 'brndle')],
                    ['question' => __('How fast is shipping?', 'brndle'), 'answer' => __('Orders placed before 2pm ship same day. Standard delivery is 2-3 business days.', 'brndle')],
                    ['question' => __('Can I pause or cancel my subscription?', 'brndle'), 'answer' => __('Yes, anytime. No commitment, no cancellation fees. Manage everything from your account.', 'brndle')],
                    ['question' => __('What is the freshness guarantee?', 'brndle'), 'answer' => __('If you are not satisfied with the freshness, we will replace your order or refund you. No questions asked.', 'brndle')],
                ],
            ]) .
            $this->serializeBlock('brndle/cta', [
                'title' => __('Your first bag ships tomorrow', 'brndle'),
                'subtitle' => __('Free shipping on orders over $35. Pause or cancel any time.', 'brndle'),
                'cta_primary' => __('Start Your Order', 'brndle'),
                'cta_primary_url' => '#',
                'cta_secondary' => __('Browse All Roasts', 'brndle'),
                'cta_secondary_url' => '#',
                'variant' => 'dark',
            ]);
    }

    // ─── Pattern 7: Portfolio / Agency ───────────────────

    private function patternPortfolioAgency(): string
    {
        return
            $this->serializeBlock('brndle/hero', [
                'eyebrow' => __('Brand & Digital Agency', 'brndle'),
                'title' => __('We build the brands people cannot stop talking about', 'brndle'),
                'subtitle' => __('Strategic branding, product design, and web development for ambitious companies. Based in New York. Working everywhere.', 'brndle'),
                'cta_primary' => __('Start a Project', 'brndle'),
                'cta_primary_url' => '#',
                'cta_secondary' => __('See Our Work', 'brndle'),
                'cta_secondary_url' => '#',
                'variant' => 'dark',
            ]) .
            $this->serializeBlock('brndle/logos', [
                'title' => __('Clients we have worked with', 'brndle'),
                'companies' => ['Shopify', 'Airbnb', 'Stripe', 'Duolingo', 'Loom', 'Notion'],
                'variant' => 'dark',
            ]) .
            $this->serializeBlock('brndle/stats', [
                'variant' => 'dark',
                'items' => [
                    ['value' => '180+', 'label' => __('Projects Launched', 'brndle')],
                    ['value' => '12yrs', 'label' => __('In Business', 'brndle')],
                    ['value' => '94%', 'label' => __('Repeat Clients', 'brndle')],
                    ['value' => '22', 'label' => __('Awwwards', 'brndle')],
                ],
            ]) .
            $this->serializeBlock('brndle/features', [
                'eyebrow' => __('Services', 'brndle'),
                'title' => __('End-to-end creative execution', 'brndle'),
                'variant' => 'light',
                'features' => [
                    ['title' => __('Brand Strategy & Identity', 'brndle'), 'description' => __('Positioning, naming, visual identity, and brand guidelines that scale from startup to enterprise.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => __('Product Design & UX', 'brndle'), 'description' => __('User research, wireframing, prototyping, and high-fidelity UI for web and mobile products.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                    ['title' => __('Web Development & CMS', 'brndle'), 'description' => __('Performant, accessible websites built on modern stacks. WordPress, headless, or custom.', 'brndle'), 'bullets' => [], 'image' => '', 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/team', [
                'eyebrow' => __('The People', 'brndle'),
                'title' => __('Small team. Massive output.', 'brndle'),
                'subtitle' => __('We stay deliberately small so every client gets senior attention.', 'brndle'),
                'columns' => '4',
                'variant' => 'light',
                'members' => [
                    ['name' => __('Alex Rivera', 'brndle'), 'role' => __('Creative Director', 'brndle'), 'bio' => __('Former lead designer at Pentagram. 15 years in brand identity.', 'brndle'), 'photo' => '', 'linkedin' => '#', 'twitter' => '#'],
                    ['name' => __('Maya Johnson', 'brndle'), 'role' => __('Strategy Lead', 'brndle'), 'bio' => __('Ex-McKinsey. Translates business goals into creative briefs.', 'brndle'), 'photo' => '', 'linkedin' => '#', 'twitter' => ''],
                    ['name' => __('Kai Nakamura', 'brndle'), 'role' => __('Lead Designer', 'brndle'), 'bio' => __('Awwwards judge. Obsessed with typography and motion.', 'brndle'), 'photo' => '', 'linkedin' => '', 'twitter' => '#'],
                    ['name' => __('Sam Chen', 'brndle'), 'role' => __('Engineering Lead', 'brndle'), 'bio' => __('Full-stack. Builds the performant frontends designers dream up.', 'brndle'), 'photo' => '', 'linkedin' => '#', 'twitter' => '#'],
                ],
            ]) .
            $this->serializeBlock('brndle/content-image-split', [
                'eyebrow' => __('Case Study', 'brndle'),
                'title' => __('How we helped Meridian grow 3x in 18 months', 'brndle'),
                'description' => __('Meridian came to us with a commoditized brand and no digital presence. We rebuilt their identity, redesigned their product, and launched a new site — in 12 weeks.', 'brndle'),
                'image' => '',
                'image_position' => 'left',
                'cta_text' => __('Read the Case Study', 'brndle'),
                'cta_url' => '#',
                'variant' => 'light',
            ]) .
            $this->serializeBlock('brndle/testimonials', [
                'title' => __('What clients say', 'brndle'),
                'items' => [
                    ['name' => __('Lauren Miller', 'brndle'), 'role' => __('Founder, Meridian', 'brndle'), 'quote' => __('They turned our brand from forgettable to magnetic. Inbound leads tripled within 3 months of launch.', 'brndle'), 'avatar' => '', 'stars' => 5],
                    ['name' => __('James Park', 'brndle'), 'role' => __('CMO, Elevate Health', 'brndle'), 'quote' => __('Best agency experience I have had in 20 years. They deliver on time, on budget, and above expectations.', 'brndle'), 'avatar' => '', 'stars' => 5],
                    ['name' => __('Olivia Zhang', 'brndle'), 'role' => __('VP Product, DataSync', 'brndle'), 'quote' => __('Our product redesign increased activation by 45%. The UX work alone justified the engagement.', 'brndle'), 'avatar' => '', 'stars' => 5],
                ],
            ]) .
            $this->serializeBlock('brndle/how-it-works', [
                'title' => __('How we work together', 'brndle'),
                'layout' => 'vertical',
                'variant' => 'light',
                'steps' => [
                    ['title' => __('Discovery & Brief', 'brndle'), 'description' => __('We learn your business, audience, and goals. You get a detailed creative brief.', 'brndle'), 'icon' => ''],
                    ['title' => __('Strategy & Concept', 'brndle'), 'description' => __('We present 2-3 strategic directions with moodboards and rationale.', 'brndle'), 'icon' => ''],
                    ['title' => __('Design & Build', 'brndle'), 'description' => __('4-8 weeks of focused execution with weekly check-ins.', 'brndle'), 'icon' => ''],
                    ['title' => __('Launch & Handover', 'brndle'), 'description' => __('We launch, train your team, and provide 30 days of post-launch support.', 'brndle'), 'icon' => ''],
                ],
            ]) .
            $this->serializeBlock('brndle/lead-form', [
                'eyebrow' => __('Work With Us', 'brndle'),
                'title' => __('Tell us about your project', 'brndle'),
                'subtitle' => __('We respond within one business day.', 'brndle'),
                'layout' => 'split',
                'variant' => 'dark',
                'fields' => [
                    ['label' => __('Name', 'brndle'), 'type' => 'text', 'required' => true, 'placeholder' => __('Your name', 'brndle')],
                    ['label' => __('Email', 'brndle'), 'type' => 'email', 'required' => true, 'placeholder' => 'you@company.com'],
                    ['label' => __('Company', 'brndle'), 'type' => 'text', 'required' => false, 'placeholder' => __('Company name', 'brndle')],
                    ['label' => __('Tell us about your project', 'brndle'), 'type' => 'textarea', 'required' => false, 'placeholder' => __('What are you building? What is the timeline?', 'brndle')],
                ],
                'button_text' => __('Send Message', 'brndle'),
                'success_message' => __('Got it — we will be in touch within 24 hours.', 'brndle'),
            ]);
    }
}
