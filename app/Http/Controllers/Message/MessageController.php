<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Service\MessageService;

class MessageController extends Controller
{
    private MessageService $service;

    public function __construct(MessageService $service)
    {
        $this->service = $service;
    }

    public function send() {
        $response = $this->service->sendEmail();
        return $response;
    }
}
