<?php

namespace App\Service;

use App\Mapper\MessageMapper;
use App\MessageEntity;
use App\Model\Message;
use Exception;

class MessageService
{

    private PostEmailService $postEmailService;
    private QueueEmailService $queueEmailService;

    public function __construct(PostEmailService $postEmailService,
                                QueueEmailService $queueEmailService)
    {
        $this->postEmailService = $postEmailService;
        $this->queueEmailService = $queueEmailService;
    }

    public function sendEmail(Message $message): void
    {
//        $this->queueEmailService->publish($message);

        $mapper = new MessageMapper();
        $json = $mapper->mapMessageToJson($message);

        $attributes = [
            'from' => $json['from']['email'],
            'messageId' => uniqid(),
            'to' => $json['to']['email'],
            'subject' => $json['subject'],
            'message' => $json['message']
        ];
        $messageToBeSaved = new MessageEntity($attributes);

        try {
            $messageToBeSaved->save();
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }

//        $this->postEmailService->post($message);
    }
}
