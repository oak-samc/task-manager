<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    // Listar tarefas com filtros
    public function index(Request $request): JsonResponse
    {
        $query = Task::with(['project', 'taskList']);
        if ($request->has('project_id')) {
            $query->where('project_id', $request->query('project_id'));
        }
        if ($request->has('task_list_id')) {
            $query->where('task_list_id', $request->query('task_list_id'));
        }
        if ($request->has('use_task_lists') && $request->query('use_task_lists') === 'true') {
            $tasks = $query->orderBy('task_list_id', 'asc')
                          ->orderBy('position', 'asc')
                          ->orderBy('created_at', 'asc')
                          ->get();
        } else {
            $tasks = $query->orderBy('status')
                          ->orderBy('position', 'asc')
                          ->orderBy('created_at', 'asc')
                          ->get();
        }

        return response()->json($tasks);
    }

    // Criar tarefa e ajustar posição
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (!isset($data['position'])) {
            if (isset($data['task_list_id'])) {
                $maxPosition = Task::where('task_list_id', $data['task_list_id'])->max('position');
            } else {
                $maxPosition = Task::where('status', $data['status'] ?? 'pending')->max('position');
            }
            $data['position'] = is_null($maxPosition) ? 1 : ($maxPosition + 1);
        }

        $task = Task::create($data);
        $task->load(['project', 'taskList']);
        return response()->json($task, 201);
    }

    // Exibir tarefa
    public function show(Task $task): JsonResponse
    {
        $task->load(['project', 'taskList']);
        return response()->json($task);
    }

    // Atualizar tarefa
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());
        $task->load(['project', 'taskList']);
        return response()->json($task);
    }

    // Excluir tarefa
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();
        return response()->json(null, 204);
    }

    // Reordenar tarefas em massa
    public function reorder(Request $request): JsonResponse
    {
        Log::info('Reorder method called', ['request_data' => $request->all()]);

        $items = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:tasks,id',
            'items.*.position' => 'required|numeric',
            'items.*.task_list_id' => 'nullable|exists:task_lists,id',
            'items.*.status' => 'nullable|string|in:pending,in_progress,completed',
        ])['items'];

        Log::info('Validated items', ['items' => $items]);

        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                $updateData = [
                    'position' => $item['position'],
                ];

                if (isset($item['task_list_id'])) {
                    $updateData['task_list_id'] = $item['task_list_id'];
                }

                if (isset($item['status'])) {
                    $updateData['status'] = $item['status'];
                }

                $updated = Task::where('id', $item['id'])->update($updateData);
                Log::info('Task updated', ['task_id' => $item['id'], 'update_data' => $updateData, 'updated' => $updated]);
            }
        });

        return response()->json(['message' => 'Tasks reordered successfully'], 200);
    }

    // Endpoint de teste do reorder
    public function testReorder(): JsonResponse
    {
        $tasks = Task::all(['id', 'title', 'position', 'task_list_id']);
        return response()->json([
            'message' => 'Test endpoint working',
            'tasks' => $tasks,
            'reorder_url' => url('/api/tasks/reorder')
        ]);
    }
}
