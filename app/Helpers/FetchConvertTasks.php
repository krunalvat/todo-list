<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Session;

class FetchConvertTasks
{
    public static function fetchTasks()
    {
        $tasks = [
            ['id' => 1, 'title' => 'Task 1', 'completed' => false],
            ['id' => 2, 'title' => 'Task 2', 'completed' => true],
            ['id' => 3, 'title' => 'Task 3', 'completed' => false],
        ];

        if(Session::get('tasks')) {
            return Session::get('tasks');
        } else{
            $lastThreeTasks = array_slice($tasks, -3);
            return $lastThreeTasks;
        }
    
    }
}