<?php

namespace App\Domain\Notification\Services;

use App\Core\Repositories\Interfaces\NotificationTemplateRepositoryInterface;
use App\DTOs\Notification\NotificationTemplateDTO;
use App\Models\NotificationTemplate;
use Illuminate\Support\Str;

class NotificationTemplateService
{
    public function __construct(private readonly NotificationTemplateRepositoryInterface $templates) {}
    public function create(NotificationTemplateDTO $dto): NotificationTemplate { return $this->templates->create(['name' => $dto->name, 'slug' => $dto->slug, 'code' => $dto->slug, 'title' => $dto->titleTemplate, 'body' => $dto->bodyTemplate, 'title_template' => $dto->titleTemplate, 'body_template' => $dto->bodyTemplate, 'channel' => $dto->channel, 'is_active' => $dto->isActive]); }
    public function render(NotificationTemplate $template, array $data): array { $replace = collect($data)->mapWithKeys(fn ($value, $key) => ['{{'.$key.'}}' => (string) $value, '{'.$key.'}' => (string) $value])->all(); return ['title' => strtr($template->title_template ?: $template->title, $replace), 'message' => strtr($template->body_template ?: $template->body, $replace)]; }
}
