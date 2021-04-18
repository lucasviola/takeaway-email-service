<?php

namespace App\Service;

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
        $this->queueEmailService->publish($message);
    }
}
