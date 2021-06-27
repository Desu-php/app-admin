<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['prefix' => '/', 'middleware' => ['auth']], function () {
    Route::get('/', 'RedirectController@index');
    Route::group(['prefix' => '/', 'middleware' => ['role:SuperAdmin']], function () {
        Route::get('/users/ajax/get', 'UserController@indexAjax')->name('users.indexAjax');
        Route::resource('/users', 'UserController');
    });

    Route::resource('whatsapp', 'WhatsappController');
    Route::get('/whatsapp/channel/create/{whatsapp}', 'WhatsappController@channelCreate')->name('whatsapp.channel.create');
    Route::get('/whatsapp/ajax/get', 'WhatsappController@indexAjax')->name('whatsapp.indexAjax');
    Route::put('/whatsapp/channel/{whatsapp}', 'WhatsappController@channelStore')->name('whatsapp.channel.store');
    Route::get('/gotowhatsap', 'WhatsappController@openChat')->name('openChat');

    Route::resource('sbisAccounts', 'SbisAccountController');
    Route::get('sbisAccounts/ajax/get', 'SbisAccountController@indexAjax');
    Route::get('sbisAccounts/create/theme', 'SbisAccountController@createTheme')->name('sbisAccounts.create_theme');
    Route::post('sbisAccounts/store_theme', 'SbisAccountController@storeTheme')->name('sbisAccounts.store_theme');
});

Route::post('/whatsapp/webhook/{id}', 'WhatsappController@webhook')->name('webhook');


Auth::routes(['registration' => false]);


