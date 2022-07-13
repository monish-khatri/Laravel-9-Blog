<x-app-layout>
    <div class="py-12">
        <div class="container">
            <div class="col-md-12 content">
                <h1>{{ $blog->name }}</h1>
                <a href="/blogs" class="btn btn-primary-color float-right">Back</a>
                <table class="center">
                    <tbody>
                        <tr>
                            <td>Id</td>
                            <td><?= $blog->id; ?></td>
                        </tr>
                        <tr>
                            <td>Title</td>
                            <td><?= $blog->title; ?></td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td><?= $blog->description; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>