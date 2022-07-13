<?php

namespace App\Http\Controllers;
use App\Models\Blog;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogs = Blog::orderBy('id', 'desc')->paginate(5);

        return view('blog.index', [
            'blogs' => $blogs
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('blog.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
            return redirect()->route('blogs.index');
        } else {
            $request->session()->flash('error', 'Blog not saved. Please check!!');
            return redirect()->route('blogs.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('blog.view', [
            'blog' => Blog::findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $blogs = Blog::findOrFail($id);

        return view('blog.edit', [
            'blog' => $blogs,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $blogs = Blog::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
        ]);

        $blogs->title = $request->title;
        $blogs->description = $request->description;
        $result = $blogs->save();

        if ($result) {

            $request->session()->flash('success', 'Blog updated!!');
            return redirect()->route('blogs.index');
        } else {

            $request->session()->flash('error', 'Blog not updated. Please check!!');
            return redirect()->route('blogs.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $blogs = Blog::find($id);
        $blogs->delete();

        $request->session()->flash('success', 'Blog deleted!!');

        return $id;
    }
}
