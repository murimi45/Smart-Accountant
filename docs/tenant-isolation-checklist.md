# Tenant isolation — code review checklist

Use this checklist for every new feature, controller, job, or migration that touches school data.

## Data model

- [ ] Model uses `BelongsToSchool`, `ScopedViaClass`, or `ScopedViaInvoice` as appropriate
- [ ] `school_id` is **not** in `$fillable` (set via scope on create or `createForSchool()`)
- [ ] Client input never sets `school_id` — use `TenantRules::prohibitedSchoolId()`
- [ ] Foreign keys validated with `TenantRules::exists()` / scoped `unique()`
- [ ] Bulk ID arrays checked with `TenantBulkIds::assertBelongToSchool()`

## HTTP / authorization

- [ ] Route is behind `auth` + `role` middleware (and `2fa` where required)
- [ ] Controller calls `$this->authorize()` or policy is applied via route model binding
- [ ] List filters use `TenantFilters::validate()` for `term_id`, `class_id`, etc.
- [ ] Route model binding goes through scoped models (not `withoutGlobalScopes()` in controllers)

## Jobs, webhooks, observers

- [ ] Background jobs carry an explicit `schoolId` argument
- [ ] Services use `createForSchool()` / `findForSchool()` — not auth inside workers
- [ ] Webhooks resolve tenant before touching data (see `MpesaTenantResolver` pattern)
- [ ] Observers pass `school_id` explicitly when creating related records

## Cache & storage

- [ ] Cache keys use `TenantCache::key()` / `TenantCache::remember()` (prefix `school:{id}:`)
- [ ] Uploaded or generated files use `TenantStorage::path()` (`schools/{id}/...`)
- [ ] No flat filenames in `storage/app/public/` shared across tenants

## Testing

- [ ] Cross-tenant case covered (403/404/422) if the endpoint accepts an ID or FK
- [ ] `TenantFixtureBuilder` or factories always set `school_id`
- [ ] Run `php artisan test --filter=CrossTenantIsolation` before merge

## Platform admin (if applicable)

- [ ] **Do not** add `Gate::before()` blanket bypass for school admins
- [ ] Platform role is `platform` with `school_id = null` only
- [ ] Elevated access uses `PlatformContext::runAsPlatform()` + `withoutGlobalScopes()` at the call site
- [ ] Every platform action is logged via `PlatformAudit`

## Security logging

- [ ] Suspected cross-tenant probes are logged automatically (route binding, bulk IDs, 403s)
- [ ] Review `storage/logs/security.log` after security-sensitive changes
- [ ] Disable with `TENANT_SECURITY_LOG=false` only in local dev if needed

## Quick reference

| Utility | Purpose |
|---------|---------|
| `TenantRules` | Scoped validation rules |
| `TenantBulkIds` | Bulk FK ownership |
| `TenantFilters` | List-screen FK validation |
| `TenantCache` | School-prefixed cache keys |
| `TenantStorage` | School-prefixed file paths |
| `CrossTenantSecurityLog` | Security event logging |
| `PlatformContext` | Audited platform admin scope bypass |
