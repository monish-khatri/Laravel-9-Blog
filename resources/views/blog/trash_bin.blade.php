<x-app-layout>
    <div class="py-12">
        <div class="container" style="margin:auto;">
            <div class="col-md-12 content">
                @if ($message = Session::get('success'))
                <x-alert-message type="{!! Session::get('type')!!}" message="{{$message}}" class="alert-block"/>
                @endif
                @if(!$published)
                    <a href="{{route('blogs.create')}}" class="btn btn-primary-color float-right">{{__('blog.new_blog_button')}}</a>
                    <h3>{{__('blog.index_blog_title')}}</h3>
                @else
                    <h3>{{__('blog.all_blogs')}}</h3>
                @endif
                @if(isset($blogs))
                <table class="center">
                    <thead>
                        <tr>
                            <th>{{__('blog.sr_no')}}</th>
                            <th>{{__('blog.title')}}</th>
                            <th>{{__('blog.description')}}</th>
                            <th>{{__('blog.published')}}</th>
                            <th>{{__('blog.blog_owner')}}</th>
                            <th class="actions">{{__('blog.actions')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blogs as $blog)
                        <tr @if($loop->odd) class="odd-row" @endif>
                            <td>{{ $loop->iteration + $blogs->firstItem() - 1 }}</td>
                            <td>
                                <a href="{{route('blogs.edit',[$blog])}}" class="btn btn-xs">
                                    {{ $blog->title }}
                                </a>
                            </td>
                            <td>{{ Str::limit($blog->description, 100) }}</td>
                            <td>
                                @if ($blog->is_published)
                                    <span @if($blog->user->id == auth()->user()->id) title="{{__('blog.change_status_text')}}" data-original-title="Tooltip on right" @endif class="@if($blog->user->id == auth()->user()->id)change-status published @endif badge badge-success" blog-id="{{$blog->id}}" blog-title="{{$blog->title}}">{{__('blog.published')}}</span>
                                @else
                                    <span @if($blog->user->id == auth()->user()->id) title="{{__('blog.change_status_text')}}" data-original-title="Tooltip on right" @endif class="@if($blog->user->id == auth()->user()->id)change-status not-published @endif badge badge-danger" blog-id="{{$blog->id}}" blog-title="{{$blog->title}}">{{__('blog.not_published')}}</span>
                                @endif
                            </td>
                            <td>
                                {{$blog->user->name}}
                            </td>
                            <td>
                                @if($published)
                                    <a href="{{route('blogs.show',[$blog])}}" class="btn btn-xs">
                                        <span>
                                        <span>{{count($blog->totalComments)}}</span>
                                        <i class="fa fa-comment"></i>
                                        </span>
                                    </a>
                                    @can('isOwner',$blog)
                                        <a href="{{route('blogs.edit',[$blog])}}" class="btn btn-xs">
                                            <span><i class="fa fa-pencil"></i></span>
                                        </a>
                                        <a onclick="removeBlog('{{route('blogs.destroy',[$blog])}}','{{$blog->title}}')" class="btn btn-xs">
                                            <span><i class="fa fa-trash"></i></span>
                                        </a>
                                    @endcan
                                @else
                                    <a href="{{route('blogs.show',[$blog])}}" class="btn btn-xs">
                                        <span><i class="fa fa-eye"></i></span>
                                    </a>
                                    <a href="{{route('blogs.edit',[$blog])}}" class="btn btn-xs">
                                        <span><i class="fa fa-pencil"></i></span>
                                    </a>
                                    <a onclick="removeBlog('{{route('blogs.destroy',[$blog])}}','{{$blog->title}}')" class="btn btn-xs">
                                        <span><i class="fa fa-trash"></i></span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6"><?= __('No record found!!!') ?></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div style="margin-top: 2%;">
                    {{$blogs->onEachSide(2)->links()}}
                </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        function removeBlog(deleteUrl,blogName){
            Swal.fire({
                icon: 'warning',
                title:'{{__('blog.confirmation_title')}}',
                html: "{!!__('blog.delete_description', ['blogName' => '"+blogName+"'])!!}",
                showCancelButton: true,
                confirmButtonText: '{{__('blog.delete_button')}}',
                cancelButtonText: '{{__('blog.cancel_button')}}',
                }).then((result) => {
                if (result.isConfirmed) {
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
            })
        }
        $(document).on('click','.change-status',function(){
            var blogId = $(this).attr('blog-id');
            var blogTitle = $(this).attr('blog-title');
            var updateUrl = "{{route('blogs.index')}}/"+blogId;
            button = "{{__('blog.publish_text')}}";
            html = "{!!__('blog.update_status_description', ['blogName' => '"+blogTitle+"','status'=> '"+button+"'])!!}"
            is_published = 1
            if($(this).hasClass('published')) {
                button = "{{__('blog.unpublish_text')}}";
                html = "{!!__('blog.update_status_description', ['blogName' => '"+blogTitle+"','status'=> '"+button+"'])!!}"
                is_published = 0
            }
            Swal.fire({
                icon: 'warning',
                title:'{{__('blog.confirmation_title')}}',
                html: html,
                showCancelButton: true,
                confirmButtonText: button,
                cancelButtonText: '{{__('blog.cancel_button')}}',
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type : "POST",
                        url : updateUrl,
                        dataType: 'json',
                        data: {'is_published':is_published,_method: 'PATCH'},
                        headers: {
                            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                        },
                        success : function(response) {
                            location.reload();
                        }
                    });
                }
            })
        })
    </script>
</x-app-layout>