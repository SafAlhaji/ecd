<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Auth\Database\OperationLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;
    use LogTrait;
    protected $table = 'branches';
    protected $guarded = [];
    protected static function boot()
    {
        parent::boot();
        static::created(function ($branch) {
            $branch->create_log('Create Branch '.$branch->id, 'NEW');
        });
        static::deleted(function ($branch) {
            $branch->create_log('Delete Branch', 'DELETED');
        });
        static::updated(function ($branch) {
            if (request()->method() == 'PUT') {
                $path = substr(request()->path(), 0, 255);
                $operation_log = OperationLog::where('input', 'Update Branch '.$branch->id)->where('path', $path)->first();
                // dd($operation_log, $path);
                if (is_null($operation_log)) {
                    $branch->create_log('Update Branch '.$branch->id, 'UPDATED');
                }
            }
        });
    }
    public function thirdparties()
    {
        return $this->belongsToMany(Branch::class, 'admin_users_branches', 'branch_id', 'admin_user_id');
    }
    public function serviceproviders()
    {
        return $this->belongsToMany(ServiceProvider::class, 'branches_embessies', 'branch_id', 'embassy_id');
    }
    public function get_request_code()
    {
        return $this->requests_code ? $this->requests_code : 'REQ00';
    }
    public function get_transaction_code()
    {
        return $this->transaction_code ? $this->transaction_code : 'TRA00';
    }
}
