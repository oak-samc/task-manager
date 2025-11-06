<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskListController;
use App\Http\Controllers\Api\ListController;
use Illuminate\Support\Facades\Route;

// Rotas da API
Route::get('/projects', [ProjectController::class, 'index']);
Route::post('/projects', [ProjectController::class, 'store']);
Route::get('/projects/{project}', [ProjectController::class, 'show']);
Route::put('/projects/{project}', [ProjectController::class, 'update']);
Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
Route::get('/projects/{project}/tasks', [ProjectController::class, 'tasks']);

Route::get('/lists', [ListController::class, 'index']);
Route::post('/lists', [ListController::class, 'store']);
Route::get('/lists/{list}', [ListController::class, 'show']);
Route::put('/lists/{list}', [ListController::class, 'update']);
Route::delete('/lists/{list}', [ListController::class, 'destroy']);
Route::get('/lists/{list}/tasks', [ListController::class, 'tasks']);

Route::get('/task-lists', [TaskListController::class, 'index']);
Route::post('/task-lists', [TaskListController::class, 'store']);
Route::get('/task-lists/{taskList}', [TaskListController::class, 'show']);
Route::put('/task-lists/{taskList}', [TaskListController::class, 'update']);
Route::delete('/task-lists/{taskList}', [TaskListController::class, 'destroy']);
Route::post('/task-lists/reorder', [TaskListController::class, 'reorder']);

Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::get('/tasks/{task}', [TaskController::class, 'show']);
Route::put('/tasks/{task}', [TaskController::class, 'update']);
Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

Route::post('/tasks/reorder', [TaskController::class, 'reorder']);
Route::get('/tasks/test-reorder', [TaskController::class, 'testReorder']);
