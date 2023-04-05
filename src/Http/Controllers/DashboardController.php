<?php

namespace Local\CMS\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Local\CMS\Traits\Helpers;
use Local\Ecommerce\Models\Order;

class DashboardController extends Controller
{
    use Helpers;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(session()->has('statistic_filter'))
        {
            $request = json_decode(session('statistic_filter'));
        }

        $statisticType = isset($request->statistics_type) ? $request->statistics_type : 'overall';

        $orders =  Order::whereHas('transaction', function ($query) {
                $query->where('payment_status', 1);
            })
            ->when($statisticType !== 'overall', fn($query) =>
                $query
                    ->where(function($query) use ( $statisticType ) {
                        if($statisticType == 'daily'){
                            $query->whereDate('created_at',date('Y-m-d'));
                        }else if ($statisticType == 'weekly'){
                            $query->whereBetween('created_at',[
                                Carbon::parse('last monday')->startOfDay(),
                                Carbon::parse('next friday')->endOfDay(),
                            ]);
                        }else if ($statisticType == 'monthly'){
                            $query->whereMonth('created_at',date('m'));
                        }else if ($statisticType == 'yearly'){
                            $query->whereYear('created_at',date('Y'));
                        }
                    })
            )
            ->withSum('transaction','amount')
            ->get();

        $bookings = Booking::with(['transaction'])
            ->whereHas('transaction', function ($query) {
                $query->where('payment_status', 1);
            })
            ->where(function($query) use ( $statisticType ) {
                if($statisticType == 'daily'){
                    $query->whereDate('created_at',date('Y-m-d'));
                }else if ($statisticType == 'weekly'){
                    $query->whereBetween('created_at',[
                        Carbon::parse('last monday')->startOfDay(),
                        Carbon::parse('next friday')->endOfDay(),
                    ]);
                }else if ($statisticType == 'monthly'){
                    $query->whereMonth('created_at',date('m'));
                }else if ($statisticType == 'yearly'){
                    $query->whereYear('created_at',date('Y'));
                }
            })
            ->where('type',1)
            ->withSum('transaction','amount');

        $tourbookings = Booking::with(['transaction'])
            ->whereHas('transaction', function ($query) {
                $query->where('payment_status', 1);
            })
            ->where(function($query) use ( $statisticType ) {
                if($statisticType == 'daily'){
                    $query->whereDate('created_at',date('Y-m-d'));
                }else if ($statisticType == 'weekly'){
                    $query->whereBetween('created_at',[
                        Carbon::parse('last monday')->startOfDay(),
                        Carbon::parse('next friday')->endOfDay(),
                    ]);
                }else if ($statisticType == 'monthly'){
                    $query->whereMonth('created_at',date('m'));
                }else if ($statisticType == 'yearly'){
                    $query->whereYear('created_at',date('Y'));
                }
            })
            ->where('type',2)
            ->withSum('transaction','amount');

        $users = User::where(function($query) use ( $statisticType ) {
            if($statisticType == 'daily'){
                $query->whereDate('created_at',date('Y-m-d'));
            }else if ($statisticType == 'weekly'){
                $query->whereBetween('created_at',[
                    Carbon::parse('last monday')->startOfDay(),
                    Carbon::parse('next friday')->endOfDay(),
                ]);
            }else if ($statisticType == 'monthly'){
                $query->whereMonth('created_at',date('m'));
            }else if ($statisticType == 'yearly'){
                $query->whereYear('created_at',date('Y'));
            }
        });

        $ecommerceSales = $orders->sum('transaction_sum_amount');
        $outletBookingSales = $bookings->get()->sum('transaction_sum_amount');
        $titTatTourBookingSales = $tourbookings->get()->sum('transaction_sum_amount');

        $totalSales = $ecommerceSales + $outletBookingSales + $titTatTourBookingSales;

        $statistic = [
            'total_sales' => [
                "sequence" => 1,
                'more_info' => false,
                'prefix' => 'RM',
                'decimal' => true,
                'value' => $totalSales,
                'label' => 'Total Sales',
                'route' => route('admin.orders.index'),
                'icon' => 'fas fa-chart-line',
                'class' => 'bg-info'
            ],
            'total_order' => [
                "sequence" => 6,
                'value' => $orders->count(),
                'label' => 'Total E-commerce Orders',
                'route' => route('admin.orders.index'),
                'icon' => 'fas fa-shopping-cart',
                'class' => 'bg-warning'
            ],
            'total_outlet_booking' => [
                "sequence" => 7,
                'value' => $bookings->count(),
                'label' => 'Total Outlet Booking Orders',
                'route' => route('admin.bookings.index'),
                'icon' => 'fas fa-calendar-week',
                'class' => 'bg-green'
            ],
            'total_tit_tat_tour_booking' => [
                "sequence" => 8,
                'value' => $tourbookings->count(),
                'label' => 'Total Tit Tar Tour Booking Orders',
                'route' => route('admin.bookings.index'),
                'icon' => 'fas fa-calendar-week',
                'class' => 'bg-purple'
            ],
            'total_new_user' => [
                "sequence" => 9,
                'value' => $users->count(),
                'label' => 'Total New Users',
                'route' => route('admin.users.index'),
                'icon' => 'fas fa-user',
                'class' => 'bg-orange'
            ],
            [
                "sequence" => 3,
                'value' => number_format($outletBookingSales, 2, '.', ''),
                'prefix' => 'RM',
                'decimal' => true,
                'label' => 'Total Outlet Booking Sales',
                'route' => route('admin.bookings.index', ['type' => 'outlet']),
                'icon' => 'fas fa-chart-line',
                'class' => 'bg-info'
            ],
            [
                "sequence" => 4,
                'value' => number_format($titTatTourBookingSales, 2, '.', ''),
                'prefix' => 'RM',
                'decimal' => true,
                'label' => 'Total Tit Tar Tour Booking Sales',
                'route' => route('admin.bookings.index', ['type' => 'tit-tar-tour']),
                'icon' => 'fas fa-chart-line',
                'class' => 'bg-info'
            ],
            [
                "sequence" => 2,
                'value' => number_format($ecommerceSales, 2, '.', ''),
                'prefix' => 'RM',
                'decimal' => true,
                'label' => 'Total E-commerce Sales',
                'route' => route('admin.orders.index'),
                'icon' => 'fas fa-chart-line',
                'class' => 'bg-info'
            ],
            [
                "sequence" => 5,
                'value' => $orders->count() + $bookings->count() + $tourbookings->count(),
                'label' => 'Total Orders',
                'route' => route('admin.bookings.index'),
                'icon' => 'fas fa-calendar-week',
                'class' => 'bg-purple'
            ],
        ];



        return view('modules::dashboard',compact('statistic','statisticType'));
    }

    public function filter(Request $request)
    {
        $request->validate([
            'statistics_type' => 'required'
        ]);
        session()->put('statistic_filter', json_encode($request->all()));
        return back();
    }

}
