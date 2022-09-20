<?php

namespace App\Console\Commands;

use App\Models\Blog;
use Illuminate\Console\Command;

class BlogCleaner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:cleaner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete soft deleted blogs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return Blog::onlyTrashed()->where(
            'deleted_at', '<=', now()->subDays(7)->toDateTimeString()
        )->forceDelete();
    }
}
