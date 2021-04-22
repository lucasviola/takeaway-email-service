<?php

namespace App\Jobs;

use App\Model\Message;
use App\Service\MessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function handle(MessageService $messageService)
    {
        Log::info('[SendEmailJob@handle] - Queuing message: ' .
            $this->message->getAttributes()['messageId']);

        $messageService->sendEmail($this->message);
    }
}
