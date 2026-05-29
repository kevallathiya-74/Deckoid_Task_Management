PROJECT: DECKOID TASK MANAGEMENT SYSTEM

FEATURE REQUEST + DATA CONSISTENCY FIX

PAGE:
http://localhost/Deckoid_Task_Management/admin/publishing

USE FULL AGENCY-AGENTS EXECUTION.

Repository:
[Agency Agents GitHub Repository](https://github.com/msitarzewski/agency-agents?utm_source=chatgpt.com)

==================================================
AGENTS REQUIRED
===============

@agency-system-architect
@agency-senior-laravel-engineer
@agency-database-architect
@agency-state-management-engineer
@agency-frontend-engineer
@agency-ui-engineer
@agency-bug-hunter
@agency-qa-engineer
@agency-data-flow-engineer
@agency-refactoring-engineer

==================================================
CURRENT ISSUE
=============

When Admin clicks:

CREATE MORE TABLE (WEEK 2)

Current behavior:

New table creates empty rows.

==================================================
REQUIRED BEHAVIOR
=================

When Admin clicks:

CREATE MORE TABLE (WEEK 2)

System must automatically clone structure from WEEK 1.

NOT blank table.

==================================================
COPY THESE VALUES
=================

From WEEK 1 copy:

Company Names

Assigned Members

Existing Rows

Row Count

Table Structure

Column Structure

Task Box Layout

Color Status Layout

Week Configuration

Category Configuration

==================================================
DO NOT COPY
===========

Task Text Values

Daily Numbers

Task Content

Notes

User Entered Data

Only copy structure.

==================================================
EXAMPLE
=======

WEEK 1

Row 1:
Company = test

Assigned =
Keval
Darsh

DAY1 = 1
DAY2 = 2
DAY3 = 3

---

Row 2:
Company = test 2

Assigned =
Keval
Darsh

---

Admin clicks:

CREATE MORE TABLE (WEEK 2)

==================================================
EXPECTED RESULT
===============

WEEK 2

Row 1:
Company = test

Assigned =
Keval
Darsh

DAY1 = empty
DAY2 = empty
DAY3 = empty

---

Row 2:
Company = test 2

Assigned =
Keval
Darsh

DAY1 = empty
DAY2 = empty
DAY3 = empty

==================================================
DATABASE REQUIREMENTS
=====================

Create proper week separation.

Week 1 data:

week_number = 1

Week 2 data:

week_number = 2

Never overwrite Week 1.

Week data must remain isolated.

==================================================
UI REQUIREMENTS
===============

After click:

CREATE MORE TABLE (WEEK 2)

Immediately render new table.

Header:

WEEK 2

Same design as WEEK 1.

Same assignments.

Same companies.

Empty task fields.

==================================================
STAFF SIDE REQUIREMENTS
=======================

Staff dashboard must also show:

WEEK 1

WEEK 2

Only assigned rows.

Same structure.

Same company names.

Same assignments.

==================================================
CRITICAL VALIDATION
===================

Prevent duplicate week creation.

If WEEK 2 already exists:

Show:

"Week 2 already created"

Do NOT create duplicate.

==================================================
AUTO INCREMENT
==============

If existing:

WEEK 1
WEEK 2

Then next button click:

Create:

WEEK 3

Automatically.

==================================================
QA TESTS
========

TEST 1

Create Week 2

Companies copied

PASS

---

TEST 2

Assignments copied

PASS

---

TEST 3

Task values empty

PASS

---

TEST 4

Week 1 untouched

PASS

---

TEST 5

Staff sees Week 2

PASS

---

TEST 6

No duplicate week creation

PASS

---

TEST 7

Week numbering automatic

PASS

==================================================
FINAL RESULT
============

CREATE MORE TABLE button must work like:

Clone Week Structure
+
Keep Assignments
+
Keep Companies
+
Create New Week
+
Keep Task Fields Empty
+
Save In Database
+
Show On Admin
+
Show On Staff

No manual recreation required.

Production-ready implementation only.
