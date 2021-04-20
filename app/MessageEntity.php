<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageEntity extends Model
{

    protected $table = 'messages';
    protected $fillable = ['from', 'to', 'messageId','subject', 'email', 'message'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

}
