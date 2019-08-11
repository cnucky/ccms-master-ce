<?php

namespace App\Providers;

use App\ComputeInstance;
use App\IPPool\IPv4;
use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6;
use App\IPPool\IPv6Assignment;
use App\Node\ComputeNode;
use App\PaymentModule;
use App\Ticket\Department;
use App\Ticket\Reply;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();

        Route::model("ipv4Pool", IPv4::class);
        Route::model("ipv6Pool", IPv6::class);
        Route::model("ipv4Assignment", IPv4Assignment::class);
        Route::model("ipv6Assignment", IPv6Assignment::class);
        Route::model("computeNode", ComputeNode::class);
        Route::model("ticketDepartment", Department::class);
        Route::model("ticketReply", Reply::class);

        Route::bind('computeInstance', function ($value) {
            $builder = ComputeInstance::query();
            if (is_numeric($value))
                $builder->where("id", $value);
            else
                $builder->where("unique_id", $value);
            return $builder->firstOrFail();
        });

        Route::model('paymentModule', PaymentModule::class);
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
