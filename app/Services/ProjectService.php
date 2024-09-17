<?php

namespace App\Services;

use App\Models\Project;

class ProjectService
{
    /**
     * update project
     * @param array $data
     * @param Project $project
     * @return Project
     */
    public function updateProject(array $data , Project $project){
        $final_data = array_filter($data, function ($value) {
            return !is_null($value);
        });
        $project->update($final_data);
        return $project;
    }
}
