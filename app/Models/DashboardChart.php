<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardChart extends Model
{
    protected $table = 'dashboard_charts';
    protected $guarded = [];
    const REUQESTS = 1;
    const BRANCHS = 2;

    public function setCountsAttribute($counts)
    {
        if (is_array($counts)) {
            $this->attributes['counts'] = json_encode($counts);
        }
    }

    public function getCountsAttribute($counts)
    {
        if (null != $counts) {
            return json_decode($counts, true);
        } else {
            return json_decode('[]', true);
        }
    }
}
