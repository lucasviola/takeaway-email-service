<?php

namespace App\Service;

use App\Mapper\MessageMapper;
use App\Model\Message;
use App\Repository\MessageRepository;

class MessageService
{

    private PostEmailService $postEmailService;
    private MessageRepository $messageRepository;
    private MessageMapper $messageMapper;

    public function __construct(PostEmailService $postEmailService,
                                MessageRepository $messageRepository,
                                MessageMapper $messageMapper)
    {
        $this->postEmailService = $postEmailService;
        $this->messageRepository = $messageRepository;
        $this->messageMapper = $messageMapper;
    }

    public function sendEmail(Message $message): void
    {
        $messageSent = $this->postEmailService->post($message);

        $entity = $this->messageMapper->mapMessageToMessageEntity($message, $messageSent);
        $this->messageRepository->saveMessage($entity);
    }

    public function findAllMessages()
    {
        return $this->messageRepository->findAll();
    }
}
