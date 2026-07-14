# REST API Standards

This document outlines the standard conventions for building and consuming APIs in the Dershane SaaS platform.

## 1. Endpoint Conventions
- **Base URL:** `/api/v1`
- **Resource Naming:** Use plural nouns for resources. Example: `/api/v1/courses`, `/api/v1/students`
- **Method Usage:**
  - `GET`: Read resources
  - `POST`: Create a new resource
  - `PUT`: Fully replace an existing resource
  - `PATCH`: Partially update a resource
  - `DELETE`: Remove a resource

## 2. JSON Response Standard
All API responses must follow a consistent JSON envelope to allow frontend consumers to parse responses predictably.

### Success Response
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Math 101"
  },
  "message": "Course retrieved successfully."
}
```

### Error Response
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The given data was invalid.",
    "details": {
      "email": ["The email field is required."]
    }
  }
}
```

## 3. Pagination Standard
Collection endpoints should use Cursor Pagination or standard Length-Aware Pagination depending on the dataset scale.
The `data` object will wrap the items, while `meta` and `links` handle traversal.

```json
{
  "success": true,
  "data": [...],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "path": "...",
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

## 4. HTTP Status Codes
- `200 OK`: Successful read or update.
- `201 Created`: Successful creation.
- `204 No Content`: Successful deletion (no body returned).
- `400 Bad Request`: General client error.
- `401 Unauthorized`: Missing or invalid authentication.
- `403 Forbidden`: Authenticated but insufficient permissions.
- `404 Not Found`: Resource does not exist.
- `422 Unprocessable Entity`: Validation failures.
- `500 Internal Server Error`: Server failure.
