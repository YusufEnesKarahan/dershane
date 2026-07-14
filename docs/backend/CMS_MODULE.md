# CMS Module Architecture

The CMS module handles database-driven page rendering with full SEO management, parent-child layout trees, status workflow tracking, and version snapshot archiving.

## Components
- **Page Model**: Extends basic pages with meta SEO tags, OpenGraph properties, revisions history JSON array, author ID, template name, hierarchy references, and sort ordering.
- **DatabaseContentService**: Intercepts content requests; reads from the `pages` table database, falling back to `DemoContentService` if no corresponding record exists.
- **PageRepository**: Supports tree lookup (`getTree()`) for sidebar rendering, publishing, and archiving page listings.
