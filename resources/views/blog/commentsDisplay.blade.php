
@foreach($comments as $comment)
    <div class="display-comment" @if($comment->parent_id != null) style="margin-left:40px;" @endif>
        <em>{{ $comment->user->name }}</em>: <em>{{ $comment->created_at->diffForHumans() }}</em><p><strong>{{ $comment->body }}</strong></p>
        <form id="commentForm_{{$comment->id}}" method="post" action="{{ route('comments.store') }}">
            @csrf
            <div class="form-group">
                <input type="text" name="body_{{$comment->id}}" placeholder="{{__('comment.comments_reply')}}" class="form-control appearance-none border-2 border-gray-200 rounded w-50 py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" />
                @error('body_'. $comment->id)
                    <div class="text-red">{{ $message }}</div>
                @enderror
                <input type="hidden" name="blog_id" value="{{ $blog_id }}" />
                <input type="hidden" name="parent_id" value="{{ $comment->id }}" />
                <a onclick="$('#commentForm_{{$comment->id}}').submit();" class="btn btn-xs">
                    <span class="badge badge-success">{{__('comment.comments_reply')}}</span>
                </a>
                <a onclick="removeComment('{{route('comments.destroy',[$comment])}}')" class="btn btn-xs">
                    <span class="badge badge-danger">{{__('blog.delete_button')}}</span>
                </a>
            </div>
        </form>
        @include('blog.commentsDisplay', ['comments' => $comment->replies])
    </div>
@endforeach