<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\landlord\Faq;
use App\Models\landlord\FaqDescription;
use App\Models\Language;
use App\Traits\CacheForget;

class FaqController extends Controller
{
    use CacheForget;

    public function index()
    {
        $faqs = DB::table('faqs')->orderBy('order', 'asc')->get();
        $grouped = $faqs->groupBy(fn($item) => $item->faq_group_id . '-' . $item->order);

        // For each group, key translations by lang_id
        $faqs = $grouped->map(function ($group) {
            return $group->keyBy('lang_id');
        });

        $faq_description = DB::table('faq_descriptions')->get()->keyBy('lang_id');
        $language_all = Language::where('is_active', true)->orderByDesc('is_default')->get();
        return view('landlord.faq', compact('faqs', 'faq_description', 'language_all'));
    }

    public function store(Request $request)
    {
        if (!config('app.user_verified'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        
        $language_all = Language::where('is_active', true)->orderByDesc('is_default')->get();
        $descriptions = $request->input('descriptions', []);
        $faqs = $request->input('faqs', []);

        $errors = [];

        // ✅ Validate faq_descriptions (at least one complete heading+sub_heading)
        $validDescriptionFound = false;

        foreach ($descriptions as $langId => $desc) {
            $languageName = $language_all->firstWhere('id', $langId)?->name;
            $heading = trim($desc['heading'] ?? '');
            $subHeading = trim($desc['sub_heading'] ?? '');

            // If either one is filled but the other is empty → error
            if ($heading || $subHeading) {
                if (!$heading || !$subHeading) {
                    $errors["descriptions.$langId"] = "Please complete both Heading and Sub Heading for language ID $languageName.";
                } else {
                    $validDescriptionFound = true;
                }
            }
        }

        if (!$validDescriptionFound) {
            $errors['descriptions'] = "At least one language must have both Heading and Sub Heading.";
        }

        // ✅ Validate faqs
        $validFaqFound = false;

        foreach ($faqs as $index => $faq) {
            $faqNumber = is_numeric($index) ? ((int)$index + 1) : $index;

            $translations = $faq['translations'] ?? [];

            foreach ($translations as $langId => $trans) {
                $languageName = $language_all->firstWhere('id', $langId)?->name;
                $question = trim($trans['question'] ?? '');
                $answer = trim($trans['answer'] ?? '');

                if ($question || $answer) {
                    if (!$question || !$answer) {
                        $errors["faqs.$index.translations.$langId"] = "Faq #$faqNumber translation for language $languageName must include both question and answer.";
                    } else {
                        $validFaqFound = true;
                    }
                }
            }
        }

        if (!$validFaqFound) {
            $errors['faqs'] = "At least one faq must include one complete language translation.";
        }

        // ❌ Return if any error found
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // ✅ Save if valid
        DB::table('faq_descriptions')->truncate();
        DB::table('faqs')->truncate();
        $this->cacheForget('faq_descriptions');
        $this->cacheForget('faqs');

        foreach ($descriptions as $langId => $desc) {
            if ($desc['heading'] && $desc['sub_heading']) {
                DB::table('faq_descriptions')->insert([
                    'lang_id' => $langId,
                    'heading' => $desc['heading'],
                    'sub_heading' => $desc['sub_heading'],
                ]);
            }
        }

        $faq_group_id = 1;
        foreach ($faqs as $index => $faq) {
            foreach ($faq['translations'] as $langId => $trans) {
                if ($trans['question'] && $trans['answer']) {
                    DB::table('faqs')->insert([
                        'lang_id' => $langId,
                        'faq_group_id' => $faq_group_id,
                        'question' => $trans['question'],
                        'answer' => $trans['answer'],
                    ]);
                }
            }
            $faq_group_id++;
        }

        return redirect()->back()->with('message', 'Data inserted successfully');
    }

}
