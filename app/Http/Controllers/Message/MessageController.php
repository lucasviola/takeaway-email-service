<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Mapper\MessageMapper;
use App\Service\MessageService;
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

    public function send(Request $request) {
        $requestBody = json_decode($request->getContent(), true);
        $message = $this->messageMapper->mapToDomainModel($requestBody);

        $externalServiceResponse =
            json_decode($this->service->sendEmail($message)->getContents(), true);

        $messageResponse = $this->messageMapper->mapMailjetResponseToMessageResponse($externalServiceResponse);
        return response()->json($messageResponse, 202);
    }
}
