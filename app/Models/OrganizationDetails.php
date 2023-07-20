<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Auth\Database\OperationLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationDetails extends Model
{
    use SoftDeletes;
    use LogTrait;
    protected $table = 'organization_details';

    const COUNTRY_KSA = 966;
    const COUNTRY_SUDAN = 249;

    protected static function boot()
    {
        parent::boot();
        static::created(function ($org_details) {
            $org_details->create_log('Create OrganizationDetails '.$org_details->id, 'NEW');
        });
        static::deleted(function ($org_details) {
            $org_details->create_log('Delete OrganizationDetails', 'DELETED');
        });
        static::updated(function ($org_details) {
            if (request()->method() == 'PUT') {
                $path = substr(request()->path(), 0, 255);
                $operation_log = OperationLog::where('input', 'Update OrganizationDetails '.$org_details->id)->where('path', $path)->first();
                // dd($operation_log, $path);
                if (is_null($operation_log)) {
                    $org_details->create_log('Update OrganizationDetails '.$org_details->id, 'UPDATED');
                }
            }
        });
    }
    public function getPhoneNumbersAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    public function setPhoneNumbersAttribute($value)
    {
        $this->attributes['phone_numbers'] = json_encode(array_values($value));
    }
}
