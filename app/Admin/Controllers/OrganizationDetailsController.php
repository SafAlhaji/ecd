<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrganizationDetails;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OrganizationDetailsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Organization Details')
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
             ->header('Display Organization Details')
            ->description(' ')
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
             ->header('Edit Organization Details')
            ->description(' ')
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
             ->header('Create Organization Details')
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
        $grid = new Grid(new OrganizationDetails());
        $grid->disableCreateButton();
        $grid->disableFilter();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->title('Title');
        // $grid->title_ar('Title (Ar)');
        $grid->activity_title('Activity Title');
        // $grid->activity_title_ar('Activity Title (Ar)');
        $grid->column('phone_numbers')->display(function ($phone_numbers) {
            return implode(' , ', $phone_numbers);
        });
        $grid->app_name('Application Name');
        $grid->tax_number('Tax No.');
        $grid->email('E-Mail');
        $grid->logo_1('Logo 1')->image(url($path = 'uploads').'/', 100, 100);
        $grid->logo_2('Logo 2')->image(url($path = 'uploads').'/', 100, 100);
        $grid->url('WebSite');
        $grid->address('Address');
        $grid->country_code('Country Code')->using([OrganizationDetails::COUNTRY_KSA => 'KSA', OrganizationDetails::COUNTRY_SUDAN => 'Sudan']);
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
     *
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(OrganizationDetails::findOrFail($id));

        // $show->id('ID');
        $show->title('Title');
        $show->title_ar('Title (Ar)');
        $show->activity_title('Activity Title');
        $show->activity_title_ar('Activity Title (Ar)');
        $show->phone_numbers('phone_numbers')->as(function ($phone_numbers) {
            return implode(' , ', $phone_numbers);
        });
        $show->app_name('Application Name');
        $show->tax_number('Tax No.');
        $show->email('E-Mail');
        $show->logo_1('Logo 1')->image(url($path = 'uploads').'/', 100, 100);
        $show->logo_2('Logo 2')->image(url($path = 'uploads').'/', 100, 100);
        $show->url('WebSite');
        $show->address('Address');
        $show->country_code('Country Code')->using([OrganizationDetails::COUNTRY_KSA => 'KSA', OrganizationDetails::COUNTRY_SUDAN => 'Sudan']);

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new OrganizationDetails());

        $form->display('ID');
        $form->text('title', 'Title')->rules('required');
        $form->text('title_ar', 'Title (Ar)')->rules('required');
        $form->text('activity_title', 'Activity Title')->rules('required');
        $form->text('activity_title_ar', 'Activity Title (Ar)')->rules('required');
        $form->text('app_name', 'Application Name')->rules('required');
        $form->text('invoice_title', 'Invoice Title')->rules('required');
        $form->text('invoice_title_ar', 'Invoice Title (Ar)')->rules('required');
        $form->list('phone_numbers', 'Phone Numbers')->rules('required|min:5')->max(10)->min(1);
        $form->text('tax_number', 'Tax No.')->rules('required');
        $form->email('email', 'E-Mail')->rules('required');
        $form->image('logo_1', 'Logo 1')->uniqueName()->rules('required');
        $form->image('logo_2', 'Logo 2')->uniqueName()->rules('required');
        $form->url('url', 'Web Site')->rules('required');
        $form->text('address', 'Address')->rules('required');
        $form->number('month_config', 'Configuration service time')->rules('required')->min(1)->default(1);
        $form->select('country_code', 'Country Code')->options([OrganizationDetails::COUNTRY_KSA => 'KSA', OrganizationDetails::COUNTRY_SUDAN => 'Sudan'])->rules('required');

        return $form;
    }
}
