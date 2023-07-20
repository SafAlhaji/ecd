<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Profession;
use App\Models\RequestType;
use App\Models\Service;
use App\Models\ServiceDetails;
use App\Models\ServiceType;
use App\Models\TaxType;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class GeneralServiceController extends Controller
{
    use HasResourceActions;
    public $profession;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('General Services')
            ->description(trans('service.index.description'))
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
            ->header(trans('service.show.detail'))
            ->description(trans('service.index.description'))
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
            ->header(trans('service.edit.edit'))
            ->description(trans('service.index.description'))
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
            ->header('Create General Service')
            ->description(trans('service.index.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Service());
        $grid->model()->where('request_type_id', RequestType::General);
        $grid->snl(trans('service.fields.snl'));
        $grid->title(trans('service.fields.title'));
        $grid->title_ar(trans('service.fields.title_ar'));
        $grid->id_updated('Request Id Updated')->display(function ($id_updated) {
            if (1 == $id_updated) {
                return '<b>Yes</b>';
            } else {
                return 'No';
            }
        });
        // $grid->service_type_id(trans('service.fields.service_type_id'))->display(function ($service_type_id) {
        //     $service_type = ServiceType::find($service_type_id);
        //     if ($service_type) {
        //         return $service_type->title;
        //     }
        // });
        $grid->column('Details')->expand(function ($model) {
            $servicedetails = $model->servicedetails()->get()->map(function ($servicedetail) {
                $servicedetail_data = [
                    $servicedetail->snl,
                    $servicedetail->servicetype->title,
                    $servicedetail->amount_service_type,
                    // $servicedetail->profession->title,
                    $servicedetail->embassy_charge, ];

                return $servicedetail_data;
            });

            return new Table([
                trans('service.fields.snl_service_details'),
                trans('service.fields.service_type_id'),
                trans('service_type.fields.amount'),
                // trans('customer.fields.profession'),
                trans('service.fields.embassy_charge'), ], $servicedetails->toArray());
        });
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('snl', trans('service.fields.snl'));
            $filter->where(function ($query) {
                $query->whereHas('servicedetails', function ($query) {
                    $query->where('service_type_id', "{$this->input}");
                });
            }, trans('service.fields.service_type_id'))->select(ServiceType::pluck('title as text', 'id'));
            // $filter->where(function ($query) {
            //     $query->whereHas('servicedetails', function ($query) {
            //         $query->where('profession_id', "{$this->input}");
            //     });
            // }, 'Profession')->select(Profession::pluck('title as text', 'id'));

            // $filter->equal('profession_id', trans('customer.fields.profession'))->select(Profession::pluck('title as text', 'id'));
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
        $show = new Show(Service::findOrFail($id));

        $show->id(trans('service.fields.id'));
        $show->title(trans('service.fields.title'));
        $show->title_ar(trans('service.fields.title_ar'));
        $show->service_type_id(trans('service.fields.service_type_id'));
        $show->servicedetails('Embassy charge details', function ($servicedetails) {
            $servicedetails->disableActions();

            $servicedetails->disablePagination();

            $servicedetails->disableCreateButton();

            $servicedetails->disableFilter();

            $servicedetails->disableRowSelector();

            $servicedetails->disableColumnSelector();

            $servicedetails->disableTools();

            $servicedetails->disableExport();

            $servicedetails->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
                $actions->disableEdit();
                $actions->disableDelete();
            });
            $servicedetails->snl(trans('service.fields.snl_service_details'));
            // $servicedetails->profession()->title(trans('customer.fields.profession'));
            $servicedetails->embassy_charge(trans('service.fields.embassy_charge'));
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Service());

        $form->display(trans('service.fields.id'));
        $form->text('title', trans('service.fields.title'))->rules('min:3|max:190|required');
        $form->text('title_ar', trans('service.fields.title_ar'))->rules('min:3|max:190|required');
        $form->hidden('request_type_id', 'Request Type')->value(RequestType::General);
        $form->radio('id_updated', 'Request Id Updated')->options([0 => 'No', 1 => 'Yes'])->default(0);
        $form->hasMany('servicedetails', 'Embassy charge details', function (Form\NestedForm $form) {
            $form->select('service_type_id', trans('service.fields.service_type_id'))
            ->options(ServiceType::pluck('title as text', 'id'))
            ->rules('required|integer');
            $form->currency('amount_service_type', trans('service_type.fields.amount'))->rules('min:1|required');
            $form->select('is_tax_include', ' Tax Included')->options([ServiceDetails::NO_VAT => 'NO VAT', ServiceDetails::WITH_VAT => 'WITH VAT', ServiceDetails::WITHOUT_VAT => 'WITHOUT VAT'])->rules('required');
            $form->select('tax_type_id', 'Tax Type')->options(function ($id) {
                $tax_type_list = TaxType::pluck('title as text', 'id');

                return $tax_type_list;
            })->rules('required');

            $form->currency('embassy_charge', 'Service Charge')->rules('min:1|required');
        })->useTable();

        return $form;
    }
}
