<div
    id="{{ $dataTableId }}"
    class="field-datatable w-100 size-{{ $size }}"
    data-control="datatable"
>
    <div class="position-absolute mt-2">
        <a class="btn btn-secondary" href="?csv=1">@lang('thoughtco.reports::default.btn_csv')</a>
    </div>

    {!! $table->render() !!}

</div>

