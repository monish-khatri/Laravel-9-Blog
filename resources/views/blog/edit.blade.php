<x-app-layout>
    <div class="py-12">
        <div class="container" >
            <div class="col-md-12 content">
                <table class="center">
                    <tbody>
                        <form method="post" action="/blogs/edit/{{ $blog->id }}" enctype="multipart/form-data">
                            @csrf
                            <table>
                                <tbody>
                                    <tr>
                                        <td><label>Title:</label></td>
                                        <td><input type="text" name="title" value="{{ $blog->title }}" />
                                            @error('title')
                                            <div class="text-red">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="description">Description</label>
                                        </td>
                                        <td>
                                            <textarea name="description" rows="5">{{ $blog->description }}</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="text-align: center;">
                                            <button type="submit" class="btn btn-primary-color">Submit</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
