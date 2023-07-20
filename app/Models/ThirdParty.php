<?php

namespace App\Models;

use App\Traits\LogTrait;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\OperationLog;

class ThirdParty extends Administrator
{
    protected $table = 'admin_users';
    protected $guarded = [];
    use LogTrait;
    const ACTIVE = 1;
    const IN_ACTIVE = 2;

    protected static function boot()
    {
        parent::boot();
        static::created(function ($admin_user) {
            $admin_user->create_log('Create User '.$admin_user->id, 'NEW');
        });
        static::deleted(function ($admin_user) {
            $admin_user->create_log('Delete User', 'DELETED');
        });
        static::updated(function ($admin_user) {
            if ('PUT' == request()->method()) {
                $path = substr(request()->path(), 0, 255);
                $operation_log = OperationLog::where('input', 'Update User '.$admin_user->id)->where('path', $path)->first();
                // dd($operation_log, $path);
                if (is_null($operation_log)) {
                    $admin_user->create_log('Update User '.$admin_user->id, 'UPDATED');
                }
            }
        });
    }

    // public function getBranchesAttribute($value)
    // {
    //     return explode(',', $value);
    // }

    // public function setBranchesAttribute($value)
    // {
    //     $this->attributes['branches'] = implode(',', $value);
    // }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'admin_users_branches', 'admin_user_id', 'branch_id');
    }
}
