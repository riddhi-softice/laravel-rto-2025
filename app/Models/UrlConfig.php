<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlConfig extends Model
{
    protected $table = 'url_config';

    // protected $fillable = ['base_url', 'url_type'];
    protected $guarded = [];

    public function dynamicParams()
    {
        return $this->hasMany(DynamicParam::class, 'url_id');
    }
}
