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
     * @param array $data
     * @return Task
     */
    public function toTask($data) {
        $task = new Task();
        $task->id                  = $data['id'] ?? null;
        $task->name                = $data['Name'] ?? null;
        $task->description         = $data['Description'] ?? null;
        $task->type                = $data['Type'] ?? Task::TYPE_BASIC;
        $task->status              = $data['Status'] ?? Task::STATUS_TODO;

        return $task;
    }
}