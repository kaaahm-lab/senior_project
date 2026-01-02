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
        'industry',
        'region',
        'country',
        'company_size',
        'website',
        'similarity_score',
        
    ];

    public function idea()
    {
        return $this->belongsTo(Idea::class);
    }
}
