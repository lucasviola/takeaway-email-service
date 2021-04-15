<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExampleCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Hello! This is an example of a command");
    }
}
