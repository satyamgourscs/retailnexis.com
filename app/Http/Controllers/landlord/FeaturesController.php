<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Language;
use App\Models\landlord\Features;
use App\Traits\CacheForget;

class FeaturesController extends Controller
{
    use CacheForget;

    public function index()
    {
        $features = DB::table('features')->orderBy('order', 'asc')->get();
        $grouped = $features->groupBy(fn($item) => $item->icon . '-' . $item->order);

        // For each group, key translations by lang_id
        $features = $grouped->map(function ($group) {
            return $group->keyBy('lang_id');
        });

        $language_all = Language::where('is_active', true)->orderByDesc('is_default')->get();
        return view('landlord.feature', compact('features', 'language_all'));
    }

    public function store(Request $request)
    {
        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        
        $language_all = Language::where('is_active', true)->orderByDesc('is_default')->get();
        $features = $request->input('features', []);

        $errors = [];

        // ✅ Validate features
        $validFeatureFound = false;

        foreach ($features as $index => $feature) {
            $featureNumber = is_numeric($index) ? ((int)$index + 1) : $index;
            $icon = trim($feature['icon'] ?? '');
            if (!$icon) {
                $errors["features.$index.icon"] = "Feature #$featureNumber must have an icon.";
                continue;
            }

            $translations = $feature['translations'] ?? [];

            foreach ($translations as $langId => $trans) {
                $languageName = $language_all->firstWhere('id', $langId)?->name;
                $name = trim($trans['name'] ?? '');

                if ($name) {
                    if (!$name) {
                        $errors["features.$index.translations.$langId"] = "Feature #$featureNumber translation for language $languageName must include name.";
                    } else {
                        $validFeatureFound = true;
                    }
                }
            }
        }

        if (!$validFeatureFound) {
            $errors['features'] = "At least one feature must include icon and one complete language translation.";
        }

        // ❌ Return if any error found
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // ✅ Save if valid
        DB::table('features')->truncate();
        $this->cacheForget('features');

        foreach ($features as $feature) {
            foreach ($feature['translations'] as $langId => $trans) {
                if ($trans['name']) {
                    DB::table('features')->insert([
                        'icon' => $feature['icon'],
                        'lang_id' => $langId,
                        'name' => $trans['name'],
                    ]);
                }
            }
        }

        return redirect()->back()->with('message', 'Data inserted successfully');
    }

}
