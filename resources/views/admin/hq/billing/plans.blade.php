@extends('layouts.admin')
@section('title', 'HQ Plans')
@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Billing Plans</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Period</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <!-- Plan loop -->
        </tbody>
    </table>
</div>
@endsection
