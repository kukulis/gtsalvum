<?php

namespace App\Http\Controllers;

use App\Services\TasksService;
use App\User;
use Illuminate\Http\Request;

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
     * @return array
     */
    public function index() {

        /** @var User $user */
        $user = auth()->user();
        $tasksRestData = $this->tasksService->getMyTasks($user);
        return $tasksRestData;
    }

    /**
     * @param Request $request
     * @return int
     * @throws \App\Exceptions\GtSalvumValidateException
     */
    public function create(Request $request) {
        /** @var User $user */
        $user = auth()->user();
        $data = $request->all();
        $rez = $this->tasksService->createTask($data, $user);
        return $rez;
    }

    /**
     * @param Request $request
     * @param $id
     * @return bool
     * @throws \App\Exceptions\GtSalvumValidateException
     */
    public function update(Request $request, $id) {
        /** @var User $user */
        $user = auth()->user();
        $data = $request->all();
        $rez = $this->tasksService->updateTask($data, $id, $user);
        return $rez;
    }

    /**
     * @param $id
     * @return bool|null
     * @throws \App\Exceptions\GtSalvumException
     * @throws \App\Exceptions\GtSalvumValidateException
     */
    public function delete($id) {
        /** @var User $user */
        $user = auth()->user();
        $rez = $this->tasksService->deleteTask($id,$user);
        return $rez;
    }

    /**
     * @param int $id
     * @return array
     * @throws \App\Exceptions\GtSalvumValidateException
     */
    public function show($id) {
        /** @var User $user */
        $user = auth()->user();
        $rez = $this->tasksService->showTask($id,$user);
        return $rez;
    }


}
