<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Verify that the user has the role of manager in the project to which the task belongs.
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function updateTask(User $user, Task $task)
    {
        return $user->projects()->wherePivot('project_id', $task->project_id)
            ->wherePivot('role', 'manager')->exists();
    }

    /**
     *  Verify that the user has the role of developer in the project to which the task belongs.
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function updateTaskStatus(User $user, Task $task)
    {
        return $user->projects()->wherePivot('project_id', $task->project_id)
            ->wherePivot('role', 'developer')->exists();
    }

    /**
     *   Verify that the user has the role of tester in the project to which the task belongs.
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function addTestNotes(User $user, Task $task)
    {
        return $user->projects()->wherePivot('project_id', $task->project_id)->where('role', 'tester')->exists();
    }
}
