<?php

namespace App\View\Composers;

use Illuminate\View\View;

class BlogComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('best_blog', 'Best');
    }

    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function create(View $view)
    {
        $view->with('best_blog', 'Creator-Best');
    }
}