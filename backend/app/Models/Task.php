<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'project_id'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
