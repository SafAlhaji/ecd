<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Auth\Database\OperationLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;
    use LogTrait;
    protected static function boot()
    {
        parent::boot();
        static::created(function ($country) {
            $country->create_log('Create Country '.$country->id, 'NEW');
        });
        static::deleted(function ($country) {
            $country->create_log('Delete Country', 'DELETED');
        });
        static::updated(function ($country) {
            if (request()->method() == 'PUT') {
                $path = substr(request()->path(), 0, 255);
                $operation_log = OperationLog::where('input', 'Update Country '.$country->id)->where('path', $path)->first();
                // dd($operation_log, $path);
                if (is_null($operation_log)) {
                    $country->create_log('Update Country '.$country->id, 'UPDATED');
                }
            }
        });
    }
    public function emabssies()
    {
        return $this->hasMany(Country::class);
    }
}
