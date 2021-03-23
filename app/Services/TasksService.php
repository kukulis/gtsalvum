<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 21.3.22
 * Time: 13.32
 */

namespace App\Services;


use App\Exceptions\GtSalvumException;
use App\Exceptions\GtSalvumValidateException;
use App\Task;
use App\Transformers\TaskTransformer;
use App\User;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class TasksService
{
    /** @var string Parameter for attaching user to task */
    const ATTACH_USER_ID='AttachUserId';

    /** @var string Parameter for detaching user from task */
    const DETACH_USER_ID='DetachUserId';

    /**
     * @param User $u
     * @return array
     */
    public function getMyTasks(User $u ) {
        $tasks = $u->tasks()->get();
        $assignedTasks= $u->assignedTasks()->get();

        $tasksArr = [];
        foreach ($tasks as $task ) {
            $tasksArr[] = $task;
        }
        foreach ($assignedTasks as $task ) {
            $tasksArr[] = $task;
        }

        $resource = new Collection($tasksArr, new TaskTransformer());

        $fractal = new Manager();
        $array = $fractal->createData($resource)->toArray();
        return $array;
    }

    /**
     * @param array $data
     * @param User $u
     * @return int The id of the created task
     * @throws GtSalvumValidateException
     */
    public function createTask ( $data, User $u ) {
        $tt = new TaskTransformer();
        $task = $tt->toTask($data, new Task());
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
     * @param array $data
     * @param int $id
     * @param User $user
     * @return bool
     * @throws GtSalvumValidateException
     */
    public function updateTask($data, $id, User $user) {
        /** @var Task $task */
        $task = Task::find($id);
        if ( $task == null ) {
            throw new GtSalvumValidateException('There is no task with id '.$id);
        }

        if ( $task->owner_id != $user->id ) {
            throw new GtSalvumValidateException('You are not allowed to update task '.$id);
        }

        $tt = new TaskTransformer();
        $task = $tt->toTask($data, $task);

        // setting the updated time the current moment
        $task->updated_at = new \DateTime();

        $validateErrors = $this->validateTask($task);
        if ( count($validateErrors) > 0 ) {
            $exception = new GtSalvumValidateException('Invalid task data');
            $exception->setErrorMessages($validateErrors);
            throw $exception;
        }

        $assignUserId = null;
        if ( array_key_exists(self::ATTACH_USER_ID, $data)) {
            $assignUserId = $data[self::ATTACH_USER_ID];
            // check if user exists
            $assignUser = User::find($assignUserId);
            if ( $assignUser == null ) {
                throw new GtSalvumValidateException('There is no user with id ['.$assignUserId.'] to attach' );
            }
        }

        $detachUserId = null;
        if ( array_key_exists(self::DETACH_USER_ID, $data)) {
            $detachUserId = $data[self::DETACH_USER_ID];
            // check if user exists
            $detachUser = User::find($detachUserId);
            if ( $detachUser == null ) {
                throw new GtSalvumValidateException('There is no user with id ['.$detachUser.'] to detach' );
            }
        }


        if ($assignUserId != null ) {
            $task->users()->attach($assignUserId);
        }

        if ( $detachUserId != null ) {
            $task->users()->detach($detachUserId);
        }

        $task->save();
        return true;
    }

    /**
     * @param int $id
     * @param User $user
     * @throws GtSalvumValidateException
     * @return array
     */
    public function showTask ($id, User $user) {
        /** @var Task $task */
        $task = Task::find($id);
        if ( $task == null ) {
            throw new GtSalvumValidateException('There is no task with id '.$id);
        }

        if ( $task->owner_id != $user->id ) {
            // may be there should be not a validation error
            throw new GtSalvumValidateException('You are not allowed to view task '.$id);
        }

        $resource = new Item($task, new TaskTransformer());

        $fractal = new Manager();
        $fractal->parseIncludes(['include'=>'users']);
        $data = $fractal->createData($resource)->toArray();

        return $data;
    }

    /**
     * @param $id
     * @param User $user
     * @return bool|null
     * @throws GtSalvumValidateException
     * @throws GtSalvumException
     */
    public function  deleteTask($id, User $user) {
        /** @var Task $task */
        $task = Task::find($id);
        if ( $task == null ) {
            throw new GtSalvumValidateException('There is no task with id '.$id);
        }

        if ( $task->owner_id != $user->id ) {
            // may be there should be not a validation error
            throw new GtSalvumValidateException('You are not allowed to delete task '.$id);
        }

        try {
            $rez = $task->delete();
        } catch ( \Exception $e ) {
            throw new GtSalvumException('Error on deleting task' );
        }
        return $rez;
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