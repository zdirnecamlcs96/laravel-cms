<?php

namespace Local\CMS;

use App\Models\Booking;
use App\Models\Outlet;
use App\Models\TitTarTour;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Local\CMS\Models\Admin;
use Local\CMS\Models\Banner;
use Local\CMS\Models\File;
use Local\CMS\Models\NewsEvent;
use Local\CMS\Models\Role;
use Local\CMS\Policies\AdminPolicy;
use Local\CMS\Policies\BannerPolicy;
use Local\CMS\Policies\BookingPolicy;
use Local\CMS\Policies\CategoryPolicy;
use Local\CMS\Policies\CouponPolicy;
use Local\CMS\Policies\FilePolicy;
use Local\CMS\Policies\NewsEventPolicy;
use Local\CMS\Policies\OrderPolicy;
use Local\CMS\Policies\OutletPolicy;
use Local\CMS\Policies\ProductPolicy;
use Local\CMS\Policies\RolePolicy;
use Local\CMS\Policies\TitTarTourPolicy;
use Local\CMS\Policies\UserPolicy;
use Local\Ecommerce\Models\Category;
use Local\Ecommerce\Models\Coupon;
use Local\Ecommerce\Models\Order;
use Local\Ecommerce\Models\Product;

class ServiceProvider extends BaseServiceProvider
{

    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Role::class => RolePolicy::class,
        Admin::class => AdminPolicy::class,
        User::class => UserPolicy::class,
        File::class => FilePolicy::class,
        Banner::class => BannerPolicy::class,
        NewsEvent::class => NewsEventPolicy::class,
        Product::class => ProductPolicy::class,
        Category::class => CategoryPolicy::class,
        Coupon::class => CouponPolicy::class,
        Order::class => OrderPolicy::class,
        Outlet::class => OutletPolicy::class,
        Booking::class => BookingPolicy::class,
        TitTarTour::class => TitTarTourPolicy::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->registerPolicies();

        // Register the model factories
        $this->loadFactoriesFrom(__DIR__ . '/../database/factories');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'modules');
        $this->publishes([
            __DIR__ . '/../resources/views', resource_path('packages/local/cms'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/assets' => public_path('vendor/cms'),
            ], 'assets');
        }

        $this->registerRoutes();

        # Build menus
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            $menu = $event->menu;

            # Role Management
            if (config('modules.role_management') && !$menu->itemKeyExists('role_management')) {
                $menu->addAfter('content_management_header', [
                    "key"         => "role_management",
                    'text'        => 'Roles',
                    'route'       => 'admin.roles.index',
                    'icon'        => 'fas fa-fw fa-user',
                    'can'         => ['role_read']
                ]);
            }
        });

        Gate::before(function ($user, $ability) {
            return in_array($user->email, explode(',', config('cms.superadmins'))) ? true : null;
        });
    }

    protected function registerRoutes()
    {
        Route::middleware(['web'])
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/modules.php', 'modules'); // this will merge one more index for admin_url
        $this->mergeConfigFrom(__DIR__ . '/../config/adminlte.php', 'adminlte');
        $this->mergeConfigFrom(__DIR__ . '/../config/auth.php', 'auth');
        $this->mergeConfigFrom(__DIR__ . '/../config/cms.php', 'cms');
    }

    /**
     * Register the application's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
