# Sprint 5.0.1 — Route Regression Report

Audit date: 24 July 2026.

## Route registry result

| Check | Result |
|---|---:|
| Registered routes | 307 |
| Named routes | 305 |
| Duplicate named routes | 0 |
| Route targets missing controller methods | 0 |
| Static app/Blade route references scanned | 232 |
| Undefined static route references | 0 |

`routes/web.php` is the single web-route registry and loads `frontend.php`, `admin.php`, `parent.php`, `teacher.php`, and `auth.php`. `bootstrap/app.php` registers only that registry; it no longer adds duplicate route groups.

## Resource and redirect regression result

- Resource declarations expose only methods implemented by their controllers.
- Named redirects and `route()` / `to_route()` calls resolve successfully.
- No duplicate route names or route-to-controller-method mismatches remain.
- Admin-menu routes are all registered.

## Runtime route finding

The admission dashboard originally failed during controller resolution:

```
AdmissionDocumentService::__construct(): Argument #1 ($documentRepo)
must be of type DocumentRepositoryInterface,
EloquentDocumentRepository given
```

Cause: the provider mapped the admission-document interface to the unrelated general archive repository. The binding now resolves to `AdmissionDocumentRepository`, which implements the declared contract. The seeded-MySQL controller and view smoke test passes after this correction.
