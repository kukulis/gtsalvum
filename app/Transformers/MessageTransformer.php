<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 21.3.23
 * Time: 01.53
 */

namespace App\Transformers;


use App\Message;
use League\Fractal\TransformerAbstract;

class MessageTransformer extends TransformerAbstract
{
    /**
     * @param Message $message
     * @return array
     */
    public function transform(Message $message) {
        return [
            'id'          => (int) $message->id,
            'Subject'     => $message->subject,
            'Message'     => $message->message,
            'task_id'     => $message->task_id,
            'Created'     => $message->created_at,
            'Updated'     => $message->updated_at,
        ];
    }

    /**
     * @param array $data
     * @param Message $message
     * @return Message
     */
    public function toMessage($data, Message $message) {
        $message->subject = $data['Subject'] ?? $message->subject;
        $message->message = $data['Message'] ?? $message->message;
        $message->task_id = $data['task_id'] ?? $message->task_id;
        return $message;
    }
}