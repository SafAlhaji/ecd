<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Auth\Database\OperationLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profession extends Model
{
    use SoftDeletes;
    use LogTrait;
    protected $table = 'profession';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::created(function ($profession) {
            $profession->create_log('Create Profession '.$profession->id, 'NEW');
        });
        static::deleted(function ($profession) {
            $profession->create_log('Delete Profession', 'DELETED');
        });
        static::updated(function ($profession) {
            if (request()->method() == 'PUT') {
                $path = substr(request()->path(), 0, 255);
                $operation_log = OperationLog::where('input', 'Update Profession '.$profession->id)->where('path', $path)->first();
                // dd($operation_log, $path);
                if (is_null($operation_log)) {
                    $profession->create_log('Update Profession '.$profession->id, 'UPDATED');
                }
            }
        });
    }
}
