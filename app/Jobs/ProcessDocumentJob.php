<?php
namespace App\Jobs;
use App\DTOs\System\CreateJobLogDTO;
use App\Domain\System\Services\JobMonitoringService;
use App\Models\{Document,DocumentLog};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
class ProcessDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;
    public int $backoff = 10;

    public function __construct(public readonly int $documentId)
    {
        $this->onQueue('documents');
    }

    public function handle(JobMonitoringService $monitoring): void
    {
        $history = $monitoring->start(new CreateJobLogDTO(self::class, 'running', ['document_id' => $this->documentId]));
        try {
            $document = Document::findOrFail($this->documentId);
            if (Storage::disk('public')->exists($document->file_path)) {
                $document->update(['file_size' => Storage::disk('public')->size($document->file_path)]);
            }
            DocumentLog::create([
                'document_id' => $document->id,
                'user_id' => $document->uploaded_by,
                'action' => 'processed',
                'created_at' => now()
            ]);
            $monitoring->complete($history);
        } catch (\Throwable $e) {
            $monitoring->fail($history, $e);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('ProcessDocumentJob failed for document ID ' . $this->documentId . ': ' . $exception->getMessage());
    }
}
