<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TaskService
{
    /**
     * update task
     * @param array $data
     * @param Task $task
     * @return void
     */
    public function updateTask(array $data, Task $task)
    {
        $final_data = array_filter($data, function ($value) {
            return !is_null($value);
        });
        $task->update($final_data);
    }

    /**
     * index tasks
     * @param array $data_request
     * @param Project $project
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasOne|object
     */
    public function indexTask(array $data_request, Project $project)
    {
        $data = $project->tasks()->get();
        if (array_key_exists('titleCondition', $data_request)) {
            $data = $project->highestPriorityTask($data_request['titleCondition'])->first();
        } elseif (array_key_exists('status', $data_request)) {
            $data = $data_request['status'] ? $project->latestTask()->first() : $project->oldestTask()->first();
        }
        return $data;
    }

    /**
     * add user by admin
     * @param User $user
     * @param Task $task
     * @return Task
     * @throws \Exception
     */
    public function addUser(User $user, Task $task)
    {
        if ($task->users()->wherePivot('user_id', $user->id)->exists()) {
            throw new \Exception('the user already exist in task');
        }
        $task->users()->attach($user->id);
        return $task->load('users');
    }

    /**
     * update status for task by developer
     * @param array $data
     * @param Task $task
     * @return void
     */
    public function updateStatus(array $data, Task $task)
    {
        if ($data['status'] == 'completed') {
            $data['due_date'] = Carbon::now();
        }
        $task->update($data);
    }

    /**
     * add duration for user's task with edit to contribution_hours in pivot table (project_user)
     * @param array $data
     * @param Task $task
     * @return Task
     */
    public function addDuration(array $data, Task $task)
    {
        DB::transaction(function () use ($data, $task) {
            $time = $task->users()->wherePivot('user_id', auth()->user()->id)->first()->pivot->duration;
            $task->users()->updateExistingPivot(auth()->user()->id, ['duration' => ($time + $data['duration'])]);

            $pivot = User::find(auth()->user()->id)->projects();
            $oldHours = $pivot->wherePivot('project_id', $task->project_id)->first()->pivot->contribution_hours;
            $pivot->updateExistingPivot($task->project_id, ['contribution_hours' => ($oldHours + $data['duration'])]);
        });
        return $task->load('users');
    }
}
