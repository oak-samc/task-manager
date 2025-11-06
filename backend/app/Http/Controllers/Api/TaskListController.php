<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaskList;
use App\Http\Requests\StoreTaskListRequest;
use App\Http\Requests\UpdateTaskListRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskListController extends Controller
{
    // Listar listas de tarefas com filtros
    public function index(Request $request)
    {
        $query = TaskList::query();

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $taskLists = $query->with(['tasks' => function($query) {
                $query->orderBy('position', 'asc');
            }])
            ->orderBy('position', 'asc')
            ->get();

        return response()->json($taskLists);
    }

    // Criar lista de tarefas e definir posição
    public function store(StoreTaskListRequest $request)
    {
        $lastPosition = TaskList::where('project_id', $request->project_id)
                               ->max('position') ?? 0;

        $taskList = TaskList::create([
            'name' => $request->name,
            'project_id' => $request->project_id,
            'user_id' => $request->user_id,
            'position' => $lastPosition + 1,
        ]);

        return response()->json($taskList, 201);
    }

    // Exibir lista com tarefas
    public function show(TaskList $taskList)
    {
        $taskList->load(['tasks' => function($query) {
            $query->orderBy('position', 'asc');
        }]);

        return response()->json($taskList);
    }

    // Atualizar lista de tarefas
    public function update(UpdateTaskListRequest $request, TaskList $taskList)
    {
        $taskList->update($request->validated());

        return response()->json($taskList);
    }

    // Excluir lista e suas tarefas
    public function destroy(TaskList $taskList)
    {
        try {
            $tasksCount = $taskList->tasks()->count();

            $taskList->tasks()->delete();

            $taskList->delete();

            return response()->json([
                'message' => 'Lista deletada com sucesso',
                'deleted_tasks' => $tasksCount,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro interno do servidor ao deletar lista',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Reordenar listas de tarefas em massa
    public function reorder(Request $request)
    {
        Log::info('TaskList reorder called', [
            'request_data' => $request->all(),
            'lists' => $request->input('lists')
        ]);

        $request->validate([
            'lists' => 'required|array',
            'lists.*.id' => 'required|exists:task_lists,id',
            'lists.*.position' => 'required|numeric',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->lists as $listData) {
                Log::info('Updating TaskList', [
                    'id' => $listData['id'],
                    'new_position' => $listData['position']
                ]);

                TaskList::where('id', $listData['id'])
                       ->update(['position' => $listData['position']]);
            }
        });

        return response()->json(['message' => 'Listas reordenadas com sucesso']);
    }
}
