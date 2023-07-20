<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Requests\ExportPDF;
use App\Admin\Extensions\RequestsExcel;
use App\Models\Requests;
use App\Models\RequestStatus;
use App\Models\ThirdParty;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\DB;

class RefundController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Refunded Requests';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Requests());
        $grid->model()->where('invoice_status', 1);

        $grid->disableCreateButton();

        $grid->disableFilter();

        $grid->disableRowSelector();

        $grid->disableColumnSelector();

        $grid->disableTools();

        // $grid->disableExport();
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
        // $grid->column('request_status_id','Request ')->display(function () {
        //     return RequestStatus::request_status[$this->request_status_id];
        // })->sortable();
        // $grid->completed_at('Completed Date')->display(function () {
        //     return $this->completed_at ?? '';
        // });
        $grid->embassy_serial_number('Enrollment no.'); //->editable()->sortable(); //('select', RequestStatus::request_status);
        $grid->renew_note('Renewing Note'); //->editable()->sortable(); //('select', RequestStatus::request_status);
        $grid->embassy()->title('Provider');
        $grid->branch()->title('Branch');
        $grid->disableActions();
        $grid->batchActions(function ($batch) {
            $batch->add(new ExportPDF());
        });
        $grid->exporter(new RequestsExcel());
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

        $show->field('id', __('Id'));
        $show->field('customer_id', __('Customer id'));
        $show->field('service_id', __('Service id'));
        $show->field('branch_id', __('Branch id'));
        $show->field('staff_id', __('Staff id'));
        $show->field('batch_id', __('Batch id'));
        $show->field('amount', __('Amount'));
        $show->field('request_status_id', __('Request status id'));
        $show->field('embassy_id', __('Embassy id'));
        $show->field('qr_string', __('Qr string'));
        $show->field('qr_image', __('Qr image'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('deleted_at', __('Deleted at'));
        $show->field('snl', __('Snl'));
        $show->field('notes', __('Notes'));
        $show->field('delivery_date_time', __('Delivery date time'));
        $show->field('service_type_id', __('Service type id'));
        $show->field('profession_id', __('Profession id'));
        $show->field('service_charge', __('Service charge'));
        $show->field('embassy_charge', __('Embassy charge'));
        $show->field('request_created_at', __('Request created at'));
        $show->field('embassy_serial_number', __('Embassy serial number'));
        $show->field('renew_note', __('Renew note'));
        $show->field('request_type_id', __('Request type id'));
        $show->field('payment_type_id', __('Payment type id'));
        $show->field('payment_ref', __('Payment ref'));
        $show->field('tax_amount', __('Tax amount'));
        $show->field('payment_status_id', __('Payment status id'));
        $show->field('completed_at', __('Completed at'));
        $show->field('invoice_status', __('Invoice status'));

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

        $form->number('customer_id', __('Customer id'));
        $form->number('service_id', __('Service id'));
        $form->number('branch_id', __('Branch id'));
        $form->text('staff_id', __('Staff id'));
        $form->number('batch_id', __('Batch id'));
        $form->text('amount', __('Amount'));
        $form->number('request_status_id', __('Request status id'));
        $form->number('embassy_id', __('Embassy id'));
        $form->text('qr_string', __('Qr string'));
        $form->text('qr_image', __('Qr image'));
        $form->text('snl', __('Snl'));
        $form->textarea('notes', __('Notes'));
        $form->text('delivery_date_time', __('Delivery date time'));
        $form->number('service_type_id', __('Service type id'));
        $form->number('profession_id', __('Profession id'));
        $form->text('service_charge', __('Service charge'));
        $form->text('embassy_charge', __('Embassy charge'));
        $form->date('request_created_at', __('Request created at'))->default(date('Y-m-d'));
        $form->text('embassy_serial_number', __('Embassy serial number'));
        $form->text('renew_note', __('Renew note'));
        $form->number('request_type_id', __('Request type id'));
        $form->number('payment_type_id', __('Payment type id'));
        $form->text('payment_ref', __('Payment ref'));
        $form->text('tax_amount', __('Tax amount'));
        $form->number('payment_status_id', __('Payment status id'));
        $form->date('completed_at', __('Completed at'))->default(date('Y-m-d'));
        $form->switch('invoice_status', __('Invoice status'));

        return $form;
    }
}
