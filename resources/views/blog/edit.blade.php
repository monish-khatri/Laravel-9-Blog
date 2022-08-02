<x-app-layout>
    <div class="py-12">
        <div class="container" >
            <div class="col-md-12 content">
                <h3>{{__('blog.update_blog_title')}}</h3>
                <table class="center">
                    <tbody>
                        <form method="post" action="{{route('blogs.update',[$blog])}}" >
                            @csrf
                            @method('PUT')
                            <table>
                                <tbody>
                                    <tr>
                                        <td><label>{{__('blog.title')}}<span class="text-red">*</span>:</label></td>
                                        <td><input class="form-control" type="text" name="title" value="{{ old('title') ?? $blog->title }}" />
                                            @error('title')
                                            <div class="text-red">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="description">{{__('blog.description')}}<span class="text-red">*</span>:</label>
                                        </td>
                                        <td>
                                            <textarea class="form-control" name="description" rows="5">{{ old('description') ?? $blog->description }}</textarea>
                                            @error('description')
                                            <div class="text-red">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="is_published">{{__('blog.published')}}:</label>
                                        </td>
                                        <td>
                                            <select class="form-control" name="is_published" id="is_published">
                                                <option value="">-----</option>
                                                <option value="1" @if (old('is_published') == "1" || $blog->is_published == "1" ) {{ 'selected' }} @endif>Yes</option>
                                                <option value="0" @if (old('is_published') == "0" || $blog->is_published == "0" ) {{ 'selected' }} @endif>No</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="text-align: center;">
                                            <button type="submit" class="btn btn-primary-color">{{__('blog.submit_button')}}</button>
                                            <a href="{{ route('blogs.index') }}" class="btn btn-danger">{{__('blog.back_button')}}</a>
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
