<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalysisReport extends Model
{
    use HasFactory;

    protected $table = 'analyses'; // مهم جداً

   protected $fillable = [
        'idea_id',
        'predicted_category',
        'confidence',
        'is_ambiguous',
        'top_k',
    ];
    protected $casts = [
        'top_k' => 'array',
        'is_ambiguous' => 'boolean',
        'confidence' => 'float',
    ];

    public function idea()
    {
        return $this->belongsTo(Idea::class, 'idea_id');
    }
}
