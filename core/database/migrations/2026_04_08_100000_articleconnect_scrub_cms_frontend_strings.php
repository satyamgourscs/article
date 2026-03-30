<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('frontends')) {
            return;
        }

        $now = Carbon::now();

        foreach (DB::table('frontends')->orderBy('id')->cursor() as $row) {
            $raw = $row->data_values;
            if (! is_string($raw) || $raw === '') {
                continue;
            }

            $decoded = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                continue;
            }

            $cleaned = $this->walk($decoded);
            $encoded = json_encode($cleaned, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($encoded !== false && $encoded !== $raw) {
                DB::table('frontends')->where('id', $row->id)->update([
                    'data_values' => $encoded,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /** @param  mixed  $node @return mixed */
    private function walk($node)
    {
        if (is_array($node)) {
            $out = [];
            foreach ($node as $key => $value) {
                $out[$key] = $this->walk($value);
            }

            return $out;
        }

        if (is_string($node)) {
            return $this->scrubString($node);
        }

        return $node;
    }

    private function scrubString(string $s): string
    {
        $phrases = [
            'Find the Best Freelance Jobs' => 'Find the Best Articleship Opportunities',
            'Connecting talent with opportunity – The future of work is here!' => 'Connecting CA Students with CA Firms',
            'Connecting talent with opportunity \u2013 The future of work is here!' => 'Connecting CA Students with CA Firms',
            'Connecting talent with opportunity' => 'Connecting CA Students with CA Firms',
            'High Quality Listings' => 'Verified CA Firms & Opportunities',
            'Higher Quality Listings' => 'Verified CA Firms & Opportunities',
            'Secure Payments' => 'Trusted Firm Connections',
            'Unlimited Job Search Resources' => 'Unlimited Articleship Access',
            'Unlimited Job Search' => 'Unlimited Articleship Access',
            'Post a Job' => 'Post Articleship Opportunity',
            'Hire Freelancers' => 'Hire CA Students',
            'Hire Freelancer' => 'Hire CA Students',
            'Get Work Done' => 'Start Working',
            'Sign Up as a Freelancer' => 'Sign Up as a CA Student',
            'Sign Up as a Buyer' => 'Sign Up as a CA Firm',
            'Showcase your skills, connect with buyers, and get hired.' => 'Showcase your skills, connect with CA Firms, and get hired.',
            'Create Freelance Account' => 'CA Student signup',
            'Create Buyer Account' => 'CA Firm signup',
            'Post jobs, hire skilled talent, and get projects done.' => 'Post articleship opportunities, hire CA Students, and grow your firm.',
            'Post Job & Hire a Pro' => 'Post opportunity & hire CA Students',
            'Bid to Find Jobs' => 'Apply for opportunities',
            'Login as Freelancer' => 'Log in as CA Student',
            'Login as Buyer' => 'Log in as CA Firm',
            'Join as Freelancer' => 'Join as CA Student',
            'Join as Buyer' => 'Join as CA Firm',
            'Top Rated Freelancers' => 'Featured CA Students',
            'Our platform connects clients with freelancers to get work done efficiently and securely.' => 'Our platform connects CA Firms with CA Students for structured articleship and internships.',
            "It's Easy to Get Work Done" => 'How Article Connect works',
            'Discover the benefits of using our platform for your freelancing and hiring needs.' => 'Discover why students and CA Firms choose Article Connect for articleship and internships.',
            'Discover the facilities, or benefits of using Article Connect for your freelancing and hiring needs.' => 'Discover what makes Article Connect different for CA articleship and firm hiring.',
            'How\'s Article Connect is Different' => 'What makes Article Connect different',
            'Find a Freelancer and Hire Top Talent' => 'Find CA Students and build your team',
            'Find a Job and Top Matches Buyer' => 'Find opportunities matched to your profile',
            'Communicating with freelancers' => 'Working with CA Students',
            'freelancing career' => 'CA articleship journey',
            'Freelance Work' => 'Articleship work',
            'freelance work' => 'articleship work',
            'Search jobs, firms, or skills' => 'Search opportunities, firms, or skills',
            'Olance' => 'Article Connect',
            'Is there bidding on stipend?' => 'Is there auction-style competition on stipend?',
            'How to bid for find work?' => 'How do I apply for opportunities?',
            'We host top-rated freelancers who are experts in their fields.' => 'We host strong CA Student profiles with clear skills and exam progress.',
            'Freelancers can bid on jobs.' => 'CA Students apply to listings.',
            'submit proposals' => 'submit applications',
            'Submit tailored proposals' => 'Submit tailored applications',
            'write a proposal' => 'write an application note',
            'write a Proposal' => 'write an application',
        ];

        uksort($phrases, fn ($a, $b) => strlen($b) <=> strlen($a));
        foreach ($phrases as $from => $to) {
            $s = str_replace($from, $to, $s);
        }

        $s = preg_replace('/\bfreelancing\b/i', 'articleship', $s) ?? $s;
        $s = preg_replace('/\bfreelancers\b/i', 'CA Students', $s) ?? $s;
        $s = preg_replace('/\bfreelancer\b/i', 'CA Student', $s) ?? $s;
        $s = preg_replace('/\bgigs\b/i', 'opportunities', $s) ?? $s;

        foreach ([
            ' with buyers' => ' with CA Firms',
            ' to buyers' => ' to CA Firms',
            ' and buyers' => ' and CA Firms',
            'for buyers' => 'for CA Firms',
            ' from buyers' => ' from CA Firms',
        ] as $from => $to) {
            $s = str_ireplace($from, $to, $s);
        }

        return $s;
    }

    public function down(): void
    {
    }
};
