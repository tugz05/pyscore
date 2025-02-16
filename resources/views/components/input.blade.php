@props([
    'type' => 'text',
    'name' => '',
    'id' => $name,
    'label' => '',
    'placeholder' => '',
    'helpText' => '',
])

<div class="form-group">
    @if($label)
        <label for="{{ $id }}">{{ $label }}</label>
    @endif
    <input type="{{ $type }}" class="form-control" id="{{ $id }}" name="{{ $name }}" placeholder="{{ $placeholder }}" {{ $attributes }}>

    @if($helpText)
        <small id="{{ $id }}Help" class="form-text text-muted">{{ $helpText }}</small>
    @endif
</div>
