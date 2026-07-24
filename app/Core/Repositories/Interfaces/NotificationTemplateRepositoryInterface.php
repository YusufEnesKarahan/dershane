<?php
namespace App\Core\Repositories\Interfaces;
use App\Models\NotificationTemplate;
use Illuminate\Support\Collection;
interface NotificationTemplateRepositoryInterface { public function active(): Collection; public function create(array $data): NotificationTemplate; }
