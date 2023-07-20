<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\DashboardChart;
use App\Models\Requests;
use App\Models\Service;
use App\Models\ThirdParty;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExportRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:chart_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all chart data to table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $period = CarbonPeriod::create(Carbon::now()->subMonth()->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
        $days = [];
        $requests_data = [];
        foreach ($period as $date) {
            $days[] = $date->format('Y-m-d');
        }
        // dd();
        DB::table('admin_operation_log')
        ->whereDate('created_at', '<', Carbon::now()->subMonth(3)->format('Y-m-d'))
        ->delete();
        DashboardChart::truncate();
        $all_users = ThirdParty::all();
        foreach ($all_users as $admin_user) {
            $requests_data = [];
            $requests_data_branch = [];
            foreach (Service::select(['id', 'title'])->get()->lazy() as $service) {
                $line_color = 'rgb('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).')';
                $amount = [];

                foreach ($days as $day) {
                    if (!$admin_user->isRole('administrator')) {
                        $amount[] = Requests::monthly()->whereDate('request_created_at', '=', $day)->where('service_id', $service->id)->whereIn('branch_id', ThirdParty::find($admin_user->id)->branches()->get(['branches.id'])->where('invoice_status', 0)->lazy())->get()->lazy()->sum('amount');
                    }
                    if ($admin_user->isRole('administrator')) {
                        $amount[] = Requests::monthly()->whereDate('request_created_at', '=', $day)->where('service_id', $service->id)->where('invoice_status', 0)->get()->sum('amount');
                    }
                }
                $requests_data[] = [$service->title, $amount, $line_color];
            }
            foreach ($requests_data as $req) {
                $new_requests = DashboardChart::create([
                    'admin_user_id' => $admin_user->id,
                    'chart_type' => DashboardChart::REUQESTS,
                    'title' => $req[0],
                    'counts' => $req[1],
                    'chart_color' => $req[2],
            ]);
            }
            if (!$admin_user->isRole('administrator')) {
                $user_branches = $admin_user->branches()->get(['branches.id'])->lazy();
                foreach ($user_branches as $branch) {
                    $branch_title = Branch::find($branch->id);
                    $line_color = 'rgb('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).')';
                    $amount = [];
                    foreach ($days as $day) {
                        $amount[] = Requests::monthly()->whereDate('request_created_at', '=', $day)->where('branch_id', $branch->id)->where('invoice_status', 0)->get()->lazy()->sum('amount');
                    }
                    $requests_data_branch[] = [$branch_title->title ?? '', $amount, $line_color];
                }
            }
            if ($admin_user->isRole('administrator')) {
                foreach (Branch::select(['id', 'title'])->get()->lazy() as $branch) {
                    $line_color = 'rgb('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).')';
                    $amount = [];
                    foreach ($days as $day) {
                        $amount[] = Requests::monthly()->whereDate('request_created_at', '=', $day)->where('invoice_status', 0)->where('branch_id', $branch->id)->get()->lazy()->sum('amount');
                    }
                    $requests_data_branch[] = [$branch->title, $amount, $line_color];
                }
            }
            foreach ($requests_data_branch as $req) {
                $new_requests = DashboardChart::create([
                    'admin_user_id' => $admin_user->id,
                    'chart_type' => DashboardChart::BRANCHS,
                    'title' => $req[0],
                    'counts' => $req[1],
                    'chart_color' => $req[2],
            ]);
            }
        }

        return 0;
    }
}
