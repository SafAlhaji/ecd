<?php

namespace App\Extensions;

use Encore\Admin\Admin;
use App\Models\Requests;
use Encore\Admin\Facades\Admin as AdminUser;

class PrintPdf
{
    protected $id;
    protected $type_url;
    public function __construct($id, $type_url)
    {
        $this->id = $id;
        $this->type_url = $type_url;
    }

    protected function script()
    {
        // url('uploads/Request_Receipt/request_receipt_'.Admin::user()->id.'.pdf');
        return <<<SCRIPT
$('.grid-check-row').on('click', function () {
var url = $(this).data('url');
// console.log(url);
print_page_view(url)
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a class='btn btn-xs btn-success fa fa-print grid-check-row' data-id='{$this->id}' data-url='".url($this->type_url)."'></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
