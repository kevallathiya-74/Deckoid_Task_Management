# PRODUCTION TODO FILTER FIX – MASTER PROMPT

## ISSUE

The Todo List is working correctly on Localhost.

Current Local Behavior:

* Admin opens Todo page.
* No staff tasks are displayed initially.
* Tasks appear only after selecting a staff member from the Staff Filter.

This is the correct behavior.

---

## PRODUCTION ISSUE

Production URL:

/admin/todo

Currently:

* All staff todos are automatically displayed when Admin opens the page.
* Admin can immediately see every staff member's tasks.

This behavior is incorrect.

I want Production to behave exactly like Localhost.

---

# REQUIRED BEHAVIOR

## DEFAULT PAGE LOAD

When Admin opens:

/admin/todo

The system must NOT load staff todos.

Show:

Admin's own todos only

OR

Show empty state

Example:

"No staff selected"

No staff tasks should be visible.

---

# STAFF FILTER LOGIC

Dropdown:

Select Staff

Default Option:

Select Staff Member

Value:

NULL

NOT:

All Staff Members

---

## WHEN PAGE LOADS

Selected Staff:

NULL

Result:

No staff tasks loaded.

Do NOT execute:

SELECT * FROM todos

Do NOT execute:

loadAllStaffTodos()

Do NOT auto-fetch staff records.

---

# ONLY LOAD TASKS WHEN STAFF IS SELECTED

Example:

Admin selects:

Darsh

Then load:

Darsh todos only

Example:

Admin selects:

Keval

Then load:

Keval todos only

Example:

Admin selects:

Administrator

Then load:

Administrator todos only

---

# REMOVE AUTO LOAD

Audit:

Todo Controller

Todo API

Todo AJAX

Todo Service

Todo Repository

Todo Query

Todo Page Initialization

Remove any code similar to:

loadAllTodos()

getAllTodos()

fetchAllTodos()

SELECT * FROM todos

showAllStaffTasks()

automatic filter population

---

# PAGE INITIALIZATION

Current Production Flow:

Page Load
→ Load All Todos

This is wrong.

Replace with:

Page Load
→ Load Nothing
→ Wait For Staff Selection
→ Load Selected Staff Todos

---

# FILTER REQUIREMENTS

Dropdown Placeholder:

Select Staff Member

NOT:

All Staff Members

Default Value:

Empty

NULL

No selection

---

# EMPTY STATE

When no staff selected:

Show:

Select a staff member to view todos.

Do not show:

All tasks

All staff records

Any user records

---

# ADMIN OWN TASKS

If admin creates personal todos:

Show Admin Todos only.

Do not mix:

Admin Todos

Staff Todos

---

# DATABASE

Do NOT modify database.

Do NOT create new tables.

Do NOT alter schema.

This is a frontend + backend filtering issue only.

---

# BACKEND VALIDATION

Verify:

Admin Todo Controller

Admin Todo API

Admin Todo AJAX Requests

Admin Todo Queries

Admin Todo Filters

Production Environment Variables

Production Cache

Production Session Handling

Production Query Conditions

---

# POSSIBLE ROOT CAUSE

Check if production contains:

if(empty($staffId))
{
loadAllTodos();
}

This must be removed.

Replace with:

if(empty($staffId))
{
return [];
}

or

showEmptyState();

---

# QA TESTING

TEST 1

Open:

/admin/todo

Expected:

No staff todos visible.

---

TEST 2

Select Darsh.

Expected:

Only Darsh todos visible.

---

TEST 3

Select Keval.

Expected:

Only Keval todos visible.

---

TEST 4

Refresh page.

Expected:

No staff todos visible again.

---

TEST 5

Production and Local behavior match exactly.

---

# IMPORTANT

DO NOT CHANGE:

* Todo Design
* Todo UI
* Todo Layout
* Todo Cards
* Todo CRUD
* Todo Status
* Todo Pinning
* Todo Assignment

Only fix:

Production filtering logic.

The final result must behave exactly like Localhost:

No staff tasks visible until a staff member is explicitly selected from the dropdown.
