@extends("layouts.adminlte2.dashboard")

@section("content-header", __("Upload entity excel"))

@section("content")

@include("layouts.adminlte2.errors")
@include("layouts.adminlte2.alert_success")

<div class="box box-info">
    <form action="{{ route('entity-import') }}" method="post" enctype="multipart/form-data" id="entity-form">
        @csrf
        <div class="box-body">
            <div class="col-md-2">
                <div class="form-group {{$errors->get('entity_type') ? 'has-error' : ''}}">
                    <label for="entity_type">@lang("Entity type")</label>
                    <select class="form-control" name="entity_type">
                        <option></option>
                        @foreach ($entity_types as $typeValue)
                        <option value="{{$typeValue}}" {{ old("entity_type")==$typeValue ? "selected" : "" }}>
                            {{ App\Models\Entity::getEntityTypeName($typeValue) }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group {{$errors->get('rewrite') ? 'has-error' : ''}}">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label>
                            <input type="hidden" name="rewrite" value="0">
                            <input type="checkbox" name="rewrite" value="1" {{ old("rewrite") ? "checked" : "" }}>
                            @lang("If the barcode is duplicated, should it be rewritten?")
                        </label>
                    </div>
                </div>
            </div>
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