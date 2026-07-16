# CMS Architecture Hardening Guide

The CMS module is updated with enterprise architecture hardening:

- **PageStatus Enum**: Eliminates magic status strings by utilizing `App\Enums\PageStatus` mapping standard states.
- **Polymorphic Events & Observers**: Dispatches event hooks (`PageCreated`, `PageUpdated`, etc.) via `PageObserver` for clean separation of concerns.
- **Signed Preview Route**: Prevents unauthorized preview edits by requiring temporary signed links (`URL::temporarySignedRoute`).
- **League CommonMark Abstraction**: Declares `MarkdownRendererInterface` letting developers switch rendering wrappers without changing domain actions.
