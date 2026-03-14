<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'publisher',
        'year',
        'isbn',
        'category',
        'description',
        'cover_image',
        'pdf_file',
        'stock',
    ];

    protected $appends = ['cover_url', 'pdf_url'];

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image
            ? Storage::url($this->cover_image)
            : null;
    }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_file
            ? Storage::url($this->pdf_file)
            : null;
    }
}
