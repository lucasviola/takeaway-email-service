<?php

namespace App\Exceptions;

use Exception;

class SendGridNotAvailableException extends Exception
{

    public function context()
    {
        return $this->message;
    }
}
