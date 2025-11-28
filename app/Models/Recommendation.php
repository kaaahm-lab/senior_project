<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'idea_id',
        'recommendation_text',
        'category',
        'priority',
    ];

    public function idea()
    {
        return $this->belongsTo(Idea::class);
    }
}
