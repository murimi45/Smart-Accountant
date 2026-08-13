# Grading module

Purchasable product module (`module:grading`).

- Roles: `admin`, `teacher`
- Routes: `app/Modules/Grading/Routes/web.php`
- Schemes: **CBC** and **International** (one active scheme per school academic year)
- Features: subjects/class-subjects, assessment types & weights, teacher assignments, assessments, Livewire mark entry, results engine, published PDF report cards (DomPDF)

Reads shared Academics entities (`Student`, `StudentEnrollment`, `Classes`, `Term`); never duplicates them.

## Setup order

1. Enable Grading for the school (platform school modules).
2. Create teachers under School Users.
3. Grading → Setup → Scheme & Year (CBC or International).
4. Subjects → Class Subjects → Assessment Types → Weights (international) → Teacher Assignments.
5. Create assessments (status Open) → Mark Entry → Report Cards → Publish.
