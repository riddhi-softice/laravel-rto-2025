<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicParam extends Model
{
    
    protected $table = 'url_dynamic_params';
    protected $guarded = [];

    // protected $fillable = ['url_id', 'param_key'];

    public function urlConfig()
    {
        return $this->belongsTo(UrlConfig::class, 'url_id');
    }
}

