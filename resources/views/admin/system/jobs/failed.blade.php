@extends('layouts.admin')
@section('title','Failed Jobs')
@section('content')<x-admin.crud.index-layout title="Failed Jobs" description="Laravel failed_jobs kayıtları."><div class="bg-white p-6 rounded-2xl shadow-premium-sm">@forelse($jobs as $job)<div class="border-b py-3"><b>{{ $job->queue }}</b><p class="text-xs text-red-600">{{ $job->exception }}</p></div>@empty<p>Başarısız iş bulunmuyor.</p>@endforelse {{ $jobs->links() }}</div></x-admin.crud.index-layout>@endsection
