@if ($messages = Session::get('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    @foreach ($messages as $message) {{$message}} <br> @endforeach
</div>
@endif