# Multitenancy (row-level isolation)

Smart Accountant is a **single-database, row-level multitenant** Laravel app. Each school is a tenant identified by `school_id` on tenant-owned tables.

## Module entitlements

Product features are gated by `modules` + `school_modules` (see `app/Core/Modules/ModuleRegistry.php`).

- Middleware alias: `module:accountant` (also `hr`, `grading`)
- Helper: `Schools::hasModule('accountant')`
- Blade: `@module('accountant') ... @endmodule` and `@role('admin', 'accountant') ... @endrole`
- Seed module catalog: `php artisan db:seed --class=ModulesSeeder`
- Enable modules per school via platform UI or `ModuleRegistry::enableForSchool()`
- Platform UI: `/platform/school-modules` (`role:platform`)

Finance HTTP routes require `module:accountant`. Shared academics (students, classes, terms, enrollment, promotions) and the dashboard shell do not. When Accountant is off, the dashboard skips finance queries and the sidebar hides Fees / Finance / Reports / SMS / Bulk / Payment Channels.

### Per-module roles

| Role | Module | Notes |
|------|--------|--------|
| `admin` | (all enabled) | School owner; sees every entitled module |
| `accountant` | `accountant` | Finance staff |
| `hr_manager` | `hr` | HR staff (MVP Phase 4) |
| `teacher` | `grading` | Teaching staff (MVP Phase 5) |
| `platform` | n/a | Platform ops; `school_id` null |

Sidebar partials live under `resources/views/layouts/partials/sidebar/`. HR and Grading currently expose a coming-soon home route when the module is entitled.

Platform users need `role = platform` and `school_id = null`. Migration `2026_07_15_000001_allow_platform_users_on_users_table` makes `users.school_id` nullable (FK `nullOnDelete`) and stores `role` as a string so `platform` is allowed. After login, go to `/platform/school-modules` (dashboard is tenant-only and returns 403).

## Application boundaries

- `app/Core` owns authentication-adjacent and platform routes.
- `app/Modules/Academics` is an always-on shared kernel. It owns students,
  classes, streams, academic years, terms, enrollments, and promotions.
- `app/Modules/Finance` is the entitled Accountant product. Its provider owns
  finance routes and observer registration.
- `app/Modules/Hr` and `app/Modules/Grading` are entitled stubs (coming-soon
  homes + role-gated routes) until Phase 4 / Phase 5 MVPs.

Academics records are created once per school and reused by every product
module. Finance and Grading may reference `student_id`, `class_id`, `term_id`,
and `enrollment_id`; they must not create duplicate module-specific versions
of those entities. Dependencies point from product modules to Academics, never
from Academics to a product module.

Web route ownership is split across:

- `app/Core/Routes/web.php`
- `app/Modules/Academics/Routes/web.php`
- `app/Modules/Finance/Routes/web.php`
- `app/Modules/Hr/Routes/web.php`
- `app/Modules/Grading/Routes/web.php`

## Middleware stack

Authenticated school routes use:

```
auth → school (login gate) → tenant (EnsureUserHasSchool) → 2fa → role (where applicable)
```

- **`tenant`** — rejects users without `school_id` (except platform role).
- **`role`** — restricts admin-only vs finance routes.

Platform operators use `role = platform` and `school_id = null`. They must not access tenant dashboards without `PlatformContext`.

## Data model rules

1. Tenant-owned models use **`BelongsToSchool`** (or `ScopedViaClass` / `ScopedViaInvoice` for child rows).
2. **`school_id` must not be mass-assignable** — `BelongsToSchool` strips it from `$fillable`. Set via:
   - global scope on `creating` when `auth()->user()->school_id` is present, or
   - **`Model::createForSchool($schoolId, $attributes)`** in jobs, imports, and tests.
3. HTTP input must never set `school_id` — validate with `TenantRules::prohibitedSchoolId()`.
4. Foreign keys in forms/imports use **`TenantRules::exists($table)`** or scoped helpers (`terms()`, `students()`, …).
5. Bulk ID arrays use **`TenantBulkIds::assertBelongToSchool()`**.

## HTTP / authorization

| Utility | Purpose |
|---------|---------|
| `TenantRules` | Scoped validation (`exists`, `unique`, `prohibitedSchoolId`) |
| `TenantBulkIds` | Bulk FK ownership checks |
| `TenantFilters` | List-screen FK validation (`term_id`, `class_id`, `academic_year_id`, …) |
| `TenantCache` | Cache keys prefixed `school:{id}:` |
| `TenantStorage` | Files under `schools/{id}/…` |
| `CrossTenantSecurityLog` | Logs suspected cross-tenant probes |
| `PlatformContext` | Audited platform scope bypass |

