<?php

namespace App\Admin\Controllers;

use App\Models\Branch;
use App\Models\Requests;
use App\Models\ThirdParty;
use App\Models\TransactionsHistory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NetIncomeController extends AdminController
{
    public $url_new;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Daily Report';

    public function index(Content $content)
    {
        $grouped_requests = Requests::where('invoice_status', 0)->select(
                DB::raw('count(service_id) as count_service,
                service_id,
                branch_id,
                embassy_id,
                staff_id,
                count(profession_id) as count_profession,
                profession_id,
                sum(amount) as sum_amount,
                sum(service_charge) as sum_service_charge,
                sum(embassy_charge) as sum_embassy_charge,
                sum(tax_amount) as sum_tax_amount'));
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $grouped_requests = $grouped_requests->whereIn('branch_id', $ids);
        }
        $expense = TransactionsHistory::whereTransactionStatus(0)->whereTransactionType(TransactionsHistory::MONEY_OUT)->select(
                DB::raw('sum(amount) as sum_amount, sum(tax_amount) as sum_tax_amount , transaction_type'));
        $date_range_request = explode(' - ', request()->daterange);

        $branch_id = request()->branch_id;
        $service_provider = request()->service_provider;
        $staff_id = request()->username;
        $service_id = request()->service_id;
        if (isset($service_id)) {
            $grouped_requests = $grouped_requests->whereServiceId($service_id);
        }
        if (isset($staff_id)) {
            $grouped_requests = $grouped_requests->whereStaffId($staff_id);
        }
        if (isset($branch_id)) {
            $grouped_requests = $grouped_requests->whereBranchId($branch_id);
            $expense = $expense->whereBranchId($branch_id);
        }
        if (isset($service_provider)) {
            $grouped_requests = $grouped_requests->whereEmbassyId($service_provider);
        }
        if (is_array($date_range_request) && count($date_range_request) > 1) {
            $grouped_requests = $grouped_requests->whereBetween('request_created_at', [$date_range_request[0], $date_range_request[1]]);
            $expense = $expense->whereBetween('created_at', [$date_range_request[0], $date_range_request[1]]);
        }

        $grouped_requests = $grouped_requests->groupBy('branch_id', 'embassy_id', 'service_id', 'profession_id', 'staff_id')->get()->sortBy('service_id')->sortBy('branch_id');
        $net_expense = 0;
        $expense = $expense->groupBy('transaction_type')->first();
        // dd($net_expense);
        $net_income = view('admin.net_income', compact('grouped_requests', 'expense'));
        $header_title = request()->branch_id ? Branch::find(request()->branch_id)->title : 'Net Income';

        return $content
        ->header('Daily Report')
        // ->description('')
        ->body(new Box('Daily Report', $net_income));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Requests());
        $grid->model()->where('invoice_status', 0);
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableActions();
        $grid->disableColumnSelector();
        $grid->disableCreateButton();
        $grid->disablePagination();
        if (!Admin::user()->isAdministrator()) {
            if (Admin::user()->can('see_all')) {
                $grid->model()->orderBy('id', 'desc');
            } else {
                $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
                $ids = [];
                foreach ($branches_ids as $branch_id) {
                    $ids[] = $branch_id['id'];
                }
                $grid->model()->whereIn('branch_id', $ids);
            }
        }
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->column(1 / 3, function ($filter) {
                if (Admin::user()->isAdministrator()) {
                    $filter->in('branch_id', 'Branch')->multipleSelect(Branch::pluck('title as text', 'id'));
                }
                if (!Admin::user()->isAdministrator()) {
                    $filter->in('branch_id', 'Branch')->multipleSelect(ThirdParty::find(Admin::user()->id)->branches()->get()->pluck('title', 'id'));
                }
            });
        });

        $grid->header(function ($query) {
            $grouped_requests = $query->where('invoice_status', 0)->select(
                DB::raw('count(service_id) as count_service,
                service_id,
                count(profession_id) as count_profession,
                profession_id,
                sum(amount) as sum_amount,
                sum(service_charge) as sum_service_charge,
                sum(embassy_charge) as sum_embassy_charge,
                sum(tax_amount) as sum_tax_amount'))
                ->groupBy('service_id', 'profession_id')->get();
            $net_income = view('admin.net_income', compact('grouped_requests'));

            return new Box('Net Income', $net_income);
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Requests::findOrFail($id));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Requests());

        return $form;
    }

    public function filter(Request $request)
    {
        $grouped_requests = Requests::where('invoice_status', 0)->select(
                DB::raw('count(service_id) as count_service,
                service_id,
                branch_id,
                embassy_id,
                staff_id,
                count(profession_id) as count_profession,
                profession_id,
                sum(amount) as sum_amount,
                sum(service_charge) as sum_service_charge,
                sum(embassy_charge) as sum_embassy_charge,
                sum(tax_amount) as sum_tax_amount'));
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $grouped_requests = $grouped_requests->whereIn('branch_id', $ids);
        }
        $expense = TransactionsHistory::whereTransactionStatus(0)->whereTransactionType(TransactionsHistory::MONEY_OUT)->select(
                DB::raw('sum(amount) as sum_amount, sum(tax_amount) as sum_tax_amount , transaction_type'));
        $date_range_request = explode(' - ', request()->daterange);
        $branch_id = request()->branch_id;
        $service_provider = request()->service_provider;
        $staff_id = request()->username;
        $service_id = request()->service_id;

        if (isset($branch_id)) {
            $grouped_requests = $grouped_requests->whereBranchId($branch_id);
            $expense = $expense->whereBranchId($branch_id);
        }
        if (isset($service_provider)) {
            $grouped_requests = $grouped_requests->whereEmbassyId($service_provider);
        }
        if (isset($staff_id)) {
            $grouped_requests = $grouped_requests->whereStaffId($staff_id);
        }
        if (isset($service_id)) {
            $grouped_requests = $grouped_requests->whereServiceId($service_id);
        }
        if (is_array($date_range_request) && count($date_range_request) > 1) {
            $grouped_requests = $grouped_requests->whereBetween('request_created_at', [$date_range_request[0], $date_range_request[1]]);
            $expense = $expense->whereBetween('created_at', [$date_range_request[0], $date_range_request[1]]);
        }
        $grouped_requests = $grouped_requests->groupBy('branch_id', 'embassy_id', 'service_id', 'profession_id', 'staff_id')->get()->sortBy('service_id')->sortBy('branch_id');
        $net_expense = 0;
        $expense = $expense->groupBy('transaction_type')->first();

        return view('pdf.net_income', compact('grouped_requests', 'expense'));
    }
}
