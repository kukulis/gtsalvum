<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 21.3.23
 * Time: 03.41
 */

namespace App\Services;


use App\Task;
use App\User;

class TaskRepository
{

    /**
     * @param Task $task
     * @param $userId
     * @return User
     */
    public function findAssignedUser(Task $task, $userId ) {
        $users = $task->users()->where('id', '=', $userId )->get();
        /** @var User $user */
        $user = $users->shift();
        return $user;
    }
}