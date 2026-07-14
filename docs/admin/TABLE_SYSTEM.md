# Table System

The framework provides a reusable Table API using Blade components.

## Structure
Wrap your tables in `<x-admin.table.layout>`. 
Pass the `head` and `body` slots using `<x-admin.table.th>` and `<x-admin.table.td>`.

## Example
```html
<x-admin.table.layout>
    <x-slot name="head">
        <x-admin.table.th>Name</x-admin.table.th>
        <x-admin.table.th>Status</x-admin.table.th>
    </x-slot>
    <x-slot name="body">
        <tr>
            <x-admin.table.td>Ahmet Yılmaz</x-admin.table.td>
            <x-admin.table.td>Active</x-admin.table.td>
        </tr>
    </x-slot>
</x-admin.table.layout>
```
