<?php

namespace App\Models;

use App\Traits\LogTrait;
use Encore\Admin\Auth\Database\OperationLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    use LogTrait;
    protected $table = 'customer';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::created(function ($customer) {
            $customer->snl = 'CL00_'.$customer->id; //$customer->check_snl();
            $customer->save();
            $customer->create_log('Create Customer '.$customer->id, 'NEW');
        });
        static::deleted(function ($customer) {
            $customer->create_log('Delete Customer', 'DELETED');
        });
        static::updated(function ($customer) {
            if ('PUT' == request()->method()) {
                $path = substr(request()->path(), 0, 255);
                $operation_log = OperationLog::where('input', 'Update Customer '.$customer->id)->where('path', $path)->first();
                // dd($operation_log, $path);
                if (is_null($operation_log)) {
                    $customer->create_log('Update Customer '.$customer->id, 'UPDATED');
                }
            }
        });
    }

    public function setOldPassportNumbersAttribute($old_passport_numbers): void
    {
        if (is_array($old_passport_numbers)) {
            $this->attributes['old_passport_numbers'] = json_encode($old_passport_numbers);
        }
    }

    public function getOldPassportNumbersAttribute($old_passport_numbers)
    {
        if (null != $old_passport_numbers) {
            return json_decode($old_passport_numbers, true);
        } else {
            return json_decode('[]', true);
        }
    }

    public function check_snl()
    {
        $account_number = $this->create_new_snl();
        $account_exists = self::where('snl', $account_number)->exists();
        if ($account_exists) {
            $this->check_snl();
        } else {
            return $account_number;
        }

        return $account_number;
    }

    public function create_new_snl()
    {
        $number = rand(1000, 9999);
        $account_number = 'CL00'.$number;

        return $account_number;
    }

    public function getPhoneNumberAttribute($phone_number)
    {
        return '0'.$phone_number;
    }

    public function getAltPhoneNumberAttribute($alt_phone_number)
    {
        return $alt_phone_number ? '0'.$alt_phone_number : '';
    }

    public function transaction_history()
    {
        return $this->hasMany(TransactionsHistory::class, 'customer_id', 'id');
    }

    public function requests()
    {
        return $this->hasMany(Requests::class, 'customer_id', 'id');
    }

    public function get_balance_status()
    {
        $balance = $this->creidt - $this->debit;
        if ($balance > 0) {
            return $balance.' creidt';
        } else {
            return $balance.' debit';
        }
    }
}
