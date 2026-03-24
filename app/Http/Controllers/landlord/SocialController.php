<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\landlord\Social;
use App\Traits\CacheForget;

class SocialController extends Controller
{
    use CacheForget; 

    public function index()
    {
        $socials = DB::table('socials')->get();
        return view('landlord.social', compact('socials'));
    }

    public function store(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        foreach ($request->name as $key => $name) {
            $data = array(
                 'icon'=>$request->icon[$key],
                 'name'=>$request->name[$key],
                 'link'=>$request->link[$key],
                 'order'=> $key+1
            );

            Social::create($data);
        }
        $this->cacheForget('social');
        return redirect()->back()->with('message', 'Data inserted successfully');
    }

    public function update(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $input = array(
            'icon'          => $request->icon,
            'name'          => $request->name,
            'link'          => $request->link,
            'social_id'     => $request->social_id
        );
        Social::find($input['social_id'])->update($input);
        $this->cacheForget('social');
        return redirect()->back()->with('message', 'Data updated successfully');
    }

    public function sort(Request $request)
    {
        $socials = Social::all();
        foreach ($socials as $social) {
            $social->timestamps = false; // To disable update_at field updation
            foreach ($request->order as $order) {
                if ($order['id'] == $social->id) {
                    $social->update(['order' => $order['position']]);
                }
            }
        }
        $this->cacheForget('social');
        return 'Order changed successfully';
    }

    public function delete($id)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $social = Social::find($id);
        $social->delete();
        $this->cacheForget('social');
        return redirect()->back()->with('message', 'Data deleted successfully');
    }
}
