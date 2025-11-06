<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreListRequest;
use App\Http\Requests\UpdateListRequest;
use App\Models\TaskList;
use Illuminate\Http\JsonResponse;

class ListController extends Controller
{
    // Listar listas com tarefas
    public function index(): JsonResponse
    {
        $lists = TaskList::with('tasks')->get();
        return response()->json($lists);
    }

    // Criar lista
    public function store(StoreListRequest $request): JsonResponse
    {
        $list = TaskList::create($request->validated());
        return response()->json($list, 201);
    }

    // Exibir lista com tarefas
    public function show(TaskList $list): JsonResponse
    {
        $list->load('tasks');
        return response()->json($list);
    }

    // Atualizar lista
    public function update(UpdateListRequest $request, TaskList $list): JsonResponse
    {
        $list->update($request->validated());
        return response()->json($list);
    }

    // Excluir lista e suas tarefas
    public function destroy(TaskList $list): JsonResponse
    {
        try {
            $tasksCount = $list->tasks()->count();
            $list->tasks()->delete();
            $list->delete();

            return response()->json([
                'message' => 'Lista deletada com sucesso',
                'deleted_tasks' => $tasksCount,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro interno do servidor ao deletar lista',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Listar tarefas da lista
    public function tasks(TaskList $list): JsonResponse
    {
        $tasks = $list->tasks()->with('project')->get();
        return response()->json($tasks);
    }

}
