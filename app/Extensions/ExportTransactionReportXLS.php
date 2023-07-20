<?php

namespace App\Extensions;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Exporters\AbstractExporter;

class ExportTransactionReportXLS extends AbstractExporter
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
        // $data_posted = $this->getCollection()->pluck('id');//$this->grid->getFilter()->execute(true);
        $data_posted = collect(request()->all());
        $url = url('uploads/tax_report.xlsx');
        $route_post = url('/admin/tax-report/full_report_excel');

        return <<<SCRIPT
$('.fulltransreport_excel-select-submit').off('click').on('click', function () {
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
            data:{collection:JSON.stringify({$data_posted})},
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

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return " <button type='button' class='fulltransreport_excel-select-submit btn btn-sm btn-twitter'>Export
        <i class='fa fa-download'></i>
    </button>
";
    }

    public function __toString()
    {
        return $this->render();
    }
}
