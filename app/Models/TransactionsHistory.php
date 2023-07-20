<?php

namespace App\Models;

use App\Traits\LogTrait;
use Carbon\Carbon;
use Encore\Admin\Auth\Database\OperationLog;
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TransactionsHistory extends Model
{
    protected $table = 'transactions_history';
    protected $guarded = [];
    const MONEY_IN = 1;
    const MONEY_OUT = 2;

    const Without_Vat = 0;
    const is_vat_include_true = 1;
    const is_vat_include_false = 2;

    use LogTrait;

    protected static function boot()
    {
        parent::boot();
        static::created(function ($transaction) {
            if (self::MONEY_OUT == $transaction->transaction_type) {
                $message = 'Create PaymentVoucher '.$transaction->id;
            } elseif (self::MONEY_IN == $transaction->transaction_type) {
                $message = 'Create ReceivedVoucher '.$transaction->id;
            } else {
                $message = 'Create ReceivedVoucher '.$transaction->id;
            }
            $transaction->create_log($message, 'NEW');
        });
        static::deleted(function ($transaction) {
            if (self::MONEY_OUT == $transaction->transaction_type) {
                $message = 'Delete PaymentVoucher '.$transaction->id;
            }
            if (self::MONEY_IN == $transaction->transaction_type) {
                $message = 'Delete ReceivedVoucher '.$transaction->id;
            }
            $transaction->create_log($message, 'DELETED');
        });
        static::updated(function ($transaction) {
            if ('PUT' == request()->method()) {
                if (self::MONEY_OUT == $transaction->transaction_type) {
                    $message = 'Update PaymentVoucher '.$transaction->id;
                }
                if (self::MONEY_IN == $transaction->transaction_type) {
                    $message = 'Update ReceivedVoucher '.$transaction->id;
                }
                if (isset($message)) {
                    $path = substr(request()->path(), 0, 255);
                    $operation_log = OperationLog::where('input', $message)->where('path', $path)->first();
                    // dd($operation_log, $path);
                    if (is_null($operation_log)) {
                        $transaction->create_log($message, 'UPDATED');
                    }
                }
            }
        });
    }

    public function request()
    {
        return $this->belongsTo(Requests::class, 'request_id', 'id')->withDefault();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id')->withDefault();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->withDefault();
    }

    public function getCreatedAtAttribute($date)
    {
        return \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
    }

    public function create_qr_code()
    {
        // $path = public_path('uploads'.DIRECTORY_SEPARATOR.'transactions_qrCode'.DIRECTORY_SEPARATOR);
        // if (!File::isDirectory($path)) {
        //     File::makeDirectory($path, 0777, true, true);
        // }

        // if (null == $this->qr_string) {
        //     $qr_string = 'transaction_num'.$this->id.'_'.Str::random(5);
        //     // $qr_image = 'transactions_qrCode/'.$qr_string.'.png';

        //     $link = env('APP_URL').'trackTransaction?qr=';
        //     $qr_link = $link.$qr_string;
        //     // $qr_code_image = base64_encode(QrCode::encoding('UTF-8')->format('png')->size(400)->color(0, 0, 0)->backgroundColor(255, 255, 255)->errorCorrection('H')->generate($qr_link, public_path('uploads/' . $qr_image)));
        //     // $this->qr_image = $qr_image;
        //     $this->qr_string = $qr_string;
        //     $this->save();
        // }
    }

    public function zatca_qr_text()
    {
        $string = '';
        $info = OrganizationDetails::find(1);
        $seller = $info->title;
        $tax_number = $info->tax_number;
        $invoice_date = $this->created_at;
        // $invoice_total_amount = $this->amount;
        $invoice_tax_amount = $this->tax_amount;
        $invoice_total_amount = round($this->amount, 2);
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
