<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Requests\SubmitDraftBatch;
use App\Models\DraftBatch;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\ThirdParty;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DraftBatchController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'DraftBatch';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DraftBatch());
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $grid->model()->whereHas('requests', function ($query) use ($ids) {
                $query->whereIn('branch_id', $ids);
            });
        }
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            if (Admin::user()->isAdministrator()) {
                $ServiceProviderList = ServiceProvider::pluck('title as text', 'id');
            } else {
                $ServiceProviderList = ServiceProvider::whereHas('branches', function ($query) {
                    $query->whereIn('branch_id', ThirdParty::find(Admin::user()->id)->branches()->pluck('branches.id'));
                })->pluck('title as text', 'id');
            }
            $filter->equal('embassy_id', 'Service Provider')->select($ServiceProviderList);
            $filter->equal('service_id', 'Service')->select(Service::pluck('title', 'id'));
        });
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableView();
            $actions->disableEdit();
            // $actions->disableDelete();
        });
        $grid->disableCreateButton();
        $grid->disableColumnSelector();

        // $grid->disableTools();
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new SubmitDraftBatch());
        });

        $grid->disableExport();
        // $grid->column('id', __('Id'));
        $grid->requests('Request')->display(function ($requests) {
            return $requests ? "<a href='/admin/requests/".$requests['id']."'>".$requests['snl'].'</a>' : '';
        });
        $grid->column('Full_Name')->display(function () {
            $full_name = $this->requests->customer->full_name ?? '';

            return $full_name;
        });
        $grid->column('Passport_Number')->display(function () {
            $passport_no = $this->requests->customer->passport_number ?? '';

            return $passport_no;
        });
        $grid->column('Service')->display(function () {
            $service = $this->requests->service->title ?? '';

            return $service;
        });
        $grid->column('created_at', __('Created at'));

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
        $show = new Show(DraftBatch::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('request_id', __('Request id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new DraftBatch());

        // $form->switch('request_id', __('Request id'));

        return $form;
    }
}
