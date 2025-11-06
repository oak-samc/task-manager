<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Adiciona colunas task_list_id e position
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('task_list_id')->nullable()->after('project_id');
            $table->decimal('position', 15, 8)->nullable()->after('task_list_id');

            // Foreign key para task_lists
            $table->foreign('task_list_id')->references('id')->on('task_lists')->onDelete('set null');

            // Índice para performance
            $table->index(['task_list_id', 'position']);
        });

        // Backfill: Atribui posições sequenciais por task_list_id
        $tasks = DB::table('tasks')
            ->orderBy('project_id')
            ->orderBy('created_at')
            ->get(['id', 'project_id']);

        $currentProject = null;
        $pos = 0;
        foreach ($tasks as $task) {
            if ($currentProject !== $task->project_id) {
                $currentProject = $task->project_id;
                $pos = 1;
            } else {
                $pos++;
            }
            DB::table('tasks')->where('id', $task->id)->update(['position' => $pos]);
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['task_list_id']);
            $table->dropIndex(['task_list_id', 'position']);
            $table->dropColumn(['task_list_id', 'position']);
        });
    }
};
