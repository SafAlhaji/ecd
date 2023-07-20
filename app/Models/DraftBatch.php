<?php

namespace App\Models;

use App\Traits\LogTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class DraftBatch extends Model
{
    use LogTrait;

    protected $table = 'draft_batch';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::created(function ($draft_req) {
            $draft_req->create_log('add Request '.$draft_req->request_id.' to draft', 'NEW');
            $draft_req->embassy_id = $draft_req->requests->embassy_id;
            $draft_req->service_id = $draft_req->requests->service_id;
            $draft_req->save();
        });
        static::deleted(function ($draft_req) {
            $draft_req->create_log('Delete Request '.$draft_req->request_id.' from draft', 'DELETED');
        });
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function requests()
    {
        return $this->belongsTo(Requests::class, 'request_id', 'id')->withDefault();
    }
}
