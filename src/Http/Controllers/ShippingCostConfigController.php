<?php

namespace Local\CMS\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use Local\Ecommerce\Models\Coupon;

class ShippingCostConfigController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Config::class);
    }

    public function index()
    {
        $east_shipping_cost = Config::firstOrCreate(['key' => 'east_shipping_cost']);
        $west_shipping_cost = Config::firstOrCreate(['key' => 'west_shipping_cost']);
        $international_shipping_cost = Config::firstOrCreate(['key' => 'international_shipping_cost']);

        return view('modules::shipping_cost_configs.index', compact('east_shipping_cost', 'west_shipping_cost', 'international_shipping_cost'));
    }

    public function store(Request $request)
    {

        $east_shipping_cost = Config::firstOrCreate(['key' => 'east_shipping_cost']);
        $east_shipping_cost->value = $request->get('east_shipping_cost');
        $east_shipping_cost->save();

        $west_shipping_cost = Config::firstOrCreate(['key' => 'west_shipping_cost']);
        $west_shipping_cost->value = $request->get('west_shipping_cost');
        $west_shipping_cost->save();

        $international_shipping_cost = Config::firstOrCreate(['key' => 'international_shipping_cost']);
        $international_shipping_cost->value = $request->get('international_shipping_cost');
        $international_shipping_cost->save();

        return redirect()->route('admin.shipping_cost_configs.index')->withSuccess('Shipping Cost updated.');
    }
}
