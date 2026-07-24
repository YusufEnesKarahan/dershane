<?php

namespace App\Observers;

use App\Models\Document;
use App\Models\DocumentLog;

class DocumentObserver
{
    public function created(Document $document): void
    {
        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => auth()->id() ?? $document->uploaded_by,
            'action' => 'upload',
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }

    public function updated(Document $document): void
    {
        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => 'update',
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }

    public function deleted(Document $document): void
    {
        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => 'delete',
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
