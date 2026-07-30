@extends('layouts.admin')
@section('title', 'Create Workflow')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black text-neutral-900 dark:text-white tracking-tight">Create Workflow</h1>
            <p class="text-sm font-bold text-neutral-500 mt-1">Define an automated workflow sequence using JSON.</p>
        </div>
        <div>
            <a href="{{ route('admin.platform.hq_central.workflows.index') }}" class="text-indigo-600 hover:text-indigo-800 font-bold text-sm">
                &larr; Back to Workflows
            </a>
        </div>
    </div>

    <form action="{{ route('admin.platform.hq_central.workflows.store') }}" method="POST" class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-black text-neutral-900 dark:text-white mb-2">Workflow Name</label>
                <input type="text" name="name" required class="w-full bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-neutral-900 dark:text-white rounded-xl focus:ring-primary focus:border-primary px-4 py-2 font-medium">
            </div>
            <div>
                <label class="block text-sm font-black text-neutral-900 dark:text-white mb-2">Slug</label>
                <input type="text" name="slug" required class="w-full bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-neutral-900 dark:text-white rounded-xl focus:ring-primary focus:border-primary px-4 py-2 font-medium">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-black text-neutral-900 dark:text-white mb-2">Trigger Event</label>
                <select name="trigger_event" required class="w-full bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-neutral-900 dark:text-white rounded-xl focus:ring-primary focus:border-primary px-4 py-2 font-medium">
                    <option value="App\Events\SystemOfflineDetected">SystemOfflineDetected</option>
                    <option value="App\Events\SecurityThreatDetected">SecurityThreatDetected</option>
                    <option value="App\Events\BackupCompleted">BackupCompleted</option>
                    <option value="App\Events\UpdateCompleted">UpdateCompleted</option>
                    <option value="App\Events\ConfigurationChanged">ConfigurationChanged</option>
                    <option value="App\Events\LicenseChanged">LicenseChanged</option>
                    <option value="App\Events\SubscriptionExpired">SubscriptionExpired</option>
                    <option value="App\Events\QuotaExceeded">QuotaExceeded</option>
                    <option value="App\Events\AlertCreated">AlertCreated</option>
                    <option value="App\Events\AuditCreated">AuditCreated</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-neutral-300 text-primary focus:ring-primary h-4 w-4">
                    <span class="ml-2 text-sm font-black text-neutral-900 dark:text-white">Active</span>
                </label>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-black text-neutral-900 dark:text-white mb-2">Steps Definition (JSON Builder)</label>
            <p class="text-xs text-neutral-500 mb-2 font-bold">Provide a valid JSON array of step objects. Format: <code>[{"type":"action", "name":"Notify", "config":{"action":"send_mail", "to":"admin@hq.com"}}]</code></p>
            <textarea name="steps_json" rows="15" required class="w-full bg-neutral-900 text-green-400 font-mono text-sm border-0 rounded-xl p-4 focus:ring-primary shadow-inner">
[
    {
        "type": "action",
        "name": "Send Alert Notification",
        "config": {
            "action": "create_alert",
            "type": "workflow.triggered",
            "severity": "warning",
            "message": "Workflow has been triggered by event."
        }
    }
]
            </textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-primary text-white font-black text-sm rounded-xl shadow-premium-sm hover:bg-primary-600 transition-colors">
                Save Workflow
            </button>
        </div>
    </form>
</div>
@endsection
