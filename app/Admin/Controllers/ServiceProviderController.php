<?php

namespace App\Admin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\Country;
use App\Models\ServiceProvider;
use App\Models\RequestType;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;

class ServiceProviderController extends Controller
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
            ->header(trans('embessies.index.header'))
            ->description(trans('embessies.index.description'))
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
            ->header(trans('embessies.show.detail'))
            ->description(trans('embessies.show.description'))
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
            ->header(trans('embessies.edit.edit'))
            ->description(trans('embessies.edit.description'))
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
            ->header(trans('embessies.create.create'))
            ->description(trans('embessies.create.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ServiceProvider);

        $grid->snl(trans('embessies.fields.snl'));
        $grid->title(trans('embessies.fields.title'));
        $grid->address(trans('embessies.fields.address'));
        $grid->phone_number(trans('embessies.fields.phone_number'));
        $grid->email(trans('embessies.fields.email'));
        $grid->country_id(trans('embessies.fields.country_id'))->display(function ($country_id) {
            $country = Country::find($country_id);
            if ($country) {
                return $country->title;
            }
        });
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            // $filter->like('phone_number', trans('embessies.fields.phone_number'));
            // $filter->like('email', trans('embessies.fields.email'));
            $filter->equal('country_id', trans('embessies.fields.country_id'))->select(Country::pluck('title as text', 'id'));
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
        $show = new Show(ServiceProvider::findOrFail($id));

        $show->snl(trans('embessies.fields.snl'));
        $show->title(trans('embessies.fields.title'));
        $show->address(trans('embessies.fields.address'));
        $show->phone_number(trans('embessies.fields.phone_number'));
        $show->email(trans('embessies.fields.email'));
        $show->country_id(trans('embessies.fields.country_id'))->as(function ($content) {
            return $this->country->title;
        });
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
        $form = new Form(new ServiceProvider);

        $form->text('title', trans('embessies.fields.title'))->rules('min:3|max:190|required');
        $form->select('request_type_id', 'Request Type')->options(function ($id) {
            $request_type = RequestType::find($id);
            $this->request_type_id = $id;
            if ($request_type) {
                return [$request_type->id => $request_type->title];
            } else {
                return  RequestType::pluck('title as text', 'id');
            }
        })->rules('required');
        $form->text('address', trans('embessies.fields.address'))->rules('min:3|max:190|required');
        $form->mobile('phone_number', trans('embessies.fields.phone_number'))->rules('min:3|max:10|required')->options(['mask' => '9999999999']);
        $form->email('email', trans('embessies.fields.email'))->rules('min:3|max:190|required');
        $form->select('country_id', trans('embessies.fields.country_id'))->options(Country::pluck('title as text', 'id'))->rules('required_if:request_type_id,1', ['country_id.required_if' => 'Country Required when service is Embassy']);
        $form->saved(function ($form) {
            $form->model()->snl = 'EMB00'.$form->model()->id;
            $form->model()->save();
        });
        return $form;
    }
}
