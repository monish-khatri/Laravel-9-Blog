<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use App\Models\Blog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $blogs = Blog::orderBy('id', 'desc')->where(['user_id' => Auth::id(),'is_published' => 0])->paginate(5)->withQueryString();

        return view('blog.index', [
            'blogs' => $blogs,
            'published' => false,
        ]);
    }

    /**
     * Display a listing of the published blog.
     *
     * @return \Illuminate\Http\Response
     */
    public function published()
    {
        $blogs = Blog::orderBy('id', 'desc')->where('is_published',1)->paginate(5)->withQueryString();

        return view('blog.index', [
            'blogs' => $blogs,
            'published' => true,
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
        $blogs->is_published = $request->is_published ?? false;
        $blogs->user_id = Auth::id();
        $result = $blogs->save();
        if ($result) {
            return redirect()->route('blogs.index')->with(['success' => __('blog.create_success_message'),'type'=>'success']);
        } else {
            return redirect()->route('blogs.add')->with(['error' => __('blog.error_message'),'type'=>'danger']);;
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
        $blogs->is_published = $request->is_published ?? false;
        $blogs->user_id = Auth::id();
        $result = $blogs->save();

        if ($result) {
            return redirect()->route('blogs.index')->with(['success' => __('blog.update_success_message'),'type'=>'success']);
        } else {
            return redirect()->route('blogs.edit')->with(['error' => __('blog.error_message'),'type'=>'danger']);;
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

        redirect()->route('blogs.index')->with(['success' => __('blog.delete_success_message'),'type'=>'success']);
        return $id;
    }
}
