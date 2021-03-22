<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 21.3.23
 * Time: 00.12
 */

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;


class UserTransformer  extends TransformerAbstract
{
    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user) {
        return [
            'id'          => (int) $user->id,
            'Name'        => $user->name,
            'Email'       => $user->email,
        ];
    }
}