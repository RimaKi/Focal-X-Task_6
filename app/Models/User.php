<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements JWTSubject
{
    use  HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'national_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class)
            ->withPivot('role', 'contribution_hours', 'last_activity')
            ->withTimestamps();
    }

    public function tasksForUser()
    {
        return $this->belongsToMany(Task::class)
            ->withPivot('duration')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasManyThrough(
            Task::class,
            ProjectUser::class,
            'user_id', // المفتاح الأساسي في جدول projects
            'project_id', // المفتاح الأجنبي في جدول tasks
            'id', // المفتاح الأساسي في جدول users
            'project_id'  // المفتاح الأساسي في جدول project_user
        );
    }


}
