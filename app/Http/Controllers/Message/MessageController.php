<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Utils\JSONParser;
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
        $requestBody = JSONParser::parseToJson($request->getContent());

        $messageId = uniqid();
        $status = 'Queued';
        $message = $this->messageMapper->mapMessageRequestToDomainModel($requestBody, $messageId, $status);

        $this->service->sendEmail($message);

        $response = ['messageId' => $messageId,'messageStatus' => 'Posted'];
        return response()->json($response, 202);
    }

    public function findAll(): JsonResponse
    {
        $response = $this->service->findAllMessages();

        return response()->json($response, 202);
    }
}
