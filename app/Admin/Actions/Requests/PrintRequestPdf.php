<?php

namespace App\Admin\Actions\Requests;

use Encore\Admin\Admin;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class PrintRequestPdf extends RowAction
{
    public $name = 'Print Pdf';

    public function handle(Model $model)
    {
        session(['newurl' => url('uploads/Request_Receipt/request_receipt_'.$model->id.'.pdf')]);

        return $this->response()->success('Success!')->refresh();
    }
}
