<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use SoftDeletes;

    protected $table = 'batch';
    protected $guarded = [];
    const Request_NO = 1;
    const FULL_NAME = 2;
    const PASSPORT_NO = 3;
    const SERVICE = 4;
    const PHONE_NO = 5;
    const Enrollment_No = 6;
    const RENEW_NOTE = 7;
    const Status = 8;
    const SELECTED_COLUMNS = [
        1 => 'snl',
        2 => 'full_name',
        3 => 'passport_number',
        4 => 'phone_number',
        5 => 'embassy_serial_number',
        6 => 'renew_note',
        7 => 'status',
        8 => 'request_status_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($batch) {
            foreach ($batch->requests as $req) {
                $req->request_status_id = RequestStatus::PENDING;
                $req->batch_id = null;
                $req->save();
            }
        });
    }

    public function requests()
    {
        return $this->hasMany(Requests::class, 'batch_id', 'id');
    }

    public function username()
    {
        return $this->belongsTo(ThirdParty::class, 'admin_user_id', 'id')->withDefault();
    }
}
