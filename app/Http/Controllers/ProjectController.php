<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\EditRoleUserRequest;
use App\Http\Requests\Project\StoreRequest;
use App\Http\Requests\Project\UpdateRequest;
use App\Http\Requests\Project\UserForProjectRequest;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;

class ProjectController extends Controller
{

    /**
     *  Display a listing of the resource.
     * @return mixed
     */
    public function index()
    {
        return Project::latest()->get();
    }


    /**
     *  Store a newly created resource in storage.
     * @param StoreRequest $request
     * @return string
     */
    public function store(StoreRequest $request)
    {
        $data = $request->only(['name', 'description']);
        Project::create($data);
        return 'Added successfully';
    }



    /**
     *  Display the specified resource.
     * @param Project $project
     * @return Project
     */
    public function show(Project $project)
    {
        return $project->load('users', 'tasks');
    }



    /**
     *  Update the specified resource in storage.
     * @param UpdateRequest $request
     * @param Project $project
     * @return Project
     */
    public function update(UpdateRequest $request, Project $project)
    {
        $data = $request->only(['name', 'description']);
        $project = (new ProjectService())->updateProject($data, $project);
        return $project;
    }


    /**
     *  Remove the specified resource from storage.
     * @param Project $project
     * @return string
     * @throws \Exception
     */
    public function destroy(Project $project)
    {
        if(!$project->delete()){
            throw new \Exception('wrong in delete');
        }
        return 'Deleted Successfully';
    }

    /**
     * add user work in project with role for user
     * @param UserForProjectRequest $request
     * @param Project $project
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function addUsersForProject(UserForProjectRequest $request, Project $project)
    {
        $data = $request->only(['user_id', 'role']);
        $project->users()->attach($data['user_id'], ['role' => $data['role']]);
        return $project->users()->get();
    }

    /**
     * edit role user in project
     * @param UserForProjectRequest $request
     * @param Project $project
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function editRoleUser(UserForProjectRequest $request, Project $project)
    {
        $data = $request->only(['user_id', 'role']);
        $project->users()->updateExistingPivot($data['user_id'], ['role' => $data['role']]);
        return $project->users()->find($data['user_id']);
    }

    /**
     * delete user from project
     * @param Project $project
     * @param User $user
     * @return string
     * @throws \Exception
     */
    public function deleteUser(Project $project, user $user)
    {
        if (!$project->users()->detach($user)) {
            throw new \Exception('Deletion error');
        }
        return 'Deleted Successfully';
    }

    /**
     * @return mixed
     */
    public function projectsForUser()
    {
        return auth()->user()->projects()->with('tasks')->get();
    }

    /**
     * view projects with deleted project
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function withTrashed()
    {
        return Project::withTrashed()->get();
    }

    /**
     * Restore a deleted project
     * @param $project
     * @return string
     * @throws \Exception
     */
    public function restore($project)
    {
        if(!Project::withTrashed()->find($project)->restore()){
            throw new \Exception('wrong in restore');
        }
        return 'restore successfully';
    }

}
