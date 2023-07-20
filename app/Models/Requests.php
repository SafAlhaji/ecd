<?php

namespace App\Models;

use App\Traits\LogTrait;
use Carbon\Carbon;
use Encore\Admin\Auth\Database\OperationLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requests extends Model
{
    use SoftDeletes;
    use LogTrait;
    const Request_NO = 1;
    const Request_Date = 2;
    const FULL_NAME = 3;
    const SERVICE = 4;
    const Status = 5;
    const Enrollment_No = 6;
    const Embassy = 7;
    const Batch_Ref_No = 8;
    const service_charge = 9;
    const embassy_charge = 10;
    const Total = 11;
    const USERNAME = 12;
    const Service_Location = 13;
    const TAX_AMOUNT = 14;

    const PAYMENT_TYPE_CASH = 1;
    const PAYMENT_TYPE_LATER = 2;
    const PAYMENT_TYPE_BANK = 3;
    const PAYMENT_TYPE = [
        1 => 'Cash',
        2 => 'Credit',
        3 => 'Bank',
    ];
    const PAYMENT_STATUS = [
        0 => 'Not Paid',
        1 => 'Paid',
    ];
    const PAYMENT_STATUS_NOT_PAID = 0;
    const PAYMENT_STATUS_PAID = 1;
    protected $table = 'requests';
    protected $guarded = [];

    protected static function boot()
    {
        $info = OrganizationDetails::find(1);
        parent::boot();
        static::created(function ($req) use ($info) {
            // $req->request_status_id = RequestStatus::PENDING;
            $req->delivery_date_time = Carbon::parse($req->request_created_at)->addMonths(intval($info->month_config));
            $req->save();
            $logs = $req->create_log('Create Request '.$req->id, 'NEW');
        });
        static::deleted(function ($req) {
            // $req->create_log('Delete Request '.$req->id, 'DELETED');
        });
        static::updated(function ($req) {
            if ('PUT' == request()->method()) {
                $path = substr(request()->path(), 0, 255);
                $operation_log = OperationLog::where('input', 'Update Request '.$req->id)->where('path', $path)->first();
                if (is_null($operation_log)) {
                    $req->create_log('Update Request '.$req->id, 'UPDATED');
                }
            }
        });
    }

    public function scopeMonthly($query)
    {
        return $query->whereBetween('request_created_at', [Carbon::now()->subMonth()->format('Y-m-d'), Carbon::now()->format('Y-m-d')]);
    }

    public function setQrStringtAttribute($value)
    {
        $info = OrganizationDetails::find(1);
        // dd($info);
        $qr_string = $this->zatca_qr_text($info->title, $info->tax_number, $this->request_created_at, $this->amount, $this->tax_amount);

        return  $qr_string;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id')->withDefault();
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id')->withDefault();
    }

    public function service_type()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id', 'id')->withDefault();
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class, 'profession_id', 'id')->withDefault();
    }

    public function embassy()
    {
        return $this->belongsTo(ServiceProvider::class, 'embassy_id', 'id')->withDefault();
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id', 'id')->withDefault();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->withDefault();
    }

    public function username()
    {
        return $this->belongsTo(ThirdParty::class, 'staff_id', 'id')->withDefault();
    }

    public function transactions_history()
    {
        return $this->hasOne(TransactionsHistory::class, 'request_id', 'id');
    }

    public function zatca_qr_text()
    {
        $string = '';
        $info = OrganizationDetails::find(1);
        $seller = $info->title ?? '';
        $tax_number = $info->tax_number ?? '';
        $invoice_date = $this->created_at;
        // $invoice_total_amount = $this->amount;
        $invoice_tax_amount = round($this->tax_amount, 2);
        $invoice_total_amount = round($this->service_charge, 2) + $invoice_tax_amount;
        $invoice_date = Carbon::parse($invoice_date)->toIso8601ZuluString();
        $string .= $this->toHex(1).$this->toHex(strlen($seller)).($seller);
        $string .= $this->toHex(2).$this->toHex(strlen($tax_number)).($tax_number);
        $string .= $this->toHex(3).$this->toHex(strlen($invoice_date)).($invoice_date);
        $string .= $this->toHex(4).$this->toHex(strlen($invoice_total_amount)).($invoice_total_amount);
        $string .= $this->toHex(5).$this->toHex(strlen($invoice_tax_amount)).($invoice_tax_amount);

        return base64_encode($string);
    }

    /**
     * To convert the string value to hex.
     *
     * @param $value
     *
     * @return false|string
     */
    protected function toHex($value)
    {
        return pack('H*', sprintf('%02X', $value));
    }
}
