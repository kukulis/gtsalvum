<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Task
 * @package App
 *
 * @property $id int
 * @property $name string
 * @property $description string
 * @property $type string
 * @property $status string
 * @property $owner_id int
 * @property $created_at string
 * @property $updated_at string
 *
 * @method static create($data)
 * @method static truncate
 *
 */

class Task extends Model
{
    const TYPE_BASIC = 'basic';
    const TYPE_ADVANCED = 'advanced';
    const TYPE_EXPERT = 'expert';

    const TYPES = [
        self::TYPE_BASIC,
        self::TYPE_ADVANCED,
        self::TYPE_EXPERT,
    ];

    const STATUS_TODO = 'todo';
    const STATUS_CLOSED = 'closed';
    const STATUS_HOLD = 'hold';

    const STATUSES = [
        self::STATUS_TODO,
        self::STATUS_CLOSED,
        self::STATUS_HOLD,
    ];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
