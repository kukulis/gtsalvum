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
    /**
     * @param Task $task
     * @return array
     */
    public function transform(Task $task) {
        return [
            'id'          => (int) $task->id,
            'Name'        => $task->name,
            'Description' => $task->description,
            'Type'        => $task->type,
            'Status'      => $task->status,
            'Created'     => $task->created_at, // may be not 100% correct
            'Updated'     => $task->updated_at,
        ];
    }


    /**
     * Out of fractal scope function. Used to transform data from array to Task object.
     * Here we expect to receive the same data as in transform function.
     *
     * @param array $data
     * @return Task
     */
    public function toTask($data, Task $task) {
        $task->id                  = $data['id'] ?? $task->id;
        $task->name                = $data['Name'] ?? $task->name;
        $task->description         = $data['Description'] ?? $task->description;
        $task->type                = $data['Type'] ?? $task->type;
        $task->status              = $data['Status'] ?? $task->status;

        return $task;
    }
}