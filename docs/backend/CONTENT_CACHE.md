# Database Content Cache

The `DatabaseContentService` contains dynamic query caching layers:

- **Cache Keys**: `cms.page.template.{template}`.
- **Cache Invalidation**: Triggers cache flushing automatically inside `PageObserver` for updating, deleting, or restoring actions.
- **Bypass / Fallback**: Reads from the active database first, falling back to `DemoContentService` if no corresponding record exists.
