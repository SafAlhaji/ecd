<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Requests\BatchRequests;
use App\Admin\Actions\Requests\ExportPDF;
use App\Admin\Actions\Requests\ImportRequests;
use App\Admin\Actions\Requests\PrintRequestPdf;
use App\Admin\Actions\Requests\RequestsChangeStatus;
use App\Admin\Actions\Requests\SubmitDraftBatch;
use App\Admin\Extensions\ReFundRequest;
use App\Admin\Extensions\RequestsExcel;
use App\Extensions\DraftBatch;
use App\Extensions\PrintPdf;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\OldPassportNumbers;
use App\Models\Profession;
use App\Models\Requests;
use App\Models\RequestStatus;
use App\Models\RequestType;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\ServiceType;
use App\Models\SmsMessage;
use App\Models\ThirdParty;
use App\Models\TransactionsHistory;
use App\Traits\SmsTraits;
use Carbon\Carbon;
use DB;
use Dompdf\Options;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Displayers\Actions;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RequestsController extends Controller
{
    use HasResourceActions;
    use SmsTraits;
    public $customer;
    public $request_type_id = 0;
    private $response = [];
    public $statusCode = 200;
    public $success_data = [];
    public $data_list = [];
    public $lang_id;
    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public $url_new;
    public $req_id;

    public function index(Content $content)
    {
        $passport_number = <<<SCRIPT
$('tr').each(function(){
var req_id = $(this).data('key');
var elemtn_data = document.getElementById(req_id);
if(elemtn_data != null){
elemtn_data.getElementsByTagName('a')[0].addEventListener('click', function() {
    var elem_attributes = elemtn_data.getElementsByTagName('a')[0].attributes;
    setTimeout(
  function()
  {
    if(elem_attributes['aria-describedby'] != undefined){
        var slelect_id = elem_attributes['aria-describedby'].value
$(document).off('change').on('change', '#'+slelect_id+' select', function(){
        var status_id = $(this).find(":selected").val();
        if(status_id == 4){
        $.ajax({
        method: 'get',
        url: './check_service_update_id?req_id=' + req_id,
        dataType : 'json',
        data:$(this).serialize(),
        success: function (data) {
        if(data.status){
        var txt;
        var passport_number = prompt("Please enter New Passport Id:", "Passport Id");
        if (passport_number == null || passport_number == "" ||  passport_number == "Passport Id") {
        toastr.error('Status Did not updated');
        $.pjax.reload('#pjax-container');
        } else {
        $.ajax({
        method: 'get',
        url: './update_passport?passport_number='+ passport_number + '&req_id=' + req_id,
        dataType : 'json',
        data:$(this).serialize(),
        success: function (data) {
            if(data.status){
                $.pjax.reload('#pjax-container');
                toastr.success(data.message);
            }else{
                $.pjax.reload('#pjax-container');
                toastr.error(data.message);
            }

        }
        });
        }
        }
        }
        });
        }
});
    }
  }, 500)


}, false);
}
});
SCRIPT;
        Admin::script($passport_number);
        if (session()->get('newurl')) {
            $this->url_new = session()->get('newurl');
            session()->forget('newurl');
            session()->regenerate();
            $script = <<<SCRIPT
print_page_view("{$this->url_new}");
SCRIPT;
            Admin::script($script);
        }

        return $content
        ->header('Requests')
        ->description(' ')
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
        ->header('Request Details')
        ->description(' ')
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
        ->header('Edit Request Details')
        ->description(' ')
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
        ->header('Create New Request')
        ->description(' ')
        ->body($this->form());
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
        $grid->setActionClass(Actions::class);
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
        $grid->snl(trans('requests.fields.snl'))->sortable();
        $grid->request_created_at('Request Date')->display(function ($request_created_at) {
            return Carbon::parse($request_created_at)->format('d-m-Y');
        })->sortable();
        $grid->customer()->full_name(trans('customer.fields.full_name'));
        $grid->service()->title('Service');
        $grid->customer()->passport_number('Passport No.')->sortable();
        $grid->customer()->old_passport_numbers('Old Passport No.')->display(function ($old_passport_numbers) {
            if ($old_passport_numbers && count($old_passport_numbers) > 0) {
                return collect($old_passport_numbers)->last();
            }
        });
        // $grid->request_status_id('Status')->editable('select', RequestStatus::request_status)->sortable();
        $grid->column('request_status_id')->display(function ($title, $column) {
            // If the value of the status field of this column is equal to 1, directly display the title field
            if (RequestStatus::COMPELETED == $this->request_status_id) {
                return "<span class='btn btn-xs btn-success'>COMPELETED</span>";
            }

            // Otherwise it is displayed as editable
            return $column->editable('select', RequestStatus::request_status);
        })->sortable();
        $grid->completed_at('Completed Date')->display(function () {
            return $this->completed_at ?? '';
        });
        $grid->embassy_serial_number('Enrollment no.')->editable()->sortable(); //('select', RequestStatus::request_status);
        $grid->renew_note('Renewing Note')->editable()->sortable(); //('select', RequestStatus::request_status);
        $grid->embassy()->title('Provider');
        $grid->branch()->title('Branch');

        $grid->perPages([20, 30, 40, 'all']);
        $query_parameter = request()->query() ?? [];
        // dd($query_parameter);
        if (count($query_parameter) > 0 && isset($query_parameter['per_page']) && 'all' == $query_parameter['per_page']) {
            $grid->disablePagination();
        }

        // $grid->qr_image('Request QrCode')->image(url($path = 'uploads').'/', 50, 50);
        // $grid->qr_string()->qrcode();
        // $grid->column('qr_string')->display(function ($qr_string, $column) {
        //     // $link = url('trackRequest?qr=');
        //     // $qr_link = $link.$qr_string;
        //     // If the value of the status field of this column is equal to 1, directly display the title field
        //     if (1 == $this->status) {
        //         return $qr_string;
        //     }

        //     // Otherwise it is displayed as editable
        //     return $column->editable();
        // });

        $grid->filter(function ($filter) {
            $filter->column(1 / 3, function ($filter) {
                $filter->where(function ($query) {
                    $query->whereHas('customer', function ($query) {
                        $query->where('phone_number', $this->input)
                        ->orwhere('alt_phone_number', $this->input)
                        ->orwhere('passport_number', $this->input)
                        ->orwhereJsonContains('old_passport_numbers', $this->input);
                    })->orwhere('snl', $this->input)->orwhere('embassy_serial_number', $this->input);
                }, 'Search')->placeholder('Request no. Customer Phone ,Passport No.');
                $filter->in('service_id', 'Service')->multipleSelect(Service::pluck('title', 'id'));
                $filter->between('request_created_at', 'Request Date')->date();
            });
            $filter->column(1 / 3, function ($filter) {
                $filter->in('profession_id', 'Profession')->multipleSelect(Profession::pluck('title as text', 'id'));
                if (Admin::user()->isAdministrator()) {
                    $filter->in('branch_id', 'Branch')->multipleSelect(Branch::pluck('title as text', 'id'));
                    $filter->in('staff_id', 'UserName')->multipleSelect(ThirdParty::pluck('username as text', 'id'));
                }
                if (!Admin::user()->isAdministrator() && Admin::user()->can('branch_filter')) {
                    $filter->in('branch_id', 'Branch')->multipleSelect(ThirdParty::find(Admin::user()->id)->branches()->get()->pluck('title', 'id'));
                    $branches_id = ThirdParty::find(Admin::user()->id)->branches()->pluck('branches.id');
                    $users_id = DB::table('admin_users_branches')->whereIn('branch_id', $branches_id)->get('admin_user_id');
                    foreach ($users_id as $u_id) {
                        $users_id_arr[] = $u_id->admin_user_id;
                    }
                    if (count($users_id_arr) > 0) {
                        $users = ThirdParty::whereIn('id', $users_id_arr)->pluck('username as text', 'id');
                    } else {
                        $users = ThirdParty::pluck('username as text', 'id');
                    }
                    $filter->in('embassy_id', 'Service Provider')->multipleSelect(ServiceProvider::whereHas('branches', function ($query) {
                        $query->whereIn('branch_id', ThirdParty::find(Admin::user()->id)->branches()->pluck('branches.id'));
                    })->pluck('title as text', 'id'));
                    $filter->in('staff_id', 'UserName')->multipleSelect($users);
                }
            });
            $filter->column(1 / 3, function ($filter) {
                $filter->in('request_status_id', 'Status')->multipleSelect(RequestStatus::request_status);
                if (!Admin::user()->isAdministrator()) {
                    $filter->in('batch_id', 'Batch Number')->multipleSelect(Batch::whereIn('branch_id', ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id']))->pluck('title as text', 'id'));
                    $filter->in('embassy_id', 'Service Provider')->multipleSelect(ServiceProvider::whereHas('branches', function ($query) {
                        $query->whereIn('branch_id', ThirdParty::find(Admin::user()->id)->branches()->pluck('branches.id'));
                    })->pluck('title as text', 'id'));
                }
                if (Admin::user()->isAdministrator()) {
                    $filter->in('batch_id', 'Batch Number')->multipleSelect(Batch::pluck('title as text', 'id'));
                    $filter->in('embassy_id', 'Service Provider')->multipleSelect(ServiceProvider::pluck('title as text', 'id'));
                }
            });
            $filter->disableIdFilter();
            // SCOPE
            $filter->scope('All', 'All')->where('request_status_id', '>=', RequestStatus::PENDING);
            $filter->scope('PENDING', 'PENDING')->where('request_status_id', RequestStatus::PENDING);
            $filter->scope('Preparing_to_Send_Embassy', 'Processing')->where('request_status_id', RequestStatus::Preparing_to_Send_Embassy);
            $filter->scope('IN_EMBASSY', 'IN EMBASSY')->where('request_status_id', RequestStatus::IN_EMBASSY);
            $filter->scope('At_Office', 'At Office')->where('request_status_id', RequestStatus::At_Office);
            $filter->scope('COMPELETED', 'COMPELETED')->where('request_status_id', RequestStatus::COMPELETED);
        });
        $grid->actions(function ($actions) {
            // $actions->add(new PrintRequestPdf);
            // $actions->getKey();

            // if (!Admin::user()->can('delete_request')) {
            $actions->disableDelete();
            // }
            if (1 == $this->row->invoice_status) {
                $actions->disableEdit();
            }
            if (0 == $this->row->invoice_status) {
                if (!Admin::user()->can('edit_requests')) {
                    $actions->disableEdit();
                }
                $actions->append(new PrintPdf($actions->getKey(), '/request_receipt/'.$actions->getKey()));
                if (Admin::user()->can('refund_request')) {
                    $actions->append(new ReFundRequest($actions->getKey(), '/refund_request/'.$actions->getKey()));
                }

                if (Admin::user()->can('create_draft')) {
                    $actions->append(new DraftBatch($actions->getKey(), '/draft_batch?req_id='.$actions->getKey()));
                }
            }
        });
        $grid->batchActions(function ($batch) {
            $batch->add(new BatchRequests());
            $batch->add(new RequestsChangeStatus());
            $batch->add(new ExportPDF());
        });
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new ImportRequests());
            // $tools->append(new SubmitDraftBatch());
        });
        $grid->exporter(new RequestsExcel());

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

        $show->snl(trans('requests.fields.snl'));
        $show->customer()->full_name()->as(function ($value) {
            return $value->full_name ?? '';
        });
        $show->customer()->passport_number('Passport No.')->as(function ($value) {
            return $value->passport_number ?? '';
        });
        $show->customer()->phone_number('Phone No.')->as(function ($value) {
            return $value->phone_number ?? '';
        });
        $show->service('Service')->as(function ($value) {
            return $value->title ?? '';
        });
        $show->service_type('Service Location')->as(function ($value) {
            return $value->title ?? '';
        });
        $show->profession('Profession Title')->as(function ($value) {
            return $value->title ?? '';
        });
        $show->embassy('Embassy')->as(function ($value) {
            if ($value) {
                return $value->title ?? '';
            }
        });
        $show->branch()->as(function ($value) {
            if ($value) {
                return $value->title ?? '';
            }
        });
        $show->panel()
    ->tools(function ($tools) {
        $tools->disableEdit();
        $tools->disableList();
        $tools->disableDelete();
    });
        $show->service_charge('Service Charge');
        $show->embassy_charge('Embassy Charge');
        $show->amount('Total');
        $show->request_status_id('Status')->as(function ($value) {
            $status = RequestStatus::request_status[$value] ?? '';
            if (RequestStatus::PENDING == $value) {
                return "<span class='label label-danger'>{$status}</span>";
            }
            if (RequestStatus::IN_EMBASSY == $value) {
                return "<span class='label label-warning'>{$status}</span>";
            }
            if (RequestStatus::COMPELETED == $value) {
                return "<span class='label label-success'>{$status}</span>";
            }

            return RequestStatus::request_status[$value] ?? '';
        })->label('default');
        $show->qr_image('qr_image')->image(url($path = 'uploads').'/', 100, 100);
        $show->embassy_serial_number('Enrollment No.');
        $show->renew_note('Renew Note');

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
        $script = <<<SCRIPT
var amount = document.getElementsByClassName("amount");
var service_charge = document.getElementsByClassName("service_charge");
var embassy_charge = document.getElementsByClassName("embassy_charge");
var tax_amount = document.getElementsByClassName("tax_amount");
$('.service_charge').keyup(function () {
update_amount(service_charge[0].value,embassy_charge[0].value,tax_amount[0].value);
});
$('.embassy_charge').keyup(function () {
update_amount(service_charge[0].value,embassy_charge[0].value,tax_amount[0].value);
});
$('.tax_amount').keyup(function () {
update_amount(service_charge[0].value,embassy_charge[0].value,tax_amount[0].value);
});
function update_amount(service_charge,embassy_charge,tax_amount) {
amount[0].value =parseFloat(service_charge.replace(",",""))   + parseFloat(embassy_charge.replace(",",""))   + parseFloat(tax_amount.replace(",",""))   ;
}
SCRIPT;
        Admin::script($script);
        $form->setWidth(8, 4);
        $form->hidden('embassy_serial_number');
        $form->hidden('request_status_id')->value(RequestStatus::PENDING);
        $form->column(1 / 3, function ($form) {
            // $form->select('request_type_id', 'Request Type')
            // ->options(ThirdParty::find(Admin::user()->id)->branches()->pluck('branches.title as text', 'branches.id'))->rules('required');
            $form->select('request_type_id', 'Request Type')->options(function ($id) {
                $request_type = RequestType::find($id);
                $this->request_type_id = $id;
                if ($request_type) {
                    return [$request_type->id => $request_type->title];
                } else {
                    return RequestType::pluck('title as text', 'id');
                }
            })->rules('required')
        ->load(
            'service_id',
            '../../get_services_by_request_type',
            'id',
            'title'
        );

            $branches = ThirdParty::find(Admin::user()->id)->branches()->pluck('branches.title as text', 'branches.id');
            if (!Admin::user()->isAdministrator()) {
                $form->select('branch_id', 'Branch')
            ->options($branches)->rules('required')->default(array_key_first($branches->toArray()));
            } else {
                $form->select('branch_id', 'Branch')->options(Branch::all()->pluck('title', 'id'))->rules('required');
            }
            $form->select('embassy_id', 'Service Provider')->options(function ($id) {
                $service_provider = ServiceProvider::find($id);
                if ($service_provider) {
                    return [$service_provider->id => $service_provider->title];
                }
            })->rules('required');
            if ($form->isCreating()) {
                $form->date('request_created_at', 'Request Date')->default(\Carbon\Carbon::now()->format('Y-m-d'))->width(300); //->format('DD-MM-YYYY');
            }
            // else {
            //     $form->date('request_created_at', 'Request Date')->width(300);
            // }
            if ($form->isEditing()) {
                $form->select('service_id', trans('service.index.header'))->options(function ($id) {
                    return Service::pluck('title as text', 'id');
                })->rules('required')
            // ->loads(
            //     ['service_type_id','profession_id'],
            //     ['./../get_servicedetails_types','./../get_servicedetails_professions'],
            //     'id',
            //     'title'
            // );
            ->load(
                'service_type_id',
                '../../get_servicedetails_types',
                'id',
                'title'
            );
            }
            if ($form->isCreating()) {
                $form->select('service_id', trans('service.index.header'))->options(function ($id) {
                    return Service::pluck('title as text', 'id');
                })->rules('required')
            // ->loads(
            //     ['service_type_id','profession_id'],
            //     ['./../get_servicedetails_types','./../get_servicedetails_professions'],
            //     'id',
            //     'title'
            // );
            ->load(
                'service_type_id',
                './../get_servicedetails_types',
                'id',
                'title'
            );
            }

            $form->select('service_type_id', 'Service Location')->options(function ($id) {
                $service_type = ServiceType::find($id);
                if ($service_type) {
                    return [$service_type->id => $service_type->title];
                }
            })->rules('required|integer');
            // dd($form->isEditing() && RequestType::Embassy == Requests::find(request()->segments()[2])->request_type_id);
            if ($form->isEditing() && RequestType::Embassy == Requests::find(request()->segments()[2])->request_type_id) {
                $form->select('profession_id', trans('customer.fields.profession'))->options(function ($id) {
                    $profession = Profession::find($id);
                    if ($profession) {
                        return [$profession->id => $profession->title];
                    }
                })->rules('required_if:request_type_id,1')->default(0)->style('display', 'block');
            }
            if ($form->isCreating()) {
                $form->select('profession_id', trans('customer.fields.profession'))->options(function ($id) {
                    return Profession::pluck('title as text', 'id');
                    // $profession = Profession::pluck('title as text', 'id');
                    // if ($profession) {
                    //     return [$profession->id => $profession->title];
                    // }
                })->rules('required_if:request_type_id,1')->default(0);
            }
        });
        $form->column(1 / 3, function ($form) {
            $form->select('customer_id', 'Search')->options(function ($id) {
                $customer_list[0] = 'New Customer';
                $customer = Customer::find($id);
                if ($customer) {
                    return [$customer->id => $customer->passport_number];
                } else {
                    return $customer_list;
                }
            })->rules('required|integer')->default(0)->ajax('../../api/customers')->config('minimumInputLength', 3);
            if ($form->isEditing()) {
                $customer = Requests::find(\request()->route()->request)->customer;
                // $form->text('customer_snl', 'Request No.')->default($customer->snl)->readonly();
                $form->text('customer_full_name', trans('customer.fields.full_name'))->rules('min:3|max:190|required')->default($customer->full_name);
                $form->mobile('customer_phone_number', trans('customer.fields.phone_number'))->rules('min:3|max:190|required')->options(['mask' => '9999999999'])->default($customer->phone_number)->width(300);
                $form->mobile('customer_alt_phone_number', 'Other Phone')->rules('nullable')->options(['mask' => '9999999999'])->default($customer->alt_phone_number)->width(300);
                $form->text('customer_passport_number', 'Passport No')->rules('min:10|max:10|required|unique:customer,passport_number,{{customer_id}},deleted_at')->default($customer->passport_number);
            }
            if ($form->isCreating()) {
                $form->text('customer_full_name', trans('customer.fields.full_name'))->rules('min:3|max:190|required');
                $form->mobile('customer_phone_number', trans('customer.fields.phone_number'))->rules('min:3|max:190|required')->options(['mask' => '9999999999'])->placeholder('05xxxxxxxx')->width(300);
                $form->mobile('customer_alt_phone_number', 'Other Phone')->rules('nullable')->options(['mask' => '9999999999'])->width(300);
                $form->text('customer_passport_number', 'Passport No')->rules(function ($form) {
                    return 'min:10|max:10|required|unique:old_passport_numbers,number';
                });
            }
            $form->textarea('notes', 'Notes')->rows(4);
        });

        $form->column(1 / 3, function ($form) {
            $form->select('payment_type_id', 'Payment Type')->options([
                Requests::PAYMENT_TYPE_CASH => 'Cash', Requests::PAYMENT_TYPE_LATER => 'Credit', Requests::PAYMENT_TYPE_BANK => 'Bank', ])->rules('required')->default(Requests::PAYMENT_TYPE_CASH);
            $form->text('payment_ref', 'Payment Reference')->rules('required_if:payment_type_id,3', ['payment_ref.required_if' => 'Add Reference Number For Bank Payment']);

            if (Admin::user()->can('edit_prices')) {
                $form->currency('service_charge', 'Service Charge')->rules('required|numeric|min:1');
                $form->currency('embassy_charge', 'Organization Charge')->rules('required|numeric|min:1');
                $form->currency('tax_amount', 'Tax Amount')->rules('required|numeric|min:1');
            } else {
                $form->currency('service_charge', 'Service Charge')->readonly()->rules('required|numeric|min:1');
                $form->currency('embassy_charge', 'Organization Charge')->readonly()->rules('required|numeric|min:1');
                $form->currency('tax_amount', 'Tax Amount')->readonly()->rules('required|numeric|min:1');
            }
            $form->currency('amount', 'Total')->readonly()->rules('required|numeric|min:1');
            // $form->textarea('notes', 'Notes')->rows(2);
        });
        $form->hidden('renew_note', 'renew_note');
        $form->submitted(function (Form $form) {
            if (null !== request()->customer_id) {
                $customer_passport_number = trim(request()->customer_passport_number);
                $this->customer = Customer::wherePassportNumber($customer_passport_number)->first();
                if (is_null($this->customer)) {
                    $this->customer = Customer::Create([
                    'passport_number' => $customer_passport_number,
                    'full_name' => request()->customer_full_name,
                    'phone_number' => ltrim(request()->customer_phone_number, '0'),
                    'alt_phone_number' => request()->customer_alt_phone_number ? ltrim(request()->customer_alt_phone_number, '0') : null,
                ]);
                }
            }
            $form->ignore(['customer_full_name',  'customer_phone_number', 'customer_alt_phone_number', 'customer_passport_number']);
        });

        $form->saving(function (Form $form) {
            if (null !== $form->customer_id) {
                $form->customer_id = $this->customer->id;
            }
            if ($form->isCreating()) {
                $customer_request_service = Requests::whereCustomerId($form->customer_id)->whereServiceId($form->service_id)->whereIn('request_status_id', [RequestStatus::IN_EMBASSY, RequestStatus::At_Office, RequestStatus::PENDING])->first();
                if ($customer_request_service) {
                    $error = new MessageBag([
                    'title' => 'Duplicate Request',
                    'message' => 'Customer Already Submitted for same Service before.',
                ]);

                    return back()->with(compact('error'));
                }
            }

            // if ($form->isEditing()) {
            //     if (1 == $form->model()->service->id_updated) {
                    // if ($form->passport_number == $form->model()->customer()->passport_number) {
                    //     return admin_toastr('passport_number not updated', 'error');
                    // }
            //     }
            // }
            // $form->request_created_at = is_null($form->request_created_at) ?  Carbon::parse($form->request_created_at)->format('Y-m-d') : $form->request_created_at;
            // $form->save();
            // $form->service_charge = doubleval($form->service_charge);
            // $form->embassy_charge = doubleval($form->embassy_charge);
            // $form->tax_amount = doubleval($form->tax_amount);
            // $form->amount = doubleval($form->amount);
        });
        $form->saved(function ($form) {
            if ($form->model()->wasChanged('embassy_serial_number') && 'PUT' == request()->method()) {
                $message_type = SmsMessage::Enrollment;
                $message_obj = SmsMessage::where('title', $message_type)->first();
                if ($message_obj) {
                    $message = $message_obj->message.' '.$form->model()->embassy_serial_number;
                    $this->send_sms($form->model()->customer->phone_number, $message, $message_obj->message_other_lang);
                }
            }
            $form->model()->snl = $form->model()->branch_id ? Branch::find($form->model()->branch_id)->get_request_code().$form->model()->id : 'REQ00'.$form->model()->id;
            if ($form->isCreating()) {
                $form->model()->staff_id = Admin::user()->id;
            }
            $form->model()->request_created_at = Carbon::parse($form->model()->request_created_at)->format('Y-m-d');
            if (RequestStatus::PENDING == $form->model()->request_status_id) {
                $form->model()->batch_id = null;
            }
            $form->model()->save();
            if ($form->model()->wasChanged('request_status_id') || $form->isCreating()) {
                $message_type = SmsMessage::Submit;
                if (RequestStatus::IN_EMBASSY == $form->model()->request_status_id) {
                    $message_type = SmsMessage::In_Embassy;
                }
                if (RequestStatus::At_Office == $form->model()->request_status_id) {
                    $message_type = SmsMessage::At_Office;
                }
                if (RequestStatus::COMPELETED == $form->model()->request_status_id) {
                    $message_type = SmsMessage::Completed;
                    $form->model()->completed_at = date('Y-m-d');
                    $form->model()->save();
                }
                $message = SmsMessage::where('title', $message_type)->first();
                if ($message) {
                    //TODO SEND SUBMIT SMS
                    // logger($form->model()->customer->phone_number);
                    $this->send_sms($form->model()->customer->phone_number, $message->message);
                }
            }

            // $path = public_path('uploads'.DIRECTORY_SEPARATOR.'requests_qrCode'.DIRECTORY_SEPARATOR);
            // if (!File::isDirectory($path)) {
            //     File::makeDirectory($path, 0777, true, true);
            // }

            // if (null == $form->model()->qr_string) {
            //     $qr_string = 'request_num'.$form->model()->id.'_'.Str::random(5);
            //     // $qr_image = 'requests_qrCode/'.$qr_string.'.png';

            //     $link = url('trackRequest?qr=');
            //     $qr_link = $link.$qr_string;
            //     // $qr_code_image = base64_encode(QrCode::encoding('UTF-8')->format('png')->size(400)->color(0, 0, 0)->backgroundColor(255, 255, 255)->errorCorrection('H')->generate($qr_link, public_path('uploads/'.$qr_image)));
            //     // $form->model()->qr_image = $qr_image;
            //     $form->model()->qr_string = $qr_string;
            //     $form->model()->save();
            // }
            $req_transaction = TransactionsHistory::updateOrCreate([
                    'request_id' => $form->model()->id,
                ], [
                'snl' => $form->model()->branch_id ? Branch::find($form->model()->branch_id)->get_transaction_code().$form->model()->id : 'TRA00'.$form->model()->id,
                'branch_id' => $form->model()->branch_id,
                'customer_id' => $form->model()->customer_id,
                'title' => $form->model()->service->title,
                'payment_type_id' => $form->model()->payment_type_id,
                'amount' => $form->model()->amount,
            ]);
            if (Requests::PAYMENT_TYPE_CASH == $form->model()->payment_type_id || Requests::PAYMENT_TYPE_BANK == $form->model()->payment_type_id) {
                $form->model()->payment_status_id = Requests::PAYMENT_STATUS_PAID;
                $form->model()->save();
                $form->model()->transactions_history->paid_at = $form->model()->created_at;
            }
            if (Requests::PAYMENT_TYPE_BANK == $form->model()->payment_type_id) {
                $form->model()->transactions_history->payment_ref = $form->model()->payment_ref;
            }
            if (Requests::PAYMENT_TYPE_LATER == $form->model()->payment_type_id) {
                $form->model()->payment_status_id = Requests::PAYMENT_STATUS_NOT_PAID;
                $form->model()->save();

                if ($form->isEditing()) {
                    if ($form->model()->wasChanged('amount') || $form->model()->wasChanged('service_id') || $form->model()->wasChanged('service_type_id') || $form->model()->wasChanged('profession_id')) {
                        $form->model()->customer->debit -= $form->model()->transactions_history->amount;
                        $form->model()->customer->save();
                        $form->model()->customer->debit += $form->model()->amount;
                        $form->model()->customer->save();
                    }
                }
            }

            $req_transaction->snl = $form->model()->branch_id ? Branch::find($form->model()->branch_id)->get_transaction_code().$req_transaction->id : 'TRA00'.$req_transaction->id;
            $req_transaction->save();

            $req_transaction->transaction_type = TransactionsHistory::MONEY_IN;
            $req_transaction->tax_amount = $form->model()->tax_amount;
            // $req_transaction->create_qr_code();
            $req_transaction->save();

            if ($form->isCreating()) {
                $data = $form->model();
                $path = public_path('uploads'.DIRECTORY_SEPARATOR.'Request_Receipt'.DIRECTORY_SEPARATOR);
                session(['newurl' => url('request_receipt/'.$data->id)]);
            }
        });

        return $form;
    }

    public function update_request_status(Request $request)
    {
        $curret_request = Requests::find($request->request_id);
        $request_status_id = $request->request_status;
        if ($curret_request) {
            if (RequestStatus::PENDING == $request_status_id) {
                $curret_request->batch_id = null;
            }
            $curret_request->request_status_id = $request_status_id;
            $curret_request->save();
            if ($curret_request->wasChanged('request_status_id')) {
                if (RequestStatus::IN_EMBASSY == $curret_request->request_status_id) {
                    $message_type = SmsMessage::In_Embassy;
                }
                if (RequestStatus::At_Office == $curret_request->request_status_id) {
                    $message_type = SmsMessage::At_Office;
                }
                if (RequestStatus::COMPELETED == $curret_request->request_status_id) {
                    $message_type = SmsMessage::Completed;
                }
                if (isset($message_type)) {
                    $message = SmsMessage::where('title', $message_type)->first();

                    if ($message) {
                        $this->send_sms($curret_request->customer->phone_number, $message->message);
                    }
                }
            }

            return response()->json(['status' => true, 'message' => 'Batch Updated']);
        } else {
            return response()->json(['status' => false, 'message' => 'Error. Try again!']);
        }
    }

    public function destroy($ids)
    {
        // if (Admin::user()->can('delete_request')) {
        //     $ids_array = explode(',', $ids);
        //     if (1 == count($ids_array)) {
        //         $validator = Validator::make(request()->all(), [
        //     'reason' => 'required',
        // ]);

        //         if ($validator->fails()) {
        //             return response()->json([
        //     'status' => false,
        //     'message' => 'Add Reason to delete request',
        // ]);
        //         }
        //     }

        //     foreach ($ids_array as $id) {
        //         $req = Requests::find($id);
        //         $batch = Batch::find($req->batch_id);
        //         if (isset($batch->requests) && 0 == count($batch->requests)) {
        //             $batch->forcedelete();
        //         }
        //         $req->transactions_history ? $req->transactions_history->forcedelete() : '';
        //         if (isset(request()->reason)) {
        //             $req->create_log('Delete Request '.$req->id.' Reason: '.request()->reason, 'DELETED');
        //         }

        //         $req->forcedelete();
        //     }

        //     return response()->json([
        //     'status' => true,
        //     'message' => 'success',
        // ]);
        // }
        return response()->json([
            'status' => true,
            'message' => "Can't Delete Request",
        ]);
    }

    public function prind_pdf_request(Request $request)
    {
        $data = Requests::find($request->req_id);
        if ($data) {
            $path = public_path('uploads'.DIRECTORY_SEPARATOR.'Request_Receipt'.DIRECTORY_SEPARATOR);
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $view = view('pdf.request_receipt', compact('data'));
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view->render());
            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
            $pdf->getDomPDF()->set_option('enable_php', true);
            $pdf->getDomPDF()->set_option('enable_javascript', true);
            $pdf->loadHTML($view->render())->save($path.'request_receipt_'.Admin::user()->id.'.pdf');

            return response()->json(['status' => true, 'message' => 'PDF READY']);
        } else {
            return response()->json(['status' => false, 'message' => 'Error. Try again!']);
        }
    }

    public function update_passport(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'req_id' => 'required|exists:requests,id,invoice_status,0',
       'passport_number' => 'required|unique:customer,passport_number|min:10|max:11',
         ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Passport Number Not Updated']);
        } else {
            $current_request = Requests::find($request->req_id);
            $customer = $current_request->customer;
            $current_request->request_status_id = RequestStatus::At_Office;
            $current_request->save();
            $old_passport_numbers = $customer->old_passport_numbers;
            $old_passport_numbers[] = $customer->passport_number;
            OldPassportNumbers::create([
                        'number' => $customer->passport_number,
                        ]);
            $customer->old_passport_numbers = $old_passport_numbers;
            $customer->passport_number = $request->passport_number;
            $customer->save();
            $message_type = SmsMessage::At_Office;
            if (isset($message_type)) {
                $message = SmsMessage::where('title', $message_type)->first();

                if ($message) {
                    $this->send_sms($current_request->customer->phone_number, $message->message);
                }
            }

            return response()->json(['status' => true, 'message' => 'Passport Number Updated']);
        }
    }

    public function check_service_update_id(Request $request)
    {
        $request_data = $request->validate([
        'req_id' => 'required|exists:requests,id,invoice_status,0',
        ]);
        $current_request = Requests::find($request_data['req_id']);
        $status = false;
        if ($current_request) {
            $id_updated = $current_request->service->id_updated;
            if (1 == $id_updated) {
                $status = true;
            }
        }

        return response()->json(['status' => $status]);
    }
}
