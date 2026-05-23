@props([
    'id',
    'name',
    'label',
    'placeholder' => '',
    'autocomplete' => 'current-password',
    'required' => true,
])

<div class="mb-3 auth-password-field">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <div class="auth-password-field__wrap">
        <input type="password" id="{{ $id }}" name="{{ $name }}"
            class="form-control @error($name) is-invalid @enderror"
            @if ($required) required @endif
            autocomplete="{{ $autocomplete }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes }}>
        <button type="button" class="auth-password-field__toggle" aria-label="Show password"
            data-password-toggle="{{ $id }}">
            <i class="bi bi-eye" aria-hidden="true"></i>
        </button>
    </div>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
