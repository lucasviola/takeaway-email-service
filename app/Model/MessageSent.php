<?php

namespace App\Model;

class MessageSent
{
    protected array $attributes;

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
