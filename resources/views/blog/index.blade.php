<x-app-layout>
    <div class="py-12">
        <div class="container" style="margin:auto;">
            <div class="col-md-12 content">
                @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
                @endif
                <a href="{{route('blogs.create')}}" class="btn btn-primary-color float-right">New Blog</a>
                <h3>Blogs</h3>
                @if(isset($blogs))
                <table class="center">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blogs as $blog)
                        <tr>
                            <td>{{ $blog->id }}</td>
                            <td>
                                <a href="{{route('blogs.edit',[$blog])}}" class="btn btn-xs">
                                    {{ $blog->title }}
                                </a>
                            </td>
                            <td>{{ $blog->description }}</td>
                            <td>
                                <a href="{{route('blogs.show',[$blog])}}" class="btn btn-xs">
                                    <span><i class="fa fa-eye"></i></span>
                                </a>
                                <a href="{{route('blogs.edit',[$blog])}}" class="btn btn-xs">
                                    <span><i class="fa fa-pencil"></i></span>
                                </a>
                                <a onclick="removeBlog('{{route('blogs.destroy',[$blog])}}')" class="btn btn-xs">
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
        function removeBlog(blog){
            $.ajax({
                type : "DELETE",
                url : blog,
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