<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Auth\Database\OperationLog;

class TaxType extends Model
{
    protected $table = 'tax_types';
    use LogTrait;
    protected static function boot()
    {
        parent::boot();
        static::created(function ($tax_type) {
            $tax_type->create_log('Create TaxType '.$tax_type->id, 'NEW');
        });
        static::deleted(function ($tax_type) {
            $tax_type->create_log('Delete TaxType', 'DELETED');
        });
        static::updated(function ($tax_type) {
            if (request()->method() == 'PUT') {
                $path = substr(request()->path(), 0, 255);
                $operation_log = OperationLog::where('input', 'Update TaxType '.$tax_type->id)->where('path', $path)->first();
                // dd($operation_log, $path);
                if (is_null($operation_log)) {
                    $tax_type->create_log('Update TaxType '.$tax_type->id, 'UPDATED');
                }
            }
        });
    }
}
