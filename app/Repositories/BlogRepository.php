<?php

namespace App\Repositories;

use App\Interfaces\BlogRepositoryInterface;
use App\Events\BlogApprovedRejected;
use App\Http\Controllers\TagController;
use App\Http\Requests\StoreBlogRequest;
use App\Models\Blog;
use App\Models\User;
use App\Notifications\BlogPublishRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class BlogRepository implements BlogRepositoryInterface
{

    /**
     * Function will give the current model
     *
     * @return App\Models\Blog;
     */
    public function model()
    {
        return Blog::class;
    }


    /**
     * Display a listing of the resource.
     *
     * @return App\Models\Blog
     */
    public function apiData()
    {
        return $this->model->sortable()->paginate(5)->withQueryString();
    }

    /**
     * Display a listing of the resource.
     *
     * @return App\Models\Blog
     */
    public function index()
    {
        $query = $this->model()::sortable()
            ->orderBy('id', 'desc')
            ->where(['user_id' => Auth::id()]);
        if (Gate::allows('isAdmin')) {
            $query = $this->model->sortable()
                ->orderBy('id', 'desc')
                ->whereNot('status',Blog::STATUS_DRAFT);
        }
        $blogs = $query->paginate(5)->withQueryString();
        return $blogs;
    }

    /**
     * Display a listing of the published blog.
     *
     * @return App\Models\Blog
     */
    public function published()
    {
        $blogs = Cache::remember('publishedBlog',now()->addSeconds(60),function(){
            return $this->model()::with(['user','tags','totalComments'])->sortable()
                ->orderBy('id', 'desc')
                ->where([
                    'is_published' => true,
                    'status' => Blog::STATUS_APPROVE
                ])
                ->paginate(5)
                ->withQueryString();
        });

        return $blogs;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tags = Cache::remember('Tags',now()->addMinutes(45),function(){
            $tagsObject = new TagController();
            return $tagsObject->get();
        });
        return $tags;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function store(StoreBlogRequest $request)
    {
        // Retrieve the validated input data...
        $validated = $request->validated();
        $blogs = new Blog();
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
        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return App\Models\Blog
     */
    public function show($id)
    {
        $blogs = $this->model()::with('comments')->where('slug', $id)->firstOrFail();
        $permission = Gate::inspect('view', $blogs);
        if (! $permission->allowed()) {
            return redirect()->route('blogs.index')->with(['success' => $permission->message(),'type'=>'danger']);
        }
        return $blogs;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return array
     */
    public function edit($id)
    {
        $tags = Cache::remember('Tags',now()->addMinutes(45),function(){
            $tagsObject = new TagController();
            return $tagsObject->get();
        });
        $blogs = $this->model()::where('slug', $id)->firstOrFail();
        $permission = Gate::inspect('update', $blogs);
        if (! $permission->allowed()) {
            return redirect()->route('blogs.index')->with(['success' => $permission->message(),'type'=>'danger']);
        }
        return [
            'blogs' => $blogs,
            'tags' => $tags,
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return array
     */
    public function update(Request $request, $id)
    {
        $blogs = $this->model()::where('slug', $id)->firstOrFail();
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

        return [
            'result' => $result,
            'isPatch' => $isPatch,
        ];
    }

    /**
     * Move the specified resource from storage to trashbin.
     *
     * @param  int  $id
     * @return bool
     */
    public function destroy($id)
    {
        $blogs = $this->model()::where('slug', $id)->firstOrFail();

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
     * @return bool
     */
    public function forceDestroy($id)
    {
        $blogs = $this->model()::withTrashed()->where('slug', $id)->firstOrFail();

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
     * @return App\Models\Blog
     */
    public function trashBin()
    {
        $blogs = $this->model()::sortable()->onlyTrashed()->where(['user_id' => Auth::id()])->paginate(5);

        return $blogs;
    }

    /**
     * restore specific post
     *
     * @return bool
     */
    public function restore($id)
    {
        $blogs = $this->model()::withTrashed()->where('slug', $id)->firstOrFail();
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
     * Restore all post
     *
     * @return bool
     */
    public function restoreAll()
    {
        $blogs = $this->model()::onlyTrashed()->where(['user_id' => Auth::id()]);
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
        $blogs = $this->model()::where('slug', $id)->firstOrFail();
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

        return [
            'result' => $result,
            'blogs' => $blogs,
        ];
    }

    /**
     * Function will give the total user which are reading blog at the moment
     *
     * @param string $id Blog Slug
     *
     * @return int
     */
    public function currentlyViewByUsers($id)
    {
        $sessionId = session()->getId();
        $counterKey = "blog-post-{$id}-counter";
        $usersKey = "blog-post-{$id}-users";

        $users = Cache::get($usersKey, []);
        $usersUpdate = [];
        $diffrence = 0;
        $now = now();
        foreach ($users as $session => $lastVisit) {
            if ($now->diffInMinutes($lastVisit) >= 1) {
                $diffrence--;
            } else {
                $usersUpdate[$session] = $lastVisit;
            }
        }

        if(! array_key_exists($sessionId, $users)
            || $now->diffInMinutes($users[$sessionId]) >= 1
        ) {
            $diffrence++;
        }

        $usersUpdate[$sessionId] = $now;
        Cache::forever($usersKey, $usersUpdate);

        if (!Cache::has($counterKey)) {
            Cache::forever($counterKey, 1);
        } else {
            Cache::increment($counterKey, $diffrence);
        }
        return Cache::get($counterKey);
    }
}
