<?php

namespace App\Console\Commands;

use App\Models\Blog;
use Illuminate\Console\Command;

class BlogList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Blog List';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // define('STDIN',fopen("php://stdin","r"));
        $this->table(
            ['Id','Title', 'Description','Author'],
            Blog::select(['blogs.id','blogs.title', 'blogs.description', 'users.name'])
                ->join('users', 'blogs.user_id', '=', 'users.id')
                ->get()
                ->toArray()
        );
    }
}
