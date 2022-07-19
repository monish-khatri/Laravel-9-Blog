<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use App\Models\Blog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

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
    public function store(StoreBlogRequest $request)
    {
        // Retrieve the validated input data...
        $validated = $request->validated();

        $blogs = new Blog;
        $blogs->title = $validated['title'];
        $blogs->description = $validated['description'];
        $blogs->is_published = $validated['is_published'] ?? false;
        $result = $blogs->save();
        if ($result) {
            $request->session()->flash('success', 'Blog created successfully!!');
            return redirect()->route('blogs.index');
        } else {
            $request->session()->flash('error', 'Something went wrong!!');
            return redirect()->route('blogs.add');
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
        $messages = [
            'required' => 'Required',
        ];
        $validated = Validator::make($request->all(), [
            'title' => 'required|between:3,50',
            'description' => 'required|min:20|max:250',
        ],$messages)->validate();

        $blogs->title = $validated['title'];
        $blogs->description = $validated['description'];
        $blogs->is_published = $validated['is_published'] ?? false;
        $result = $blogs->save();

        if ($result) {
            $request->session()->flash('success', 'Blog updated successfully!!');
            return redirect()->route('blogs.index');
        } else {

            $request->session()->flash('error', 'Something went wrong!!');
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

        $request->session()->flash('success', 'Blog deleted successfully!!');

        return $id;
    }
}
