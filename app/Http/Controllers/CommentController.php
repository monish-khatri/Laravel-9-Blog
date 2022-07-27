<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $validated = Validator::make($request->all(), [
            'body' => 'required',
        ],$messages)->validate();

        $comment = new Comment;
        $comment->body = $validated['body'];
        $comment->parent_id = $request->parent_id ?? null;
        $comment->blog_id = $request->blog_id;
        $comment->user_id = Auth::id();
        $comment->save();

        return back();
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
        $comment = Comment::find($id);
        $comment->delete();

        return $id;
    }
}