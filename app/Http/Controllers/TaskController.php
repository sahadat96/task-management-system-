<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Task;

class TaskController extends Controller
{
     public function index()
    {
        return view('tasks');
    }

    public function store(StoreTaskRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $tasks = [];
            $now = now();
            
            foreach ($request->tasks as $taskData) {
                $tasks[] = array_merge($taskData, [
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
            
            Task::insert($tasks);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => count($tasks) . ' task(s) created successfully',
                'count' => count($tasks)
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTasks()
    {
        $tasks = Task::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $tasks,
            'count' => $tasks->count()
        ]);
    }

}
