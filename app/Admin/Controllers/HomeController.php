<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Dashboard;
use App\Models\Profession;
use App\Models\Requests;
use App\Models\RequestStatus;
use App\Models\Service;
use App\Models\ServiceDetails;
use App\Models\ServiceType;
use App\Models\ThirdParty;
use App\Models\TransactionsHistory;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            // ->title('Dashboard')
            // ->description('Description...')
            // ->row(Dashboard::title())
            ->row(function (Row $row) {
                // number of instructors section
                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::total_requests('Total number of Requests', 'primary'));
                });
                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::total_requests_status('Status of Requests', 'danger'));
                });
                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::net_profit('Net Profit', 'success'));
                });
                // // number of clients section
                // $row->column(4, function (Column $column) {
                //     // get clients count without trashed records
                //     $clientsCounter = Client::count();

                //     $column->append(Dashboard::customInfoBox('Total number of clients', $clientsCounter, 'warning'));
                // });

                // // number of requests sections
                // $row->column(4, function (Column $column) {
                //     // get requests count without trashed records
                //     $clientRequestCounter = Requests::count();

                //     $column->append(Dashboard::customInfoBox('Total number of requests', $clientRequestCounter, 'success'));
                // });
            })
        ->row(function (Row $row) {
            $row->column(12, function (Column $column) {
                $column->append(Dashboard::environment());
            });
        })
        ->row(function (Row $row) {
            $row->column(12, function (Column $column) {
                $column->append(Dashboard::extensions());
            });
        });
    }

    public function get_customer(Request $request)
    {
        $customer_id = $request->get('q');
        $customer = Customer::find($customer_id);

        return $customer;
    }

    public function get_service(Request $request)
    {
        $service_id = $request->input('service_id');
        $type_id = $request->input('type_id');
        $profession_id = $request->input('profession_id');
        $service = Service::find($service_id);
        $professions_ids = [];
        if ($service) {
            $service_details = $service->servicedetails()->whereServiceTypeId($type_id)->whereProfessionId($profession_id)->first();
            $professions_ids = $service->servicedetails()->whereServiceTypeId($type_id)->get(['profession_id']);
        }
        $response = new \stdClass();
        if (isset($service_details)) {
            $response->amount_service_type = $service_details->amount_service_type;
            $response->embassy_charge = $service_details->embassy_charge;
            $response->tax_percentage = floatval($service_details->tax_type->amount) / 100;
            $total_amount = floatval($service_details->amount_service_type) + floatval($service_details->embassy_charge);
            $tax_amount = ($service_details->amount_service_type * floatval($service_details->tax_type->amount)) / 100;
            if (ServiceDetails::NO_VAT == $service_details->is_tax_include) {
                $response->tax_amount = 0;
                $response->total_amount = $total_amount;
            } elseif (ServiceDetails::WITH_VAT == $service_details->is_tax_include) {
                $response->amount_service_type = number_format($service_details->amount_service_type / (floatval($response->tax_percentage) + 1), 2);
                $response->tax_amount = number_format($service_details->amount_service_type - $response->amount_service_type, 2);
                $response->total_amount = $total_amount;
            } elseif (ServiceDetails::WITHOUT_VAT == $service_details->is_tax_include) {
                $response->tax_amount = $tax_amount;
                $response->total_amount = $total_amount + $tax_amount;
            }
        }
        if (count($professions_ids) > 0) {
            $professions = Profession::whereIn('id', $professions_ids)->get(['id', 'title']);
            $response->professions = $professions;
        }

        return response()->json($response, 200);
    }

    public function get_professions(Request $request)
    {
        $service_id = $request->input('service_id');
        $type_id = $request->input('type_id');
        $service = Service::find($service_id);
        $professions = [];
        if ($service) {
            $professions = $service->servicedetails()->whereServiceTypeId($type_id)->get(['id', 'title']);
        }

        return response()->json($professions, 200);
    }

    public function get_servicedetails_types(Request $request)
    {
        $service_id = $request->input('q');
        $service = Service::find($service_id);
        $servicedetails = [];
        $service_types = [];
        if ($service) {
            $servicedetails = $service->servicedetails()->get(['service_type_id']);
        }
        if (count($servicedetails) > 0) {
            $service_types = ServiceType::whereIn('id', $servicedetails)->get(['id', 'title']);
        }

        return $service_types;
    }

    public function get_servicedetails_professions(Request $request)
    {
        $service_id = $request->input('q');
        $service = Service::find($service_id);
        $servicedetails = [];
        $professions = [];
        if ($service) {
            $servicedetails = $service->servicedetails()->get(['profession_id']);
        }
        // dd($servicedetails);
        if (count($servicedetails) > 0) {
            $professions = Profession::whereIn('id', $servicedetails)->get(['id', 'title']);
        }

        return $professions;
    }

    public function get_service_amount(Request $request)
    {
        $service_id = $request->input('service_id');
        $type_id = $request->input('type_id');
        $profession_id = $request->input('profession_id');
        $service = Service::find($service_id);
        if ($service) {
            $service_details = $service->servicedetails()->whereServiceTypeId($type_id)->whereProfessionId($profession_id)->first();
        }
        // dd($service_details);
        $response = new \stdClass();
        if (isset($service_details)) {
            $response->amount_service_type = $service_details->amount_service_type;
            $response->embassy_charge = $service_details->embassy_charge;
            $response->tax_percentage = floatval($service_details->tax_type->amount) / 100;
            $total_amount = floatval($service_details->amount_service_type) + floatval($service_details->embassy_charge);
            $tax_amount = ($service_details->amount_service_type * floatval($service_details->tax_type->amount)) / 100;
            if (ServiceDetails::WITHOUT_VAT == $service_details->is_tax_include) {
                $response->tax_amount = 0;
                $response->total_amount = $total_amount;
            } elseif (ServiceDetails::is_vat_include_true == $service_details->is_tax_include) {
                $response->amount_service_type = number_format($service_details->amount_service_type / (floatval($response->tax_percentage) + 1), 2);
                $response->tax_amount = number_format($service_details->amount_service_type - $response->amount_service_type, 2);
                $response->total_amount = $total_amount;
            } elseif (ServiceDetails::is_vat_include_false == $service_details->is_tax_include) {
                $response->tax_amount = $tax_amount;
                $response->total_amount = $total_amount + $tax_amount;
            }
        }
        // dd($response);
        return response()->json($response, 200);
    }

    public function requests_payment_type($date_type)
    {
        // DB::raw('SUM(IF(payment_type_id=1,amount,0)) AS core');
        $requests = Requests::whereInvoiceStatus(0)->select(['amount', 'payment_type_id', DB::raw('COUNT(id) as count_data'), DB::raw('SUM(IF(payment_type_id=1,amount,0)) AS cash'), DB::raw('SUM(IF(payment_type_id=2,amount,0)) AS bank'), DB::raw('SUM(IF(payment_type_id=3,amount,0)) AS credit')]);
        // dd($requests);
        if (1 == $date_type) {
            $requests->whereDate('request_created_at', date('Y-m-d'));
        } elseif (2 == $date_type) {
            $requests->whereBetween('request_created_at', [Carbon::now()->subMonth()->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')]);
        } elseif (3 == $date_type) {
            $requests->whereBetween('request_created_at', [Carbon::now()->subYear()->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')]);
        }
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $requests->whereIn('branch_id', $ids);
        }
        $data = $requests->groupBy('amount', 'payment_type_id')->get()->lazy();
        $sum_cash = $data->sum('cash');
        $sum_bank = $data->sum('bank');
        $sum_credit = $data->sum('credit');
        $response['all']['count'] = $data->sum('count_data');
        $response['all']['amount'] = $sum_cash + $sum_bank + $sum_credit;
        $response['cash']['count'] = $data->where('cash', '>', 0)->sum('count_data');
        $response['cash']['amount'] = $sum_cash;
        $response['bank']['count'] = $data->where('bank', '>', 0)->sum('count_data');
        $response['bank']['amount'] = $sum_bank;
        $response['credit']['count'] = $data->where('credit', '>', 0)->sum('count_data');
        $response['credit']['amount'] = $sum_credit;

        return response()->json($response, 200);
    }

    public function requests_status($date_type)
    {
        $requests = Requests::whereInvoiceStatus(0)->select(['request_status_id', DB::raw('COUNT(id) as count_data')]);
        if (1 == $date_type) {
            $requests->whereDate('request_created_at', date('Y-m-d'));
        } elseif (2 == $date_type) {
            $requests->whereBetween('request_created_at', [Carbon::now()->subMonth()->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')]);
        } elseif (3 == $date_type) {
            $requests->whereBetween('request_created_at', [Carbon::now()->subYear()->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')]);
        }
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $requests->whereIn('branch_id', $ids);
        }
        $data = $requests->groupBy('request_status_id')->get()->lazy();
        $response['all'] = $data->sum('count_data');
        $response['pending'] = $data->where('request_status_id', RequestStatus::PENDING)->sum('count_data');
        $response['in_embassy'] = $data->where('request_status_id', RequestStatus::IN_EMBASSY)->sum('count_data');
        $response['at_office'] = $data->where('request_status_id', RequestStatus::At_Office)->sum('count_data');
        $response['compeleted'] = $data->where('request_status_id', RequestStatus::COMPELETED)->sum('count_data');

        return response()->json($response, 200);
    }

    public function net_profit($date_type)
    {
        $requests = Requests::whereInvoiceStatus(0)->select([DB::raw('SUM(tax_amount) as total_tax_amount'), DB::raw('SUM(amount) as total_amount')]);
        $transactions = TransactionsHistory::whereTransactionStatus(0)->select([DB::raw('SUM(tax_amount) as total_tax_amount'), DB::raw('SUM(amount) as total_amount')])->where('transaction_type', TransactionsHistory::MONEY_OUT);
        if (1 == $date_type) {
            $requests->whereDate('request_created_at', date('Y-m-d 00:00:00'));
            $transactions->whereDate('created_at', date('Y-m-d 00:00:00'));
        } elseif (2 == $date_type) {
            $requests->whereBetween('request_created_at', [Carbon::now()->subMonth()->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')]);
            $transactions->whereBetween('created_at', [Carbon::now()->subMonth()->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')]);
        } elseif (3 == $date_type) {
            $requests->whereBetween('request_created_at', [Carbon::now()->subYear()->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')]);
            $transactions->whereBetween('created_at', [Carbon::now()->subYear()->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')]);
        }
        $income = $requests->get()->lazy();
        $expenses = $transactions->get()->lazy();
        $response['net_income'] = round($income->sum('total_amount') - $income->sum('total_tax_amount'), 2);
        $response['net_expenses'] = round($expenses->sum('total_amount') - $expenses->sum('total_tax_amount'), 2);
        $response['net_profit'] = round(doubleval($response['net_income']) - doubleval($response['net_expenses']), 2);
        if (!Admin::user()->isAdministrator()) {
            $response['net_income'] = 0;
            $response['net_expenses'] = 0;
            $response['net_profit'] = 0;
        }

        return response()->json($response, 200);
    }
}
