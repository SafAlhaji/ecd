<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Requests\BatchRequests;
use App\Admin\Actions\Requests\PrintRequestPdf;
use App\Admin\Actions\Requests\RequestsChangeStatus;
use App\Admin\Extensions\PrintPdf;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Profession;
use App\Models\Requests;
use App\Models\RequestStatus;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\ServiceType;
use App\Models\ThirdParty;
use Barryvdh\DomPDF\Facade as PDF;
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
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SendToEmbassyController extends Controller
{
    use HasResourceActions;
    public $customer;
    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public $url_new;

    public function index(Content $content)
    {
        if (session()->get('newurl')) {
            $this->url_new = session()->get('newurl');
            session()->forget('newurl');
            session()->regenerate();
            $script = <<<SCRIPT
var url = "{$this->url_new}";
// $("body").append('<a target="_blank" id="download_this_file" href="'+url+'"></a>');
// $("#download_this_file")[0].click();
// $("#download_this_file").remove();
printJS(url);

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
        ->description(' ');
        // ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Requests());
        $grid->setActionClass(Actions::class);
        if (!Admin::user()->isAdministrator()) {
            $grid->model()->whereIn('branch_id', ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id']));
        }
        $grid->model()->where('request_status_id', RequestStatus::Preparing_to_Send_Embassy);
        // $grid->model()->whereNull('batch_id');
        $grid->snl(trans('requests.fields.snl'));
        $grid->customer()->full_name(trans('customer.fields.full_name'));
        $grid->service()->title('Service Title');
        $grid->amount('Total');
        $grid->column('request_status_id')->display(function ($title, $column) {
            // If the value of the status field of this column is equal to 1, directly display the title field
            if (RequestStatus::COMPELETED == $this->request_status_id) {
                return "<span class='btn btn-xs btn-success'>COMPELETED</span>";
            }

            // Otherwise it is displayed as editable
            return $column->editable('select', RequestStatus::request_status);
        })->sortable();
        $grid->embassy_serial_number('Enrollment no.')->editable(); //('select', RequestStatus::request_status);
        $grid->embassy()->title('Embassy Title');
        // $grid->qr_image('Request QrCode')->image(url($path = 'uploads') . "/", 100, 100);

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            // $filter->like('snl', 'Request no.');
            // $filter->where(function ($query) {
            //     $query->whereHas('customer', function ($query) {
            //         $query->where('passport_number', $this->input);
            //     });
            // }, 'Passport Number');
            $filter->where(function ($query) {
                $query->whereHas('customer', function ($query) {
                    $query->where('phone_number', $this->input)->orwhere('alt_phone_number', $this->input)->orwhere('passport_number', $this->input);
                })->orwhere('snl', $this->input);
            }, 'Search')->placeholder('Request no. Customer Phone ,Passport no.');
            $filter->equal('service_id', 'Service')->select(Service::pluck('title as text', 'id'));
            $filter->equal('request_status_id', 'Status')->select(RequestStatus::request_status);
            $filter->equal('batch_id', 'Batch Number')->select(Batch::pluck('title as text', 'id'));
        });
        $grid->actions(function ($actions) {
            // $actions->add(new PrintRequestPdf);
            $actions->append(new PrintPdf($actions->getKey()));
        });
        $grid->batchActions(function ($batch) {
            $batch->add(new BatchRequests());
            $batch->add(new RequestsChangeStatus());
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

        $show->snl(trans('requests.fields.snl'));
        $show->customer('Customer Full Name')->as(function ($value) {
            return $value->full_name;
        });
        $show->service('Service')->as(function ($value) {
            return $value->title;
        });
        $show->service_type('Service Location')->as(function ($value) {
            return $value->title;
        });
        $show->profession('Profession Title')->as(function ($value) {
            return $value->title;
        });
        $show->embassy('Embassy')->as(function ($value) {
            if ($value) {
                return $value->title;
            }
        });
        // $show->service_id('service_id');
        // // $show->branch_id('branch_id');
        // // $show->staff_id('staff_id');
        // // $show->batch_id('batch_id');
        $show->service_charge('Service Charge');
        $show->embassy_charge('Embassy Charge');
        $show->amount('Total');
        $show->request_status_id('Status')->as(function ($value) {
            $status = RequestStatus::request_status[$value];
            if (RequestStatus::PENDING == $value) {
                return "<span class='label label-danger'>{$status}</span>";
            }
            if (RequestStatus::IN_EMBASSY == $value) {
                return "<span class='label label-warning'>{$status}</span>";
            }
            if (RequestStatus::COMPELETED == $value) {
                return "<span class='label label-success'>{$status}</span>";
            }
        })->label('default');
        // $show->qr_image('qr_image')->image(url($path = 'uploads').'/', 100, 100);

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
        // dd(ThirdParty::find(Admin::user()->id)->branches()->pluck('branches.title as text', 'branches.id'));
        $form->hidden('embassy_serial_number');
        $form->hidden('request_status_id');
        // $form->select('is_customer')->options(['1' => 'Current Customer', '2'=> 'New Customer']);
        // $form->tab('Customer Details', function ($form) {
        $form->select('customer_id', 'Search')->options(function ($id) {
            $customer_list = Customer::pluck('passport_number as text', 'id');
            $customer_list[0] = 'New Customer';
            $customer = Customer::find($id);
            if ($customer) {
                return [$customer->id => $customer->passport_number];
            } else {
                return $customer_list;
            }
        })->rules('required|integer')->default(0);
        $form->select('embassy_id', 'Embassy')->options(ServiceProvider::all()->pluck('title', 'id'));

        $form->select('branch_id', 'Branch')->options(ThirdParty::find(Admin::user()->id)->branches()->pluck('branches.title as text', 'branches.id'))->rules('required');
        $form->divider();
        // $form->column(1/2, function ($form) {
        if ($form->isEditing()) {
            $customer = Requests::find(\request()->route()->request)->customer;
            // $form->text('customer_snl', 'Request No.')->default($customer->snl)->readonly();
            $form->text('customer_full_name', trans('customer.fields.full_name'))->rules('min:3|max:190|required')->readonly()->default($customer->full_name);
            $form->mobile('customer_phone_number', trans('customer.fields.phone_number'))->rules('min:3|max:190|required')->readonly()->options(['mask' => '999 999 9999'])->default($customer->phone_number);
            $form->mobile('customer_alt_phone_number', 'Other Phone')->rules('min:3|max:190|required')->readonly()->options(['mask' => '999 999 9999'])->default($customer->alt_phone_number);
            $form->text('customer_passport_number', 'Passport No.')->rules('min:3|max:190|required|unique:customer,passport_number,deleted_at')->readonly()->default($customer->passport_number);
        }

        if ($form->isCreating()) {
            // $form->text('customer_snl', 'Request No.')->readonly();
            $form->text('customer_full_name', trans('customer.fields.full_name'))->rules('min:3|max:190|required');
            $form->mobile('customer_phone_number', trans('customer.fields.phone_number'))->rules('min:3|max:190|required')->options(['mask' => '999 999 9999']);
            $form->mobile('customer_alt_phone_number', 'Other Phone')->rules('min:3|max:190|required')->options(['mask' => '999 999 9999']);
            $form->text('customer_passport_number', 'Passport No.')->rules(function ($form) {
                return 'min:3|max:190|required|unique:customer,passport_number,{{customer_id}},deleted_at';
            });
            // }'min:3|max:190|required|unique:customer,passport_number,deleted_at');
        }
        // });

        // $form->column(1/2, function ($form) {
        $form->date('request_created_at', 'Request Date')->format('YYYY-MM-DD')->default(\Carbon\Carbon::now());

        $form->select('service_id', trans('service.index.header'))->options(function ($id) {
            return Service::pluck('title as text', 'id');
        })->rules('required')->loads(
            ['service_type_id', 'profession_id'],
            ['/get_servicedetails_types', '/get_servicedetails_professions'],
            'id',
            'title'
        );
        $form->select('service_type_id', 'Service Location')->options(function ($id) {
            $service_type = ServiceType::find($id);
            if ($service_type) {
                return [$service_type->id => $service_type->title];
            }
        })->rules('required|integer');
        $form->select('profession_id', trans('customer.fields.profession'))->options(function ($id) {
            $profession = Profession::find($id);
            if ($profession) {
                return [$profession->id => $profession->title];
            }
        })
            ->rules('required');
        $form->text('service_charge', 'Service Charge')->readonly();
        $form->text('embassy_charge', 'Embassy Charge')->readonly();
        $form->text('amount', 'Total')->readonly();
        $form->textarea('notes', 'Notes')->rows(2);

        // $form->text('branch_id', 'branch_id');
        // $form->text('staff_id', 'staff_id');
        // $form->text('batch_id', 'batch_id');
        // $form->text('request_status_id', 'request_status_id');
        // });

        // $form->text('qr_string', 'qr_string');
        // $form->text('qr_image', 'qr_image');
        // });

        $form->submitted(function (Form $form) {
            if (null !== request()->customer_id) {
                $this->customer = Customer::create([
                    'full_name' => request()->customer_full_name,
                    'phone_number' => request()->customer_phone_number,
                    'alt_phone_number' => request()->customer_alt_phone_number,
                    'passport_number' => request()->customer_passport_number,
                    'profession_id' => request()->customer_profession_id,
                ]);
            }
            $form->ignore(['customer_full_name', 'service_snl', 'service_title', 'service_service_type_id', 'service_service_type_charge',
                'service_profession_detail_snl', 'service_profession_detail_embassy_charge', 'service_profession_detail_title',
                'customer_phone_number', 'customer_snl', 'customer_alt_phone_number', 'customer_passport_number', 'customer_profession_id',
                'service_servicedetails_type_id', 'service_servicedetails_profession', ]);
        });
        // $form->setAction('/admin/requests/get_pdf');
        $form->saving(function (Form $form) {
            if (null !== $form->customer_id) {
                $form->customer_id = $this->customer->id;
            }
        });
        $form->saved(function ($form) {
            $form->model()->snl = Branch::find($form->model()->branch_id)->get_request_code().$form->model()->id;
            $form->model()->staff_id = Admin::user()->id;
            $form->model()->save();
            $path = public_path('uploads/requests_qrCode');
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // if (null == $form->model()->qr_string) {
            //     $qr_string = 'request_num'.$form->model()->id.'_'.Str::random(5);
            //     // $qr_image = 'requests_qrCode/'.$qr_string.'.png';

            //     $link = env('APP_URL').'trackRequest?qr=';
            //     $qr_link = $link.$qr_string;
            //     // $qr_code_image = base64_encode(QrCode::encoding('UTF-8')->format('png')->size(400)->color(0, 0, 0)->backgroundColor(255, 255, 255)->errorCorrection('H')->generate($qr_link, public_path('uploads/'.$qr_image)));
            //     // $form->model()->qr_image = $qr_image;
            //     $form->model()->qr_string = $qr_string;
            //     $form->model()->save();
            // }
            $data = $form->model();
            $path = public_path('uploads/Request_Receipt/');
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            if ($form->isCreating()) {
                $view = view('pdf.request_receipt', compact('data'));
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view->render());
                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
                $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
                $pdf->getDomPDF()->set_option('enable_php', true);
                $pdf->getDomPDF()->set_option('enable_javascript', true);
                // $pdf->loadView('pdf.request_receipt', compact('data'))->save('uploads/Request_Receipt/'.'request_receipt_'.$data->id.'.pdf');
                $pdf->loadHTML($view->render())->save('uploads/Request_Receipt/'.'request_receipt_'.$data->id.'.pdf');

                session(['newurl' => url('uploads/Request_Receipt/request_receipt_'.$data->id.'.pdf')]);
            }
        });

        return $form;
    }
}
