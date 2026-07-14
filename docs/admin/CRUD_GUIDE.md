# CRUD Development Guide

When building new features (like managing Students or Blogs), rely entirely on the provided `<x-admin.crud.*>` components to ensure uniform UI and UX.

## Core Components
- **`<x-admin.crud.index-layout>`**: The standard wrapper for list views. Accepts `title`, `description`, and an `actions` slot for primary buttons (e.g., "Create New").
- **`<x-admin.crud.create-layout>` & `<x-admin.crud.edit-layout>`**: Standard wrappers for forms.

## Best Practices
- Never use inline CSS or manual flex layouts for page headers. The `index-layout` automatically aligns the Title and Action buttons correctly across mobile and desktop.
