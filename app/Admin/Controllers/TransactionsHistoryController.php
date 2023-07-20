<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Requests;
use App\Models\ThirdParty;
use App\Models\TransactionsHistory;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TransactionsHistoryController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Transactions ')
            ->description(trans('admin.description'))
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('admin.detail'))
            ->description(trans('admin.description'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.edit'))
            ->description(trans('admin.description'))
            ->body('');
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.create'))
            ->description(trans('admin.description'))
            ->body('');
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
        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        $grid->disableExport();
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $grid->model()->whereIn('branch_id', $ids);
        }
        $grid->filter(function ($filter) {
            $filter->column(1 / 2, function ($filter) {
                $filter->equal('payment_type_id', 'Payment Type')->select([
                Requests::PAYMENT_TYPE_CASH => 'Cash', Requests::PAYMENT_TYPE_LATER => 'Credit', Requests::PAYMENT_TYPE_BANK => 'Bank', ]);
                if (Admin::user()->isAdministrator()) {
                    $filter->in('branch_id', 'Branch')->multipleSelect(Branch::pluck('title as text', 'id'));
                }
                if (!Admin::user()->isAdministrator() && Admin::user()->can('branch_filter')) {
                    $filter->in('branch_id', 'Branch')->multipleSelect(ThirdParty::find(Admin::user()->id)->branches()->get()->pluck('title', 'id'));
                }
            });
            $filter->column(1 / 2, function ($filter) {
                $filter->between('paid_at', 'Transaction Date')->date();
            });
            $filter->disableIdFilter();
        });

        $grid->transaction_type('Transaction Type')->display(function ($transaction_type) {
            $type = '';
            if (TransactionsHistory::MONEY_IN == $transaction_type) {
                if (is_null($this->request_id)) {
                    $type = "<span class='btn btn-success'>Received Voucher</span>";
                } else {
                    $type = "<span class='btn btn-primary'>Request</span>";
                }
            }
            if (TransactionsHistory::MONEY_OUT == $transaction_type) {
                $type = "<span class='btn btn-info'>Payment Voucher</span>";
            }

            return $type;
        });
        $grid->request('Request SNL')->display(function ($req) {
            return $req ? '<a href='.url('admin/requests/'.$req['id']).'>'.$req['snl'].'</a>' : ''; //$req->request->snl;
        });
        // $grid->request('Transaction Title')->display(function ($req_title) {
        //     // dd($req_title);
        //     // return "<a href=".url('admin/requests/'.$req['id']).">".$req['snl']."</a>";//$req->request->snl;
        // });
        $grid->title('Transaction Title');
        $grid->payment_type_id('Payment Type')->using([
                Requests::PAYMENT_TYPE_CASH => 'Cash', Requests::PAYMENT_TYPE_LATER => 'Credit', Requests::PAYMENT_TYPE_BANK => 'Bank', ]);
        $grid->amount('Amount');
        $grid->paid_at('Transaction Paid At');
        // $grid->tax_amount('tax_amount');
        $grid->payment_ref('Transaction Ref.');
        $grid->created_at(trans('admin.created_at'));

        // $grid->updated_at(trans('admin.updated_at'));

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

        $show->id('ID');
        $show->request_id('request_id');
        $show->title('title');
        $show->payment_type_id('payment_type_id');
        $show->amount('amount');
        $show->paid_at('paid_at');
        $show->tax_amount('tax_amount');
        $show->payment_ref('payment_ref');
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

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

        // $form->display('ID');
        $transaction_type = TransactionsHistory::MONEY_IN;
        if (isset(request()->type)) {
            $transaction_type = intval(request()->type);
        }
        $form->hidden('transaction_type')->value($transaction_type);
        $form->select('customer_id', 'Account Number')->options(function ($id) {
            $customer = Customer::find($id);
            if ($customer) {
                return [$customer->id => $customer->snl];
            }
        })->rules('required|integer')->default(0)->ajax('/api/payment_customers');
        // $form->text('request_id', 'request_id');
        $form->text('title', 'Title')->rules('required');
        $form->select('payment_type_id', 'Payment Type')->options([
                Requests::PAYMENT_TYPE_CASH => 'نقدا', Requests::PAYMENT_TYPE_LATER => 'آجل', Requests::PAYMENT_TYPE_BANK => 'بنك', ])->rules('required');
        $form->text('payment_ref', 'Payment Reference')->rules('required_if:payment_type_id,3', ['payment_ref.required_if' => 'Add Reference Number For Bank Payment']);
        $form->currency('amount', 'amount')->rules('required');
        // $form->text('paid_at', 'paid_at');

        // $form->display(trans('admin.created_at'));
        $form->saved(function (Form $form) {
            $customer = $form->model()->customer;
            if ($customer && TransactionsHistory::MONEY_IN == $form->model()->transaction_type) {
                $customer_transactions = $customer->transaction_history()->whereNotNull('request_id')->whereNull('paid_at')->get();
                // dd($customer_transactions);
                $amount = doubleval($customer->creidt) + $form->model()->amount;
                // dd($amount);
                foreach ($customer_transactions as $customer_transaction) {
                    $request = $customer_transaction->request;
                    // dd($customer_transaction, $customer_transaction->request);
                    // dd($customer_transaction, $customer_transaction->request->amount, $customer_transaction->request);
                    if ($amount > 0 && $request && $amount >= $request->amount) {
                        $amount -= $customer_transaction->amount;
                        $customer->debit -= $customer_transaction->amount;
                        $customer_transaction->paid_at = $form->model()->created_at;
                        $customer_transaction->save();
                        $request->payment_status_id = Requests::PAYMENT_STATUS_PAID;
                        $request->save();
                    }
                }
                $customer->creidt = $amount;
                $customer->save();
            }
        });

        return $form;
    }
}
