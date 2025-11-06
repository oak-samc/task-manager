<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    // Listar projetos e tarefas relacionadas
    public function index(): JsonResponse
    {
        $projects = Project::with('tasks')->get();
        return response()->json($projects);
    }

    // Criar projeto
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());
        return response()->json($project, 201);
    }

    // Exibir projeto com tarefas
    public function show(Project $project): JsonResponse
    {
        $project->load('tasks');
        return response()->json($project);
    }

    // Atualizar projeto
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project->update($request->validated());
        return response()->json($project);
    }

    // Listar tarefas do projeto
    public function tasks(Project $project): JsonResponse
    {
        $tasks = $project->tasks()
                        ->orderBy('status')
                        ->orderBy('position')
                        ->get();
        return response()->json($tasks);
    }

    // Excluir projeto e relacionamentos
    public function destroy(Project $project): JsonResponse
    {
        try {
            $tasksCount = $project->tasks()->count();
            $taskListsCount = $project->taskLists()->count();

            $project->tasks()->delete();

            $project->taskLists()->delete();

            $project->delete();

            return response()->json([
                'message' => 'Projeto deletado com sucesso',
                'deleted_tasks' => $tasksCount,
                'deleted_task_lists' => $taskListsCount
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro interno do servidor ao deletar projeto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
