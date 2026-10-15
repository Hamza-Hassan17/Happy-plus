<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'thumbnail', 'video', 'video_size', 'is_active'];
}
