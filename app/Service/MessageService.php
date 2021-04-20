<?php

namespace App\Service;

use App\Repository\MessageRepository;
use App\Model\Message;

class MessageService
{

    private PostEmailService $postEmailService;
    private QueueEmailService $queueEmailService;
    private MessageRepository $messageRepository;

    public function __construct(PostEmailService $postEmailService,
                                QueueEmailService $queueEmailService,
                                MessageRepository $messageRepository)
    {
        $this->postEmailService = $postEmailService;
        $this->queueEmailService = $queueEmailService;
        $this->messageRepository = $messageRepository;
    }

    public function sendEmail(Message $message): void
    {
        $this->postEmailService->post($message);
        $this->messageRepository->saveMessage($message);
    }
}
