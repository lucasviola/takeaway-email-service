<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Model\Message;
use App\Model\To;
use App\Model\From;
use App\Service\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    private MessageService $service;

    public function __construct(MessageService $service)
    {
        $this->service = $service;
    }

    public function send(Request $request) {
        $subject = $request->input('subject');
        $message = $request->input('message');

        $toName = $request->input('to.name');
        $toEmail = $request->input('to.email');
        $to = new To($toName, $toEmail);

        $fromName = $request->input('from.name');
        $fromEmail = $request->input('from.email');
        $from = new From($fromName, $fromEmail);

        $message = new Message($from, $to, $subject, $message);

        $response = $this->service->sendEmail($message);

        return $response;
    }
}
