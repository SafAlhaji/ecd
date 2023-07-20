<?php

//namespace Encore\Admin\Controllers;

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Encore\Admin\Facades\Admin;

class Dashboard
{
    public static function title()
    {
        return view('dashboard.title');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function environment()
    {
        // $period = CarbonPeriod::create(Carbon::now()->subMonth()->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
        // $days = [];
        // $requests_data = [];
        // foreach ($period as $date) {
        //     $days[] = $date->format('Y-m-d');
        // }
        // // // dd($days);
        // // $new_requests = Requests::whereIn('request_created_at', $days)->get();
        // // dd($new_requests);
        // foreach (Service::select(['id', 'title'])->get()->lazy() as $service) {
        //     $line_color = 'rgb('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).')';
        //     $amount = [];

        //     foreach ($days as $day) {
        //         if (!Admin::user()->isAdministrator()) {
        //             $amount[] = Requests::select(['id', 'amount', 'request_created_at', 'service_id', 'branch_id'])->monthly()->whereDate('request_created_at', '=', $day)->where('service_id', $service->id)->whereIn('branch_id', ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->lazy())->get()->lazy()->sum('amount');
        //         }
        //         if (Admin::user()->isAdministrator()) {
        //             $amount[] = Requests::select(['id', 'amount', 'request_created_at', 'service_id', 'branch_id'])->monthly()->whereDate('request_created_at', '=', $day)->where('service_id', $service->id)->get()->lazy()->sum('amount');
        //         }
        //     }
        //     // dd($amount);
        //     $requests_data[] = [$service->title, $amount, $line_color];
        // }
        // // dd($requests_data);
        // DashboardChart::truncate();
        // foreach ($requests_data as $req) {
        //     $new_requests = DashboardChart::create([
        //         'chart_type' => DashboardChart::REUQESTS,
        //         'title' => $req[0],
        //         'counts' => $req[1],
        //         'chart_color' => $req[2],
        // ]);
        // }
        // dd($new_requests);

        return view('dashboard.environment');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function extensions()
    {
        // $period = CarbonPeriod::create(Carbon::now()->subMonth()->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
        // $days = [];
        // foreach ($period as $date) {
        //     $days[] = $date->format('Y-m-d');
        // }
        // $requests_data_branch = [];
        // if (!Admin::user()->isAdministrator()) {
        //     $user_branches = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->lazy();
        //     foreach ($user_branches as $branch) {
        //         $branch_title = Branch::find($branch->id);
        //         $line_color = 'rgb('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).')';
        //         $amount = [];
        //         foreach ($days as $day) {
        //             $amount[] = Requests::select(['id', 'amount', 'request_created_at', 'service_id', 'branch_id'])->monthly()->whereDate('request_created_at', '=', $day)->where('branch_id', $branch->id)->get()->lazy()->sum('amount');
        //         }
        //         $requests_data_branch[] = [$branch_title->title ?? '', $amount, $line_color];
        //     }
        // }
        // if (Admin::user()->isAdministrator()) {
        //     foreach (Branch::select(['id', 'title'])->get()->lazy() as $branch) {
        //         $line_color = 'rgb('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).')';
        //         $amount = [];
        //         foreach ($days as $day) {
        //             $amount[] = Requests::select(['id', 'amount', 'request_created_at', 'service_id', 'branch_id'])->monthly()->whereDate('request_created_at', '=', $day)->where('branch_id', $branch->id)->get()->lazy()->sum('amount');
        //         }
        //         $requests_data_branch[] = [$branch->title, $amount, $line_color];
        //     }
        // }
        // dd($requests_data_branch);

        return view('dashboard.extensions');

        return view('dashboard.extensions', compact('days', 'requests_data_branch'));
    }

    public static function total_requests($title = '', $color = 'default')
    {
        $requests = Requests::whereInvoiceStatus(0)->select(['amount', 'payment_type_id', DB::raw('COUNT(id) as count_data'), DB::raw('SUM(IF(payment_type_id=1,amount,0)) AS cash'), DB::raw('SUM(IF(payment_type_id=2,amount,0)) AS bank'), DB::raw('SUM(IF(payment_type_id=3,amount,0)) AS credit')]);
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $requests->whereIn('branch_id', $ids);
        }
        $data = $requests->whereDate('request_created_at', date('Y-m-d 00:00:00'))->groupBy('amount', 'payment_type_id')->get()->lazy();

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

        return view('admin.customInfoBox', ['title' => $title, 'response' => $response, 'color' => $color]);
    }

    public static function total_requests_status($title = '', $color = 'default')
    {
        $requests = Requests::whereInvoiceStatus(0)->select(['request_status_id', DB::raw('COUNT(id) as count_data')]);
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $requests->whereIn('branch_id', $ids);
        }
        $data = $requests->whereDate('request_created_at', date('Y-m-d 00:00:00'))->groupBy('request_status_id')->get()->lazy();
        $response['all'] = $data->sum('count_data');
        $response['pending'] = $data->where('request_status_id', RequestStatus::PENDING)->sum('count_data');
        $response['in_embassy'] = $data->where('request_status_id', RequestStatus::IN_EMBASSY)->sum('count_data');
        $response['at_office'] = $data->where('request_status_id', RequestStatus::At_Office)->sum('count_data');
        $response['compeleted'] = $data->where('request_status_id', RequestStatus::COMPELETED)->sum('count_data');

        return view('admin.request_status', ['title' => $title, 'response' => $response, 'color' => $color]);
    }

    public static function net_profit($title = '', $color = 'default')
    {
        $income = Requests::whereInvoiceStatus(0)->select([DB::raw('SUM(tax_amount) as total_tax_amount'), DB::raw('SUM(amount) as total_amount'), DB::raw('SUM(embassy_charge) as total_provider_charge')])->whereDate('request_created_at', date('Y-m-d 00:00:00'))->get()->lazy();
        $expenses = TransactionsHistory::whereTransactionStatus(0)->select([DB::raw('SUM(tax_amount) as total_tax_amount'), DB::raw('SUM(amount) as total_amount')])->where('transaction_type', TransactionsHistory::MONEY_OUT)->whereDate('created_at', date('Y-m-d 00:00:00'))->groupBy('transaction_type')->get()->lazy();
        $total_income = round($income->sum('total_amount') - $income->sum('total_provider_charge'), 2);
        $response['net_income'] = round($total_income - $income->sum('total_tax_amount'), 2);
        $response['net_expenses'] = round($expenses->sum('total_amount') - $expenses->sum('total_tax_amount'), 2);
        $response['net_profit'] = round(doubleval($response['net_income']) - doubleval($response['net_expenses']), 2);
        if (!Admin::user()->isAdministrator()) {
            $response['net_income'] = 0;
            $response['net_expenses'] = 0;
            $response['net_profit'] = 0;
        }

        return view('admin.net_profit', ['title' => $title, 'response' => $response, 'color' => $color]);
    }
}
