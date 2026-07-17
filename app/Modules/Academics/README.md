# Academics shared kernel

Academics is an always-on shared kernel, not a purchasable module.

It owns the lifecycle and HTTP surface for:

- students and student history
- academic years and terms
- classes and streams
- student enrollments
- promotion runs

Finance and Grading may reference these shared records by foreign key. They
must not duplicate students, classes, terms, or enrollments in module-specific
tables.

## Dependency rule

Product modules may depend on Academics. Academics must not depend on Finance,
HR, or Grading.

The existing `App\Models` namespaces remain stable during the incremental
extraction so queued jobs, polymorphic relations, policies, and integrations
do not break. Ownership is established first through this provider and route
file; namespace moves can then be performed one aggregate at a time.
