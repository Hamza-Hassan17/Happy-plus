<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'image', 'book_pdf', 'is_active'];

    public function translations(): HasMany
    {
        return $this->hasMany(BookTranslation::class);
    }

    public function translation(string $locale): ?BookTranslation
    {
        return $this->translations->firstWhere('locale', $locale);
    }
}
