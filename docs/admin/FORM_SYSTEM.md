# Form System

Forms are built using the `<x-admin.form.*>` components.

## Components
- **`<x-admin.form.layout>`**: Wraps the `<form>` tag, automatically injecting CSRF and method spoofing directives (`@method`).
- **`<x-admin.form.field-group>`**: Wraps an individual input, providing standard label formatting and inline validation error rendering.

## Example
```html
<x-admin.form.layout action="/admin/users" method="POST">
    <x-admin.form.field-group label="Email Address" id="email" :error="$errors->first('email')">
        <input type="email" name="email" id="email" class="form-input">
    </x-admin.form.field-group>
</x-admin.form.layout>
```
