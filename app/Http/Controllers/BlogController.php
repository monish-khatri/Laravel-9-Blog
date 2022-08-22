<?php

namespace App\Http\Controllers;

use App\Events\BlogApprovedRejected;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Resources\BlogCollection;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Services\Counter;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\BlogPublishRequest;
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
        $query = Blog::sortable()
            ->orderBy('id', 'desc')
            ->where(['user_id' => Auth::id()]);
        if (Gate::allows('isAdmin')) {
            $query = Blog::sortable()
                ->orderBy('id', 'desc')
                ->whereNot('status',Blog::STATUS_DRAFT);
        }
        $blogs = $query->paginate(5)->withQueryString();
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
        $blogs = Cache::remember('publishedBlog',now()->addSeconds(60),function(){
            return Blog::with(['user','tags','totalComments'])->sortable()
                ->orderBy('id', 'desc')
                ->where([
                    'is_published' => true,
                    'status' => Blog::STATUS_APPROVE
                ])
                ->paginate(5)
                ->withQueryString();
        });

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
        $tagsObject = new TagController();
        $tags = $tagsObject->get();
        return view('blog.add',compact('tags'));
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
        $blogs->is_published = (bool)$request->is_published ?? false;
        $blogs->user_id = Auth::id();
        $blogs->status = $this->blogStatus($blogs);
        if ($image = $request->file('image')) {
            $destinationPath = storage_path('app/public/blog/');
            $blogImage = $blogs->slug . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $blogImage);
            $blogs->image = $blogImage;
        }
        $result = $blogs->save();
        if($request->filled('tags')){
            $tags = new TagController();
            $relatedTags = $tags->store($request->input('tags'));

            $blogs->tags()->sync($relatedTags);
        }

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
        $blogs = Blog::with('comments')->where('slug', $id)->firstOrFail();
        $permission = Gate::inspect('view', $blogs);
        if (! $permission->allowed()) {
            return redirect()->route('blogs.index')->with(['success' => $permission->message(),'type'=>'danger']);
        }
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
        $tagsObject = new TagController();
        $tags = $tagsObject->get();
        $blogs = Blog::where('slug', $id)->firstOrFail();
        $permission = Gate::inspect('update', $blogs);
        if (! $permission->allowed()) {
            return redirect()->route('blogs.index')->with(['success' => $permission->message(),'type'=>'danger']);
        }
        return view('blog.edit', [
            'blog' => $blogs,
            'tags' => $tags,
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
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],$messages)->validate();
            $blogs->title = $validated['title'];
            $blogs->slug = Str::slug($blogs->title , "-");
            $blogs->description = $validated['description'];
            $blogs->user_id = Auth::id();
            if ($image = $request->file('image')) {
                $destinationPath = storage_path('app/public/blog/');
                $blogImage = $blogs->slug . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $blogImage);
                $blogs->image = $blogImage;
                if (! empty($request->old_image) && $request->old_image != $blogImage){
                    unlink(storage_path('app/public/blog/').$request->old_image);
                }
            }
            if($request->filled('tags')){
                $tags = new TagController();
                $relatedTags = $tags->store($request->input('tags'));
                $blogs->tags()->sync($relatedTags);
            }
        }
        $blogs->is_published = (bool)$request->is_published ?? false;
        $blogs->status = $this->blogStatus($blogs);
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

        if (! empty($blogs->image)){
            unlink(storage_path('app/public/blog/').$blogs->image);
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
        $blogs = Blog::sortable()->onlyTrashed()->where(['user_id' => Auth::id()])->paginate(5);

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


    /**
     * Function will return the status for published/unpublished blog
     *
     * @param bool published blog is $published or Not
     * @param bool $blogStatus pervious status of the blog
     *
     * @return string
     */
    public function blogStatus($blog)
    {
        if($blog->is_published) {
            $status = Blog::STATUS_PENDING;
            $users = User::where('role',User::ADMIN_ACCESS)->get();
            $users->each->notify(new BlogPublishRequest($blog));
        } elseif(! $blog->is_published || empty($blog->status)) {
            $status = Blog::STATUS_DRAFT;
        }

        return $status;
    }

    /**
     * Function description
     *
     * @param int variable Description $variable comment about this variable
     *
     * @return array
     */
    public function updateStatus(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'status'     => 'required|string',
            'reject_reason' => 'required_if:status,==,rejected|nullable',
        ]);
        if($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()]);
        }
        $blogs = Blog::where('slug', $id)->firstOrFail();
        $permission = Gate::inspect('update', $blogs);
        if (! $permission->allowed()) {
            return response()->json(['success' => $permission->message(),'type'=>'danger']);
        }
        if ($request->status == Blog::STATUS_REJECTED) {
            $blogs->status = Blog::STATUS_REJECTED;
            $blogs->reject_reason = $request->reject_reason;
            $blogs->is_published = false;
        } elseif($request->status == Blog::STATUS_APPROVE){
            $blogs->status = Blog::STATUS_APPROVE;
            $blogs->published_at = now();
        }
        $blogs->action_by = Auth::id();
        $result = $blogs->save();
        if ($result){
            event(new BlogApprovedRejected($blogs));
            return true;
        }
        return false;
    }
}
