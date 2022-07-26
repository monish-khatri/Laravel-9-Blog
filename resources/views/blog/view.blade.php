<x-app-layout>
    <div class="py-12">
        <div class="container">
            <div class="col-md-12 content">
                <h3>{!!__('blog.view_blog_title',['blogName' => $blog->title])!!}</h3>
                <a href="{{route('blogs.index')}}" class="btn btn-danger float-right">{{__('blog.back_button')}}</a>
                <table class="center">
                    <tbody>
                        <tr>
                            <td>{{__('blog.title')}}:</td>
                            <td><strong><?= $blog->title; ?></strong></td>
                        </tr>
                        <tr>
                            <td>{{__('blog.description')}}:</td>
                            <td><strong><?= $blog->description; ?></strong></td>
                        </tr>
                        <tr>
                            <td>{{__('blog.blog_owner')}}:</td>
                            <td><strong><?= $blog->user->name; ?></strong></td>
                        </tr>
                        <tr>
                            <td>{{__('blog.published')}}:</td>
                            <td>
                                @if ($blog->is_published == 1)
                                    <span class="badge badge-success">{{__('blog.published')}}</span>
                                @else
                                    <span class="badge badge-danger">{{__('blog.not_published')}}</span>
                                @endif</td>
                            </tr>
                        <tr>
                            <td>{{__('blog.created_date')}}:</td>
                            <td><strong><?= $blog->created_at->format('d M Y H:i A') ; ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @if($blog->is_published == 1)
            <div class="col-md-12 content">
                <h3>{{__('comment.comments_title')}}</h4>
                @include('blog.commentsDisplay', ['comments' => $blog->comments, 'blog_id' => $blog->id])
                <form method="post" action="{{ route('comments.store') }}">
                    @csrf
                    <div class="form-group">
                        <textarea class="form-control" name="body"></textarea>
                        <input type="hidden" name="blog_id" value="{{ $blog->id }}" />
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary-color" value="{{__('comment.add_comment')}}" />
                    </div>
                </form>
            </div>
        @endif
        </div>
    </div>
     <script>
        function removeComment(deleteUrl){
            $.ajax({
                type : "DELETE",
                url : deleteUrl,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                },
                success : function(response) {
                    location.reload();
                }
            });
        }
    </script>
</x-app-layout>