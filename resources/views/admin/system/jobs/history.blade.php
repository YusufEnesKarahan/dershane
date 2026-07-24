@extends('layouts.admin')
@section('title','Job History')
@section('content')<x-admin.crud.index-layout title="Job History" description="Uygulama işleri için izleme kaydı.">@include('admin.system.jobs.partials.table',['jobs'=>$jobs])</x-admin.crud.index-layout>@endsection
