<?php

namespace App\Repository;

use App\Mapper\MessageMapper;
use App\MessageEntity;
use App\Model\Message;
use App\Model\MessageModel;

class MessageRepository
{
    private MessageMapper $messageMapper;

    public function __construct(MessageMapper $messageMapper)
    {
        $this->messageMapper = $messageMapper;
    }

    public function saveMessage(MessageModel $message): void {

        $attributes = $this->messageMapper->mapMessageToMessageEntity($message);
        $messageEntity = new MessageEntity($attributes);

        $messageEntity->save();
    }

    public function findAll() {
        return MessageEntity::all();
    }
}
