<select name="{{ $attribute->code }}" id="{{ $attribute->code }}" name="{{ $attribute->code }}">
    @foreach ($attribute->options as $option)
        <option value="{{ $option->id }}">{{ $option->value }}</option>
    @endforeach
</select>