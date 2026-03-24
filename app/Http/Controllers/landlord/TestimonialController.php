<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\landlord\Testimonial;
use App\Traits\CacheForget;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TestimonialController extends Controller
{
    use CacheForget;

    public function index()
    {
        $testimonials = DB::table('testimonials')->orderBy('order', 'asc')->get();
        return view('landlord.testimonial', compact('testimonials'));
    }

    public function store(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $this->validate($request, [
            'image.*' => 'image|mimes:jpg,jpeg,png|max:100000',
        ]);

        foreach ($request->name as $key => $name) {
            $data = array(
                'name'=>$request->name[$key],
                'business_name'=>$request->business_name[$key],
                'text'=>$request->text[$key],
                'order'=> $key+1

            );

            if($request->image[$key]){
                $image = $request->image[$key];
                $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
                $imageName = date("Ymdhis") . '.' . strtolower($ext);
                $image->move(public_path('landlord/images/testimonial'), $imageName);

                $manager = new ImageManager(Driver::class);
                $image = $manager->read(public_path('landlord/images/testimonial/'). $imageName);
                $image->cover(100, 100)->save(public_path('landlord/images/testimonial/'). $imageName, 100);

                $data['image'] = $imageName;
            }

            Testimonial::create($data);
        }

        $this->cacheForget('testimonials');
        return redirect()->back()->with('message', 'Data inserted successfully');
    }

    public function update(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $input = array(
            'name'            => $request->name,
            'business_name'   => $request->business_name,
            'text'            => $request->text,
            'testimonial_id'  => $request->testimonial_id
        );
        Testimonial::find($input['testimonial_id'])->update($input);
        $this->cacheForget('testimonials');
        return redirect()->back()->with('message', 'Data updated successfully');
    }

    public function sort(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $testimonials = Testimonial::all();

        foreach ($testimonials as $testimonial) {
            $testimonial->timestamps = false; // To disable update_at field updation
            foreach ($request->order as $order) {
                if ($order['id'] == $testimonial->id) {
                    $testimonial->update(['order' => $order['position']]);
                }
            }
        }
        return 'Order changed successfully';
    }

    public function delete($id){
        $testimonial = Testimonial::find($id);
        $testimonial->delete();
        $this->cacheForget('testimonials');
        return redirect()->back()->with('message', 'Data deleted successfully');
    }
}
