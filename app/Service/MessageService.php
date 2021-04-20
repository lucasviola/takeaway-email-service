<?php

namespace App\Service;

use App\Message;
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

    public function sendEmail($message): void
    {
//        $this->queueEmailService->publish($message);

        $messageToBeSaved = new Message('lucas@email.com', 'takemeawaytotakeaway@netherlands.com',
            'subject', 'message');

        try {
            $messageToBeSaved->save();
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }

//        $this->postEmailService->post($message);
    }
}
