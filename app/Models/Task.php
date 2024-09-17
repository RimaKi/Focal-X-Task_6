<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'project_id',
        'note',
        'assigned_to',
        'duration'
    ];

    public function setPriorityAttribute($value)
    {
        switch ($value) {
            case 'medium':
                $this->attributes['priority'] = 1;
                break;
            case 'high':
                $this->attributes['priority'] = 2;
                break;
            default:
                $this->attributes['priority'] = 0;  // إذا كانت القيمة غير معروفة او مدخاة low ، يتم تعيين القيمة 0.
                break;
        }
    }

    public function getPriorityAttribute()
    {
        return $this->attributes['priority'] == 0 ? 'low' : ($this->attributes['priority'] == 1 ? 'medium' : 'high');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }



    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('duration')
            ->withTimestamps();
    }

}
