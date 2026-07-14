# Activity Log Architecture

This project uses the dependency inversion principle to implement a scalable, decoupled activity logger.

## Architecture
- **ActivityLoggerInterface**: Defines the signature `log(string $action, array $data = [], ?int $userId = null)`.
- **NullActivityLogger**: Default fallback provider. Performs no calculations or database queries, avoiding performance overhead.
- **DatabaseActivityLogger**: A concrete database-logging implementation. Ready to be mapped via container binding when the database logger feature flag or model is added.

Any domain Actions that need to log activity inject the `ActivityLoggerInterface` contract instead of importing concrete logger instances.
