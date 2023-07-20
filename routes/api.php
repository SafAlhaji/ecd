<?php

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/customers', function (Request $request) {
    $q = $request->get('q');

    return Customer::where('passport_number', 'like', '%'.$q.'%')->paginate(null, ['id', 'passport_number as text']);
});
Route::get('/payment_customers', function (Request $request) {
    $q = $request->get('q');

    return Customer::where('snl', 'like', '%'.$q.'%')->paginate(null, ['id', 'snl as text']);
});
