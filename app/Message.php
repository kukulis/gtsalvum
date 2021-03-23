<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Message
 * @package App
 * @property $id int
 * @property $subject string
 * @property $message string
 * @property $owner_id int
 * @property $task_id int
 * @property $created_at string|DateTime
 * @property $updated_at string|DateTime
 * @method static find($id)
 */
class Message extends Model
{
    const SUBJECT_LENGTH = 255;
    const MESSAGE_LENGTH = 4096;
}
