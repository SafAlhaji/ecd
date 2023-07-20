<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestStatus extends Model
{
    use SoftDeletes;

    protected $table = 'request_status';
    const PENDING = 1;
    const Preparing_to_Send_Embassy = 2;
    const IN_EMBASSY = 3;
    const At_Office = 4;
    const COMPELETED = 5;
    const request_status =
    [
        1 => 'PENDING',
        3 => 'In Embassy',
        4 => 'At Office',
        5 => 'COMPELETED',
    ];
}
