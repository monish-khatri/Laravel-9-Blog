<?php

namespace App\Console\Commands;

use App\Models\Blog;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BlogDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:delete {--id= Id of the blog}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Blog';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $blogId = $this->option('id');
        if(empty($blogId)){
            $blogId = $this->ask('Which blog you want to delete?');
        }
        try {
            $blogs = Blog::where('id', $blogId)->firstOrFail();
            if ($this->confirm('Are You Sure you want to delete "'.$blogs->title.'"?')) {
                $deleted  = $blogs->delete();
                if($deleted){
                    $this->info('Blog Deleted Succussfully!!');
                } else {
                    $this->error('Something went wrong!');
                }
            }
        } catch(ModelNotFoundException $e){
            $this->error('Blog Not Found!!');
        }
    }
}
