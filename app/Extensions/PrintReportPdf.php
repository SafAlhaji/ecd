<?php

namespace App\Extensions;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Exporters\AbstractExporter;

class PrintReportPdf extends AbstractExporter
{
    private $grid_model;

    public function __construct($grid_model)
    {
        $this->grid_model = $grid_model;
    }

    public function export()
    {
    }

    protected function script()
    {
        // $data_posted = $this->grid_model->getFilter()->execute(true);
        $data_posted = collect(request()->all());
        $url = url('uploads/full_report.pdf');
        $route_post = url('/admin/request_report/full_report');

        return <<<SCRIPT
$('.fullreport-select-submit').off('click').on('click', function () {
    var selected = [];
    $('.fullreport-select-item:checked').each(function () {
        selected.push($(this).val());
    });

    if (selected.length == 0) {
        return;
    }
    var url = new URL(location.href+'/full_report');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
        $.ajax({
            method: 'post',
            url: '{$route_post}',
            dataType : 'json',
            data:{items:selected.toString(),collection:JSON.stringify({$data_posted})},
            success: function (data) {
                $.pjax.reload('#pjax-container');
                if (typeof data === 'object') {
                    if (data.status) {
                        $("#my-content-div").html(data.view);
                        printJS('my-content-div','html');
                    }else{
                        toastr.error('No Data Found!')
                    }
                }
            }
        });
});

$('.fullreport-select-all').off('click').on('click', function () {
    $('.fullreport-select-item').iCheck('check');
    return false;
});

$('.fullreport-select-item').iCheck({
    checkboxClass:'icheckbox_minimal-blue'
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "
        <div class='dropdown pull-right fullreport-selector'>
    <button type='button' class='btn btn-sm btn-danger dropdown-toggle' data-toggle='dropdown'>PDF
        <i class='fa fa-print'></i>
        &nbsp;
        <span class='caret'></span>
    </button>
    <ul class='dropdown-menu' role='menu'>
        <li>
            <ul>
                <li class='checkbox icheck'>
                    <label>
                        <input type='checkbox' class='fullreport-select-item' value='1' />&nbsp;&nbsp;&nbsp; Request No.<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport-select-item' value='2' />&nbsp;&nbsp;&nbsp;Request Date<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport-select-item' value='3' />&nbsp;&nbsp;&nbsp; Name<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport-select-item' value='4' />&nbsp;&nbsp;&nbsp; Service<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport-select-item' value='13' />&nbsp;&nbsp;&nbsp; Service Location<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport-select-item' value='5' />&nbsp;&nbsp;&nbsp; Status<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport-select-item' value='6' />&nbsp;&nbsp;&nbsp; Enrollment no.<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport-select-item' value='7' />&nbsp;&nbsp;&nbsp; Embassy<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport-select-item' value='8' />&nbsp;&nbsp;&nbsp; Batch Ref No.<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport-select-item' value='9' />&nbsp;&nbsp;&nbsp; Service Charge<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport-select-item' value='10' />&nbsp;&nbsp;&nbsp; Embassy Charge <br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport-select-item' value='14' />&nbsp;&nbsp;&nbsp; Tax Amount <br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport-select-item' value='11' />&nbsp;&nbsp;&nbsp; Total<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport-select-item' value='12' />&nbsp;&nbsp;&nbsp; USER<br>
                    </label>

                </li>
            </ul>
        </li>
        <li class='divider'></li>
        <li class='text-right'>
            <button class='btn btn-sm btn-default fullreport-select-all'>ALL</button>&nbsp;&nbsp;
            <button class='btn btn-sm btn-primary fullreport-select-submit'>Submit</button>
        </li>
    </ul>
</div>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
