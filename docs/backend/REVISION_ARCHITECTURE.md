# Revision Archive Architecture

Rather than keeping snapshots in a JSON column within the `pages` table, the system isolates revision logs into a dedicated table:

- **page_revisions**: Keeps foreign keys, revision number identifiers, and snapshots (`seo_snapshot`, `content_snapshot`).
- **Trigger**: Invoked automatically inside `PageObserver::updating`.
- **Threshold**: Exposes configurable maximum limits (default: 20 versions) defined under `config/cms.php`.
