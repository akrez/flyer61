@if (count($errors) > 0)
<div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    @if ($errorsHeader = Session::get('errors_header')) <strong>{{$errorsHeader}}</strong><br> @endif
    @foreach ($errors->all() as $message) {{$message}} <br> @endforeach
</div>
@endif