<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Http\MessageRequestValidator;
use App\Mapper\MessageMapper;
use App\Service\MessageService;
use App\Utils\JSONParser;
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
        $requestValidator = new MessageRequestValidator();

        if ($requestValidator->hasErrors($requestBody)) {
            return response()->json(['error' => $requestValidator->getErrors()],
                400);
        }

        $messageId = uniqid();
        $status = 'Posted';
        $message = $this->messageMapper->mapMessageRequestToDomainModel($requestBody, $messageId, $status);

        $this->service->sendEmail($message);

        $response = ['messageId' => $messageId,'messageStatus' => $status];
        return response()->json($response, 202);
    }

    public function findAll(): JsonResponse
    {
        $response = $this->service->findAllMessages();

        return response()->json($response, 202);
    }
}
