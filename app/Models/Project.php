<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

//use Illuminate\Database\Schema\Builder;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description'
    ];
    protected $appends = [
        'workHours'
    ];

    public function getWorkHoursAttribute()
    {
        return $this->users()->sum('contribution_hours');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'contribution_hours', 'last_activity')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function latestTask()
    {
        return $this->hasOne(Task::class)->latestOfMany();
    }

    public function oldestTask()
    {
        return $this->hasOne(Task::class)->oldestOfMany();
    }

    public function highestPriorityTask($titleCondition)
    {
        return $this->hasOne(Task::class)
            ->ofMany(['priority' => 'max'], function ($q) use ($titleCondition) {
                $q->where('title', 'LIKE', "%$titleCondition%");
            });
    }


}
