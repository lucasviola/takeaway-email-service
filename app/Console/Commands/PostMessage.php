<?php

namespace App\Console\Commands;

use App\Mapper\MessageMapper;
use App\Model\From;
use App\Model\Message;
use App\Model\To;
use App\Service\PostEmailService;
use Illuminate\Console\Command;

class PostMessage extends Command
{
    protected $signature = 'message:post';
    protected $description = 'Send e-mail message to provider and saves it to the database.';

    private PostEmailService $postEmailService;

    public function __construct(PostEmailService $postEmailService)
    {
        parent::__construct();
        $this->postEmailService = $postEmailService;
    }

    public function handle()
    {
//        $input = $this->ask('JSON Message to be sent: ');

        $message = new Message(uniqid(), new From('name', 'email'),
            new To('name', 'email'), 'Test', 'Test');

        $this->postEmailService->post($message);

        return 0;
    }
}
