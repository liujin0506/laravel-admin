<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
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
    protected $backend_namespace = 'App\Http\Controllers\Admin';
    protected $wechat_namespace = 'App\Http\Controllers\Wechat';
    protected $api_namespace = 'App\Http\Controllers\Api';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            if (config('domain.backend') === $_SERVER['HTTP_HOST']) {
                $this->mapBackendRoutes();
            } elseif (config('domain.wechat') === $_SERVER['HTTP_HOST']) {
                $this->mapWechatRoutes();
            } elseif (config('domain.api') === $_SERVER['HTTP_HOST']) {
                $this->mapApiRoutes();
            }
        }
    }

    /**
     * 管理后台路由
     */
    protected function mapBackendRoutes()
    {
        Route::middleware('backend')
             ->namespace($this->backend_namespace)
             ->group(base_path('routes/backend.php'));
    }

    /**
     * 微信端路由
     */
    protected function mapWechatRoutes()
    {
        Route::middleware('wechat')
            ->namespace($this->wechat_namespace)
            ->group(base_path('routes/wechat.php'));
    }

    /**
     * api路由 小程序|App
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->api_namespace)
             ->group(base_path('routes/api.php'));
    }
}
