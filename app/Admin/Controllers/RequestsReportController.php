<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ReportExcel;
use App\Extensions\ExportReportXLS;
use App\Extensions\PrintReportPdf;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\Profession;
use App\Models\Requests;
use App\Models\RequestStatus;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\ServiceType;
use App\Models\ThirdParty;
use App\Traits\SmsTraits;
use Carbon\Carbon;
use DB;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RequestsReportController extends Controller
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
        return $content
        ->header('General Requests Report')
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
        ->header('Report Details')
        ->description(' ');
        // ->body($this->detail($id));
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
        ->header('Report Details')
        ->description(' ');
        // ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
        ->header('Report')
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
        $grid->model()->where('invoice_status', 0);
        $grid->tools(function ($tools) use ($grid) {
            $tools->append(new PrintReportPdf($grid));
            $tools->append(new ExportReportXLS($grid));
        });
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $grid->model()->whereIn('branch_id', $ids);
        }
        $grid->column('snl', trans('requests.fields.snl'))->sortable();
        $grid->request_created_at('Date')->display(function ($request_created_at) {
            return Carbon::parse($request_created_at)->format('d-m-Y');
        })->sortable();
        $grid->customer()->full_name('Name');
        $grid->customer()->phone_number('Phone No.');
        $grid->customer()->passport_number('Passport No');
        $grid->service()->title('Service');
        $grid->service_type()->title('Location');
        $grid->request_status_id('Status')->display(function ($request_status_id) {
            return RequestStatus::request_status[$request_status_id] ?? 'PENDING';
        })->sortable();

        $grid->embassy_serial_number('Enrollment no.')->sortable();
        // $grid->renew_note('Renewing Note');//('select', RequestStatus::request_status);
        $grid->embassy()->title('Provider');
        $grid->batch()->title('Batch Ref No.');
        $grid->service_charge('Service Charge')->sortable();
        $grid->embassy_charge('Provider Charge')->sortable();
        $grid->tax_amount('Tax')->sortable();
        $grid->amount('Total')->sortable();
        if (Admin::user()->isAdministrator()) {
            $grid->username()->username('User');
        }
        $grid->footer(function ($query) {
            $detail_table = [];
            $services = $query->select(DB::raw('count(id) as count, service_id,
                SUM(amount) as total_amount,
                SUM(embassy_charge) as total_embassy_charge,
                SUM(service_charge) as total_service_charge,
                SUM(tax_amount) as total_tax_amount'))
            ->groupBy('service_id')->get()->toArray();
            $amount = 0;
            $total_requests = 0;
            $embassy_charge = 0;
            $service_charge = 0;
            $tax_amount = 0;
            $detail_table = [];
            if (count($services) > 0) {
                foreach ($services as $value) {
                    $service_title = $value['service'] ? $value['service']['title'] : '';
                    $service_count = $value['count'];
                    $total_requests += $service_count;
                    $amount += $value['total_amount'];
                    $embassy_charge += $value['total_embassy_charge'];
                    $service_charge += $value['total_service_charge'];
                    $tax_amount += $value['total_tax_amount'];
                    $detail_table[] = ['title' => $service_title, 'count' => $service_count];
                }
            }

            $report_details['table'] = $detail_table;
            $report_details['amount'] = $amount;
            $report_details['embassy_charge'] = round($embassy_charge, 2);
            $report_details['service_charge'] = round($service_charge, 2);
            $report_details['tax_amount'] = round($tax_amount, 2);
            $report_details['total_requests'] = round($total_requests, 2);
            $view_data = view('admin.report', compact('report_details'));

            return new Box('Report Detail', $view_data);
        });
        // $grid->qr_image('Request QrCode')->image(url($path = 'uploads') . "/", 100, 100);
        $grid->filter(function ($filter) {
            $filter->column(1 / 3, function ($filter) {
                $filter->where(function ($query) {
                    $query->whereHas('customer', function ($query) {
                        $query->where('phone_number', $this->input)->orwhere('alt_phone_number', $this->input)->orwhere('passport_number', $this->input);
                    })->orwhere('snl', $this->input);
                }, 'Search')->placeholder('Request no. Customer Phone ,Passport no.');
                $filter->in('service_id', 'Service')->multipleSelect(Service::pluck('title', 'id'));
                $filter->in('profession_id', 'Profession')->multipleSelect(Profession::pluck('title as text', 'id'));
                $filter->between('request_created_at', 'Request Date')->date();
            });
            $filter->column(1 / 3, function ($filter) {
                if (Admin::user()->isAdministrator()) {
                    $filter->in('embassy_id', 'Service Provider')->multipleSelect(ServiceProvider::pluck('title as text', 'id'));
                    $filter->in('staff_id', 'UserName')->multipleSelect(ThirdParty::pluck('username as text', 'id'));
                }
                if (Admin::user()->isAdministrator()) {
                    $filter->in('branch_id', 'Branch')->multipleSelect(Branch::pluck('title as text', 'id'));
                }
                if (!Admin::user()->isAdministrator() && Admin::user()->can('branch_filter')) {
                    $filter->in('branch_id', 'Branch')->multipleSelect(ThirdParty::find(Admin::user()->id)->branches()->get()->pluck('title', 'id'));
                }
                if (!Admin::user()->isAdministrator()) {
                    $branches_id = ThirdParty::find(Admin::user()->id)->branches()->pluck('branches.id');
                    $users_id = DB::table('admin_users_branches')->whereIn('branch_id', $branches_id)->get('admin_user_id');
                    foreach ($users_id as  $u_id) {
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
                if (!Admin::user()->isAdministrator()) {
                    $filter->in('batch_id', 'Batch Number')->multipleSelect(Batch::whereIn('branch_id', ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id']))->pluck('title as text', 'id'));
                }
                if (Admin::user()->isAdministrator()) {
                    $filter->in('batch_id', 'Batch Number')->multipleSelect(Batch::pluck('title as text', 'id'));
                }
                $filter->in('request_status_id', 'Status')->multipleSelect(RequestStatus::request_status);
                $filter->in('service_type_id', 'Service Location')->multipleSelect(ServiceType::pluck('title as text', 'id'));
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
        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->perPages([100, 200, 300, 400, 500, 1000, 'all']);
        $query_parameter = request()->query() ?? [];
        // dd($query_parameter);
        if (count($query_parameter) > 0 && isset($query_parameter['per_page']) && 'all' == $query_parameter['per_page']) {
            $grid->disablePagination();
        }

        return $grid;
    }

    public function print_pdf(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '999');
        $items_display = explode(',', $request->items);
        // $collection = json_decode($request->collection);
        $filter_request = json_decode($request->collection);
        $requests = Requests::select('*');
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $requests = Requests::whereIn('branch_id', $ids);
        }
        if (isset($filter_request->c668e89fcce104e077d4ade67d36eb03)) {
            $text_input = $filter_request->c668e89fcce104e077d4ade67d36eb03;
            $requests = $requests->whereHas('customer', function ($query) use ($text_input) {
                $query->where('phone_number', $text_input)->orwhere('alt_phone_number', $text_input)->orwhere('passport_number', $text_input);
            })->orwhere('snl', $text_input);
        }
        // dd($requests);
        $array_search = ['service_id', 'profession_id', 'embassy_id', 'staff_id', 'branch_id', 'batch_id', 'request_status_id', 'service_type_id'];
        foreach ($array_search as $item_search) {
            if (isset($filter_request->$item_search)) {
                $requests = $requests->whereIn($item_search, $filter_request->$item_search);
            }
        }
        if (isset($filter_request->request_created_at)) {
            $end = $filter_request->request_created_at->end ?? Carbon::now()->format('Y-m-d');
            $requests = $requests->whereBetween('request_created_at', [$filter_request->request_created_at->start, $end]);
        }
        $collection = $requests->get()->lazy();

        if (count($collection) > 0) {
            $view = view('pdf.full_report', compact('collection', 'items_display'));

            return response()->json(['status' => true, 'message' => 'Report Ready To print', 'view' => $view->render()]);
        } else {
            return response()->json(['status' => false, 'message' => 'No Data Found']);
        }
    }

    public function excel(Request $request)
    {
        $items_display = explode(',', $request->items);
        // dd($request->all());
        $collection = json_decode($request->collection);
        $requests = Requests::select('*');
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $requests = Requests::whereIn('branch_id', $ids);
        }
        if (isset($collection->c668e89fcce104e077d4ade67d36eb03)) {
            $text_input = $collection->c668e89fcce104e077d4ade67d36eb03;
            $requests = $requests->whereHas('customer', function ($query) use ($text_input) {
                $query->where('phone_number', $text_input)->orwhere('alt_phone_number', $text_input)->orwhere('passport_number', $text_input);
            })->orwhere('snl', $text_input);
        }
        // dd($requests);
        $array_search = ['service_id', 'profession_id', 'embassy_id', 'staff_id', 'branch_id', 'batch_id', 'request_status_id', 'service_type_id'];
        foreach ($array_search as $item_search) {
            if (isset($collection->$item_search)) {
                $requests = $requests->whereIn($item_search, $collection->$item_search);
            }
        }
        // dd($requests->whereBetween('request_created_at', [$collection->request_created_at->start,$collection->request_created_at->end])->get());
        if (isset($collection->request_created_at)) {
            $end = $collection->request_created_at->end ?? Carbon::now()->format('Y-m-d');
            $requests = $requests->whereBetween('request_created_at', [$collection->request_created_at->start, $end]);
        }
        $filter_requests = $requests->get()->lazy();

        if (count($filter_requests) > 0) {
            Excel::store(new ReportExcel($filter_requests, $items_display), 'report.xlsx', 'admin');

            return response()->json(['status' => true, 'message' => 'Report Ready To print']);
        } else {
            return response()->json(['status' => false, 'message' => 'No Data Found']);
        }
    }
}
