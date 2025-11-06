<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TaskList extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'user_id',
        'project_id',
        'position',
    ];
    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('position');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
