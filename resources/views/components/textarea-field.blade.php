@props([
    'name',
    'label',
    'placeholder' => '',
    'value' => '',
    'rows' => 4,
    'maxlength' => null,
])

<div class="mt-4">
    <label for="{{ $name }}">{{ $label }}</label>
    <textarea
        class="form-control @error($name) is-invalid @enderror"
        rows="{{ $rows }}"
        name="{{ $name }}"
        id="{{ $name }}"
        @if($maxlength) maxlength="{{ $maxlength }}" @endif
        placeholder="{{ $placeholder }}"
    >{{ $value }}</textarea>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
