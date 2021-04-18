<?php

namespace App\Exceptions;

use Exception;

class MailjetNotAvailable extends Exception
{

    public function context()
    {
        return $this->message;
    }
}
