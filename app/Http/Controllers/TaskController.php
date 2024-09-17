<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\AddDurationRequest;
use App\Http\Requests\Task\indexRequest;
use App\Http\Requests\Task\NoteRequest;
use App\Http\Requests\Task\storeRequest;
use App\Http\Requests\Task\updateRequest;
use App\Http\Requests\Task\updateStatusRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     *  Display a listing of the resource with filter
     * @param indexRequest $request
     * @param Project $project
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasOne|object
     */
    public function index(indexRequest $request, Project $project)
    {
        $data_request = $request->only(['status', 'titleCondition']);
        return (new TaskService())->indexTask($data_request, $project);
    }


    /**
     *  Store a new task with add user for this task
     * @param storeRequest $request
     * @param Project $project
     * @return string
     */
    public function store(storeRequest $request, Project $project)
    {
        $data = $request->only(['title', 'description', 'status', 'priority']);
        $data['project_id'] = $project->id;
        $task = Task::create($data);
        $task->users()->attach(auth()->user()->id);
        return 'Added Successfully';
    }


    /**
     *  Display the specified task with projects and user.
     * @param Task $task
     * @return Task
     */
    public function show(Task $task)
    {
        return $task->load('project', 'users');
    }


    /**
     * Update the specified task.
     * @param updateRequest $request
     * @param Task $task
     * @return Task
     */
    public function update(updateRequest $request, Task $task)
    {
        $data = $request->only(['title', 'description', 'status', 'priority', 'project_id']);
        (new TaskService())->updateTask($data, $task);
        return $task;
    }

    /**
     * add user for task
     * @param User $user
     * @param Task $task
     * @return Task
     * @throws \Exception
     */
    public function addUserForTask(User $user, Task $task)
    {
        return (new TaskService())->addUser($user, $task);
    }

    /**
     * Show all tasks related to a project that works with a user
     * @return mixed
     */
    public function userTasks()
    {
        return auth()->user()->tasks()->get();
    }

    /**
     * View usernames and filter by task status and priority your projects
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function filterUser(Request $request)
    {
        $data = User::query();
        if ($request->has('status')) {
            $data = $data->whereRelation('tasks', 'status', $request->status);
        }
        if ($request->has('priority')) {
            $data = $data->whereRelation('tasks', 'priority', $request->priority);
        }
        return $data->get();
    }

    /**
     * update status task
     * @param updateStatusRequest $request
     * @param Task $task
     * @return Task
     */
    public function updateStatus(updateStatusRequest $request, Task $task)
    {
        $data = $request->only('status');
        (new TaskService())->updateStatus($data, $task);
        return $task;
    }

    /**
     * add note for task by tester
     * @param NoteRequest $request
     * @param Task $task
     * @return Task
     */
    public function addNote(NoteRequest $request, Task $task)
    {
        $task->update(['note' => $request->note ?? $task->note]);
        return $task;
    }

    /**
     * add duration for task and update  contribution_hours for project_user
     * @param AddDurationRequest $request
     * @param Task $task
     * @return Task
     */
    public function addDuration(AddDurationRequest $request, Task $task)
    {
        $data = $request->only('duration');
        return (new TaskService())->addDuration($data, $task);

    }

    /**
     * delete user from task
     * @param Task $task
     * @param User $user
     * @return string
     * @throws \Exception
     */
    public function deleteUser(Task $task, User $user)
    {
        if (!$task->users()->detach($user)) {
            throw new \Exception('Deletion error');
        }
        return 'Deleted Successfully';
    }


}
