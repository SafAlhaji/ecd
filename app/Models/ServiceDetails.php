<?php

namespace App\Models;

use App\Traits\LogTrait;
use Encore\Admin\Auth\Database\OperationLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceDetails extends Model
{
    use SoftDeletes;
    use LogTrait;
    protected $table = 'service_details';
    protected $guarded = [];
    const NO_VAT = 0;
    const WITH_VAT = 1;
    const WITHOUT_VAT = 2;

    const tax_include = [
        0 => 'NO_VAT',
        1 => 'WITH_VAT',
        2 => 'WITHOUT_VAT',
    ];

    protected static function boot()
    {
        parent::boot();
        static::created(function ($service_details) {
            $service_details->snl = 'SRPRO00'.$service_details->id;
            $service_details->save();
            $service_details->create_log('Create ServiceDetails '.$service_details->id, 'NEW');
        });
        static::deleted(function ($service_details) {
            $service_details->create_log('Delete ServiceDetails', 'DELETED');
        });
        static::updated(function ($service_details) {
            if ('PUT' == request()->method()) {
                $path = substr(request()->path(), 0, 255);
                $operation_log = OperationLog::where('input', 'Update ServiceDetails '.$service_details->id)->where('path', $path)->first();
                // dd($operation_log, $path);
                if (is_null($operation_log)) {
                    $service_details->create_log('Update ServiceDetails '.$service_details->id, 'UPDATED');
                }
            }
        });
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class, 'profession_id', 'id')->withDefault();
    }

    public function servicetype()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id', 'id')->withDefault();
    }

    public function tax_type()
    {
        return $this->belongsTo(TaxType::class, 'tax_type_id', 'id')->withDefault();
    }
}
