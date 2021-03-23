<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 21.3.23
 * Time: 13.57
 */

namespace App\Transformers;


use App\ViewLog;
use League\Fractal\TransformerAbstract;

class ViewLogTransformer extends TransformerAbstract
{
    /**
     * @param ViewLog $viewLog
     * @return array
     */
    public function transform(ViewLog $viewLog) {
        return [
            'id'             => (int) $viewLog->id,
            'user_id'        => $viewLog->user_id,
            'viewed_at'      => $viewLog->created_at,
        ];
    }
}