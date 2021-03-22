<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 21.3.22
 * Time: 13.32
 */

namespace App\Services;


use App\Transformers\TaskTransformer;
use App\User;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class TasksService
{
    public function getMyTasks(User $u ) {
        $tasks = $u->tasks()->get();
        $resource = new Collection($tasks, new TaskTransformer());

        $fractal = new Manager();
        $array = $fractal->createData($resource)->toArray();
        return $array;
    }
}