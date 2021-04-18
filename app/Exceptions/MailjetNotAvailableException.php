<?php

namespace App\Exceptions;

use Exception;

class MailjetNotAvailableException extends Exception
{

    public function context()
    {
        return $this->message;
    }
}
