<?php
namespace App\Http\Controllers\Admin;
use App\DTOs\Notification\NotificationTemplateDTO;
use App\Domain\Notification\Actions\CreateTemplate;
use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class NotificationTemplateController extends Controller { public function index() { $this->authorize('manage', NotificationTemplate::class); return view('admin.notifications.templates', ['templates' => NotificationTemplate::latest()->get()]); } public function store(Request $request, CreateTemplate $action) { $this->authorize('manage', NotificationTemplate::class); $data=$request->validate(['name'=>'required|string|max:255','slug'=>'nullable|string|max:255|unique:notification_templates,slug','title_template'=>'required|string|max:255','body_template'=>'required|string','channel'=>'required|in:panel,email,sms']); $action->execute(new NotificationTemplateDTO($data['name'], $data['slug'] ?? Str::slug($data['name']), $data['title_template'], $data['body_template'], $data['channel'])); return back()->with('success','Şablon kaydedildi.'); } }
