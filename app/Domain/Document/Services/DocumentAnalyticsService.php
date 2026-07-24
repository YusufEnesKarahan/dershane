<?php

namespace App\Domain\Document\Services;

use App\Core\Repositories\DocumentRepository;
use App\Models\DocumentLog;

class DocumentAnalyticsService
{
    public function __construct(
        protected DocumentRepository $documentRepo
    ) {}

    public function getDashboardMetrics()
    {
        $analytics = $this->documentRepo->getAnalytics();
        
        $recentLogs = DocumentLog::with(['document', 'user'])->orderBy('created_at', 'desc')->limit(10)->get();

        return array_merge($analytics, [
            'recent_logs' => $recentLogs,
        ]);
    }
}
