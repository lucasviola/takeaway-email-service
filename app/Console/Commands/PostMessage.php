<?php

namespace App\Console\Commands;

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
        $input = $this->ask('JSON Message to be sent: ');


        return 0;
    }
}
