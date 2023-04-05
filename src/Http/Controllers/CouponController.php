<?php

namespace Local\CMS\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Local\Ecommerce\Models\Coupon;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Coupon::class);
    }

    public function index()
    {
        $coupons = Coupon::all();
        return view('modules::coupons.index', compact('coupons'));
    }

    public function edit(Coupon $coupon)
    {
        return view('modules::coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        

        $this->validate($request, [
            'name' => "required",
            'code' => "required|unique:coupons,code," . $coupon->id . ",id,deleted_at,NULL",
            'discount_type' => "required",
            'discount' => "required|numeric",
            'min_spend' => "required|min:0",
            'max_discount' => "required_if:discount_type,Percent",
            'start_date' => "required|date_format:d/m/Y",
            'end_date' => "required|after:start_date|date_format:d/m/Y",
            'usage_limit_per_coupon' => "required",
            'usage_limit_per_customer' => "required",
            'active' => "required",
        ]);

        $request['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'));
        $request['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'));

        $coupon->update($request->all());
        // $coupon->update(['max_spend'])
        return redirect()->route('admin.coupons.index')->withSuccess('Coupon updated.');
    }

    public function create()
    {
        return view('modules::coupons.create');
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => "required",
            'code' => "required|unique:coupons,code,NULL,id,deleted_at,NULL",
            'discount_type' => "required",
            'discount' => "required|numeric",
            'min_spend' => "required|min:0",
            'max_discount' => "required_if:discount_type,Percent",
            'start_date' => "required|date_format:d/m/Y",
            'end_date' => "required|after:start_date|date_format:d/m/Y",
            'usage_limit_per_coupon' => "required",
            'usage_limit_per_customer' => "required",
            'active' => "required",
        ]);

        
        $request['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'));
        $request['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'));

        Coupon::create($request->all());
        return redirect()->route('admin.coupons.index')->withSuccess('Coupon created.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->withSuccess('Coupon deleted.');
    }
}
