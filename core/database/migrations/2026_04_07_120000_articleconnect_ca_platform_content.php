<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = Carbon::now();

        if (Schema::hasTable('general_settings')) {
            DB::table('general_settings')->where('id', 1)->update(['site_name' => 'Article Connect']);
        }

        if (Schema::hasTable('frontends')) {
            DB::table('frontends')->where('data_keys', 'blog.element')->delete();
            DB::table('frontends')->where('data_keys', 'blog.content')->delete();

            $fe = [
                1 => [
                    'seo_image' => '1',
                    'keywords' => ['article connect', 'CA articleship', 'chartered accountant internship', 'audit trainee', 'GST', 'tally', 'ICAI'],
                    'description' => 'Article Connect is India\'s focused platform for CA articleship and finance internships. Verified CA firms and companies connect with CA Final, IPCC, and B.Com students for structured training roles.',
                    'social_title' => 'Article Connect — CA Articleship & Internships',
                    'social_description' => 'Apply to articleship and internship roles with CA firms, audit practices, and finance teams. Build GST, audit, and accounting skills in real workplaces.',
                    'image' => '679de5c787bc51738401223.png',
                ],
                24 => [
                    'has_image' => '1',
                    'heading' => 'Built for ICAI Articleship & Finance Internships',
                    'image' => '67d931272de8c1742287143.png',
                ],
                28 => [
                    'heading' => 'Why Article Connect',
                    'subheading' => 'Structured training, transparent applications, and roles that match your CA or commerce journey.',
                ],
                33 => [
                    'heading' => 'Why professionals choose Article Connect',
                    'sub_heading' => 'From GST returns to statutory audit — find training that matches your goals.',
                ],
                34 => [
                    'title' => 'Verified employers',
                    'description' => 'CA firms, audit practices, and finance teams post articleship and internship openings in one place.',
                    'feature_icon' => 'las la-building',
                ],
                35 => [
                    'trx_type' => 'withdraw',
                    'service_icon' => '<i class="las la-file-invoice"></i>',
                    'title' => 'GST & compliance exposure',
                    'description' => 'Roles often include GST filing support, reconciliations, and client documentation.',
                ],
                36 => [
                    'trx_type' => 'deposit',
                    'heading' => 'One platform for training roles',
                    'subheading' => 'Browse openings, apply with your profile, and track applications without generic freelance noise.',
                ],
                64 => [
                    'has_image' => '1',
                    'heading' => 'Find the Best Articleship Opportunities',
                    'subheading' => 'Connecting CA Students with CA Firms',
                    'subtitle' => 'Article Connect – CA Articleship Platform',
                    'feature_one' => 'Structured training',
                    'feature_two' => 'Articleship & internship roles',
                    'feature_three' => 'GST · Audit · Accounts',
                    'image' => '67d92c14906c01742285844.png',
                    'shape' => '673af8b35ae361731918003.png',
                ],
                65 => [
                    'question' => 'What roles can I find on Article Connect?',
                    'answer' => 'Articleship with CA firms, audit internships, tax & GST traineeships, corporate finance internships, and accounting assistant roles — focused on commerce and CA students.',
                ],
                73 => [
                    'heading' => 'How Article Connect works',
                    'subheading' => 'CA Firms post articleship openings; CA Students apply with profiles that show audit, tax, and accounts readiness.',
                ],
                74 => [
                    'icon' => '<i class="fas fa-briefcase"></i>',
                    'title' => 'Post Articleship Opportunity',
                    'content' => 'Share title, duration, stipend (if any), location, and skills — e.g. audit, GST, Tally, Excel.',
                ],
                75 => [
                    'icon' => '<i class="fas fa-user-graduate"></i>',
                    'title' => 'Hire CA Students',
                    'content' => 'Review applications from CA Final, IPCC, or B.Com students whose skills match your firm.',
                ],
                76 => [
                    'icon' => '<i class="fas fa-check-square"></i>',
                    'title' => 'Start Working',
                    'content' => 'Coordinate interviews and training start dates through clear communication on the platform.',
                ],
                77 => [
                    'icon' => '<i class="fas fa-hand-holding-usd"></i>',
                    'title' => 'Trusted firm connections',
                    'content' => 'Align stipend and documentation with your firm\'s ICAI and HR policies.',
                ],
                78 => [
                    'has_image' => '1',
                    'freelancer_title' => 'Sign Up as a CA Student',
                    'freelancer_content' => 'CA, IPCC, or B.Com profile with skills such as GST, Tally, audit basics, and Excel.',
                    'freelancer_button_name' => 'CA Student signup',
                    'buyer_title' => 'Sign Up as a CA Firm',
                    'buyer_content' => 'Post articleship and internship openings for your CA firm, audit practice, or finance team.',
                    'buyer_button_name' => 'CA Firm signup',
                    'freelancer' => '67d929f13124f1742285297.png',
                    'buyer' => '67d929f13efd31742285297.png',
                ],
                79 => [
                    'heading' => 'Why Article Connect',
                    'subheading' => 'Purpose-built for Indian articleship and finance internships.',
                ],
                80 => [
                    'has_image' => '1',
                    'title' => 'Verified CA Firms & Opportunities',
                    'content' => 'Roles focus on articleship, audit, tax, and accounts — reviewed for seriousness and training value.',
                    'image' => '67d92d4630f5f1742286150.png',
                ],
                81 => [
                    'has_image' => '1',
                    'title' => 'Structured applications',
                    'content' => 'CA Students apply with clear notes and profiles; CA Firms choose the right fit for each desk.',
                    'image' => '67d92d3aea8ba1742286138.png',
                ],
                82 => [
                    'has_image' => '1',
                    'title' => 'Trusted Firm Connections',
                    'content' => 'Identity checks and firm verification options help keep the network trustworthy.',
                    'image' => '67d92d322b52c1742286130.png',
                ],
                83 => [
                    'has_image' => '1',
                    'title' => 'Post articleship roles easily',
                    'content' => 'List duration, location, partner-in-charge expectations, and software (TallyPrime, SAP, etc.).',
                    'image' => '67d92d2981d5d1742286121.png',
                ],
                84 => [
                    'has_image' => '1',
                    'title' => 'Apply with one clear flow',
                    'content' => 'One application flow per listing; stipends and terms are set by the CA Firm in the description.',
                    'image' => '67d92d210a8a91742286113.png',
                ],
                85 => [
                    'has_image' => '1',
                    'title' => 'Strong talent pool',
                    'content' => 'Students highlight ICAI exam levels, articleship eligibility, and practical software skills.',
                    'image' => '67d92d169e8d61742286102.png',
                ],
                86 => [
                    'has_image' => '1',
                    'subtitle' => 'Find your training',
                    'heading' => 'Match skills to the right desk',
                    'subheading' => 'GST, audit, accounts payable, MIS, and more — filter roles that fit your CA journey.',
                    'button_name' => 'Browse openings',
                    'image' => '67d930681c42a1742286952.png',
                    'shape' => '673b2b970126d1731931031.png',
                ],
                87 => ['find_step' => 'Build a profile with education, tools (Tally, Excel), and preferred city.'],
                88 => ['find_step' => 'Apply to articleship or internship posts with a short note for the partner or HR.'],
                89 => ['find_step' => 'Track applications and follow up professionally.'],
                90 => [
                    'has_image' => '1',
                    'heading' => 'What makes us different',
                    'subheading' => 'Focused on statutory audit, tax, and accounts training for CA articleship.',
                    'image' => '67d9265b17fcb1742284379.png',
                ],
                91 => [
                    'title' => 'Verified CA Firms & opportunities',
                    'content' => 'Listings emphasize training outcomes: audit files, ITR, GST, and client coordination.',
                ],
                92 => [
                    'title' => 'Trusted Firm Connections',
                    'content' => 'Work with verified CA Firms, audit practices, and finance teams that hire through Article Connect.',
                ],
                93 => [
                    'title' => 'Unlimited Articleship Access',
                    'content' => 'Search and filter by practice area, city, and stipend to find roles that fit your CA journey.',
                ],
                94 => [
                    'has_image' => '1',
                    'heading' => 'Ready to start?',
                    'subheading' => 'From first-year articles to final-year corporate internships — simplify your next step.',
                    'image' => '67d925f7de2fb1742284279.png',
                ],
                95 => ['done_step' => 'Connect with CA firms and finance teams actively hiring trainees.'],
                96 => ['done_step' => 'Support for verified accounts and application history.'],
                97 => ['done_step' => 'Transparent communication between student and firm.'],
                98 => [
                    'heading' => 'What firms and students say',
                    'subheading' => 'Focused hiring for Indian audit and tax practices.',
                ],
                99 => [
                    'has_image' => '1',
                    'quote' => 'We filled two audit internship seats in days — every applicant understood debits & credits and Tally basics.',
                    'name' => 'Partner, Mid-size CA Firm',
                    'country' => 'India',
                    'image' => '67d9338740e281742287751.png',
                ],
                100 => [
                    'has_image' => '1',
                    'quote' => 'Clear role descriptions for GST work. I knew exactly what the firm expected before I applied.',
                    'name' => 'CA Final student',
                    'country' => 'India',
                    'image' => '67d9337f5f5951742287743.png',
                ],
                101 => [
                    'has_image' => '1',
                    'quote' => 'Better than general job boards — everyone here speaks ICAI and articleship language.',
                    'name' => 'B.Com (Finance)',
                    'country' => 'India',
                    'image' => '67d9335d03c161742287709.png',
                ],
                102 => [
                    'has_image' => '1',
                    'quote' => 'Our finance team uses Article Connect for structured summer projects in FP&A and reporting.',
                    'name' => 'Head of Finance',
                    'country' => 'India',
                    'image' => '67d933753ae241742287733.png',
                ],
                103 => [
                    'has_image' => '1',
                    'quote' => 'I listed Tally + Excel and got matched to an audit role where I actually filed GST returns.',
                    'name' => 'Articleship trainee',
                    'country' => 'India',
                    'image' => '67d9334a3c9131742287690.png',
                ],
                104 => [
                    'heading' => 'Featured CA Students',
                    'subheading' => 'CA, IPCC, and commerce students ready for desk work in audit, tax, and accounts.',
                ],
                105 => [
                    'icon' => '<i class="far fa-star"></i>',
                    'digit' => '120',
                    'content' => 'CA Students listing GST, Tally, audit support, and statutory compliance skills',
                ],
                106 => [
                    'icon' => '<i class="fa-solid fa-sack-dollar"></i>',
                    'digit' => '85',
                    'content' => 'CA firms, tax boutiques, and finance teams hiring through Article Connect',
                ],
                107 => [
                    'icon' => '<i class="fas fa-hourglass-half"></i>',
                    'digit' => '5',
                    'content' => 'Days typical for CA Firms to shortlist applications for internship batches',
                ],
                108 => [
                    'has_image' => '1',
                    'heading' => 'Updates on articleship & hiring',
                    'subheading' => 'Occasional tips on ICAI compliance, firm expectations, and interview prep.',
                    'image' => '67d9340c2ee7b1742287884.png',
                    'shape' => '673b4178ca7a71731936632.png',
                ],
                112 => [
                    'heading' => 'Frequently asked questions',
                    'subheading' => 'Articleship eligibility, internships, and how applications work on Article Connect.',
                    'has_image' => '1',
                    'image' => '67d9248a6a2f51742283914.png',
                ],
                113 => [
                    'question' => 'Can I apply before registering with ICAI for articleship?',
                    'answer' => 'Some firms post “pre-articleship” internships; always confirm eligibility with the firm and ICAI rules before accepting.',
                ],
                114 => [
                    'question' => 'How do I improve my chances?',
                    'answer' => 'Complete your profile with exam level, software skills (Tally, Excel), city preference, and any prior internship.',
                ],
                115 => [
                    'question' => 'Is there auction-style competition on stipend?',
                    'answer' => 'No. CA Students submit one application per listing; stipend and terms are set by the CA Firm in the opportunity description.',
                ],
                116 => [
                    'question' => 'What should my application note include?',
                    'answer' => 'Brief introduction, articleship stage, relevant coursework, availability, and why you fit that firm’s practice.',
                ],
                120 => [
                    'has_image' => '1',
                    'title' => 'Find trainees with the right skills',
                    'content' => 'Search profiles that highlight GST, audit cycles, bank reconciliations, and statutory due dates.',
                    'image' => '67d945cd160991742292429.png',
                ],
                121 => [
                    'has_image' => '1',
                    'title' => 'Students: land a real desk',
                    'content' => 'Filter by city, practice area, and stipend range to target serious training roles.',
                    'image' => '67d945b86adf21742292408.png',
                ],
                122 => [
                    'has_image' => '1',
                    'title' => 'Transparent expectations',
                    'content' => 'Firms outline hours, partners, and tools; students know what they are signing up for.',
                    'image' => '67d945a395d981742292387.png',
                ],
                125 => [
                    'heading' => 'Trusted by audit & finance teams',
                ],
                144 => [
                    'freelancer_login_button' => 'Log in as CA Student',
                    'buyer_login_button' => 'Log in as CA Firm',
                    'freelancer_register_button' => 'Join as CA Student',
                    'buyer_register_button' => 'Join as CA Firm',
                ],
            ];

            foreach ($fe as $id => $payload) {
                DB::table('frontends')->where('id', $id)->update([
                    'data_values' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => $now,
                ]);
            }
        }

        if (Schema::hasTable('pages')) {
            $home = DB::table('pages')->where('id', 1)->value('secs');
            if ($home) {
                $arr = json_decode($home, true);
                if (is_array($arr)) {
                    $arr = array_values(array_filter($arr, fn ($s) => $s !== 'blog'));
                    DB::table('pages')->where('id', 1)->update([
                        'secs' => json_encode($arr),
                        'updated_at' => $now,
                    ]);
                }
            }
            DB::table('pages')->where('id', 3)->update([
                'name' => 'Browse opportunities',
                'updated_at' => $now,
            ]);
            DB::table('pages')->where('id', 4)->update([
                'name' => 'Students',
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('plans')) {
            DB::table('plans')->where('id', 1)->update(['name' => 'Articleship Starter (Free)', 'updated_at' => $now]);
            DB::table('plans')->where('id', 2)->update(['name' => 'Articleship Plus', 'updated_at' => $now]);
            DB::table('plans')->where('id', 3)->update(['name' => 'Firm Starter (Free)', 'updated_at' => $now]);
            DB::table('plans')->where('id', 4)->update(['name' => 'Firm Hiring Plus', 'updated_at' => $now]);
        }

        $categoryNames = [
            'CA Articleship',
            'Audit & Assurance',
            'GST & Indirect Tax',
            'Accounting & MIS',
            'Corporate Finance',
        ];

        if (Schema::hasTable('categories')) {
            $count = DB::table('categories')->count();
            if ($count === 0) {
                foreach ($categoryNames as $name) {
                    DB::table('categories')->insert([
                        'name' => $name,
                        'status' => 1,
                        'is_featured' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            } else {
                $ids = DB::table('categories')->orderBy('id')->limit(count($categoryNames))->pluck('id')->all();
                foreach ($ids as $i => $id) {
                    if (! isset($categoryNames[$i])) {
                        break;
                    }
                    DB::table('categories')->where('id', $id)->update([
                        'name' => $categoryNames[$i],
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        $skillNames = [
            'GST return filing', 'TallyPrime', 'Statutory audit', 'Internal audit',
            'Income tax return', 'Financial statements', 'Excel (advanced)', 'Ind AS basics',
            'ROC filings', 'Bank reconciliation', 'Payroll processing', 'Cost accounting',
        ];

        if (Schema::hasTable('skills')) {
            if (DB::table('skills')->count() === 0) {
                foreach ($skillNames as $skillName) {
                    DB::table('skills')->insert([
                        'name' => $skillName,
                        'status' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            } else {
                $sids = DB::table('skills')->orderBy('id')->limit(count($skillNames))->pluck('id')->all();
                foreach ($sids as $i => $id) {
                    if (! isset($skillNames[$i])) {
                        break;
                    }
                    DB::table('skills')->where('id', $id)->update([
                        'name' => $skillNames[$i],
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        $policy = DB::table('frontends')->where('id', 145)->first();
        if ($policy) {
            $d = json_decode($policy->data_values, true);
            if (is_array($d)) {
                $d['title'] = 'Application policy';
                DB::table('frontends')->where('id', 145)->update([
                    'data_values' => json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => $now,
                ]);
            }
        }

        if (DB::table('frontends')->where('id', 147)->exists()) {
            DB::table('frontends')->where('id', 147)->update([
                'data_values' => json_encode([
                    'question' => 'How do firms shortlist students?',
                    'answer' => 'Firms review your profile, exam level, and application note. Many schedule a short interview before confirming articleship or internship offers.',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('notification_templates')) {
            DB::table('notification_templates')->where('act', 'FREELANCER_INVITATION')->update([
                'name' => 'CA Student invitation',
                'subject' => '{{site_name}} — Invitation to apply',
                'push_title' => '{{site_name}} — CA Firm invitation',
                'email_body' => '<p><b>{{buyer}}</b> (CA Firm) invited you to explore <b>{{active_post}}</b> open listing(s).</p><p>Browse their opportunities: <a href="{{post_page}}">{{post_page}}</a></p>',
                'sms_body' => 'CA Firm ({{buyer}}) invited you. Open: {{post_page}}',
                'push_body' => 'Invitation from CA Firm {{buyer}}',
                'shortcodes' => '{"buyer":"CA Firm name","active_post":"Open listings count","post_page":"Firm opportunities URL"}',
                'updated_at' => $now,
            ]);

            DB::table('notification_templates')->where('act', 'BID_PLACED')->update([
                'name' => 'Application submitted',
                'subject' => '{{site_name}} — New application',
                'push_title' => '{{site_name}} — Application received',
                'email_body' => '<div>A CA Student submitted an application for <b>{{title}}</b>.</div><div><br></div><div>Applicant: {{freelancer}}</div><div>Expected stipend / note: {{amount}}</div><div>Budget type: {{budget_type}}</div><div>Availability: {{estimate}}</div><div><br></div><div>Review the application in your dashboard.</div>',
                'sms_body' => 'A CA Student applied to {{title}}.',
                'push_body' => 'New application on {{title}}',
                'updated_at' => $now,
            ]);

            DB::table('notification_templates')->where('act', 'BID_WITHRAWN')->update([
                'name' => 'Application withdrawn',
                'subject' => '{{site_name}} — Application withdrawn',
                'email_body' => '<p>The CA Student <strong>{{freelancer}}</strong> withdrew their application for <strong>{{job}}</strong>.</p>',
                'sms_body' => 'A CA Student withdrew from {{job}}.',
                'push_body' => 'Application withdrawn',
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
    }
};
