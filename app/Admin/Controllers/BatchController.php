<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\BatchExcel;
use App\Extensions\EditRequestStatus;
use App\Extensions\ExcelBatch;
use App\Extensions\PrintBatch;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\OrganizationDetails;
use App\Models\RequestStatus;
use App\Models\SmsMessage;
use App\Models\ThirdParty;
use App\Traits\SmsTraits;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Displayers\Actions;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use File;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BatchController extends Controller
{
    use HasResourceActions;
    use SmsTraits;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
//         if (session()->get('batch_deleted')) {
//             session()->forget('batch_deleted');
//             $script = <<<SCRIPT
        // location.reload();
        // SCRIPT;
//             Admin::script($script);
//         }
        return $content
            ->header('Batch Requests')
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
        $grid = new Grid(new Batch());
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $grid->model()->whereIn('branch_id', $ids);
            $grid->model()->where('admin_user_id', Admin::user()->id);
        }
        $grid->disableCreateButton();
        // $grid->disableFilter();
        $grid->disableRowSelector();
        // $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->title('title')->expand(function ($model) {
            $get_requests = $model->requests()->get()->map(function ($requests) {
                $requests_data = [
                    $requests->snl,
                    $requests->customer->full_name,
                    $requests->customer->passport_number,
                    $requests->customer->phone_number,
                    $requests->embassy_serial_number,
                    $requests->renew_note,
                    RequestStatus::request_status[$requests->request_status_id] ?? '',
                    new EditRequestStatus($requests->id), ];

                return $requests_data;
            });

            return new Table(['Request No.', 'Name', 'Passport No.', 'Phone No.', 'Enrollment no.', 'Renew Note', 'Current Status', 'Change Status'], $get_requests->toArray());
        });
        $grid->username()->name('User');
        $grid->batch_status_id('Status')->editable('select', RequestStatus::request_status)->sortable();

        $grid->bank_ref('Bank Ref')->sortable();
        $grid->batch_date('Deposit Date')->display(function ($batch_date) {
            return Carbon::parse($batch_date)->format('d-m-Y');
        })->sortable();
        $grid->column('Processing')->display(function () {
            return $this->requests()->where('request_status_id', RequestStatus::Preparing_to_Send_Embassy)->count();
        });
        $grid->column('In Embassy')->display(function () {
            return $this->requests()->where('request_status_id', RequestStatus::IN_EMBASSY)->count();
        });
        $grid->column('At Office')->display(function () {
            return $this->requests()->where('request_status_id', RequestStatus::At_Office)->count();
        });
        $grid->column('COMPELETED')->display(function () {
            return $this->requests()->where('request_status_id', RequestStatus::COMPELETED)->count();
        });
        $grid->column('Total')->display(function () {
            return $this->requests()->count();
        });
        $grid->column('Deposit')->display(function () {
            return $this->requests()->sum('embassy_charge');
        });
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->where(function ($query) {
                $query->where('title', $this->input)->orwhere('bank_ref', $this->input);
            }, 'Search')->placeholder('Batch Title , or Bank Ref.');
            // $filter->equal('service_id', 'Service')->select(Service::pluck('title as text', 'id'));
            // $filter->equal('request_status_id', 'Status')->select(RequestStatus::request_status);
            // $filter->equal('batch_id', 'Batch Number')->select(Batch::pluck('title as text', 'id'));
            $filter->between('created_at', 'Request Date')->date();
            if (Admin::user()->isAdministrator()) {
                $filter->in('branch_id', 'Branch')->multipleSelect(Branch::pluck('title as text', 'id'));
            }
            if (!Admin::user()->isAdministrator() && Admin::user()->can('branch_filter')) {
                $filter->in('branch_id', 'Branch')->multipleSelect(ThirdParty::find(Admin::user()->id)->branches()->get()->pluck('title', 'id'));
            }
            // SCOPE
        });
        $grid->setActionClass(Actions::class);
        $grid->actions(function ($actions) {
            $actions->append(new PrintBatch($actions->getKey()));
            $actions->append(new ExcelBatch($actions->getKey()));
            if (!Admin::user()->isAdministrator()) {
                $actions->disableDelete();
            }

            $actions->disableEdit();
            // $actions->disableView();
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
        $show = new Show(Batch::findOrFail($id));
        $show->panel()
    ->tools(function ($tools) {
        $tools->disableEdit();
        // $tools->disableList();
        $tools->disableDelete();
    });

        // $show->id('ID');
        $show->title('Title');
        // $show->description('description');
        $show->batch_status_id('Status')->as(function ($batch_status_id) {
            if (isset($batch_status_id)) {
                return RequestStatus::request_status[$batch_status_id];
            } else {
                return  RequestStatus::request_status[1];
            }
        });
        $show->bank_ref('Bank Ref');
        $show->batch_date('Deposit Date')->as(function ($batch_date) {
            return Carbon::parse($batch_date)->format('d-m-Y');
        });
        $show->requests('Requests', function ($show_request) {
            $show_request->setResource('/admin/requests');
            $show_request->disableCreateButton();
            $show_request->disableFilter();
            $show_request->disableRowSelector();
            $show_request->disableColumnSelector();
            $show_request->disableExport();
            $show_request->snl('Request No.');
            $show_request->customer()->full_name('Name');
            $show_request->customer()->passport_number('Passport No.');
            $show_request->customer()->phone_number('Phone No.');
            $show_request->embassy_serial_number('Enrollment no.');
            $show_request->renew_note('Renew Note');
            $show_request->disableActions();
        });
        $show->created_at(trans('admin.created_at'))->as(function ($created_at) {
            return Carbon::parse($created_at)->format('d-m-Y');
        });

        // $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Batch());

        $form->display('ID');
        $form->text('title', 'title');
        $form->text('description', 'description');
        $form->text('batch_status_id', 'batch_status_id');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));
        $form->saved(function ($form) {
            if (RequestStatus::PENDING == $form->model()->batch_status_id) {
                if (count($form->model()->requests) > 0) {
                    foreach ($form->model()->requests as $batch_request) {
                        $batch_request->batch_id = null;
                        $batch_request->request_status_id = RequestStatus::PENDING;
                        $batch_request->save();
                    }
                }
                $form->model()->forcedelete();
            }
            if (RequestStatus::Preparing_to_Send_Embassy == $form->model()->batch_status_id) {
                if (count($form->model()->requests) > 0) {
                    foreach ($form->model()->requests as $batch_request) {
                        $batch_request->request_status_id = RequestStatus::Preparing_to_Send_Embassy;
                        $batch_request->save();
                    }
                }
            }
            if (RequestStatus::IN_EMBASSY == $form->model()->batch_status_id) {
                if (count($form->model()->requests) > 0) {
                    foreach ($form->model()->requests as $batch_request) {
                        $batch_request->request_status_id = RequestStatus::IN_EMBASSY;
                        $batch_request->save();
                        $message = SmsMessage::where('title', SmsMessage::In_Embassy)->first();
                        if ($message) {
                            $this->send_sms($batch_request->customer->phone_number, $message->message);
                        }
                    }
                }
            }
            if (RequestStatus::At_Office == $form->model()->batch_status_id) {
                if (count($form->model()->requests) > 0) {
                    foreach ($form->model()->requests as $batch_request) {
                        $batch_request->request_status_id = RequestStatus::At_Office;
                        $batch_request->save();
                        $message = SmsMessage::where('title', SmsMessage::At_Office)->first();
                        if ($message) {
                            $this->send_sms($batch_request->customer->phone_number, $message->message);
                        }
                    }
                }
            }
            if (RequestStatus::COMPELETED == $form->model()->batch_status_id) {
                if (count($form->model()->requests) > 0) {
                    foreach ($form->model()->requests as $batch_request) {
                        $batch_request->request_status_id = RequestStatus::COMPELETED;
                        $batch_request->save();
                        $message = SmsMessage::where('title', SmsMessage::Completed)->first();
                        if ($message) {
                            $this->send_sms($batch_request->customer->phone_number, $message->message);
                        }
                    }
                }
            }
        });

        return $form;
    }

    public function print_pdf(Request $request)
    {
        $batch_id = $request->batch_id;
        $items_display = explode(',', $request->items);
        $collection = Batch::find($batch_id);
        // $path = public_path('uploads'.DIRECTORY_SEPARATOR.'Batch_Table'.DIRECTORY_SEPARATOR);
        // if (!File::isDirectory($path)) {
        //     File::makeDirectory($path, 0777, true, true);
        // }
        $branch = ThirdParty::find(Admin::user()->id)->branches()->first();
        if ($branch) {
            $branch = ThirdParty::find(Admin::user()->id)->branches()->first();
        } else {
            $branch = OrganizationDetails::find(1);
        }
        $view = view('pdf.custom_batch_table', compact('collection', 'items_display', 'branch'));
        // $pdf = \App::make('dompdf.wrapper');
        // $pdf->loadHTML($view->render());
        // $pdf = \App::make('dompdf.wrapper');
        // $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        // $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        // $pdf->getDomPDF()->set_option('enable_php', true);
        // $pdf->getDomPDF()->set_option('enable_javascript', true);
        // $pdf->loadHTML($view->render())->save($path.'batch_table_'.Admin::user()->id.'.pdf');
        return response()->json(['status' => true, 'message' => 'Report Ready To print', 'view' => $view->render()]);
        // return response()->json(['status' => true, 'message' => 'Batch Ready To print']);
    }

    public function excel(Request $request)
    {
        $batch_id = $request->batch_id;
        $items_display = explode(',', $request->items);
        $collection = Batch::find($batch_id);

        return Excel::download(new BatchExcel($collection, $items_display), 'report.xlsx');
    }
}
