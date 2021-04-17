<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Mapper\MessageMapper;
use App\Model\Message;
use App\Model\To;
use App\Model\From;
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

        $externalServiceResponse =  $this->service->sendEmail($message);
        $body = json_decode($externalServiceResponse->getContents(), true);

        return response()->json($this->buildResponseBodyFrom($body), 202);
    }

    public function buildResponseBodyFrom($externalServiceResponse): array {
        $status = $externalServiceResponse['Messages'][0]['Status'];
        $messageId = $externalServiceResponse['Messages'][0]['To'][0]['MessageID'];
        $responseBody = ['messageId' => "$messageId", 'status' => $status];

        return $responseBody;
    }
}
