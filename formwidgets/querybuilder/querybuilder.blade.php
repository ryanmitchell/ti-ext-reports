<div
    class="field-query-builder"
    data-control="query-builder"
    data-value='@json($value)'
    data-filters='@json($filters)'
    {!! $field->getAttributes() !!}>

    <div class="control selectlist">
        <select
            id="{{ $field->getId('type') }}"
            class="form-control querybuilder-options-select"
        >
            @foreach ($filters as $value => $option)
                <option value="{{ $value }}">{{ $option['label'] }}</option>
            @endforeach
        </select>
    </div>

    <div class="control">
        <div class="w-100 querybuilder"></div>
    </div>
    
    <textarea name="{{ $field->getName() }}" class="d-none"></textarea>
    
</div>
