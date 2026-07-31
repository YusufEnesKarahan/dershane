@extends('layouts.admin')
@section('title', 'HQ Subscriptions')
@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Tenant Subscriptions</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tenant</th>
                <th>Plan</th>
                <th>Status</th>
                <th>Starts At</th>
                <th>Expires At</th>
            </tr>
        </thead>
        <tbody>
            <!-- Subscription loop -->
        </tbody>
    </table>
</div>
@endsection
