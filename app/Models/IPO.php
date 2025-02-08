<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class IPO extends Authenticatable
{
    protected $table = "ipos";
    protected $guarded = [];
}
