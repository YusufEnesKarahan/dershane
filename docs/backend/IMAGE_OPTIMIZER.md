# Image Optimizer and Thumbnails

Image optimization and conversions are completed using PHP native GD extension:

- **WebP compression**: Automatically resamples JPG/PNG uploads and saves optimized `.webp` formats.
- **Conversions**:
  - `thumb` (150x150 crop)
  - `small` (300px width)
  - `medium` (600px width)
  - `large` (1200px width)
- **Directory**: Saved under `storage/app/public/{collection}/{conversion}/{filename}`.
