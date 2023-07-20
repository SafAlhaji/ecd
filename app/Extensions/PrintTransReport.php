<?php

namespace App\Extensions;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Exporters\AbstractExporter;

class PrintTransReport extends AbstractExporter
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
        $route_post = url('/admin/tax-report/full_report');

        return <<<SCRIPT
$('.fulltransreport-select-submit').off('click').on('click', function () {
    var url = new URL(location.href+'/tax_full_report');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
        $.ajax({
            method: 'post',
            url: '{$route_post}',
            dataType : 'json',
            data:{collection:JSON.stringify({$data_posted})},
            success: function (data) {
                $.pjax.reload('#pjax-container');
                if (typeof data === 'object') {
                    if (data.status) {
                        $("#my-content2-div").html(data.view);
                        printJS('my-content2-div','html');
                    }else{
                        toastr.error('No Data Found!')
                    }
                }
            }
        });
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return " <button type='button' class='fulltransreport-select-submit btn btn-sm btn-danger'>Print
        <i class='fa fa-print'></i>
    </button>
";
    }

    public function __toString()
    {
        return $this->render();
    }
}
