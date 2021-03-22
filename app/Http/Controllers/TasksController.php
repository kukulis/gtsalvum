<?php

namespace App\Http\Controllers;

use App\Services\TasksService;
use App\User;

class TasksController extends Controller
{
    /** @var TasksService */
    private $tasksService;

    /**
     * TasksController constructor.
     * @param TasksService $tasksService
     */
    public function __construct(TasksService $tasksService)
    {
        $this->tasksService = $tasksService;
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index() {

        /** @var User $user */
        $user = auth()->user();
        $tasksRestData = $this->tasksService->getMyTasks($user);
        return $tasksRestData;
    }
}
