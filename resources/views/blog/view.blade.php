<x-app-layout>
    <div class="py-12">
        <div class="container">
            <div class="col-md-12 content">
                <h1>{{ $blog->name }}</h1>
                <a href="{{route('blogs.index')}}" class="btn btn-primary-color float-right">Back</a>
                <table class="center">
                    <tbody>
                        <tr>
                            <td>Title:</td>
                            <td><strong><?= $blog->title; ?></strong></td>
                        </tr>
                        <tr>
                            <td>Description:</td>
                            <td><strong><?= $blog->description; ?></strong></td>
                        </tr>
                        <tr>
                            <td>Date Created:</td>
                            <td><strong><?= $blog->created_at->format('d M Y H:i A') ; ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>