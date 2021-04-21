<?php

namespace App\Repository;

use App\Mapper\MessageMapper;
use App\MessageEntity;
use App\Model\Message;

class MessageRepository
{
    private MessageMapper $messageMapper;

    public function __construct(MessageMapper $messageMapper)
    {
        $this->messageMapper = $messageMapper;
    }

    public function saveMessage(MessageEntity $messageEntity): void {
        $messageEntity->save();
    }

    public function findAll() {
        return MessageEntity::all();
    }
}
