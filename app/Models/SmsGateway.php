<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Auth\Database\OperationLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsGateway extends Model
{
    use SoftDeletes;
    use LogTrait;
    protected $table = 'sms_gateway';
    protected $guarded = [];
    const GET =1;
    const POST =2;

    const METHODS =
    [
        1 => 'GET',
        2 => 'POST',
    ];

    protected static function boot()
    {
        parent::boot();
        static::created(function ($sms_gateway) {
            $sms_gateway->create_log('Create SmsGateway '.$sms_gateway->id, 'NEW');
        });
        static::deleted(function ($sms_gateway) {
            $sms_gateway->create_log('Delete SmsGateway', 'DELETED');
        });
        static::updated(function ($sms_gateway) {
            if (request()->method() == 'PUT') {
                $path = substr(request()->path(), 0, 255);
                $operation_log = OperationLog::where('input', 'Update SmsGateway '.$sms_gateway->id)->where('path', $path)->first();
                // dd($operation_log, $path);
                if (is_null($operation_log)) {
                    $sms_gateway->create_log('Update SmsGateway '.$sms_gateway->id, 'UPDATED');
                }
            }
        });
    }
    public function getOtherParametersAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    public function setOtherParametersAttribute($value)
    {
        $this->attributes['other_parameters'] = json_encode(array_values($value));
    }
}
