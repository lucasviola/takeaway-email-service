<?php

namespace App\Service;

use App\Service\PostEmailService;
use Psr\Http\Message\StreamInterface;

class MessageService
{

    private PostEmailService $postEmailService;

    public function __construct(PostEmailService $postEmailService)
    {
        $this->postEmailService = $postEmailService;
    }

    public function sendEmail($message)
    {
        return $this->postEmailService->post($message);
    }
}
