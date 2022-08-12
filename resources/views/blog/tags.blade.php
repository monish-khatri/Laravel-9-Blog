
<select class="form-control select2" name="tags[]" id="tags" multiple="multiple">
    @foreach ($tags as $tagId => $tagName )
        <option value="{{$tagName}}" data-option-id="{{$tagId}}" @if(in_array($tagName,$selected)) selected @endif>{{$tagName}}</option>
    @endforeach
</select>

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('#tags').select2({
            tags: true
        });
    });
</script>
@endpush