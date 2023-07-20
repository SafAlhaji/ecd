<?php

use App\Models\Customer;
use App\Models\OldPassportNumbers;
use App\Models\Requests;
use Illuminate\Support\Facades\File;
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

Route::get('/', 'HomeController@index')->name('home');

Route::group(['middleware' => ['admin.permission:check,create_request', 'auth:admin']], function () {
    Route::get('/customer', 'GeneralController@get_customer');
    Route::get('/get_servicedetails_types', 'GeneralController@get_servicedetails_types');
    Route::get('/get_servicedetails_professions', 'GeneralController@get_servicedetails_professions');
    Route::get('/get_service', '\App\Admin\Controllers\HomeController@get_service');
    Route::get('/get_service_amount', '\App\Admin\Controllers\HomeController@get_service_amount');
    Route::get('/get_services_by_request_type', 'GeneralController@get_services_by_request_type');
    Route::get('/get_service_providers_by_request_type', 'GeneralController@get_service_providers_by_request_type');
});

Route::get('/trackRequest', 'GeneralController@track_request');
Route::get('/trackTransaction', 'GeneralController@track_transaction');

Route::group(['middleware' => ['auth:admin']], function () {
    Route::get('/request_receipt/{request_id}', 'GeneralController@request_receipt');
    Route::get('/refund_request/{request_id}', 'GeneralController@refund_request');
    Route::get('/net_income_report', '\App\Admin\Controllers\NetIncomeController@filter');

    Route::get('/received_voucher/{trans_id}', 'GeneralController@received_voucher');
    Route::get('/payment_voucher/{trans_id}', 'GeneralController@payment_voucher');
    Route::get('/draft_batch', 'GeneralController@draft_batch');
    Route::get('/update_requests_chart', 'GeneralController@update_requests_chart');
    Route::get('/update_branchs_chart', 'GeneralController@update_branchs_chart');
});
Route::get('/test_tracking', function () {
    return view('tracking.test_request_track');
});
// Route::get('/test_xml.xml', function () {
//     $requests = Requests::first();
//     // dd($requests);
//     $content = view('pdf.test_xml', compact('requests'))->render();
//     // dd(public_path('/invoices').'/file.xml');
//     File::put(public_path('/invoices').'/file.xml', $content);

//     return response()->view('pdf.test_xml', compact('requests'))->header('Content-Type', 'text/xml');
// });
// Route::get('/update_customers', function () {
//     $old_passport_numbers = Customer::select('old_passport_numbers')->pluck('old_passport_numbers')->filter()->collapse()->unique();
//     foreach ($old_passport_numbers as $old_number) {
//         OldPassportNumbers::create([
//                         'number' => $old_number,
//                         ]);
//     }

//     $customers = Customer::all()->groupBy('passport_number');
//     // dd($customers);
//     $data = [];
//     // foreach ($customers as $key => $value) {
//     //     if (count($value) > 1) {
//     //         $data[] = $value;
//     //     }
//     // }
//     // foreach ($data as $key => $value) {
//     //     foreach ($value as $customer) {
//     //         $reqs[] = $customer->requests;
//     //     }
//     // }
//     // dd($data);
//     // $customers = Customer::where('snl', 1)->get();
//     // foreach ($customers as $customer) {
//     //     // dd($customer->check_snl());
//     //     $customer->snl = 'CL00_'.$customer->id;
//     //     $customer->save();
//     // }

//     // return Customer::where('snl', 1)->get();
// });
