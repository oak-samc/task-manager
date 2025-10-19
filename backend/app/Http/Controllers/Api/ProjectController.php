<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        $projects = Project::with('tasks')->get();
        return response()->json($projects);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());
        return response()->json($project, 201);
    }

    public function show(Project $project): JsonResponse
    {
        $project->load('tasks');
        return response()->json($project);
    }

    public function tasks(Project $project): JsonResponse
    {
        $tasks = $project->tasks()->get();
        return response()->json($tasks);
    }
}
