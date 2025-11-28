<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'idea_id',
        'name',
        'description',
        'similarity_score',
        'url',
    ];

    public function idea()
    {
        return $this->belongsTo(Idea::class);
    }
}
