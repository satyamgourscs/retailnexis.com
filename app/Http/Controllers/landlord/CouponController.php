<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Traits\CacheForget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Keygen\Keygen;

class CouponController extends Controller
{
    use CacheForget;

    public function index()
    {
        $lims_coupon_all = Coupon::where('is_active', true)->orderBy('id', 'desc')->get();
        return view('landlord.coupon.index', compact('lims_coupon_all'));
    }

    public function create()
    {
        //
    }

    public function generateCode()
    {
        $id = Keygen::alphanum(10)->generate();
        return $id;
    }

    public function store(Request $request)
    {
        if (!config('app.demo_unlocked'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        
        $data = $request->all();
        $data['used'] = 0;
        $data['user_id'] = Auth::id();
        $data['is_active'] = true;
        Coupon::create($data);
        $this->cacheForget('coupon_list');
        return redirect()->route('coupon.index')->with('message', 'Coupon created successfully');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $data = $request->all();
        $lims_coupon_data = Coupon::find($data['coupon_id']);
        $lims_coupon_data->update($data);

        $this->cacheForget('coupon_list');
        return redirect()->route('coupon.index')->with('message', 'Coupon updated successfully');
    }

    public function destroy(string $id)
    {
        $lims_coupon_data = Coupon::find($id);
        $lims_coupon_data->is_active = false;
        $lims_coupon_data->save();
        $this->cacheForget('coupon_list');
        return redirect()->route('coupon.index')->with('not_permitted', 'Coupon deleted successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $coupon_id = $request['couponIdArray'];
        foreach ($coupon_id as $id) {
            $lims_coupon_data = Coupon::find($id);
            $lims_coupon_data->is_active = false;
            $lims_coupon_data->save();
        }
        $this->cacheForget('coupon_list');
        return 'Coupon deleted successfully!';
    }
}
