<?php

namespace App\Models;

use App\Traits\LogTrait;
use Encore\Admin\Auth\Database\OperationLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;
    use LogTrait;
    protected $table = 'service';

    protected static function boot()
    {
        parent::boot();
        static::created(function ($service) {
            $service->snl = 'SR00'.$service->id;
            $service->save();
            $service->create_log('Create Service '.$service->id, 'NEW');
        });
        static::deleted(function ($service) {
            $service->create_log('Delete Service', 'DELETED');
        });
        static::updated(function ($service) {
            if ('PUT' == request()->method()) {
                $path = substr(request()->path(), 0, 255);
                $operation_log = OperationLog::where('input', 'Update Service '.$service->id)->where('path', $path)->first();
                // dd($operation_log, $path);
                if (is_null($operation_log)) {
                    $service->create_log('Update Service '.$service->id, 'UPDATED');
                }
            }
        });
    }

    public function servicedetails()
    {
        return $this->hasMany(ServiceDetails::class, 'service_id', 'id');
    }
}
