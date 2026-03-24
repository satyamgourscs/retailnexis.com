<?php

namespace App\Http\Controllers\landlord;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\landlord\Language;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    use \App\Traits\CacheForget;

    protected string $langPath;

    public function __construct()
    {
        $this->langPath = resource_path('lang');
    }

    public function index()
    {
        $lims_language_all = Language::all();
        return view('landlord.language.index', compact('lims_language_all'));
    }

    public function store(Request $request)
    {
        if (!config('app.user_verified'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        $request->validate([
            'code' => 'required|string|max:10|unique:languages,code',
            'name' => 'required|string|max:100|unique:languages,name',
        ]);

        $data = $request->only(['code', 'name', 'is_default']);

        $targetFile = "{$this->langPath}/{$data['code']}.php";
        $masterFile = "{$this->langPath}/master.php";

        if (!copy($masterFile, $targetFile)) {
            return redirect()->back()->with('not_permitted',  __('db.failed_create_lang_file'));
        }

        if (isset($request->is_default)) {
            $data['is_default'] = true;
        } else {
            $data['is_default'] = false;
        }

        $language = new Language();
        $language->forceFill($data)->save();
        $this->cacheForget('languages');
        return redirect()->back()->with('message', 'Language created successfully');
    }

    public function update(Request $request)
    {
        if (!config('app.user_verified'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        $request->validate([
            'name' => 'required|string|max:100|unique:languages,name,' . $request->language_id,
        ]);

        $data = $request->only(['name', 'is_default']);

        $language = Language::find($request->language_id);

        if (isset($request->is_default)) {
            $data['is_default'] = true;

            $defaultLang = Language::where('is_default', true)->first();
            if ($defaultLang) {
                $defaultLang->update(['is_default' => false]);
            }
            cache()->forget('hero');
            cache()->forget('module_descriptions');
            cache()->forget('faq_descriptions');
            cache()->forget('tenant_signup_descriptions');
        } else {
            $data['is_default'] = false;
        }

        $language->forceFill($data)->save();
        $this->cacheForget('languages');
        return redirect()->back()->with('message', __('db.lang_updated'));
    }

    public function destroy($id)
    {
        $language = Language::findOrFail($id);

        // Determine file path
        $filePath = "{$this->langPath}/{$language->code}.php";

        // Delete the language file first
        if (file_exists($filePath)) {
            try {
                unlink($filePath); // delete the file
            } catch (\Exception $e) {
                return redirect()->back()->with('not_permitted', 'Failed to delete language file: ' . $e->getMessage());
            }
        }

        // Delete the database row
        $language->delete();

        // Clear caches
        cache()->forget('hero');
        cache()->forget('module_descriptions');
        cache()->forget('faq_descriptions');
        cache()->forget('tenant_signup_descriptions');
        $this->cacheForget('languages');

        return redirect()->back()->with('not_permitted', 'Language deleted successfully');
    }

    /**
     * Show translation edit page for a language
     */
    public function editTranslation(string $langCode)
    {
        // Sync only the selected language file
        $this->syncLanguage($langCode);

        $file = "{$this->langPath}/{$langCode}.php";
        if (!file_exists($file)) {
            $targetFile = "{$this->langPath}/{$langCode}.php";
            $masterFile = "{$this->langPath}/master.php";

            if (!copy($masterFile, $targetFile)) {
                return redirect()->back()->with('not_permitted',  __('db.failed_create_lang_file'));
            }
        }

        $data = include $file;
        $translations = $data['db'] ?? [];

        return view('landlord.language.translation', compact('langCode', 'translations'));
    }

    /**
     * Sync new keys from master.php to all other language files
     */
    protected function syncLanguage(string $langCode)
    {
        $masterFile = "{$this->langPath}/master.php";
        $targetFile = "{$this->langPath}/{$langCode}.php";

        if (!file_exists($masterFile) || !file_exists($targetFile)) {
            return;
        }

        $master = include $masterFile;
        $masterDb = $master['db'] ?? [];

        $current = include $targetFile;
        $currentDb = $current['db'] ?? [];

        $newKeysAdded = false;

        // Add missing keys from master.php into the target file
        foreach ($masterDb as $key => $value) {
            if (!array_key_exists($key, $currentDb)) {
                $currentDb[$key] = $value;
                $newKeysAdded = true;
            }
        }

        // Only write if there were new keys
        if ($newKeysAdded) {
            $current['db'] = $currentDb;
            $content = "<?php\n\nreturn " . var_export($current, true) . ";\n";
            file_put_contents($targetFile, $content);
        }
    }


    public function updateTranslation(Request $request, string $langCode)
    {
        $file = resource_path("lang/{$langCode}.php");

        if (!file_exists($file)) {
            return redirect()->back()->with('error', __('db.language_data_not_found'));
        }

        $existing = include($file);

        // Get all translations from form
        $submitted = $request->input('translations', []);

        // Replace all existing keys with submitted values
        $existing['db'] = $submitted;

        // Save file
        $content = "<?php\n\nreturn " . var_export($existing, true) . ";\n";
        file_put_contents($file, $content);

        return redirect()->back()->with('message', __('db.lang_updated'));
    }
}
