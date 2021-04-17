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
        $requestBodyAsAnArray = $request->toArray();
        $message = $this->messageMapper->mapToDomainModel($requestBodyAsAnArray);

        $response = $this->service->sendEmail($message);

        return response()->json($response->getContents(), 202);
    }
}
