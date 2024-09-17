<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{

    /**
     *  Handle the Task "created" event and make some adjustments
     * @param Task $task
     * @return void
     */
    public function created(Task $task): void
    {
        // الحصول على المستخدم الحالي
        $user = auth()->user();

        // تحديث عمود last_activity في جدول project_user
        if ($user) {
            $user->projects()
                ->updateExistingPivot($task->project_id, ['last_activity' => now()]);
        }
    }


    /**
     *  Handle the Task "updated" event and make some adjustments
     * @param Task $task
     * @return void
     */
    public function updated(Task $task): void
    {
        // تحديث last_activity عند تعديل المهمة
        $user = auth()->user();

        if ($user) {
            $user->projects()
                ->updateExistingPivot($task->project_id, ['last_activity' => now()]);
        }
    }


    /**
     *  Handle the Task "deleted" event and make some adjustments
     * @param Task $task
     * @return void
     */
    public function deleted(Task $task): void
    {
        // تحديث last_activity عند حذف المهمة
        $user = auth()->user();

        if ($user) {
            $user->projects()
                ->updateExistingPivot($task->project_id, ['last_activity' => now()]);
        }
    }

}
