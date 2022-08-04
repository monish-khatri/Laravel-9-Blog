<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use App\Http\Resources\BlogCollection;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Services\Counter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    private $counter;

    public function __construct(Counter $counter)
    {
        $this->counter = $counter;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Call Command Programmatically
        // dd(Artisan::call('blog:list'));
        $blogs = Blog::orderBy('id', 'desc')->where(['user_id' => Auth::id()])->paginate(5)->withQueryString();

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
        $blogs->slug = Str::slug($blogs->title , "-");
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
        $blogs = Cache::tags(['blog'])->remember("blog-{$id}", 60, function() use($id) {
            return Blog::with('comments','user')->where('slug', $id)->firstOrFail();
        });
        // Gate::authorize('blog-actions', $blogs); // Throws 403 "THIS ACTION IS UNAUTHORIZED"
        return view('blog.view', [
            'blog' => $blogs,
            'counter' => $this->counter->increment("blog-{$id}", ['blog']),
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
        $blogs = Blog::where('slug', $id)->firstOrFail();
        $permission = Gate::inspect('update', $blogs);
        if (! $permission->allowed()) {
            return redirect()->route('blogs.index')->with(['success' => $permission->message(),'type'=>'danger']);
        }

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
        $blogs = Blog::where('slug', $id)->firstOrFail();
        $permission = Gate::inspect('update', $blogs);
        if (! $permission->allowed()) {
            return redirect()->route('blogs.index')->with(['success' => $permission->message(),'type'=>'danger']);
        }
        $isPatch = $request->isMethod('PATCH');
        if(! $isPatch) {
            $messages = [
                'required' => 'Required',
            ];
            $validated = Validator::make($request->all(), [
                'title' => 'required|between:3,50',
                'description' => 'required|min:20|max:250',
            ],$messages)->validate();
            $blogs->title = $validated['title'];
            $blogs->slug = Str::slug($blogs->title , "-");
            $blogs->description = $validated['description'];
            $blogs->user_id = Auth::id();
        }
        $blogs->is_published = (bool)$request->is_published ?? false;
        $result = $blogs->save();
        if ($isPatch){
            return true;
        }
        if ($result) {
            return redirect()->route('blogs.index')->with(['success' => __('blog.update_success_message'),'type'=>'success']);
        } else {
            return redirect()->route('blogs.edit')->with(['error' => __('blog.error_message'),'type'=>'danger']);;
        }
    }

    /**
     * Move the specified resource from storage to trashbin.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $blogs = Blog::where('slug', $id)->firstOrFail();

        if (! Gate::allows('isOwner', $blogs)) {
            redirect()->route('blogs.index')->with(['success' => __('blog.permission_denied_error'),'type'=>'danger']);
            return true;
        }
        $blogs->delete();

        redirect()->route('blogs.index')->with(['success' => __('blog.delete_success_message'),'type'=>'success']);
        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDestroy($id)
    {
        $blogs = Blog::withTrashed()->where('slug', $id)->firstOrFail();

        $permission = Gate::inspect('forceDelete', $blogs);

        if (! $permission->allowed()) {
            redirect()->route('blogs.index')->with(['success' => __('blog.permission_denied_error'),'type'=>'danger']);
            return true;
        }

        $blogs->forceDelete();

        redirect()->route('blogs.trash_bin')->with(['success' => __('blog.delete_success_message'),'type'=>'success']);
        return true;
    }

    /**
     * restore specific post
     *
     * @return void
     */
    public function trashBin()
    {
        $blogs = Blog::onlyTrashed()->where(['user_id' => Auth::id()])->paginate(5);

        return view('blog.trash_bin', [
            'blogs' => $blogs,
            'published' => false,
        ]);
    }

    /**
     * restore specific post
     *
     * @return void
     */
    public function restore($id)
    {
        $blogs = Blog::withTrashed()->where('slug', $id)->firstOrFail();
        $permission = Gate::inspect('restore', $blogs);

        if (! $permission->allowed()) {
            redirect()->route('blogs.index')->with(['success' => __('blog.permission_denied_error'),'type'=>'danger']);
            return true;
        }

        $blogs->restore();

        redirect()->route('blogs.trash_bin')->with(['success' => __('blog.restore_success_message'),'type'=>'success']);
        return true;
    }

    /**
     * restore all post
     *
     * @return response()
     */
    public function restoreAll()
    {
        $blogs = Blog::onlyTrashed()->where(['user_id' => Auth::id()]);
        $blogs->restore();

        redirect()->route('blogs.index')->with(['success' => __('blog.restore_success_message'),'type'=>'success']);
        return true;
    }
}
