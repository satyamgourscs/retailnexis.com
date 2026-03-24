<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LandingFaqTopupSeeder extends Seeder
{
    /**
     * Ensure at least 6 FAQ items exist for landing page rendering.
     * Adds only missing records through the same dynamic DB source.
     */
    public function run()
    {
        $faqCount = DB::table('faqs')->count();
        if ($faqCount >= 6) {
            return;
        }

        $maxOrder = DB::table('faqs')->max('order');
        $nextOrder = is_null($maxOrder) ? 1 : ((int) $maxOrder + 1);

        $candidates = [
            [
                'question' => 'Can I access my data from multiple locations?',
                'answer' => 'Yes. Since the system is cloud-based, you can securely access your dashboard from anywhere with proper login credentials.',
            ],
            [
                'question' => 'Is there a free trial before subscribing?',
                'answer' => 'Yes. You can start with a free trial period (if enabled by admin) and then upgrade to a suitable subscription plan.',
            ],
            [
                'question' => 'Can I manage multiple warehouses in one account?',
                'answer' => 'Yes. The platform supports multi-warehouse operations including stock transfers, reporting, and role-based access control.',
            ],
            [
                'question' => 'Do you provide regular updates and support?',
                'answer' => 'Yes. We continuously improve the product and provide support based on your selected package and service scope.',
            ],
        ];

        foreach ($candidates as $candidate) {
            if ($faqCount >= 6) {
                break;
            }

            $exists = DB::table('faqs')->where('question', $candidate['question'])->exists();
            if ($exists) {
                continue;
            }

            DB::table('faqs')->insert([
                'question' => $candidate['question'],
                'answer' => $candidate['answer'],
                'order' => $nextOrder++,
                'lang_id' => 1,
                'faq_group_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $faqCount++;
        }
    }
}

