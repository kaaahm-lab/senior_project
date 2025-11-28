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
        'strengths',
        'weaknesses',
        'report',
        'pdf_path',
        'report_type',
        'storage_disk',
    ];

    public function idea()
    {
        return $this->belongsTo(Idea::class);
    }
}
