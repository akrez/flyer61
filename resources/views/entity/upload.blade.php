@extends("layouts.adminlte2.dashboard")

@section("content-header", __("Upload entity excel"))

@section("content")

@include("layouts.adminlte2.errors")
@include("layouts.adminlte2.alert_success")

<div class="box box-info">
    <form action="{{ route('entity-import') }}" method="post" enctype="multipart/form-data" id="entity-form">
        @csrf
        <div class="box-body">
            <div class="form-group {{$errors->get('file') ? 'has-error' : ''}}">
                <div class="col-md-12">
                    <label class="btn btn-default btn-social" for="entity-file-input">
                        <i class="fas fa-file"></i>
                        <span id="entity-file-placeholder">@lang("Choose File")</span>
                    </label>
                    <input name="file" id="entity-file-input" type="file" class="hidden"
                        onchange="$('#entity-file-placeholder').text(this.files[0].name)">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {{$errors->get('import_mode') ? 'has-error' : ''}}">
                    <label for="import_mode"></label>
                    <select class="form-control" name="import_mode">
                        @foreach ($import_modes as $import_mode)
                        <option value="{{$import_mode}}" {{ old("import_mode")==$import_mode ? "selected" : "" }}>
                            @lang('import_mode_' . $import_mode)
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <div class="col-md-2">
                <button type="submit" class="btn btn-success btn-social btn-block">
                    <i class="fas fa-file-upload"></i>
                    @lang("Upload")
                </button>
            </div>
        </div>
    </form>
</div>
@endsection