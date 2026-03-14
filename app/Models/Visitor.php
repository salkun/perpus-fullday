<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'name',
        'email',
        'phone',
        'address',
        'gender',
        'birth_date',
        'occupation',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function visitLogs()
    {
        return $this->hasMany(VisitLog::class);
    }

    public function latestVisit()
    {
        return $this->hasOne(VisitLog::class)->latestOfMany('visited_at');
    }
}
