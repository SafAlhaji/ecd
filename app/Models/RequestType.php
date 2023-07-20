<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    protected $table = 'requests_types';
    protected $guarded = [];
    const Embassy = 1;
    const General = 2;

    const Requests_Types =[
        1 => 'Embassy Service',
        2 => 'General Service'
    ];
}
