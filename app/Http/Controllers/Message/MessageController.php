<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Http\MessageRequestValidator;
use App\Jobs\SendEmailJob;
use App\Mapper\MessageMapper;
use App\Model\MessageStatus;
use App\Service\MessageService;
use App\Utils\JSONParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    private MessageService $service;
    private MessageMapper $messageMapper;
    private MessageRequestValidator $requestValidator;

    public function __construct(MessageService $service, MessageMapper $messageMapper,
                                MessageRequestValidator $messageRequestValidator)
    {
        $this->service = $service;
        $this->messageMapper = $messageMapper;
        $this->requestValidator = $messageRequestValidator;
    }

    public function send(Request $request): JsonResponse
    {
        Log::info('[MessageController@send] -
        POST request received. Payload: ' . $request->getContent());

        $requestBody = JSONParser::parseToJson($request->getContent());

        if ($this->requestValidator->hasErrors($requestBody)) {
            Log::error('[MessageController@send] - Message contains validation errors: ' .
                $this->requestValidator->getErrors());

            return response()->json(['error' => $this->requestValidator->getErrors()],
                400);
        }

        $messageId = uniqid();
        $message = $this->messageMapper->mapMessageRequestToDomainModel($requestBody, $messageId,
            MessageStatus::POSTED);

        SendEmailJob::dispatch($message);

        $response = ['messageId' => $messageId,'messageStatus' => MessageStatus::QUEUED];

        return response()->json($response, 202);
    }

    public function findAll(): JsonResponse
    {
        $response = $this->service->findAllMessages();

        return response()->json($response, 202);
    }
}
