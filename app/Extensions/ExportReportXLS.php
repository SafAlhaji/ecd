<?php

namespace App\Extensions;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Exporters\AbstractExporter;

class ExportReportXLS extends AbstractExporter
{
    protected $grid;

    public function __construct($grid)
    {
        $this->grid = $grid;
    }

    public function export()
    {
    }

    protected function script()
    {
        $data_posted = collect(request()->all());
        //$this->getCollection()->pluck('id');//request()->all();//$this->getData()->all();////$this->grid->getFilter()->execute(true);
        // dd($data_posted);
        $url = url('uploads/report.xlsx');
        $route_post = url('/admin/request_report/full_report_excel');

        return <<<SCRIPT
$('.fullreport_excel-select-submit').off('click').on('click', function () {
    var selected = [];
    $('.fullreport_excel-select-item:checked').each(function () {
        selected.push($(this).val());
    });

    if (selected.length == 0) {
        return;
    }
    var url = new URL(location.href+'/full_report_excel');
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
                // console.log(data);
                $.pjax.reload('#pjax-container');
                if (typeof data === 'object') {
                    if (data.status) {
                        window.location.assign('{$url}');
                    }else{
                        toastr.error('No Data Found!')
                    }
                }
            }
        });
});

$('.fullreport_excel-select-all').off('click').on('click', function () {
    $('.fullreport_excel-select-item').iCheck('check');
    return false;
});

$('.fullreport_excel-select-item').iCheck({
    checkboxClass:'icheckbox_minimal-blue'
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<div class='dropdown pull-right fullreport_excel-selector'>
    <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown'>Excel
        <i class='fa fa-download'></i>
        &nbsp;
        <span class='caret'></span>
    </button>
    <ul class='dropdown-menu' role='menu'>
        <li>
            <ul>
                <li class='checkbox icheck'>
                    <label>
                        <input type='checkbox' class='fullreport_excel-select-item' value='1' />&nbsp;&nbsp;&nbsp; Request No.<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport_excel-select-item' value='2' />&nbsp;&nbsp;&nbsp;Request Date<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport_excel-select-item' value='3' />&nbsp;&nbsp;&nbsp; Name<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport_excel-select-item' value='4' />&nbsp;&nbsp;&nbsp; Service<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport_excel-select-item' value='13' />&nbsp;&nbsp;&nbsp; Service Location<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport_excel-select-item' value='5' />&nbsp;&nbsp;&nbsp; Status<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport_excel-select-item' value='6' />&nbsp;&nbsp;&nbsp; Enrollment no.<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport_excel-select-item' value='7' />&nbsp;&nbsp;&nbsp; Embassy<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport_excel-select-item' value='8' />&nbsp;&nbsp;&nbsp; Batch Ref No.<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport_excel-select-item' value='9' />&nbsp;&nbsp;&nbsp; Service Charge<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport_excel-select-item' value='10' />&nbsp;&nbsp;&nbsp; Embassy Charge <br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport_excel-select-item' value='14' />&nbsp;&nbsp;&nbsp; Tax Amount <br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport_excel-select-item' value='11' />&nbsp;&nbsp;&nbsp; Total<br>
                    </label>
                    <label>
                        <input type='checkbox' class='fullreport_excel-select-item' value='12' />&nbsp;&nbsp;&nbsp; USER<br>
                    </label>
                </li>
            </ul>
        </li>
        <li class='divider'></li>
        <li class='text-right'>
            <button class='btn btn-sm btn-default fullreport_excel-select-all'>ALL</button>&nbsp;&nbsp;
            <button class='btn btn-sm btn-primary fullreport_excel-select-submit'>Submit</button>
        </li>
    </ul>
</div>
<div id='my-content-div'></div>";
        // return "<a class='btn btn-xs btn-success fa fa-print grid-check-pdf' id='print_batch' target='_blank' data-id='{$this->id}' data-url='".url('admin/request_batches/print')."'></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
