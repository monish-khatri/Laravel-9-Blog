
<select class="form-control" name="tags[]" id="tags" multiple="multiple">
    @foreach ($tags as $tagId => $tagName )
        <option value="{{$tagName}}" data-option-id="{{$tagId}}" @if(in_array($tagName,$selected)) selected @endif>{{$tagName}}</option>
    @endforeach
</select>

@push('css')
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('#tags').select2({
            tags: true
        });
    });
</script>
@endpush