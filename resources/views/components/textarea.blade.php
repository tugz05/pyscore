<div class="mb-3">
    @if($label)
        <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endif
    <textarea name="{{ $name }}" id="{{ $id }}" class="form-control" rows="{{ $rows }}">{{ old($name, $value) }}</textarea>
</div>
