<?php

namespace App\Admin\Controllers;

use App\Models\Country;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CountryController extends Controller
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
            ->header(trans('countries.index.header'))
            ->description(trans('countries.index.description'))
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
            ->header(trans('countries.show.detail'))
            ->description(trans('countries.show.description'))
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
            ->header(trans('countries.edit.edit'))
            ->description(trans('countries.edit.description'))
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
            ->header(trans('countries.create.create'))
            ->description(trans('countries.create.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Country);
        $grid->name_code(trans('countries.fields.name_code'));
        $grid->title(trans('countries.fields.title'));
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('title', trans('countries.fields.title'));
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
        $show = new Show(Country::findOrFail($id));
        $show->name_code(trans('countries.fields.name_code'));
        $show->title(trans('countries.fields.title'));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Country);
        $form->text('name_code', trans('countries.fields.name_code'))->rules(function ($form) {
            if (!$id = $form->model()->id) {
                return 'min:3|max:190|required|unique:countries,name_code';
            } else {
                return 'required';
            }
        });
        $form->text('title', trans('countries.fields.title'))->rules(function ($form) {
            if (!$id = $form->model()->id) {
                return 'min:3|max:190|required|unique:countries,title';
            } else {
                return 'required';
            }
        });
        return $form;
    }
}
