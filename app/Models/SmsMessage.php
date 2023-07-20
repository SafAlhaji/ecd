<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Auth\Database\OperationLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsMessage extends Model
{
    use SoftDeletes;
    use LogTrait;
    const Submit = 1;//pending
    const In_Embassy = 3;
    const At_Office = 4;
    const Completed = 5;
    const Enrollment = 10;

    protected $table = 'sms_message';

    const TYPES = [1 => 'submit',3 =>'In Embassy' ,4 => 'At Office' , 5 => 'Completed',10 => 'Enrollment'];

    protected static function boot()
    {
        parent::boot();
        static::created(function ($sms_message) {
            $sms_message->create_log('Create SmsMessage '.$sms_message->id, 'NEW');
        });
        static::deleted(function ($sms_message) {
            $sms_message->create_log('Delete SmsMessage', 'DELETED');
        });
        static::updated(function ($sms_message) {
            if (request()->method() == 'PUT') {
                $path = substr(request()->path(), 0, 255);
                $operation_log = OperationLog::where('input', 'Update SmsMessage '.$sms_message->id)->where('path', $path)->first();
                // dd($operation_log, $path);
                if (is_null($operation_log)) {
                    $sms_message->create_log('Update SmsMessage '.$sms_message->id, 'UPDATED');
                }
            }
        });
    }
}
