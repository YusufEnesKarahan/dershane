# Sales Analytics Engine

The CRM Analytics engine aggregates lead volumes and salesperson conversion performance:

- **Metrics**: Computes overall registration conversion rates, advisor close volumes, source channel efficiency, and branch sales comparison.
- **DB Optimization**: Performs group-by queries on indexed status/source keys, avoiding heavy nested N+1 relationships.
