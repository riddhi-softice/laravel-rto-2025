<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    protected $table = 'videos';
    protected $fillable = ['title','video_url','video_button','button_link'];
}
