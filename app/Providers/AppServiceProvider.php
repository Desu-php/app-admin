<?php

namespace App\Providers;

use App\Models\User;
use App\Services\Sbis;
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
                if (Auth::user()->hasRole(User::EMPLOYEE)){
                    abort_if(is_null(Auth::user()->user->whatsapp), 403);

                    $api_key = Auth::user()->user->whatsapp->api_key;
                }else{
                    abort_if(is_null(Auth::user()->whatsapp), 403);

                    $api_key = Auth::user()->whatsapp->api_key;
                }

            }
            return new Wazzup($api_key);
        });

        $this->app->singleton(Sbis::class, function (){

            abort_if(is_null(Auth::user()->sbis), 403);

            $sbisAccount = Auth::user()->sbis;

            return new Sbis($sbisAccount->app_client_id, $sbisAccount->app_secret, $sbisAccount->secret_key );
        });
    }
}
