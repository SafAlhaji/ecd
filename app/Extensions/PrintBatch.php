<?php

namespace App\Extensions;

use Encore\Admin\Admin;
use Encore\Admin\Facades\Admin as AdminUser;

class PrintBatch
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        $url =  url('uploads/Batch_Table/batch_table_'.AdminUser::user()->id.'.pdf');
        $route_post = url('/admin/request_batches/print');
        // $url =  url('uploads/Request_Receipt/request_receipt_'.$this->id.'.pdf');
        return <<<SCRIPT
$('.expand-select-submit_$this->id').off('click').on('click', function () {
    var selected = [];

    $('.expand-select-item_$this->id:checked').each(function () {
        selected.push($(this).val());
    });

    if (selected.length == 0) {
        return;
    }
    var url = new URL(location+'/print');
    var batch_id = $this->id;
    var complete_url = url+'?batch_id='+batch_id+'&items='+selected.toString();
        $.ajax({
            method: 'get',
            url: complete_url,
            dataType : 'json',
            data:$(this).serialize(),
            success: function (data) {
                // console.log(data);
                $.pjax.reload('#pjax-container');
                if (typeof data === 'object') {
                    if (data.status) {
                        $("#my-content-div").html(data.view);
                        printJS('my-content-div','html');
                    }
                }
            }
        });
});

$('.expand-select-all_$this->id').off('click').on('click', function () {
    $('.expand-select-item_$this->id').iCheck('check');
    return false;
});

$('.expand-select-item_$this->id').iCheck({
    checkboxClass:'icheckbox_minimal-blue'
});
// $('#print_batch_$this->id').on('click', function () {
//     var batch_id = $(this).data('id');
//     var url = $(this).data('url');
//     var complete_url = url+'?batch_id='+batch_id;
//         $.ajax({
//             method: 'get',
//             url: complete_url,
//             dataType : 'json',
//             data:$(this).serialize(),
//             success: function (data) {
//                 // console.log(data);
//                 $.pjax.reload('#pjax-container');
//                 if (typeof data === 'object') {
//                     if (data.status) {
//                         printJS('{$url}');
//                     }
//                 }
//             }
//         });
// });

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());
        return "<div class='dropdown pull-right expand-selector'>
    <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown'>PDF
        <i class='fa fa-print'></i>
        &nbsp;
        <span class='caret'></span>
    </button>
    <ul class='dropdown-menu' role='menu'>
        <li>
            <ul>
                <li class='checkbox icheck'>
                    <label>
                        <input type='checkbox' class='expand-select-item_$this->id' value='1' />&nbsp;&nbsp;&nbsp; Request No.<br>
                    </label>
                    <label>
                        <input type='checkbox' class='expand-select-item_$this->id' value='2' />&nbsp;&nbsp;&nbsp; Name<br>
                    </label>
                    <label>
                        <input type='checkbox' class='expand-select-item_$this->id' value='3' />&nbsp;&nbsp;&nbsp; Passport No.<br>
                    </label>
                    <label>
                        <input type='checkbox' class='expand-select-item_$this->id' value='4' />&nbsp;&nbsp;&nbsp; Service<br>
                    </label>
                    <label>
                        <input type='checkbox' class='expand-select-item_$this->id' value='5' />&nbsp;&nbsp;&nbsp; Phone No.<br>
                    </label>
                    <label>
                        <input type='checkbox' class='expand-select-item_$this->id' value='6' />&nbsp;&nbsp;&nbsp; Enrollment no.<br>
                    </label>
                    <label>
                        <input type='checkbox' class='expand-select-item_$this->id' value='7' />&nbsp;&nbsp;&nbsp; Renew Note<br>
                    </label>
                    <label>
                        <input type='checkbox' class='expand-select-item_$this->id' value='8' />&nbsp;&nbsp;&nbsp; Status<br>
                    </label>


                </li>
            </ul>
        </li>
        <li class='divider'></li>
        <li class='text-right'>
            <button class='btn btn-sm btn-default expand-select-all_$this->id'>ALL</button>&nbsp;&nbsp;
            <button class='btn btn-sm btn-primary expand-select-submit_$this->id'>Submit</button>
        </li>
    </ul>
</div>
<div id='my-content-div'></div>";
        // return "<a class='btn btn-xs btn-success fa fa-print grid-check-pdf' id='print_batch_$this->id' target='_blank' data-id='{$this->id}' data-url='".url('admin/request_batches/print')."'></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
