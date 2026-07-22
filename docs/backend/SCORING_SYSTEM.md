# Scoring & Ranking Engine

Handles net calculations and standing calculations:

- **Net Formula**: $Net = Correct - \frac{Wrong}{4}$.
- **Rankings Engine (`RankingService`)**: Updates `branch_rank` and `global_rank` automatically when exam results are saved.
- **Subject Breakdown (`exam_subject_results`)**: Tracks subject-specific correct/wrong/net counts.
