<?php
namespace App\Jobs;

use App\Models\Media;
use App\Domain\Media\Conversions\ConversionStrategyRegistry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OptimizeImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 60;
    public int $backoff = 5;

    public function __construct(protected Media $media)
    {
        $this->onQueue('media');
    }

    public function handle(ConversionStrategyRegistry $registry): void
    {
        $mime = $this->media->mime_type;

        foreach ($registry->getStrategies() as $strategy) {
            if ($strategy->canConvert($mime)) {
                $strategy->convert($this->media);
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('OptimizeImageJob failed for media ID ' . $this->media->id . ': ' . $exception->getMessage());
    }
}
