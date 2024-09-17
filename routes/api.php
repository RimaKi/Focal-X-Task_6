<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('add-user', 'addUser')->middleware('role:admin');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::post('/change-password', 'changePassword');
    });

    Route::prefix('projects')->controller(ProjectController::class)->group(function () {
        Route::get('/for-user', 'projectsForUser');
        Route::middleware('role:admin')->group(function () {
            Route::get('/trashed', 'withTrashed');
            Route::post('/restore/{project}', 'restore');
            Route::post('/', 'store');
            Route::get('/', 'index');
            Route::get('/{project}', 'show');
            Route::put('/{project}', 'update');
            Route::delete('/{project}', 'destroy');
            Route::post('/add-users-for-project/{project}', 'addUsersForProject');
            Route::put('/edit-role-user/{project}', 'editRoleUser');
            Route::delete('/delete-user/{project}/{user}', 'deleteUser');
            Route::get('/view-project', 'viewProjects')->middleware('role:admin');
        });
    });

    Route::prefix('tasks')->group(function () {

        Route::get('/user-tasks', [TaskController::class, 'userTasks']);
        Route::get('/user-filter', [TaskController::class, 'filterUser']);
        Route::get('/show-task/{task}', [TaskController::class, 'show']);
        Route::post('/add-duration/{task}', [TaskController::class, 'addDuration']);

        Route::get('/{project}', [TaskController::class, 'index'])->middleware('can:manageTask,project');
        Route::post('/{project}', [TaskController::class, 'store'])->middleware('can:manageTask,project');
        Route::patch('/{task}', [TaskController::class, 'update'])->middleware('can:updateTask,task');
        Route::delete('/delete-user/{task}/{user}', [TaskController::class, 'deleteUser'])->middleware('can:updateTask,task');
        Route::post('/add/{user}/{task}', [TaskController::class, 'addUserForTask'])->middleware('can:updateTask,task');
        Route::patch('/status/{task}', [TaskController::class, 'updateStatus'])->middleware('can:updateTaskStatus,task');
        Route::post('/note/{task}', [TaskController::class, 'addNote'])->middleware('can:addTestNotes,task');

    });

});



