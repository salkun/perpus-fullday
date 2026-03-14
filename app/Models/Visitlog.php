<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'purpose',
        'notes',
        'visited_at',
        'left_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'left_at'    => 'datetime',
    ];

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }
}
