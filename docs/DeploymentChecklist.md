# Deployment Checklist

Before deploying a new version to production, ensure the following steps are completed.

## 1. Code Quality
- [ ] All tests pass (`php artisan test`).
- [ ] No debugging code exists (`dd()`, `dump()`, `console.log()`).
- [ ] Environment variables are properly mapped in `.env.example`.

## 2. Optimization
- [ ] Run `php artisan optimize:clear` to flush old caches.
- [ ] Run `php artisan optimize` to cache routes, config, and views.
- [ ] Ensure frontend assets are built (`npm run build`).

## 3. Database
- [ ] Ensure all migrations are safely written (`php artisan migrate --force`).
- [ ] Validate that data seeding scripts work for fresh environments.

## 4. Background Services
- [ ] Restart Supervisor queue workers (`php artisan queue:restart`).
- [ ] Ensure CRON entries are active (`* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1`).

## 5. Sanity Check
- [ ] Verify Dashboard loads correctly.
- [ ] Verify a core operation (e.g., Student Creation).
- [ ] Verify License Limit and Tenant Context correctly applies.
