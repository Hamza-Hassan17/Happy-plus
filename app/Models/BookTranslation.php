<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookTranslation extends Model
{
    protected $fillable = ['book_id', 'locale', 'name', 'description', 'book_pdf'];
}
