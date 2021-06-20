<?php

namespace App\Providers;

use App\Services\Wazzup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->app->bind(Wazzup::class, function () {
            if (!empty(Request()->api_key)) {
                $api_key = Request()->api_key;
            } else {
                if (is_null(Auth::user()->whatsapp)) {
                    abort(403);
                }
                $api_key = Auth::user()->whatsapp->api_key;
            }
            return new Wazzup($api_key);
        });
    }
}
