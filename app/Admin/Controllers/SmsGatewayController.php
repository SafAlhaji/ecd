<?php

namespace App\Admin\Controllers;

use App\Models\SmsGateway;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SmsGatewayController extends Controller
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
        $grid = new Grid(new SmsGateway);
        $grid->disableCreateButton();
        $grid->disableFilter();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        $grid->disableExport();
        // $grid->id('ID');
        $grid->title('Title');
        $grid->url('url');
        $grid->method('method')->display(function ($method) {
            return SmsGateway::METHODS[$method];
        });
        $grid->message_parameter_name('Message Parameter Name');
        $grid->to_parameter_name('To Parameter Name');
        $grid->sender_parameter_name('Sender Parameter Name');
        $grid->sender_name('Sender Name');
        $grid->other_parameters('Other Parameters')->table();
        // $grid->created_at(trans('admin.created_at'));
        // $grid->updated_at(trans('admin.updated_at'));
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            // $actions->disableEdit();
            // $actions->disableView();
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
        $show = new Show(SmsGateway::findOrFail($id));

        $show->id('ID');
        $show->title('title');
        $show->url('url');
        $show->method('method')->as(function ($value) {
            return SmsGateway::METHODS[$value];
        });
        $show->message_parameter_name('Message Parameter Name');
        $show->to_parameter_name('To Parameter Name');
        $show->sender_parameter_name('Sender Parameter Name');
        $show->sender_name('Sender Name');
        // $show->other_parameters('other_parameters')->as(function ($other_parameters) {
        //     foreach ($other_parameters as $value) {
        //     }
        //     return json_decode($value, true);
        // });
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
        $form = new Form(new SmsGateway);

        // $form->display('ID');
        $form->text('title', 'Title')->rules('min:3|max:190|required');
        $form->url('url', 'URL')->rules('min:3|max:190|required|url');
        $form->select('method', 'Method')->options(SmsGateway::METHODS)->rules('required');
        $form->text('message_parameter_name', 'Message Parameter Name')->rules('min:3|max:190|required|alpha_dash');
        $form->text('to_parameter_name', 'To Parameter Name')->rules('min:3|max:190|required|alpha_dash');
        $form->text('sender_parameter_name', 'Sender Parameter Name')->rules('min:3|max:190|required|alpha_dash');
        $form->text('sender_name', 'Sender Name')->rules('min:3|max:190|required');
        // $form->text('other_parameters', 'Other Parameters');
        $form->table('other_parameters', 'Other Parameters', function ($table) {
            $table->text('key')->rules('alpha_dash');
            $table->text('value')->rules('alpha_dash');
        });
        return $form;
    }
}
