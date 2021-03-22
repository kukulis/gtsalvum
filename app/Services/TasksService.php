<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 21.3.22
 * Time: 13.32
 */

namespace App\Services;


use App\User;

class TasksService
{
    public function getMyTasks(User $u ) {
        // TODO use fractal
        return $u->tasks()->get();
    }
}