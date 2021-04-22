<?php

namespace App\Service;

use App\Mapper\MessageMapper;
use App\Model\Message;
use App\Repository\MessageRepository;

class MessageService
{

    private PostEmailService $postEmailService;
    private QueueEmailService $queueEmailService;
    private MessageRepository $messageRepository;
    private MessageMapper $messageMapper;

    public function __construct(PostEmailService $postEmailService,
                                QueueEmailService $queueEmailService,
                                MessageRepository $messageRepository,
                                MessageMapper $messageMapper)
    {
        $this->postEmailService = $postEmailService;
        $this->queueEmailService = $queueEmailService;
        $this->messageRepository = $messageRepository;
        $this->messageMapper = $messageMapper;
    }

    public function sendEmail(Message $message): void
    {
        $this->postEmailService->post($message);

        $entity = $this->messageMapper->mapMessageToMessageEntity($message);
        $this->messageRepository->saveMessage($entity);
    }

    public function findAllMessages()
    {
        return $this->messageRepository->findAll();
    }
}
