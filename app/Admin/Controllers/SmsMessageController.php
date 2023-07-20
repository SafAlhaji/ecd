<?php

namespace App\Admin\Controllers;

use App\Models\SmsMessage;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SmsMessageController extends Controller
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
            ->header(trans('admin.index'))
            ->description(trans('admin.description'))
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
            ->header(trans('admin.detail'))
            ->description(trans('admin.description'))
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
            ->header(trans('admin.edit'))
            ->description(trans('admin.description'))
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
        $grid = new Grid(new SmsMessage);

        // $grid->id('ID');
        $grid->title('Title')->display(function ($title) {
            return SmsMessage::TYPES[intval($title)] ?? '';
        });
        $grid->message('Message');
        // $grid->message_other_lang('Message other Lang');
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
        $show = new Show(SmsMessage::findOrFail($id));

        // $show->id('ID');
        $show->title('Title')->as(function ($title) {
            return SmsMessage::TYPES[$title] ?? '';
        });
        $show->message('Message');
        // $show->message_other_lang('Message other Lang');
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
        $form = new Form(new SmsMessage);

        // $form->display('ID');
        $form->select('title', 'Title')->options(SmsMessage::TYPES)->rules('required');
        $form->text('message', 'Message')->rules('required');
        // $form->radio('message_other_lang', 'Message other Lang')->options([1 => 'Pengal', 2 => 'Other'])->stacked()->default(2)->rules('required');
        // $form->display(trans('admin.created_at'));
        // $form->display(trans('admin.updated_at'));
        $form->saved(function ($form) {
            // $form->model()->title = strtolower($form->model()->title);
            // $form->model()->save();
        });
        return $form;
    }
}
