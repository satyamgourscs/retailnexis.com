<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\landlord\Module;
use App\Models\Language;
use App\Traits\CacheForget;

class ModuleController extends Controller
{
    use CacheForget;

    public function index()
    {
        $modules = DB::table('modules')->orderBy('order', 'asc')->get();
        $grouped = $modules->groupBy(fn($item) => $item->icon . '-' . $item->order);

        // For each group, key translations by lang_id
        $modules = $grouped->map(function ($group) {
            return $group->keyBy('lang_id');
        });

        $module_description = DB::table('module_descriptions')->get()->keyBy('lang_id');
        $language_all = Language::where('is_active', true)->orderByDesc('is_default')->get();
        return view('landlord.module', compact('modules', 'module_description', 'language_all'));
    }

    public function store(Request $request)
    {
        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        
        $language_all = Language::where('is_active', true)->orderByDesc('is_default')->get();
        $descriptions = $request->input('descriptions', []);
        $modules = $request->input('modules', []);

        $errors = [];

        // ✅ Validate module_descriptions (at least one complete heading+sub_heading)
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

        // ✅ Validate modules
        $validModuleFound = false;

        foreach ($modules as $index => $module) {
            $moduleNumber = is_numeric($index) ? ((int)$index + 1) : $index;
            $icon = trim($module['icon'] ?? '');
            if (!$icon) {
                $errors["modules.$index.icon"] = "Module #$moduleNumber must have an icon.";
                continue;
            }

            $translations = $module['translations'] ?? [];

            foreach ($translations as $langId => $trans) {
                $languageName = $language_all->firstWhere('id', $langId)?->name;
                $name = trim($trans['name'] ?? '');
                $desc = trim($trans['description'] ?? '');

                if ($name || $desc) {
                    if (!$name || !$desc) {
                        $errors["modules.$index.translations.$langId"] = "Module #$moduleNumber translation for language $languageName must include both name and description.";
                    } else {
                        $validModuleFound = true;
                    }
                }
            }
        }

        if (!$validModuleFound) {
            $errors['modules'] = "At least one module must include icon and one complete language translation.";
        }

        // ❌ Return if any error found
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // ✅ Save if valid
        DB::table('module_descriptions')->truncate();
        DB::table('modules')->truncate();
        $this->cacheForget('module_descriptions');
        $this->cacheForget('modules');

        foreach ($descriptions as $langId => $desc) {
            if ($desc['heading'] && $desc['sub_heading']) {
                DB::table('module_descriptions')->insert([
                    'lang_id' => $langId,
                    'heading' => $desc['heading'],
                    'sub_heading' => $desc['sub_heading'],
                ]);
            }
        }

        foreach ($modules as $module) {
            foreach ($module['translations'] as $langId => $trans) {
                if ($trans['name'] && $trans['description']) {
                    DB::table('modules')->insert([
                        'icon' => $module['icon'],
                        'lang_id' => $langId,
                        'name' => $trans['name'],
                        'description' => $trans['description'],
                    ]);
                }
            }
        }

        return redirect()->back()->with('message', 'Data inserted successfully');
    }

}
