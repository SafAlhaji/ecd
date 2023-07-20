<?php

use App\Admin\Extensions\Form\CKEditor;
use Encore\Admin\Form;

/*
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 "https://printjs-4de6.kxcdn.com/print.min.js"
"https://printjs-4de6.kxcdn.com/print.min.css"
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */
Form::init(function (Form $form) {
    $form->disableEditingCheck();

    $form->disableCreatingCheck();

    $form->disableViewCheck();

    $form->tools(function (Form\Tools $tools) {
        $tools->disableDelete();
        $tools->disableView();
        // $tools->disableList();
    });
});
Encore\Admin\Form::forget(['map']);
app('view')->prependNamespace('admin', resource_path('views/admin/views'));
Form::extend('ckeditor', CKEditor::class);
Admin::js('https://unpkg.com/printd@1.4.2/printd.umd.min.js');
Admin::js('js/print_view.js');
Admin::js('js/new_requests.js');
Admin::js('js/requests.js');
Admin::js('js/customer.js');
Admin::js('js/service.js');
Admin::css('css/login-ten.css');
Admin::js('https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js');
Admin::js('https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js');
Admin::js('https://cdn.datatables.net/buttons/1.7.0/js/buttons.colVis.min.js');
Admin::js('https://cdn.datatables.net/buttons/1.7.0/js/buttons.flash.min.js');
Admin::js('https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js');
Admin::js('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js');
Admin::js('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js');
Admin::js('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js');
Admin::js('https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js');

Admin::css('https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css');
Admin::css('https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css');
Admin::css('css/expand.css');
Admin::css('css/batch_excel.css');
Admin::css('css/fullreport.css');
Admin::css('css/fullreport_excel.css');
Admin::css('css/custom_form.css');

Admin::css('https://printjs-4de6.kxcdn.com/print.min.css');
Admin::js('https://printjs-4de6.kxcdn.com/print.min.js');
