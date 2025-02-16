@props([
    'name' => '',
    'id' => $name,
    'label' => '',
    'options' => [],
    'selected' => '',
])

<div class="form-group">
    @if($label)
        <label for="{{ $id }}">{{ $label }}</label>
    @endif
    <select class="form-control" id="{{ $id }}" name="{{ $name }}" {{ $attributes }}>
        @foreach($options as $key => $value)
            <option value="{{ $key }}" {{ $key == $selected ? 'selected' : '' }}>{{ $value }}</option>
        @endforeach
    </select>
</div>
