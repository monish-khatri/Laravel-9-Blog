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
        $this->table(
            ['Id','Title', 'Description'],
            Blog::all(['id','title', 'description'])->toArray()
        );
    }
}
