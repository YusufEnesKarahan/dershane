# Media Picker Component

The dynamic `<x-admin.media-picker>` component offers an easy modal selector.

## Usage
```blade
<x-admin.media-picker name="og_image" label="Öne Çıkarılan Görsel" :value="$page->og_image ?? ''" />
```

## Features
- Dynamic list load using AJAX `/admin/media-picker` list parameters.
- Exposes folder filters and title search tags.
- Binds direct image preview links dynamically.
