<?php

namespace App\Http\Controllers;

use App\Helpers\FetchConvertTasks;
use Illuminate\Http\Request;
use Session;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = FetchConvertTasks::fetchTasks();

        return view('welcome',compact('tasks'));
    }

    public function addTask(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $tasks = FetchConvertTasks::fetchTasks();

        $existingTask = collect($tasks)->first(function ($task) use ($validatedData) {
            return $task['title'] === $validatedData['title'];
        });

        if ($existingTask) {
            return response()->json([
                'success' => false,
                'message' => 'The task ' . $validatedData['title'] . ' is already in the list.',
            ]);
        }

        $newTask = [
            'id' => count($tasks) + 1,
            'title' => $validatedData['title'],
            'completed' => false,
        ];

        $tasks[] = $newTask;

        Session::put('tasks', $tasks);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'tasks' => $tasks,
        ]);
    }

    public function update($taskId) 
    {
        $tasks = FetchConvertTasks::fetchTasks();

        foreach ($tasks as &$task) {
            if ($task['id'] == $taskId) {
                $task['completed'] = true;
                break;
            }
        }

        Session::put('tasks', $tasks);
        
        return response()->json(['message' => 'Task status updated successfully','tasks' => $tasks], 200);
    }

    public function delete($taskId)
    {
        $tasks = FetchConvertTasks::fetchTasks();

        $index = null;
        foreach ($tasks as $key => $task) {
            if ($task['id'] == $taskId) {
                $index = $key;
                break;
            }
        }

        if ($index !== null) {
            unset($tasks[$index]);
            $tasks = array_values($tasks);
            Session::put('tasks', $tasks);

            return response()->json(['message' => 'Task deleted successfully', 'tasks' => $tasks], 200);
        } else {
            return response()->json(['message' => 'Task not found'], 404);
        }
    }

}
