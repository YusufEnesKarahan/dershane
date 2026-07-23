# Analytics & BI Cache Engine

To prevent heavy queries from causing database slowdowns, this module uses a central database-backed analytics cache:

- **Database Cache (`analytics_cache`)**: Persists computed multi-module statistics (such as total net averages or collection amounts).
- **TTL Cache Handling**: `AnalyticsCacheService` controls cache check times, refreshing stats automatically after 15 minutes or when explicit actions are triggered.
