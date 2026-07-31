@extends('layouts.admin')
@section('title', 'HQ Invoices')
@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Invoices</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Invoice Number</th>
                <th>Tenant</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <!-- Invoice loop -->
        </tbody>
    </table>
</div>
@endsection
