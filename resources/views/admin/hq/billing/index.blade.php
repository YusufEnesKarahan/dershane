@extends('layouts.admin')
@section('title', 'HQ Billing & Subscriptions')
@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Billing Dashboard</h1>
    <div class="row">
        <!-- Links to Plans, Subscriptions, Invoices, Usage -->
        <a href="{{ url('/admin/hq/billing/plans') }}" class="btn btn-primary m-2">Manage Plans</a>
        <a href="{{ url('/admin/hq/billing/subscriptions') }}" class="btn btn-primary m-2">View Subscriptions</a>
        <a href="{{ url('/admin/hq/billing/invoices') }}" class="btn btn-primary m-2">Invoices</a>
        <a href="{{ url('/admin/hq/billing/usage') }}" class="btn btn-primary m-2">Usage Metrics</a>
    </div>
</div>
@endsection