**Route model binding** on `BelongsToSchool` models is scoped to the authenticated user's school (404 on foreign IDs).

**Policies** — registered in `AuthServiceProvider`. Controllers call `$this->authorize()` for finance reads and mutations.

## `withoutGlobalScopes()` — when it is safe

Use only when **auth global scope cannot apply** (queue workers, webhooks, console) and always pair with an explicit tenant filter:

| Location | Pattern | Tenant guard |
|----------|---------|--------------|
| `BulkImportExportService` | `Model::withoutGlobalScopes()->where('school_id', $schoolId)` | `$schoolId` from controller |
| `InvoiceService` | Same + `findForSchool` | Method `$schoolId` argument |
| `InvoiceWaiverService` | Lock/update by id + `school_id` | From waiver/invoice row |
| `PromotionService` / `RunPromotion` job | `where('school_id', $this->schoolId)` | Job constructor carries `schoolId` |
| `SendPaymentNotification` job | Invoice lookup + student school check | Job carries `schoolId`; throws on mismatch |
| `OverdueSmsReminderService::runAll` | Iterates all schools' settings | Each invoice query filters `setting->school_id` |
| `MpesaTenantResolver` / `MpesaController` | Resolve student/invoice by admission + channel | Channel and student scoped to school |
| `LedgerService` / `BackfillLedger` command | Platform/maintenance | Explicit `school_id` or operator context |
| `BelongsToSchool::resolveRouteBinding` | Probe foreign existence for security log | Never returns foreign row to controller |
| Observers (`InvoicePaymentReversal`, `ClassFee`) | Load parent without scope | Parent id from trusted in-tenant child |

**Do not** use `withoutGlobalScopes()` in controllers for convenience — use `forSchool()`, policies, or route binding.

## Testing

Cross-tenant regression suite:

```bash
php artisan test --filter=CrossTenantIsolation
```

- Use `Tests\Support\TenantFixtureBuilder::createPair()` for two isolated schools.
- Expect **403**, **404**, or **422** (or validation redirect) for cross-tenant probes.
- Fixture data that exists only for isolation tests (e.g. pending waivers) should be created on **tenant B only** when it would break same-tenant feature tests.

## Code review checklist

Use this for every new feature, controller, job, or migration that touches school data.

### Data model

- [ ] Model uses `BelongsToSchool`, `ScopedViaClass`, or `ScopedViaInvoice`
- [ ] `school_id` is not in `$fillable` (use `createForSchool()`)
- [ ] Client input never sets `school_id`
- [ ] Foreign keys validated with `TenantRules::exists()` / scoped `unique()`
- [ ] Bulk ID arrays checked with `TenantBulkIds::assertBelongToSchool()`

### HTTP / authorization

- [ ] Route behind `auth` + `tenant` + `role` (+ `2fa`)
- [ ] Controller calls `$this->authorize()` or scoped route binding
- [ ] List filters use `TenantFilters::validate()`
- [ ] No `withoutGlobalScopes()` in controllers

### Jobs, webhooks, observers

- [ ] Jobs carry explicit `schoolId`
- [ ] Services use `createForSchool()` / `findForSchool()` in workers
- [ ] Webhooks resolve tenant before writes (`MpesaTenantResolver` pattern)
- [ ] Observers set `school_id` explicitly on related creates

### Cache & storage

- [ ] `TenantCache::key()` / `TenantStorage::path()` for tenant-specific assets

### Platform admin

- [ ] No `Gate::before()` blanket bypass for school admins
- [ ] Platform role: `platform`, `school_id = null`
- [ ] Elevated access: `PlatformContext::runAsPlatform()` + audited `withoutGlobalScopes()`
- [ ] Platform actions logged via `PlatformAudit`

### Security logging

- [ ] Cross-tenant probes logged (`CrossTenantSecurityLog`)
- [ ] Review `storage/logs/security-*.log` after security changes
- [ ] `TENANT_SECURITY_LOG=false` only in local dev

## Policy coverage (finance reads)

| Model | Policy | Typical ability |
|-------|--------|-----------------|
| `Account` | `AccountPolicy` | `viewAny` |
| `CashbookEntry` | `CashbookEntryPolicy` | `viewAny` |
| `LedgerEntry` | `LedgerEntryPolicy` | `viewAny` |
| `BankDeposit` | `BankDepositPolicy` | `viewAny`, `create`, `update`, `delete` |
| `BankReconciliationMatch` | `BankReconciliationMatchPolicy` | `create`, `delete` |
| `SmsLog` | `SmsLogPolicy` | `viewAny` |
| `Transaction` | `TransactionPolicy` | `viewAny` (Mpesa audit) |

Other tenant models (students, invoices, expenses, …) are registered in `AuthServiceProvider`.
