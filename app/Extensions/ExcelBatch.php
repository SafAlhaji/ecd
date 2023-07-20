<?php

namespace App\Extensions;

use Encore\Admin\Admin;

class ExcelBatch
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT
$('.excel-select-submit_$this->id').off('click').on('click', function () {
    var selected = [];

    $('.excel-select-item_$this->id:checked').each(function () {
        selected.push($(this).val());
    });

    if (selected.length == 0) {
        return;
    }
    var url = new URL(location+'/excel');
    var batch_id = $this->id;
    var complete_url = url+'?batch_id='+batch_id+'&items='+selected.toString();
 window.location.assign(complete_url);

});

$('.excel-select-all_$this->id').off('click').on('click', function () {
    $('.excel-select-item_$this->id').iCheck('check');
    return false;
});

$('.excel-select-item_$this->id').iCheck({
    checkboxClass:'icheckbox_minimal-blue'
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());
        return "<div class='dropdown pull-right excel-selector'>
    <button type='button' class='btn btn-sm btn-info dropdown-toggle' data-toggle='dropdown'>EXCEL
        <i class='fa fa-download'></i>
        &nbsp;
        <span class='caret'></span>
    </button>
    <ul class='dropdown-menu' role='menu'>
        <li>
            <ul>
                <li class='checkbox icheck'>
                    <label>
                        <input type='checkbox' class='excel-select-item_$this->id' value='1' />&nbsp;&nbsp;&nbsp; Request No.<br>
                    </label>
                    <label>
                        <input type='checkbox' class='excel-select-item_$this->id' value='2' />&nbsp;&nbsp;&nbsp; Name<br>
                    </label>
                    <label>
                        <input type='checkbox' class='excel-select-item_$this->id' value='3' />&nbsp;&nbsp;&nbsp; Passport No.<br>
                    </label>
                    <label>
                        <input type='checkbox' class='excel-select-item_$this->id' value='4' />&nbsp;&nbsp;&nbsp; Service<br>
                    </label>
                    <label>
                        <input type='checkbox' class='excel-select-item_$this->id' value='5' />&nbsp;&nbsp;&nbsp; Phone No.<br>
                    </label>
                    <label>
                        <input type='checkbox' class='excel-select-item_$this->id' value='6' />&nbsp;&nbsp;&nbsp; Enrollment no.<br>
                    </label>
                    <label>
                        <input type='checkbox' class='excel-select-item_$this->id' value='7' />&nbsp;&nbsp;&nbsp; Renew Note<br>
                    </label>
                    <label>
                        <input type='checkbox' class='excel-select-item_$this->id' value='8' />&nbsp;&nbsp;&nbsp; Status<br>
                    </label>

                </li>
            </ul>
        </li>
        <li class='divider'></li>
        <li class='text-right'>
            <button class='btn btn-sm btn-default excel-select-all_$this->id'>ALL</button>&nbsp;&nbsp;
            <button class='btn btn-sm btn-primary excel-select-submit_$this->id'>Submit</button>
        </li>
    </ul>
</div>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
