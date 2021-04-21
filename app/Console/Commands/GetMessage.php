<?php

namespace App\Console\Commands;

use App\MessageEntity;
use Illuminate\Console\Command;

class GetMessage extends Command
{
    protected $signature = 'message:get';
    protected $description = 'Retrieve all the sent emails.';


    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $messages = MessageEntity::all();

        $this->info('Retrieving all messages...' . PHP_EOL);

        $this->info($messages);

        $this->info('All messages retrieved...' . PHP_EOL);
        return 0;
    }
}
