@extends('layouts.adminlte2.dashboard')

@section('content-header', __('Entities index'))

@section('content')

@include('layouts.adminlte2.errors')
@include('layouts.adminlte2.alert_success')

<div class="box box-info">
    <form action="{{ route('entity-index') }}" method="get" enctype="multipart/form-data" id="entity-form">
        <div class="box-header">
            <div class="col-md-9">
            </div>
            <div class="col-md-3">
                <a class="btn btn-success btn-social btn-block" href="{{ route('entity-export', request()->query()) }}">
                    <i class="far fa-file-excel"></i>
                    @lang("Download entity excel")
                </a>
            </div>
        </div>
        <div class="box-body">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>
                                @lang('Index')
                            </th>
                            <th>
                                @lang('validation.attributes.title')
                            </th>
                            <th>
                                @lang('validation.attributes.barcode')
                            </th>
                            <th>
                                @lang('validation.attributes.place')
                            </th>
                            <th>
                                @lang('validation.attributes.qty')
                            </th>
                            <th>
                                @lang('validation.attributes.description')
                            </th>
                            <th>
                                @lang('validation.attributes.entity_type')
                            </th>
                            <th>
                                @lang('validation.attributes.upload_seq')
                            </th>
                            <th>
                                @lang('validation.attributes.created_at')
                            </th>
                            <th>
                                @lang('validation.attributes.updated_at')
                            </th>
                            <th>
                            </th>
                        </tr>
                        <tr class="filters">
                            <td>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="title"
                                    value="{{ request()->get('title') }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="barcode"
                                    value="{{ request()->get('barcode') }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="place"
                                    value="{{ request()->get('place') }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="qty" value="{{ request()->get('qty') }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="description"
                                    value="{{ request()->get('description') }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="entity_type"
                                    value="{{ request()->get('entity_type') }}">
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
                        @foreach ($entities as $entity)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $entity->title }}</td>
                            <td>{{ $entity->barcode }}</td>
                            <td>{{ $entity->place }}</td>
                            <td>{{ $entity->qty }}</td>
                            <td>{{ $entity->description }}</td>
                            <td>{{ $entity->entity_type }}</td>
                            <td>{{ $entity->upload_seq }}</td>
                            <td>{{ $entity->jalaliCreatedAt() }}</td>
                            <td>{{ $entity->jalaliUpdatedAt() }}</td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if ($entities->hasPages())
        <div class="box-footer">
            <div class="col-md-12">
                {{ $entities->onEachSide(5)->links() }}
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