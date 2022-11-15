<?php

namespace App\Http\Controllers;

use App\Events\BlogApprovedRejected;
use App\Exports\BlogExport;
use App\Http\Requests\StoreBlogRequest;
use App\Imports\BlogImport;
use App\Interfaces\BlogRepositoryInterface;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BlogController extends Controller
{
    public function __construct(
        private BlogRepositoryInterface $blogRepository
    ) {
        $this->blogRepository = $blogRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function apiData()
    {
        $blogs = $this->blogRepository->apiData();
        return response()->json($blogs);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Support\Facades\View
     */
    public function index()
    {
        $blogs = $this->blogRepository->index();
        return view('blog.index', [
            'blogs' => $blogs,
            'published' => false,
        ]);
    }

    /**
     * Display a listing of the published blog.
     *
     * @return \Illuminate\Support\Facades\View
     */
    public function published()
    {
        $blogs = $this->blogRepository->published();
        return view('blog.index', [
            'blogs' => $blogs,
            'published' => true,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Support\Facades\View
     */
    public function create()
    {
        $tags = $this->blogRepository->create();

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
        $result = $this->blogRepository->store($request);
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
     * @return \Illuminate\Support\Facades\View
     */
    public function show($id)
    {
        $blogs = $this->blogRepository->show($id);
        $counter = $this->currentlyViewByUsers($id);
        return view('blog.view', [
            'blog' => $blogs,
            'counter' => $counter,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Support\Facades\View
     */
    public function edit($id)
    {
        $response = $this->blogRepository->edit($id);
        return view('blog.edit', [
            'blog' => $response['blogs'],
            'tags' => $response['tags'],
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
        $response = $this->blogRepository->update($request,$id);
        if ($response['isPatch']){
            return true;
        }
        if ($response['result']) {
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
        return $this->blogRepository->destroy($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDestroy($id)
    {
        return $this->blogRepository->forceDestroy($id);
    }

    /**
     * restore specific post
     *
     * @return \Illuminate\Support\Facades\View
     */
    public function trashBin()
    {
        $blogs = $this->blogRepository->trashBin();

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
        return $this->blogRepository->restore($id);
    }

    /**
     * restore all post
     *
     * @return bool
     */
    public function restoreAll()
    {
        return $this->blogRepository->restoreAll();
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
        return $this->blogRepository->blogStatus($blog);
    }

    /**
     * Function description
     *
     * @param int variable Description $variable comment about this variable
     *
     * @return bool
     */
    public function updateStatus(Request $request,$id)
    {
        $response = $this->blogRepository->updateStatus($request,$id);
        if ($response['result']){
            event(new BlogApprovedRejected($response['blogs']));
            return true;
        }
        return false;
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
        return $this->blogRepository->currentlyViewByUsers($id);
    }

    /**
     * Function description
     *
     * @return array
     */
    public function export()
    {
        return Excel::download(new BlogExport, 'blogs.csv',\Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Function description
     *
     * @return array
     */
    public function import()
    {
        Excel::import(new BlogImport,'/<path_to_file>/blogs.xlsx');

        return redirect()->route('blogs.index')->with('success', 'All good!');
    }
}
