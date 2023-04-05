<?php

namespace Local\CMS\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Libraries\AjaxDatatable;
use App\Mail\OrderEmail;
use Carbon\Carbon;
use App\Models\Country;
use Exception;
use Illuminate\Http\Request;
use Local\Ecommerce\Models\Category;
use Local\Ecommerce\Models\Order;
use Log;
use Mail;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Order::class);
    }

    public function index()
    {
        // dd('order');
        $hiddenFields = [
            'Billing Address' => 'formatted_billing_address',
            'Shipping Address' => 'formatted_shipping_address',
        ];

        $orders = Order::whereHas('transaction', function ($query) {
            $query->where('payment_status', 1);
        })
        ->latest()
        ->get();
        // dd($orders);
        return view('modules::orders.index', compact('orders','hiddenFields'));
    }

    public function show(Order $order){
        $status_options = Order::STATUS_OPTIONS;
        $countries = Country::orderBy('display_name', 'asc')->get()->map(function ($country) {
            return ['id' => $country->phone_code, 'name' => "+" . $country->phone_code . " (" . $country->display_name . ")"];
        })->pluck('name', 'id');

        return view('modules::orders.show', compact('order', 'status_options', 'countries'));
    }

    public function edit(Order $order)
    {
        $status_options = Order::STATUS_OPTIONS;
        $countries = Country::orderBy('display_name', 'asc')->get()->map(function ($country) {
            return ['id' => $country->phone_code, 'name' => "+" . $country->phone_code . " (" . $country->display_name . ")"];
        })->pluck('name', 'id');

        return view('modules::orders.edit', compact('order', 'status_options', 'countries'));
    }

    public function update(Request $request, Order $order)
    {
        $this->validate($request, [
            'billing_country' => 'required|string|max:190',
            'billing_city' => 'required|string|max:190',
            'billing_street_address1' => 'required|string|max:190',
            'billing_street_address2' => 'nullable|string|max:190',
            'billing_phone_code' => 'required|string|max:190',
            'billing_phone' => 'required|string|max:190',
            'billing_name' => 'required|string|max:190',
            'billing_postcode' => 'required|string|max:190',
            'billing_email' => 'required|email|max:190',
            'billing_state' => 'required|string|max:190',

            'shipping_country' => 'required_if:shipping_method,Standard Shipping|string|max:190',
            'shipping_city' => 'required_if:shipping_method,Standard Shipping|string|max:190',
            'shipping_street_address1' => 'required_if:shipping_method,Standard Shipping|string|max:190',
            'shipping_street_address2' => 'required_if:shipping_method,Standard Shipping|string|max:190',
            'shipping_phone_code' => 'required_if:shipping_method,Standard Shipping|string|max:190',
            'shipping_phone' => 'required_if:shipping_method,Standard Shipping|string|max:190',
            'shipping_name' => 'required_if:shipping_method,Standard Shipping|string|max:190',
            'shipping_postcode' => 'required_if:shipping_method,Standard Shipping|string|max:190',
            'shipping_email' => 'required_if:shipping_method,Standard Shipping|email|max:190',
            'shipping_state' => 'required_if:shipping_method,Standard Shipping|string|max:190',


            'collector_name' => 'required_if:shipping_method,Self Collect|string|max:190',
            'collector_phone' => 'required_if:shipping_method,Self Collect|string|max:190',
            'collector_email' => 'required_if:shipping_method,Self Collect|email|max:190',
        ]);

        $order->update(
            $request->except(['_token', '_method', 'shipping_method'])
        );



        return back()->withSuccess('Billing and shipping address updated.');
    }

    public function sentEmail($id)
    {
        $order = Order::find($id);
        try {
            Mail::send(new OrderEmail($order));
        } catch (Exception $e) {
            Log::info(json_encode($e->getMessage()));
        }

        return response()->json('Successfully sent');
    }

    public function changeStatus(Request $request)
    {
        $order = Order::find($request->get('order_id'));
        if ($request->get('status') == Order::COMPLETED) {
            $order->completed_at = Carbon::now();
        }

        if ($request->get('status') == Order::SHIPPING) {
            $order->shipping_at = Carbon::now();
        }

        if($request->get('status') == Order::CANCELED) {
            $order->cancelled_at = Carbon::now();
        }

        $order->status = $request->get('status');
        $order->save();

        try {
            Mail::send(new OrderEmail($order));
        } catch (Exception $e) {
            Log::info(json_encode($e->getMessage()));
        }

        return response()->json('Successfully changed');
    }

    public function changeTrackingInfo(Request $request, Order $order)
    {

        $order->tracking_no = $request->get('tracking_no');
        $order->tracking_url = $request->get('tracking_url');
        $order->status = Order::SHIPPING;
        $order->shipping_at = Carbon::now();
        $order->save();

        try {
            Mail::send(new OrderEmail($order));
        } catch (Exception $e) {
            Log::info(json_encode($e->getMessage()));
        }

        return redirect()->back()->withSuccess('Tracking info updated.');
    }

    public function downloadReceipt($id)
    {
        $order = Order::find($id);
        return view('design/myaccount/download_receipt', compact('order'));
    }

    public function datatable(Request $request)
    {
        $ajaxDt = new AjaxDatatable($request,new Order);
        $ajaxDt->setColumn([
            "order_no",
            "customer_name",
            "customer_email",
            "pickup_type",
            "status",
            "total",
            "created_at",
        ]);
        $result = $ajaxDt->getDatatableRequest();

        return $this->__apiDataTable(
            (OrderResource::collection($result['records'])),
            $result['totalRecords'],
            $result["draw"],
            $result['totalFilteredRecords'],
            [
                'totalSales' => $result['rawQuery']->get()->sum('total')
            ]
        );


    }


}
