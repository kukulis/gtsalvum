<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 21.3.22
 * Time: 15.25
 */

namespace App\Transformers;


use App\Task;
use League\Fractal\TransformerAbstract;

class TaskTransformer extends TransformerAbstract
{
    public function transform(Task $task) {
        return [
            'id'      => (int) $task->id,
            'Name'   => $task->name,
            'Description'    => $task->description,
            'Type'    => $task->type,
            'Status'    => $task->status,
            'Created'    => $task->created_at, // may be not 100% correct
            'Updated'    => $task->updated_at,
        ];
    }

}