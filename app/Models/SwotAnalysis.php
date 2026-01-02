<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwotAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'idea_id',
        'strengths',
        'weaknesses',
        'opportunities',
        'threats',
        
    ];

    /**
     * Cast JSON fields to arrays automatically
     */
    protected $casts = [
        'strengths'     => 'array',
        'weaknesses'    => 'array',
        'opportunities' => 'array',
        'threats'       => 'array',
    ];

    /**
     * Relation: SWOT belongs to Idea
     */
    public function idea()
    {
        return $this->belongsTo(Idea::class);
    }
}
