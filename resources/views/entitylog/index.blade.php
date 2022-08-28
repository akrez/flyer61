@extends('layouts.adminlte2.dashboard')

@section('content-header', __('Entities log index'))

@section('content')

@include('layouts.adminlte2.errors')
@include('layouts.adminlte2.alert_success')

<div class="box box-info">
    <form action="{{ route('entitylog-index') }}" method="get" enctype="multipart/form-data" id="entity-form">
        <div class="box-header">
            <div class="col-md-9">
            </div>
            <div class="col-md-3">
                <a class="btn btn-success btn-social btn-block"
                    href="{{ route('entitylog-export', request()->query()) }}">
                    <i class="far fa-file-excel"></i>
                    @lang("Download entity log excel")
                </a>
            </div>
        </div>
        <div class="box-body">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th> @lang('Index') </th>
                            <th> @lang('validation.attributes.barcode') </th>
                            <th> @lang('validation.attributes.user_id') </th>
                            <th> @lang('validation.attributes.attribute') </th>
                            <th> @lang('validation.attributes.old_value') </th>
                            <th> @lang('validation.attributes.new_value') </th>
                            <th> @lang('validation.attributes.upload_seq') </th>
                            <th> @lang('validation.attributes.created_at') </th>
                            <th> @lang('validation.attributes.updated_at') </th>
                            <th> </th>
                        </tr>
                        <tr class="filters">
                            <td>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="barcode"
                                    value="{{ request()->get('barcode') }}">
                            </td>
                            <td>
                            <td>
                                <div class="form-group {{ $errors->get('attribute') ? 'has-error' : '' }}">
                                    <select class="form-control" name="attribute">
                                        <option></option>
                                        @foreach ($attributes as $attribute)
                                        <option {{ request()->get('attribute') == $attribute ? 'selected' : '' }}
                                            value="{{ $attribute }}" >
                                            @lang('validation.attributes.' . $attribute)
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="old_value"
                                    value="{{ request()->get('old_value') }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="new_value"
                                    value="{{ request()->get('new_value') }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="upload_seq"
                                    value="{{ request()->get('upload_seq') }}">
                            </td>
                            <td>
                                <input type="text" class="form-control persian-datepicker" name="created_at_since"
                                    value="{{ request()->get('created_at_since') }}">
                                <input type="text" class="form-control persian-datepicker" name="created_at_until"
                                    value="{{ request()->get('created_at_until') }}">
                            </td>
                            <td>
                                <input type="text" class="form-control persian-datepicker" name="updated_at_since"
                                    value="{{ request()->get('updated_at_since') }}">
                                <input type="text" class="form-control persian-datepicker" name="updated_at_until"
                                    value="{{ request()->get('updated_at_until') }}">
                            </td>
                            <td>
                                <button type="submit" class="btn btn-info btn-block btn-social">
                                    <i class="fas fa-search"></i>
                                    @lang('Search')
                                </button>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entitylogs as $entitylog)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $entitylog->barcode }}</td>
                            <td>{{ $entitylog->user ? $entitylog->user->email : '' }}</td>
                            <td>@lang('validation.attributes.' . $entitylog->attribute)</td>
                            <td>{{ $entitylog->old_value }}</td>
                            <td>{{ $entitylog->new_value }}</td>
                            <td>{{ $entitylog->upload_seq }}</td>
                            <td>{{ $entitylog->jalaliCreatedAt() }}</td>
                            <td>{{ $entitylog->jalaliUpdatedAt() }}</td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if ($entitylogs->hasPages())
        <div class="box-footer">
            <div class="col-md-12">
                {{ $entitylogs->onEachSide(5)->links() }}
            </div>
        </div>
        @endif
    </form>
</div>
@endsection
@section('POS_END')
<script>
    jQuery(function($) {
        $(".persian-datepicker").persianDatepicker({
            calendar: {
                persian: {
                    showHint: true,
                    locale: "fa"
                },
                gregorian: {
                    showHint: true
                }
            },
            "toolbox": {
                "calendarSwitch": {
                    "enabled": false,
                }
            },
            initialValue: false,
            initialValueType: "persian",
            autoClose: true,
            observer: true,
            format: "YYYY-MM-DD"
        });
    });
</script>
@endsection