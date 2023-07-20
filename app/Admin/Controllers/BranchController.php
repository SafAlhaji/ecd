<?php

namespace App\Admin\Controllers;

use App\Models\Branch;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\ServiceProvider;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;

class BranchController extends Controller
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
            ->header(trans('branches.index.header'))
            ->description(trans('branches.index.description'))
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
            ->header(trans('branches.show.detail'))
            ->description(trans('branches.show.description'))
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
            ->header(trans('branches.edit.edit'))
            ->description(trans('branches.edit.description'))
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
            ->header(trans('branches.create.create'))
            ->description(trans('branches.create.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Branch);

        $grid->snl(trans('branches.fields.snl'));
        $grid->title(trans('branches.fields.title'));
        $grid->title_ar(trans('branches.fields.title_ar'));
        $grid->phone_number(trans('branches.fields.phone_number'));
        $grid->alt_phone_number('Other Phone');
        $grid->address(trans('branches.fields.address'));
        // $grid->total_revenue('total_revenue');
        $grid->email(trans('branches.fields.email'));
        $grid->requests_code('Code Of Request');
        $grid->transaction_code('Code Of Transactions');

        // $grid->created_at(trans('admin.created_at'));
        // $grid->updated_at(trans('admin.updated_at'));
        $grid->column('embassies', trans('embessies.index.header'))->pluck('title')->label();
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('title', trans('branches.fields.title'));
            $filter->like('snl', trans('branches.fields.snl'));
            // $filter->like('title_ar', trans('branches.fields.title_ar'));
            // $filter->like('phone_number', trans('branches.fields.phone_number'));
            // $filter->like('email', trans('branches.fields.email'));
        });
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
        $show = new Show(Branch::findOrFail($id));

        $show->snl(trans('branches.fields.snl'));
        $show->title(trans('branches.fields.title'));
        $show->title_ar(trans('branches.fields.title_ar'));
        $show->phone_number(trans('branches.fields.phone_number'));
        $show->alt_phone_number('Other Phone');
        $show->address(trans('branches.fields.address'));
        // $show->total_revenue('total_revenue');
        $show->email(trans('branches.fields.email'));
        $show->requests_code('Code Of Request');
        $show->transaction_code('Code Of Transactions');
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
        $form = new Form(new Branch);

        // $form->display('ID');
        $form->text('title', trans('branches.fields.title'))->rules('min:3|max:190|required');
        $form->text('title_ar', trans('branches.fields.title_ar'))->rules('min:3|max:190');
        $form->mobile('phone_number', trans('branches.fields.phone_number'))->options(['mask' => '9999999999'])->rules('min:3|max:10|required');
        $form->mobile('alt_phone_number', 'Other Phone')->options(['mask' => '9999999999'])->rules('min:3|max:10');
        $form->text('address', trans('branches.fields.address'))->rules('min:3|max:190');
        $form->text('requests_code', 'Code Of Request')->rules('min:3|max:190|required');
        $form->text('transaction_code', 'Code Of Transactions')->rules('min:3|max:190|required');
        $form->email('email', trans('branches.fields.email'))->rules('min:3|max:190');
        $form->multipleSelect('serviceproviders', 'Service Providers ')->options(ServiceProvider::all()->pluck('title', 'id'))->rules('required');
        // $form->display(trans('admin.created_at'));
        // $form->display(trans('admin.updated_at'));
        $form->saved(function ($form) {
            $form->model()->snl = 'BR00'.$form->model()->id;
            $form->model()->save();
        });
        return $form;
    }
}
