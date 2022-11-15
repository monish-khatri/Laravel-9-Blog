<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HelloWorld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:hello_world';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print Hello World';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->error('Something went wrong!');
        $this->line('Display this on the screen');
        $this->info('Hello World!!!');
    }
}
