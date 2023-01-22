<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function getAllTasks(Request $request)
    {
       try {
        Log::info('getAllTasks');

        $userId = auth()->user()->id;

        $tasks = Task::query()
            ->where('user_id', $userId)
            ->get();
            // ->toArray();

        return response()->json(
            [
                "success" => true,
                "message" => "Tasks retrieved successfully",
                "data" => $tasks
            ]
        );
       } catch (\Throwable $th) {
        Log::info('Error retrieving getAllTasks: '.$th->getMessage());

        return response()->json(
            [
                "success" => false,
                "message" => "Error retrieving getAllTasks",
            ]
        );
       }
    }

    public function createTask(Request $request)
    {
       try {
        Log::info('createTask');

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "error" => $validator->errors()
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $userId = auth()->user()->id;
        $title = $request->get('title');
        $description = $request->get('description');

        Task::create([
            'title' => $title,
            'description' => $description,
            'status' => false,
            'user_id' => $userId
        ]);

        return response()->json(
            [
                "success" => true,
                "message" => 'Task created successfully',
            ],
            Response::HTTP_CREATED
        );
       } catch (\Throwable $th) {
        Log::info('Error creating Tasks: '.$th->getMessage());

        return response()->json(
            [
                "success" => false,
                "message" => "Error creating Tasks",
            ]
        );
       }
    }
}
