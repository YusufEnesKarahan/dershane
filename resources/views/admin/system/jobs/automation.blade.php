@extends('layouts.admin')
@section('title','Automation Logs')
@section('content')<x-admin.crud.index-layout title="Automation Logs" description="Zamanlanmış otomasyon çalışmaları.">@include('admin.system.jobs.partials.table',['jobs'=>$logs])</x-admin.crud.index-layout>@endsection
