<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchesEmbessies extends Model
{
    use SoftDeletes;

    protected $table = 'branches_embessies';
}
