
@foreach ($images as $image)

<div class="col-md-2" style="margin-bottom: 10px">
	@if (pathinfo($image->name, PATHINFO_EXTENSION) == 'mp4')
	<img src="{{ url('uploads/video_thumb.png') }}" class="img img-responsive" data-value="{{ $image->id }}" data-name="{{ $image->name }}">
	@else
	<img src="{{ url('uploads/thumbs/'.$image->name) }}" class="img img-responsive" data-value="{{ $image->id }}" data-name="{{ $image->name }}">
	@endif
</div>

@endforeach