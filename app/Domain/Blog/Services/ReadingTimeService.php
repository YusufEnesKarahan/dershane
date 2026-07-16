<?php
namespace App\Domain\Blog\Services;

class ReadingTimeService
{
    public function calculate(string $content): int
    {
        $words = str_word_count(strip_tags($content));
        $minutes = ceil($words / 200); // 200 words-per-minute
        return max(1, (int) $minutes);
    }
}
