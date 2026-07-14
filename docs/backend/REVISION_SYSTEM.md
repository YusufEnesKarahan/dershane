# Page Revision System

To maintain edit safety, `UpdatePageAction` invokes `RevisionService` before committing any updates to database columns.

## Flow
1. **JSON Snapshot**: Serializes page title, content, excerpt, updater ID, and timestamp into a snapshot array.
2. **Persistence**: Saves the snapshots in the `revisions` JSON column.
3. **Threshold**: Keeps the last 10 revisions to minimize database footprint.
