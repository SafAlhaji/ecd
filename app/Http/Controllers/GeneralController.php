<?php

namespace App\Http\Controllers;

use App\Http\Requests\DraftBatchRequest;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\DashboardChart;
use App\Models\DraftBatch;
use App\Models\Profession;
use App\Models\Requests;
use App\Models\Service;
use App\Models\ServiceDetails;
use App\Models\ServiceProvider;
use App\Models\ThirdParty;
use App\Models\TransactionsHistory;
use App\Traits\LogTrait;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GeneralController extends Controller
{
    use LogTrait;

    public function track_request(Request $request)
    {
        $qrcode = $request->input('qr');
        if ($qrcode) {
            $customer_request = Requests::whereQrString($qrcode)->first();
            if ($customer_request) {
                return view('tracking.request_track', compact('customer_request'));
            }
        }
        abort(404);
    }

    public function track_transaction(Request $request)
    {
        $qrcode = $request->input('qr');
        if ($qrcode) {
            $data = TransactionsHistory::whereQrString($qrcode)->first();
            if ($data) {
                if (TransactionsHistory::MONEY_IN == $data->transaction_type) {
                    return view('tracking.transaction_received_track', compact('data'));
                } elseif (TransactionsHistory::MONEY_OUT == $data->transaction_type) {
                    return view('tracking.transaction_payment_track', compact('data'));
                } else {
                    abort(404);
                }
            }
        }
        abort(404);
    }

    public function get_services_by_request_type(Request $request)
    {
        $request_type_id = $request->get('q');
        $services = Service::whereRequestTypeId($request_type_id)->get(['id', 'title']);

        return $services;
    }

    public function get_service_providers_by_request_type(Request $request)
    {
        $request_type_id = $request->get('q');
        $service_provider = [];
        if (!Admin::user()->isAdministrator()) {
            $service_provider = ServiceProvider::whereRequestTypeId($request_type_id)->whereHas('branches', function ($query) {
                $query->whereIn('branch_id', ThirdParty::find(Admin::user()->id)->branches()->pluck('branches.id'));
            })->get(['id', 'title']);
        } else {
            $service_provider = ServiceProvider::whereRequestTypeId($request_type_id)->get(['id', 'title']);
        }
        $response['service_provider'] = $service_provider;
        if (1 == $request_type_id) {
            $response['professions'] = Profession::get(['id', 'title']);
        }

        return response()->json($response, 200);
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
        if (count($professions_ids) > 0) {
            $professions = Profession::whereIn('id', $professions_ids)->get(['id', 'title']);
            $response->professions = $professions;
        }

        return response()->json($response, 200);
    }

    public function request_receipt($request_id)
    {
        $data = Requests::find($request_id);
        if ($data) {
            $this->create_log('Print request receipt', 'PRINT');

            return view('pdf.request_receipt', compact('data'));
        }
        abort(404);
    }

    public function received_voucher($trans_id)
    {
        $data = TransactionsHistory::find($trans_id);
        if ($data) {
            $this->create_log('Print received voucher', 'PRINT');

            return view('pdf.received_voucher', compact('data'));
        }
        abort(404);
    }

    public function payment_voucher($trans_id)
    {
        $data = TransactionsHistory::find($trans_id);
        if ($data) {
            $this->create_log('Print payment voucher', 'PRINT');

            return view('pdf.payment_voucher', compact('data'));
        }
        abort(404);
    }

    public function draft_batch(DraftBatchRequest $request)
    {
        // $request_id = $request->input('req_id');
        // $validator = Validator::make($request->all(), [
        // 'req_id' => 'required|'.Rule::exists('requests', 'id')->whereNull('batch_id').'|unique:draft_batch,request_id',
        // ]);
        // if ($validator->fails()) {
        //     return response()->json(['status' => false, 'message' => 'Request Error']);
        // } else {
        $current_request = Requests::find($request->req_id);
        DraftBatch::updateOrCreate([
            'request_id' => $current_request->id,
            'embassy_id' => $current_request->embassy_id,
            'service_id' => $current_request->service_id,
        ]);

        return response()->json(['status' => true, 'message' => 'Request added to draft successfully ']);
        // }

        // $requests_ids1 = \Illuminate\Support\Facades\Session::get('draft_batch_requests') ?? [];
        // if (!in_array($request_id, $requests_ids1)) {
        //     $requests_ids1[] = intval($request_id);
        // }
        // \Illuminate\Support\Facades\Session::put('draft_batch_requests', $requests_ids1);
    }

    public function update_requests_chart()
    {
        $period = CarbonPeriod::create(Carbon::now()->subMonth()->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
        $days = [];
        $requests_data = [];
        foreach ($period as $date) {
            $days[] = $date->format('Y-m-d');
        }

        foreach (Service::select(['id', 'title'])->get()->lazy() as $service) {
            $line_color = 'rgb('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).')';
            $amount = [];

            foreach ($days as $day) {
                if (!Admin::user()->isAdministrator()) {
                    $amount[] = Requests::select(['id', 'amount', 'request_created_at', 'service_id', 'branch_id'])->monthly()->whereDate('request_created_at', '=', $day)->where('service_id', $service->id)->whereIn('branch_id', ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->whereInvoiceStatus(0)->lazy())->get()->lazy()->sum('amount');
                }
                if (Admin::user()->isAdministrator()) {
                    $amount[] = Requests::monthly()->whereDate('request_created_at', '=', $day)->where('service_id', $service->id)->whereInvoiceStatus(0)->get()->lazy()->sum('amount');
                }
            }
            // dd($amount);
            $requests_data[] = [$service->title, $amount, $line_color];
        }
        // dd($requests_data);
        $requests_user_data = DashboardChart::whereChartType(DashboardChart::REUQESTS)->whereAdminUserId(Admin::user()->id)->count();
        if ($requests_user_data) {
            DashboardChart::whereChartType(DashboardChart::REUQESTS)->whereAdminUserId(Admin::user()->id)->delete();
        }

        foreach ($requests_data as $req) {
            $new_requests = DashboardChart::create([
                    'admin_user_id' => Admin::user()->id,
                    'chart_type' => DashboardChart::REUQESTS,
                    'title' => $req[0],
                    'counts' => $req[1],
                    'chart_color' => $req[2],
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Request added to draft successfully ']);
    }

    public function update_branchs_chart()
    {
        $period = CarbonPeriod::create(Carbon::now()->subMonth()->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
        $days = [];
        foreach ($period as $date) {
            $days[] = $date->format('Y-m-d');
        }
        $requests_data_branch = [];
        if (!Admin::user()->isAdministrator()) {
            $user_branches = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->lazy();
            foreach ($user_branches as $branch) {
                $branch_title = Branch::find($branch->id);
                $line_color = 'rgb('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).')';
                $amount = [];
                foreach ($days as $day) {
                    $amount[] = Requests::select(['id', 'amount', 'request_created_at', 'service_id', 'branch_id'])->monthly()->whereDate('request_created_at', '=', $day)->whereInvoiceStatus(0)->where('branch_id', $branch->id)->get()->lazy()->sum('amount');
                }
                $requests_data_branch[] = [$branch_title->title ?? '', $amount, $line_color];
            }
        }
        if (Admin::user()->isAdministrator()) {
            foreach (Branch::select(['id', 'title'])->get()->lazy() as $branch) {
                $line_color = 'rgb('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).')';
                $amount = [];
                foreach ($days as $day) {
                    $amount[] = Requests::select(['id', 'amount', 'request_created_at', 'service_id', 'branch_id'])->monthly()->whereDate('request_created_at', '=', $day)->where('branch_id', $branch->id)->whereInvoiceStatus(0)->get()->lazy()->sum('amount');
                }
                $requests_data_branch[] = [$branch->title, $amount, $line_color];
            }
        }
        $branch_data = DashboardChart::whereChartType(DashboardChart::BRANCHS)->whereAdminUserId(Admin::user()->id)->count();
        if ($branch_data > 0) {
            DashboardChart::whereChartType(DashboardChart::BRANCHS)->whereAdminUserId(Admin::user()->id)->delete();
        }

        foreach ($requests_data_branch as $req) {
            $new_requests = DashboardChart::create([
                    'admin_user_id' => Admin::user()->id,
                    'chart_type' => DashboardChart::BRANCHS,
                    'title' => $req[0],
                    'counts' => $req[1],
                    'chart_color' => $req[2],
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Branchs Data Updated']);
    }

    public function refund_request($request_id)
    {
        $req = Requests::find($request_id);
        if ($req) {
            if (Admin::user()->can('refund_request')) {
                $req->transactions_history->transaction_status = 1;
                $req->transactions_history->save();
                $req->create_log('REFUND Request '.$req->id, 'REFUND');
                $req->invoice_status = 1;
                $req->save();
                // $req->forcedelete();
                return response()->json([
                'status' => true,
                'message' => 'refunded successfully',
            ]);
            }
        }

        return response()->json(['status' => false, 'message' => 'ERROR WHEN REFUND']);
    }
}
