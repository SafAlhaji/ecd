<?php

namespace App\Extensions;

use Encore\Admin\Admin;
use App\Models\Requests;
use Encore\Admin\Facades\Admin as AdminUser;

class PrintPdfTransaction
{
    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT
$('.grid-transaction-check-row').on('click', function () {
var transactionurl = $(this).data('transactionurl');
print_page_view(transactionurl)
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a class='btn btn-xs btn-success fa fa-print grid-transaction-check-row' data-id='{$this->id}' data-transactionurl='".url('received_voucher/'.$this->id)."'></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
