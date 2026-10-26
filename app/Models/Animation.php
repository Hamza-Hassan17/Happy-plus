<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Animation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'thumbnail', 'video', 'video_size', 'is_active'];

    public function translations(): HasMany
    {
        return $this->hasMany(AnimationTranslation::class);
    }

    public function translation(string $locale): ?AnimationTranslation
    {
        return $this->translations->firstWhere('locale', $locale);
    }
}
