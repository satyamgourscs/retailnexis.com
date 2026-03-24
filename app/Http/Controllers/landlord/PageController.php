<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use App\Models\landlord\Page;
use Cache;
use App\Traits\CacheForget;
use Intervention\Image\Facades\Image;

class PageController extends Controller
{
    use CacheForget; 

    public function index()
    {
        $pages = DB::table('pages')->get();
        return view('landlord.page.index', compact('pages'));
    }

    public function details($slug)
    {
        if(cache()->has('general_settings')){
            $general_setting = cache()->get('general_settings');
        } else {
            $general_setting =  Cache::remember('general_setting', 60*60*24*365, function () {
                return DB::table('general_settings')->latest()->first();
            });
        }
        if(cache()->has('socials')){
            $socials = cache()->get('socials');
        }
        else {
            $socials =  Cache::remember('socials', 60*60*24*365, function () {
                return DB::table('socials')->get();
            });
        }
        if(cache()->has('pages')){
            $pages = cache()->get('pages');
        }
        else {
            $pages =  Cache::remember('pages', 60*60*24*365, function () {
                return DB::table('pages')->get();
            });
        }
        $page = DB::table('pages')->where('slug',$slug)->first();
        $languages =  Cache::remember('languages', 60*60*24*30, function () {
            return DB::table('languages')->where('is_active', true)->get();
        });
        return view('landlord.page-details', compact('page','general_setting','socials', 'pages', 'languages'));
    }

    public function store(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        $this->validate($request, [
            'featured_image' => 'image|mimes:jpg,jpeg,png|max:100000',
        ]);

        $data = array(
            'title'=> $request->title,
            'slug'=> Str::slug($request->title, '-'),
            'text'=> $request->text,
            'meta_title'=> $request->meta_title,
            'meta_description'=> $request->meta_description,
        );
        Page::create($data);
        $this->cacheForget('pages');
        return redirect()->back()->with('message', 'Data inserted successfully');
    }

    public function edit($id){
        $page = Page::find($id);
        
        return response()->json($page);
    }

    public function update(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $input = $request->all();
        Page::find($input['page_id'])->update($input);
        $this->cacheForget('pages');
        return redirect()->back()->with('message', 'Data updated successfully');
    }

    public function sort(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $pages = Page::all();
        foreach ($pages as $page) {
            $page->timestamps = false; // To disable update_at field updation
            foreach ($request->order as $order) {
                if ($order['id'] == $page->id) {
                    $page->update(['order' => $order['position']]);
                }
            }
        }
        $this->cacheForget('pages');
        return 'Order changed successfully';
    }

    public function delete($id)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $page = Page::find($id);
        $page->delete();
        $this->cacheForget('pages');
        return redirect()->back()->with('message', 'Data deleted successfully');
    }
}
