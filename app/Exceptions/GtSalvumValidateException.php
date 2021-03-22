<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 21.3.22
 * Time: 19.00
 */

namespace App\Exceptions;


class GtSalvumValidateException extends \Exception
{

    private $errorMessages;

    /**
     * @return mixed
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * @param mixed $errorMessages
     */
    public function setErrorMessages($errorMessages): void
    {
        $this->errorMessages = $errorMessages;
    }
}