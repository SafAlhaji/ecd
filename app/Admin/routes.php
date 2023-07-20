<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
    'as' => config('admin.route.prefix').'.',
], function (Router $router) {
    $router->get('/', 'HomeController@index')->name('home');

    Route::get('/get_customer', 'HomeController@get_customer');
    Route::get('/get_servicedetails_types', 'HomeController@get_servicedetails_types');
    Route::get('/get_servicedetails_professions', 'HomeController@get_servicedetails_professions');
    Route::get('/get_service', 'HomeController@get_service');
    Route::get('/requests_payment_type/{type}', 'HomeController@requests_payment_type');
    Route::get('/requests_status/{type}', 'HomeController@requests_status');
    Route::get('/net_profit/{type}', 'HomeController@net_profit');

    $router->get('/update_passport', 'RequestsController@update_passport');
    $router->get('/check_service_update_id', 'RequestsController@check_service_update_id');

    $router->get('/requests/printpdf', 'RequestsController@prind_pdf_request');
    $router->get('/requests/request_status', 'RequestsController@update_request_status');
    $router->get('/requests/requests_pdf', 'RequestsController@requests_pdf');
    $router->get('request_batches/print', 'BatchController@print_pdf');
    $router->get('request_batches/excel', 'BatchController@excel');

    $router->resource('auth/users', ThirdPartyController::class);
    $router->resource('countries', CountryController::class);
    $router->resource('service_provider', ServiceProviderController::class);
    $router->resource('branches', BranchController::class);
    $router->resource('service_type', ServiceTypeController::class);
    $router->resource('embassy_service', ServiceController::class);
    $router->resource('general_service', GeneralServiceController::class);
    $router->resource('/requests', RequestsController::class);
    $router->get('/get_requests', 'RequestsController@get_requests')->name('requests.list');
    $router->resource('customers', CustomerController::class);
    $router->resource('professions', ProfessionController::class);
    $router->resource('organization_details', OrganizationDetailsController::class);
    $router->resource('request_batches', BatchController::class);
    $router->resource('send_request_embassy', SendToEmbassyController::class);
    $router->resource('in_embassy_requests', InEmbassyRequestsCotroller::class);
    $router->resource('sms_messages', SmsMessageController::class);
    $router->resource('sms_gateway', SmsGatewayController::class);
    $router->resource('request_type', RequestTypeController::class);
    $router->resource('tax_type', TaxTypeController::class);
    $router->resource('setup_invoice', InvoiceSetupController::class);
    $router->resource('trans_recived', TransRecivedController::class);
    $router->resource('trans_pay', TransPayController::class);
    $router->resource('transactions', TransactionsHistoryController::class);
    $router->resource('auth/logs', CustomLogController::class);
    $router->post('tax-report/full_report', 'TaxReportController@print_pdf');
    $router->post('tax-report/full_report_excel', 'TaxReportController@excel');
    $router->resource('tax-report', TaxReportController::class);
    $router->get('/net_income', 'NetIncomeController@filter');
    $router->resource('net_income', NetIncomeController::class);

    $router->post('request_report/full_report', 'RequestsReportController@print_pdf');
    $router->post('request_report/full_report_excel', 'RequestsReportController@excel');

    $router->resource('request_report', RequestsReportController::class);
    $router->resource('draft_batches', DraftBatchController::class);

    $router->resource('refunded_report', RefundController::class);
});
