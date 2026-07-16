<?php
namespace App\Domain\Blog\Services;

class ContentAnalyzerService
{
    public function analyze(string $title, string $content, ?string $description = null): array
    {
        $score = 100;
        $checklist = [];

        // Heading Structure check
        if (!str_contains($content, '# ') && !str_contains($content, '## ')) {
            $score -= 10;
            $checklist[] = 'Heading structure is missing. Consider adding sub-headings.';
        }

        // Paragraph Length check
        $paragraphs = explode("\n", $content);
        foreach ($paragraphs as $p) {
            if (str_word_count($p) > 150) {
                $score -= 5;
                $checklist[] = 'Some paragraphs contain over 150 words. Break them up for better readability.';
                break;
            }
        }

        // Missing Alt tags on images
        if (preg_match_all('/<img[^>]+>/i', $content, $matches)) {
            foreach ($matches[0] as $img) {
                if (!str_contains($img, 'alt=') || preg_match('/alt=["\']\s*["\']/i', $img)) {
                    $score -= 10;
                    $checklist[] = 'One or more images are missing the ALT attribute.';
                    break;
                }
            }
        }

        // Duplicate Title/Description Checks
        if (strlen($title) < 10) {
            $score -= 10;
            $checklist[] = 'Title is too short. Try to make it descriptive (above 10 characters).';
        }

        if (empty($description) || strlen($description) < 30) {
            $score -= 10;
            $checklist[] = 'SEO meta description is missing or too short.';
        }

        return [
            'score' => max(0, $score),
            'checklist' => $checklist,
            'readability' => $score > 70 ? 'Good' : 'Needs Improvement',
        ];
    }
}
