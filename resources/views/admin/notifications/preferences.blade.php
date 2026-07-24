@extends('layouts.admin')
@section('title', 'Bildirim Tercihleri')
@section('content')
<x-admin.crud.index-layout title="Bildirim Tercihleri" description="İletişim kanallarınızı yönetin."><form action="{{ route('admin.notifications.preferences.update') }}" method="POST" class="bg-white p-6 rounded-2xl shadow-premium-sm space-y-4">@csrf @method('PUT')<label class="block"><input type="checkbox" name="panel_enabled" value="1" @checked($preference->panel_enabled)> Panel içi bildirim</label><label class="block"><input type="checkbox" name="email_enabled" value="1" @checked($preference->email_enabled)> E-posta</label><label class="block"><input type="checkbox" name="sms_enabled" value="1" @checked($preference->sms_enabled)> SMS</label><button class="px-4 py-2 rounded-xl bg-primary text-white text-sm">Kaydet</button></form></x-admin.crud.index-layout>
@endsection
