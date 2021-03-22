<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 21.3.22
 * Time: 13.32
 */

namespace App\Services;


use App\Exceptions\GtSalvumValidateException;
use App\Task;
use App\Transformers\TaskTransformer;
use App\User;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class TasksService
{
    /**
     * @param User $u
     * @return array
     */
    public function getMyTasks(User $u ) {
        $tasks = $u->tasks()->get();
        $resource = new Collection($tasks, new TaskTransformer());

        $fractal = new Manager();
        $array = $fractal->createData($resource)->toArray();
        return $array;
    }

    /**
     * @param array $data
     * @param User $u
     * @return int
     * @throws GtSalvumValidateException
     */
    public function createTask ( $data, User $u ) {
        $tt = new TaskTransformer();
        $task = $tt->toTask($data);
        $task->owner_id = $u->id;

        $validateErrors = $this->validateTask($task);
        if ( count($validateErrors) > 0 ) {
            $exception = new GtSalvumValidateException('Invalid task data');
            $exception->setErrorMessages($validateErrors);
            throw $exception;
        }

        $task->save();
        return $task->id;
    }

    /**
     * @param Task $task
     * @return array
     * The correct way would be to assign error code in each error case.
     * The messages would be extracted from the translation file for each code.
     */
    public function validateTask(Task $task )
    {
        $errorMessages = [];
        if ( empty($task->name) ) {
            $errorMessages[] = 'Task should have non empty name';
        }

        if ( strlen($task->name) > Task::NAME_LENGTH ) {
            $errorMessages[] = 'Taks name length should exceed '.Task::NAME_LENGTH;
        }

        if ( strlen($task->description) > Task::DESCRIPTION_LENGTH ) {
            $errorMessages[] = 'Taks name length should exceed '.Task::DESCRIPTION_LENGTH;
        }

        if (array_search($task->status, Task::STATUSES) === false ) {
            $errorMessages[] = 'Task status should be one of ['. join ( ', ', Task::STATUSES ).']';
        }

        if (array_search($task->type, Task::TYPES) === false ) {
            $errorMessages[] = 'Task type should be one of ['. join ( ', ', Task::TYPES ).']';
        }

        return $errorMessages;
    }
}