# Grading module

Purchasable product module (`module:grading`).

- Roles: `admin`, `teacher`
- Routes: `app/Modules/Grading/Routes/web.php`
- MVP (subjects, assessments, grades, report cards) is Phase 5

Reads shared Academics entities (`Student`, `Classes`, `Term`); never duplicates them.
