<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\TransactionsExcel;
use App\Extensions\ExportTransactionReportXLS;
use App\Extensions\PrintTransReport;
use App\Models\Branch;
use App\Models\ThirdParty;
use App\Models\TransactionsHistory;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TaxReportController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Tax Report';
    public $grid_data;

    public function index(Content $content)
    {
        return $content
        ->header('Tax Report')
        ->description("<div id='my-content2-div'></div>")
        ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new TransactionsHistory());
        $grid->model()->where('transaction_status', 0);
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $grid->model()->whereIn('branch_id', $ids);
        }
        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
        });
        $grid->tools(function ($tools) use ($grid) {
            $tools->append(new PrintTransReport($grid));
            $tools->append(new ExportTransactionReportXLS($grid));
        });
        $grid->disableExport();
        $grid->model()->whereNotNull('tax_amount');
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            if (Admin::user()->isAdministrator()) {
                $filter->in('branch_id', 'Branch')->multipleSelect(Branch::pluck('title as text', 'id'));
            }
            if (!Admin::user()->isAdministrator() && Admin::user()->can('branch_filter')) {
                $filter->in('branch_id', 'Branch')->multipleSelect(ThirdParty::find(Admin::user()->id)->branches()->get()->pluck('title', 'id'));
            }
            $filter->between('created_at', 'Date')->date();
            $filter->scope('Tax In', 'Tax In')->where('transaction_type', TransactionsHistory::MONEY_IN);
            $filter->scope('Tax Out', 'Tax Out')->where('transaction_type', TransactionsHistory::MONEY_OUT);
        });
        $grid->column('id', __('Id'));
        $grid->column('trans_snl', 'Transaction No.')->display(function () {
            if ($this->request) {
                return $this->request->snl;
            } else {
                return $this->snl;
            }
        });
        $grid->column('trans_date', 'Date')->display(function () {
            if ($this->request) {
                return $this->request->request_created_at;
            } else {
                return date_format(date_create($this->created_at), 'Y-m-d');
            }
        });
        $grid->column('amount', 'Amount')->display(function ($amount) {
            if ($this->request) {
                return $amount;
            }

            return $amount - $this->tax_amount;
        });
        $grid->column('tax_amount', 'Tax Amount');
        $grid->footer(function ($query) {
            session(['footerquery' => $query->get()]);
            $total_requests_get = $query->where('transaction_type', TransactionsHistory::MONEY_IN);
            $total_expenses = session()->get('footerquery')->where('transaction_type', TransactionsHistory::MONEY_OUT);
            $total_requests_amount = $total_requests_get->sum('amount');
            $total_requests_tax = $total_requests_get->sum('tax_amount');
            $total_expensess_amount = $total_expenses->sum('amount');
            $total_expensess_tax = $total_expenses->sum('tax_amount');
            $net_tax = $total_requests_tax - $total_expensess_tax;
            $compact_data = ['total_requests_amount' => round($total_requests_amount, 2),
                        'total_requests_tax' => round($total_requests_tax, 2),
                        'total_expensess_amount' => round($total_expensess_amount, 2),
                        'total_expensess_tax' => round($total_expensess_tax, 2),
                        'net_tax' => round($net_tax, 2), ];
            session()->forget('footerquery');
            $view_data = view('admin.tax_report', compact('compact_data'));

            return new Box('Report Detail', $view_data);
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
        $show = new Show(TransactionsHistory::findOrFail($id));

        // $show->field('id', __('Id'));
        // $show->field('request_id', __('Request id'));
        // $show->field('customer_id', __('Customer id'));
        // $show->field('branch_id', __('Branch id'));
        // $show->field('title', __('Title'));
        // $show->field('payment_type_id', __('Payment type id'));
        // $show->field('amount', __('Amount'));
        // $show->field('paid_at', __('Paid at'));
        // $show->field('tax_amount', __('Tax amount'));
        // $show->field('payment_ref', __('Payment ref'));
        // $show->field('transaction_type', __('Transaction type'));
        // $show->field('snl', __('Snl'));
        // $show->field('qr_image', __('Qr image'));
        // $show->field('qr_string', __('Qr string'));
        // $show->field('created_at', __('Created at'));
        // $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new TransactionsHistory());

        // $form->number('request_id', __('Request id'));
        // $form->number('customer_id', __('Customer id'));
        // $form->number('branch_id', __('Branch id'));
        // $form->text('title', __('Title'));
        // $form->number('payment_type_id', __('Payment type id'));
        // $form->text('amount', __('Amount'));
        // $form->text('paid_at', __('Paid at'));
        // $form->text('tax_amount', __('Tax amount'));
        // $form->text('payment_ref', __('Payment ref'));
        // $form->number('transaction_type', __('Transaction type'));
        // $form->text('snl', __('Snl'));
        // $form->text('qr_image', __('Qr image'));
        // $form->text('qr_string', __('Qr string'));

        return $form;
    }

    public function print_pdf(Request $request)
    {
        // $collection = json_decode($request->collection);
        $filter_request = json_decode($request->collection);
        $trans = TransactionsHistory::select('*')->where('transaction_status', 0);
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $trans = TransactionsHistory::whereIn('branch_id', $ids);
        }
        if (isset($filter_request->branch_id) && count($filter_request->branch_id) > 0) {
            $trans = $trans->whereIn('branch_id', $filter_request->branch_id);
        }
        if (isset($filter_request->created_at)) {
            $end = $filter_request->request_created_at->end ?? Carbon::now()->format('Y-m-d');
            $trans = $trans->whereBetween('created_at', [$filter_request->created_at->start, $end]);
        }
        $collection = $trans->get()->lazy();

        if (count($collection) > 0) {
            $view = view('pdf.tax_full_report', compact('collection'));

            return response()->json(['status' => true, 'message' => 'Report Ready To print', 'view' => $view->render()]);
        } else {
            return response()->json(['status' => false, 'message' => 'No Data Found']);
        }
    }

    public function excel(Request $request)
    {
        $collection = json_decode($request->collection);
        $trans = TransactionsHistory::whereTransactionStatus(0)->select('*');
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $trans = TransactionsHistory::whereTransactionStatus(0)->whereIn('branch_id', $ids);
        }
        if (isset($collection->branch_id) && count($collection->branch_id) > 0) {
            $trans = $trans->whereIn('branch_id', $collection->branch_id);
        }
        if (isset($collection->created_at)) {
            $end = $collection->request_created_at->end ?? Carbon::now()->format('Y-m-d');
            $trans = $trans->whereBetween('created_at', [$collection->created_at->start, $end]);
        }
        $filter_trans = $trans->get()->lazy();
        if (count($filter_trans) > 0) {
            Excel::store(new TransactionsExcel($filter_trans), 'tax_report.xlsx', 'admin');

            return response()->json(['status' => true, 'message' => 'Report Ready To export']);
        } else {
            return response()->json(['status' => false, 'message' => 'No Data Found']);
        }
    }
}
