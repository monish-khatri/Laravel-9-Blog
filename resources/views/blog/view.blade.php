<x-app-layout>
    <div class="py-12">
        <div class="container">
            <div class="col-md-12 content">
                <h1>{{ $blog->name }}</h1>
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
        </div>
    </div>
</x-app-layout>