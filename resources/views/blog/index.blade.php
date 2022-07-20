<x-app-layout>
    <div class="py-12">
        <div class="container" style="margin:auto;">
            <div class="col-md-12 content">
                @if ($message = Session::get('success'))
                <x-alert-message type="{!! Session::get('type')!!}" message="{{$message}}" class="alert-block"/>
                @endif
                <a href="{{route('blogs.create')}}" class="btn btn-primary-color float-right">{{__('blog.new_blog_button')}}</a>
                <h3>{{__('blog.index_blog_title')}}</h3>
                @if(isset($blogs))
                <table class="center">
                    <thead>
                        <tr>
                            <th>{{__('blog.sr_no')}}</th>
                            <th>{{__('blog.title')}}</th>
                            <th>{{__('blog.description')}}</th>
                            <th>{{__('blog.published')}}</th>
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
                            <td>{{ $blog->description }}</td>
                            <td>
                                @if ($blog->is_published == 1)
                                    <span class="badge badge-success">{{__('blog.published')}}</span>
                                @else
                                    <span class="badge badge-danger">{{__('blog.not_published')}}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{route('blogs.show',[$blog])}}" class="btn btn-xs">
                                    <span><i class="fa fa-eye"></i></span>
                                </a>
                                <a href="{{route('blogs.edit',[$blog])}}" class="btn btn-xs">
                                    <span><i class="fa fa-pencil"></i></span>
                                </a>
                                <a onclick="removeBlog('{{route('blogs.destroy',[$blog])}}','{{$blog->title}}')" class="btn btn-xs">
                                    <span><i class="fa fa-trash"></i></span>
                                </a>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="4"><?= __('No record found!!!') ?></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div style="margin-top: 2%;">
                    {{$blogs->links()}}
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
    </script>
</x-app-layout>