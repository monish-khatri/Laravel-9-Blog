 <div {{ $attributes->merge(['class' => 'alert alert-'.$type]) }}>
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>