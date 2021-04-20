<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Mapper\MessageMapper;
use App\Service\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    private MessageService $service;
    private MessageMapper $messageMapper;

    public function __construct(MessageService $service, MessageMapper $messageMapper)
    {
        $this->service = $service;
        $this->messageMapper = $messageMapper;
    }

    public function send(Request $request): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);
        $messageId = uniqid();
        $message = $this->messageMapper->mapMessageRequestToDomainModel($requestBody, $messageId);

        $this->service->sendEmail($message);

        $response = ['messageId' => $messageId,'messageStatus' => 'Queued'];
        return response()->json($response, 202);
    }

    public function findAll(): JsonResponse
    {
        $response = $this->service->findAllMessages();

        return response()->json($response, 202);
    }
}
