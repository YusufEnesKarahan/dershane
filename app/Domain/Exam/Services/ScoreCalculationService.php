<?php
namespace App\Domain\Exam\Services;

class ScoreCalculationService
{
    /**
     * Net calculation: Net = Correct - (Wrong / 4)
     */
    public function calculateNet(int $correct, int $wrong): float
    {
        $net = $correct - ($wrong / 4.0);
        return max(0, round($net, 2));
    }

    /**
     * Simple score calculation scaled to 500 points
     */
    public function calculateScore(float $net, int $totalQuestions = 120): float
    {
        if ($totalQuestions <= 0) return 0;
        $score = ($net / $totalQuestions) * 500.0;
        return round($score, 2);
    }
}
