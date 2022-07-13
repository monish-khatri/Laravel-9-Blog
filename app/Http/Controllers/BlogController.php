<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use App\Jobs\QueueJob;

class BlogController extends Controller
{

    /**
     * get all blog data
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $blogs = Blog::orderBy('id', 'desc')->paginate(5);

        return view('blog.index', [
            'blogs' => $blogs
        ]);
    }

    /**
     * view details of blog
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return view('blog.view', [
            'blog' => Blog::findOrFail($id)
        ]);
    }

    /**
     * Add new blog.
     *
     * @return \Illuminate\View\View
     */
    public function add(Request $request)
    {
        if ($request->method() == 'POST') {
            $validated = $request->validate([
                'title' => 'required|max:255',
                'description' => 'required|max:255',
            ]);
            $blogs = new Blog;
            $blogs->title = $request->title;
            $blogs->description = $request->description;
            $result = $blogs->save();

            if ($result) {
                $request->session()->flash('success', 'Blog saved!!');
                return redirect()->route('blog.index');
            } else {
                $request->session()->flash('error', 'Blog not saved. Please check!!');
                return redirect()->route('blog.index');
            }
        } else {
            return view('blog.add');
        }
    }

    /**
     * Edit blog.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $blogs = Blog::findOrFail($id);

        if ($request->method() == 'POST') {

            $validated = $request->validate([
                'title' => 'required|max:255',
                'description' => 'required|max:255',
            ]);

            $blogs->title = $request->title;
            $blogs->description = $request->description;
            $result = $blogs->save();

            if ($result) {

                $request->session()->flash('success', 'Blog updated!!');
                return redirect()->route('blog.index');
            } else {

                $request->session()->flash('error', 'Blog not updated. Please check!!');
                return redirect()->route('blog.index');
            }
        } else {
            return view('blog.edit', [
                'blog' => $blogs,
            ]);
        }
    }

    /**
     * Delete blog
     * @param $id integer
     * @return boolean
     */
    public function delete(Request $request, $id)
    {

        $blogs = Blog::find($id);
        $blogs->delete();

        $request->session()->flash('success', 'Blog deleted!!');

        return redirect()->route('blog.index');
    }
}
