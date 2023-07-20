<?php

namespace App\Models;

use App\Traits\LogTrait;
use Encore\Admin\Auth\Database\OperationLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProvider extends Model
{
    use SoftDeletes;
    use LogTrait;
    protected $table = 'embessies';

    protected static function boot()
    {
        parent::boot();
        static::created(function ($service_provider) {
            $service_provider->create_log('Create ServiceProvider '.$service_provider->id, 'NEW');
        });
        static::deleted(function ($service_provider) {
            $service_provider->create_log('Delete ServiceProvider', 'DELETED');
        });
        static::updated(function ($service_provider) {
            if ('PUT' == request()->method()) {
                $path = substr(request()->path(), 0, 255);
                $operation_log = OperationLog::where('input', 'Update ServiceProvider '.$service_provider->id)->where('path', $path)->first();
                // dd($operation_log, $path);
                if (is_null($operation_log)) {
                    $service_provider->create_log('Update ServiceProvider '.$service_provider->id, 'UPDATED');
                }
            }
        });
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id')->withDefault();
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branches_embessies', 'embassy_id', 'branch_id');
    }
}
