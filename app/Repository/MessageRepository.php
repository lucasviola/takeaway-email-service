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

    public function saveMessage(Message $message) {

        $attributes = $this->messageMapper->mapMessageToMessageEntity($message);
        $messageEntity = new MessageEntity($attributes);

        $messageEntity->save();
    }
}
