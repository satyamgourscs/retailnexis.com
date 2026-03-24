<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\Hero;
use App\Models\Language;
use DB;
use App\Traits\CacheForget;

class HeroController extends Controller
{
    use CacheForget;

    public function index()
    {
        $heroes = DB::table('heroes')->get()->keyBy('lang_id');
        $language_all = Language::where('is_active', true)->orderByDesc('is_default')->get();
        return view('landlord.hero', compact('heroes', 'language_all'));
    }

    public function store(Request $request)
    {
        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        
        $heroes = DB::table('heroes')->get()->keyBy('lang_id');
        $language_all = Language::where('is_active', true)->orderByDesc('is_default')->get();

        $imageRequired = !$heroes->first()?->image && !$request->hasFile('image');

        // Validate image if required
        $request->validate([
            'image' => $imageRequired ? 'required|image|mimes:jpg,jpeg,png,gif|max:100000' : 'nullable|image|mimes:jpg,jpeg,png,gif|max:100000',
        ]);

        $anyFilled = false;

        foreach ($request->lang_ids as $index => $langId) {
            $heading     = trim($request->heading[$index] ?? '');
            $sub_heading  = trim($request->sub_heading[$index] ?? '');
            $button_text  = trim($request->button_text[$index] ?? '');

            $filled = $heading || $sub_heading || $button_text;

            // If any field is filled, all must be filled
            if ($filled) {
                $anyFilled = true;

                if (!$heading || !$sub_heading || !$button_text) {
                    $languageName = $language_all->firstWhere('id', $langId)?->name;
                    return back()->with(
                        'not_permitted', "All fields are required for language: $languageName"
                    );
                }
            }
        }

        if (!$anyFilled) {
            return back()->with(
                'not_permitted', 'Please fill out at least one language tab completely.'
            );
        }


        // Handle image upload
        $imageName = $heroes->first()?->image;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $ext = $image->getClientOriginalExtension();
            $imageName = now()->format('YmdHis') . '.' . $ext;
            $image->move(public_path('landlord/images/'), $imageName);
        }

        // Save entries
        foreach ($request->lang_ids as $index => $langId) {
            $heading    = trim($request->heading[$index] ?? '');
            $sub_heading = trim($request->sub_heading[$index] ?? '');
            $button_text = trim($request->button_text[$index] ?? '');

            if (!($heading || $sub_heading || $button_text)) continue;

            $hero = Hero::firstOrNew(['lang_id' => $langId]);
            $hero->heading     = $heading;
            $hero->sub_heading  = $sub_heading;
            $hero->button_text = $button_text;
            $hero->image       = $imageName;
            $hero->save();
        }

        $this->cacheForget('hero');

        return back()->with('message', 'Data updated successfully');
    }
}
