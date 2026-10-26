<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimationTranslation extends Model
{
    protected $fillable = ['animation_id', 'locale', 'name'];
}
