<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        return request()->user()->tasks()->toResourceCollection();
    }

    public function show(Task $task)
    {
        return $task->toResource();
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());
        return $task->toResource();
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        return $task->toResource();
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->noContent();
    }
}


