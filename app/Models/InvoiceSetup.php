<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Auth\Database\OperationLog;

class InvoiceSetup extends Model
{
    protected $table = 'invoice_setup';
    use LogTrait;
    protected static function boot()
    {
        parent::boot();
        static::created(function ($invoice_setup) {
            $invoice_setup->create_log('Create Invoice Setting '.$invoice_setup->id, 'NEW');
        });
        static::deleted(function ($invoice_setup) {
            $invoice_setup->create_log('Delete Invoice Setting', 'DELETED');
        });
        static::updated(function ($invoice_setup) {
            if (request()->method() == 'PUT') {
                $path = substr(request()->path(), 0, 255);
                $operation_log = OperationLog::where('input', 'Update Invoice Setting '.$invoice_setup->id)->where('path', $path)->first();
                // dd($operation_log, $path);
                if (is_null($operation_log)) {
                    $invoice_setup->create_log('Update Invoice Setting '.$invoice_setup->id, 'UPDATED');
                }
            }
        });
    }
}
