<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Profession;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;

class CustomerController extends Controller
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
            ->header(trans('customer.index.header'))
            ->description(trans('customer.index.description'))
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
            ->header(trans('customer.show.detail'))
            ->description(trans('customer.index.description'))
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
            ->header(trans('customer.edit.edit'))
            ->description(trans('customer.index.description'))
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
            ->header(trans('customer.create.create'))
            ->description(trans('customer.index.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Customer());

        $grid->snl(trans('customer.fields.snl'));
        $grid->full_name(trans('customer.fields.full_name'))->editable();
        $grid->phone_number(trans('customer.fields.phone_number'));
        // $grid->alt_phone_number(trans('customer.fields.alt_phone_number'));
        // $grid->email(trans('customer.fields.email'));
        // $grid->address(trans('customer.fields.address'));
        // $grid->national_id(trans('customer.fields.national_id'));
        $grid->passport_number('Document No.')->editable();
        $grid->old_passport_numbers('Old Document No.')->display(function ($old_passport_numbers) {
            return implode(' - ', $old_passport_numbers);
        })->label();
        // $grid->passport_image(trans('customer.fields.passport_image'))->image(url($path = 'uploads') . "/", 100, 100);
        // $grid->valid_passport_date(trans('customer.fields.valid_passport_date'));
        $grid->profession_id(trans('customer.fields.profession'))->display(function ($profession_id) {
            $profession = Profession::find($profession_id);
            if ($profession) {
                return $profession->title;
            }
        });
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('phone_number', trans('customer.fields.phone_number'));
            $filter->like('snl', trans('customer.fields.snl'));
            // $filter->like('email', trans('customer.fields.email'));
            $filter->equal('passport_number', trans('customer.fields.passport_number'));
            // $filter->equal('national_id', trans('customer.fields.national_id'));
            $filter->equal('profession_id', trans('customer.fields.profession'))->select(Profession::pluck('title as text', 'id'));
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
        $show = new Show(Customer::findOrFail($id));

        $show->snl(trans('customer.fields.snl'));
        $show->full_name(trans('customer.fields.full_name'));
        $show->phone_number(trans('customer.fields.phone_number'));
        $show->alt_phone_number(trans('customer.fields.alt_phone_number'));
        $show->email(trans('customer.fields.email'));
        $show->address(trans('customer.fields.address'));
        $show->national_id(trans('customer.fields.national_id'));
        $show->passport_number('Document No.');
        // $show->passport_image(trans('customer.fields.passport_image'))->image(url($path = 'uploads') . "/", 100, 100);
        $show->valid_passport_date(trans('customer.fields.valid_passport_date'));
        $show->profession_id(trans('customer.fields.profession'))->as(function ($profession_id) {
            $profession = Profession::find($profession_id);
            if ($profession) {
                return $profession->title;
            }
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
        $form = new Form(new Customer());

        $form->display('ID');
        $form->text('full_name', trans('customer.fields.full_name'))->creationRules('min:3|max:190|required');
        $form->mobile('phone_number', trans('customer.fields.phone_number'))->creationRules('min:3|max:190|required');
        $form->mobile('alt_phone_number', trans('customer.fields.alt_phone_number'))->creationRules('min:3|max:190');
        $form->text('email', trans('customer.fields.email'))->creationRules('min:3|max:190');
        $form->text('address', trans('customer.fields.address'))->creationRules('min:3|max:190');
        $form->text('national_id', trans('customer.fields.national_id'))->creationRules('min:3|max:190');
        // $form->text('passport_number', 'Document No.')->creationRules('min:3|max:190|required|unique:customer,passport_number,{{customer_id}},deleted_at');
        $form->text('passport_number', 'Document No.')
            ->creationRules(['required', 'unique:customer'])
            ->updateRules(['unique:customer,passport_number,{{id}}']);
        // ->rules(function ($form) {
        //     // If it is not an edit state, add field unique verification
        //     if (!$id = $form->model()->id) {
        //         return 'unique:customer,passport_number';
        //     }
        // });
        // $form->image('passport_image', trans('customer.fields.passport_image'))->uniqueName();
        // $form->date('valid_passport_date', trans('customer.fields.valid_passport_date'));
        $form->select('profession_id', trans('customer.fields.profession'))->options(function ($id) {
            $profession_list = Profession::pluck('title as text', 'id');
            $profession_list[0] = 'New Profession';

            return $profession_list;
        })->creationRules('required');
        $form->text('profession_title', 'New Profession Title')->placeholder('Insert New Profession....')->readonly();
        $form->submitted(function (Form $form) {
            if ($form->isCreating()) {
                if (0 == request()->profession_id) {
                    if (request()->profession_title) {
                        $this->profession = Profession::create([
                        'title' => request()->profession_title,
                    ]);
                    } else {
                        $error = new MessageBag([
                        'title' => 'Profession Title Required',
                        'message' => 'Insert New Profession Or select from list',
                    ]);

                        return back()->with(compact('error'));
                    }
                }
            }

            $form->ignore('profession_title');
        });
        $form->saving(function (Form $form) {
            if ($form->isCreating()) {
                if (0 == $form->profession_id) {
                    $form->profession_id = $this->profession->id;
                }
            }
            // dd($form->model(), $form->passport_number);
            if ($form->model()->passport_number != $form->passport_number) {
                $old_passport_numbers = $form->model()->old_passport_numbers;
                $old_passport_numbers[] = $form->model()->passport_number;
                $form->model()->old_passport_numbers = $old_passport_numbers;
                $form->model()->save();
            }
        });
        // $form->saved(function (Form $form) {
        //     dd($form->model()->wasChanged('passport_number'), $form->model()->getRawOriginal('passport_number'));
        // });

        return $form;
    }
}
