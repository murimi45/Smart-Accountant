# Finance module

Finance is the purchasable `accountant` product module.

It owns:

- class and extra fees
- invoices, payments, waivers, and statements
- expenses, other income, budgets, and audit logs
- cashbook, chart of accounts, ledger, and financial reports
- bank reconciliation and payment channels
- finance imports/exports and fee reminders
- finance observers and posting workflows

Every HTTP route in `Routes/web.php` is protected by
`module:accountant`. Finance may read shared Academics entities such as
students, classes, terms, and enrollments, but Academics must not depend on
Finance.

The existing `App\Models`, `App\Services`, and controller namespaces remain
stable during the incremental extraction to preserve queued payloads,
polymorphic type values, integrations, and route compatibility. Runtime
ownership is now isolated in `FinanceServiceProvider`; implementation classes
can be moved aggregate-by-aggregate behind compatibility aliases.
