<?php
namespace App\Core\Repositories;
use App\Core\Repositories\Interfaces\NotificationTemplateRepositoryInterface;
use App\Models\NotificationTemplate;
use Illuminate\Support\Collection;
class NotificationTemplateRepository implements NotificationTemplateRepositoryInterface { public function active(): Collection { return NotificationTemplate::query()->where('is_active', true)->orderBy('name')->get(); } public function create(array $data): NotificationTemplate { return NotificationTemplate::create($data); } }
