<?php

namespace App\Admin\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Requests;
use App\Models\TaxType;
use App\Models\ThirdParty;
use App\Models\TransactionsHistory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TransPayController extends AdminController
{
    use HasResourceActions;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'TransactionsHistory';

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        if (session()->get('newurl3')) {
            $this->url_new3 = session()->get('newurl3');
            session()->forget('newurl3');
            session()->regenerate();
            $trans_script = <<<SCRIPT
print_page_view("{$this->url_new3}");
SCRIPT;
            Admin::script($trans_script);
        }

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
            ->body($this->form()->edit($id));
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
            ->body($this->form());
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
        $grid->model()->where('transaction_type', TransactionsHistory::MONEY_OUT);
        $grid->disableActions();
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $grid->model()->whereIn('branch_id', $ids);
        }
        // $grid->disableCreateButton();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        $grid->disableExport();
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
        $grid->title('Transaction Title');
        $grid->payment_type_id('Payment Type')->using([Requests::PAYMENT_TYPE_CASH => 'Cash', Requests::PAYMENT_TYPE_BANK => 'Bank']);
        $grid->amount('Amount');
        $grid->paid_at('Transaction Paid At');
        // $grid->tax_amount('tax_amount');
        // $grid->payment_ref('Transaction Ref.');
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
        // $show->request_id('request_id');
        $show->title('Title');
        // $show->payment_type_id('payment_type_id');
        $show->amount('Amount');
        $show->paid_at('Paid at');
        // $show->tax_amount('tax_amount');
        // $show->payment_ref('payment_ref');
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
        if (!Admin::user()->isAdministrator()) {
            $branches = ThirdParty::find(Admin::user()->id)->branches()->pluck('branches.title as text', 'branches.id');
            $form->select('branch_id', 'Branch')
            ->options($branches)->rules('required')->default(array_key_first($branches->toArray()));
        } else {
            $form->select('branch_id', 'Branch')->options(Branch::all()->pluck('title', 'id'))->rules('required');
        }
        // $form->display('ID');|
        $form->hidden('transaction_type')->value(TransactionsHistory::MONEY_OUT);
        // $form->select('customer_id', 'Account Number')->options(function ($id) {
        //     $customer = Customer::find($id);
        //     if ($customer) {
        //         return [$customer->id => $customer->snl];
        //     }
        // })->rules('required|integer')->default(0)->ajax('/api/payment_customers');
        // $form->text('request_id', 'request_id');
        $form->text('title', 'Title')->rules('required');
        $form->select('payment_type_id', 'Payment Type')->options([Requests::PAYMENT_TYPE_CASH => 'Cash', Requests::PAYMENT_TYPE_BANK => 'Bank'])->rules('required');
        $form->select('is_tax_include', ' Tax Included')->options([0 => 'Without Vat', 1 => 'Included Vat', 2 => 'Not Included Vat'])->rules('required');
        $form->select('tax_type_id', 'Tax Type')->options(function ($id) {
            $tax_type_list = TaxType::pluck('title as text', 'id');

            return $tax_type_list;
        })->rules('required');
        // $form->text('payment_ref', 'Payment Reference')->rules('required_if:payment_type_id,3', ['payment_ref.required_if' => 'Add Reference Number For Bank Payment']);
        $form->currency('amount', 'amount')->rules('required');
        $form->textarea('notes', 'Notes')->rules('max:254');
        $form->hidden('paid_at', 'paid_at')->value(\Carbon\Carbon::now());

        // $form->display(trans('admin.created_at'));
        // $form->saved(function (Form $form) {
        //     $customer = $form->model()->customer;
        //     if ($customer && $form->model()->transaction_type == TransactionsHistory::MONEY_IN) {
        //         $customer_transactions = $customer->transaction_history()->whereNotNull('request_id')->whereNull('paid_at')->get();
        //         // dd($customer_transactions);
        //         $amount = doubleval($customer->creidt) + $form->model()->amount;
        //         // dd($amount);
        //         foreach ($customer_transactions as $customer_transaction) {
        //             $request = $customer_transaction->request;
        //             // dd($customer_transaction, $customer_transaction->request);
        //             // dd($customer_transaction, $customer_transaction->request->amount, $customer_transaction->request);
        //             if ($amount > 0 && $request && $amount >= $request->amount) {
        //                 $amount -= $customer_transaction->amount;
        //                 $customer->debit -= $customer_transaction->amount;
        //                 $customer_transaction->paid_at = $form->model()->created_at;
        //                 $customer_transaction->save();
        //                 $request->payment_status_id = Requests::PAYMENT_STATUS_PAID;
        //                 $request->save();
        //             }
        //         }
        //         $customer->creidt = $amount;
        //         $customer->save();
        //     }
        // });
        $form->saved(function ($form) {
            // $form->model()->create_qr_code();
            $tax_amount = TaxType::find($form->model()->tax_type_id);
            $form->model()->snl = $form->model()->branch_id ? Branch::find($form->model()->branch_id)->get_transaction_code().$form->model()->id : 'TRA00'.$form->model()->id;
            if (TransactionsHistory::Without_Vat == $form->model()->is_tax_include) {
                $form->model()->tax_amount = 0;
                $form->model()->save();
            } elseif (TransactionsHistory::is_vat_include_true == $form->model()->is_tax_include) {
                if ($tax_amount) {
                    $tax_amount_percentage = $tax_amount->amount;
                    $tax_amount_val = 1 + ($tax_amount_percentage / 100);
                    $amount = $form->model()->amount / $tax_amount_val;
                    $trans_tax_amount = $form->model()->amount - $amount;
                    $form->model()->amount = round($amount, 2);
                    $form->model()->tax_amount = round($trans_tax_amount, 2);
                    $form->model()->save();
                }
            } elseif (TransactionsHistory::is_vat_include_false == $form->model()->is_tax_include) {
                if ($tax_amount) {
                    $tax_amount_percentage = $tax_amount->amount;
                    $tax_amount_val = 1 + ($tax_amount_percentage / 100);
                    $amount = $form->model()->amount * $tax_amount_val;
                    $trans_tax_amount = $amount - $form->model()->amount;
                    $form->model()->amount = round($amount, 2);
                    $form->model()->tax_amount = round($trans_tax_amount, 2);
                    $form->model()->save();
                }
            }
            if ($form->isCreating()) {
                $data = $form->model();
                session(['newurl3' => url('payment_voucher/'.$data->id)]);
            }
        });

        return $form;
    }
}
