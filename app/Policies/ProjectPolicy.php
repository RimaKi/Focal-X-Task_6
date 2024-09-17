<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Verify that the user has the manager role in this project.
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function manageTask(User $user, Project $project)
    {
        return $user->projects()->wherePivot('project_id', $project->id)
            ->wherePivot('role', 'manager')->exists();
    }


}
