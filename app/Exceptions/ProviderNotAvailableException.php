<?php


namespace App\Exceptions;

use Exception;

class ProviderNotAvailableException extends Exception
{

    public function context()
    {
        return $this->message;
    }
}
