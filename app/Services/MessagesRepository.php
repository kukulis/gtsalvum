<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 21.3.23
 * Time: 04.51
 */

namespace App\Services;


use App\Message;
use Illuminate\Support\Facades\DB;

class MessagesRepository
{

    /**
     * @param int $userId
     * @param $offset
     * @param $limit
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getUserMessages(int $userId, $offset, $limit ) {
        $sql = /** @lang MySQL */
        "select id from messages m
          where owner_id = $userId
           or  m.task_id in
               (
                  select id
                      from tasks t
                      join task_user tu on t.id=tu.task_id
                  where t.owner_id=$userId or tu.user_id=$userId )
          order by m.created_at
          limit $offset, $limit
        ";

        $messagesIds =DB::select(
            DB::raw($sql));

        $ids = array_map ( function($obj){return $obj->id;}, $messagesIds);

        $messages = Message::query()->whereIn('id', $ids )->get();
        return $messages;
    }
}