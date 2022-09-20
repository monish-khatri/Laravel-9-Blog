<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Mail\SendMailable;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(Request $request)
   {
        $messages = [
            'required' => 'Required',
        ];
        $attributes = [
            'body_'.$request->parent_id => 'body',
        ];
        $validated = Validator::make($request->all(), [
            'body_'.$request->parent_id => 'required',
        ],$messages,$attributes)->validate();

        $comment = new Comment;
        $comment->body = $validated['body_'.$request->parent_id];
        $comment->parent_id = $request->parent_id ?? null;
        $comment->blog_id = $request->blog_id;
        $comment->user_id = Auth::id();
        $result = $comment->save();
        if($result){
            // if you want to Send mail after sometime use delay function with carbon
            $userFrom = User::find($comment->user_id);
            $blog = Blog::find($comment->blog_id);
            dispatch(new SendEmailJob($userFrom,$blog->user,$blog,$comment));
        }

        return back();
   }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function destroy($id)
    {
        $comments = Comment::find($id);
        $comments->deleteNestedComments();
        $comments->delete();

        return $id;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function pinComment($id)
    {
        $type = request()->type == 'pin' ? true : false;
        $comments = Comment::find($id);
        $comments->pinned = $type;
        $comments->save();
        return true;
    }
}
