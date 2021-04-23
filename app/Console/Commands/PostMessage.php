<?php

namespace App\Console\Commands;

use App\Model\Message;
use App\Service\PostEmailService;
use App\Utils\JSONParser;
use Illuminate\Console\Command;

class PostMessage extends Command
{
    protected $signature = 'message:post';
    protected $description = 'Send e-mail message to provider and saves it to the database.';

    public function __construct(PostEmailService $postEmailService)
    {
        parent::__construct();
        $this->postEmailService = $postEmailService;
    }

    public function handle()
    {
        $this->alert('To be implemented.');
        return 0;
    }
}
