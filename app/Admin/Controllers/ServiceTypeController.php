<?php

namespace App\Admin\Controllers;

use App\Models\ServiceType;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ServiceTypeController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header(trans('service_type.index.header'))
            ->description(trans('service_type.index.description'))
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('service_type.show.detail'))
            ->description(trans('service_type.index.description'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('service_type.edit.edit'))
            ->description(trans('service_type.index.description'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('service_type.create.create'))
            ->description(trans('service_type.index.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ServiceType);

        $grid->id(trans('service_type.fields.id'));
        $grid->title(trans('service_type.fields.title'));
        $grid->title_ar(trans('service_type.fields.title_ar'));
        // $grid->amount(trans('service_type.fields.amount'));
        // $grid->created_at(trans('admin.created_at'));
        // $grid->updated_at(trans('admin.updated_at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(ServiceType::findOrFail($id));

        $show->id(trans('service_type.fields.id'));
        $show->title(trans('service_type.fields.title'));
        $show->title_ar(trans('service_type.fields.title_ar'));
        // $show->amount(trans('service_type.fields.amount'));
        // $show->created_at(trans('admin.created_at'));
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
        $form = new Form(new ServiceType);

        $form->display(trans('service_type.fields.id'));
        $form->text('title', trans('service_type.fields.title'))->rules('min:3|max:190|required');
        $form->text('title_ar', trans('service_type.fields.title_ar'))->rules('min:3|max:190|required');
        // $form->currency('amount', trans('service_type.fields.amount'))->rules('min:1|required');
        return $form;
    }
}
