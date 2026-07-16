# SEO Analyzer Service

The `SeoAnalyzerService` provides real-time SEO scoring and validation reports directly in the edit page view.

## Checked Criteria
- **Meta Title**: Exposes missing title penalties and warns if character lengths exceed 60 characters.
- **Meta Description**: Evaluates description presence and warns if length exceeds 160 characters.
- **H1 Header tags**: Scans content body for `# Title` indicators (exactly 1 H1 tag yields optimal score).
- **Keywords, Robots & Canonicals**: Standard tags presence validation check.
