@extends('layouts.admin')
@section('title', 'HQ Usage Records')
@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Usage Metring</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Tenant</th>
                <th>Metric Name</th>
                <th>Value</th>
                <th>Period</th>
            </tr>
        </thead>
        <tbody>
            <!-- Usage loop -->
        </tbody>
    </table>
</div>
@endsection
