TASK TABLE SCROLLBAR FIX — EXACT IMPLEMENTATION REQUIRED

IMPORTANT:
Current fix did NOT solve the issue.

The horizontal scrollbar is STILL rendering outside the table card container at the very bottom.

This is NOT a styling issue only.
This is a WRONG container structure issue.

==================================================
EXACT PROBLEM
=============

Current structure is likely:

Card Container
├── Table
├── Pagination
└── Browser/Page Scrollbar

OR

overflow-x applied on WRONG parent container.

==================================================
REQUIRED RESULT
===============

The horizontal scrollbar MUST appear:

DIRECTLY BELOW TABLE ROWS
INSIDE THE TABLE WRAPPER
ABOVE PAGINATION

Like professional SaaS dashboards.

==================================================
CORRECT STRUCTURE REQUIRED
==========================

Use EXACT structure:

Card Container
├── Table Scroll Wrapper
│     ├── Table
│     └── Horizontal Scrollbar
│
└── Pagination Footer

==================================================
IMPORTANT FIX
=============

Apply overflow-x ONLY to the table wrapper.

NOT:

* card container
* page container
* content wrapper
* body
* dashboard layout

==================================================
REQUIRED HTML STRUCTURE
=======================

Use structure similar to:

<div class="task-table-card">

```
<div class="table-scroll-wrapper">
    <table>
        ...
    </table>
</div>

<div class="table-footer-pagination">
    ...
</div>
```

</div>

==================================================
REQUIRED CSS FIX
================

.table-scroll-wrapper {
width: 100%;
overflow-x: auto;
overflow-y: hidden;
display: block;
position: relative;
padding-bottom: 6px;
}

.task-table-card {
overflow: hidden;
}

body {
overflow-x: hidden;
}

==================================================
REMOVE THESE ISSUES
===================

Fix and remove:

* page-level horizontal scrollbar
* body horizontal scrolling
* scrollbar outside card
* huge white empty gap
* pagination pushed downward
* container overflow leak

==================================================
DATATABLE FIX
=============

If using DataTables:

Check:

* .dataTables_wrapper
* .table-responsive
* overflow wrappers

IMPORTANT:
DataTables may be creating extra wrapper causing scrollbar outside card.

Fix DataTables wrapper structure properly.

==================================================
RESPONSIVE FIX
==============

Desktop:

* scrollbar inside table card

Tablet:

* smooth horizontal table scroll

Mobile:

* responsive table wrapper
* no body overflow

==================================================
FINAL RESULT REQUIRED
=====================

Correct layout should become:

[TABLE ROWS]
[SCROLLBAR]
[PAGINATION]

NOT:

[TABLE ROWS]
[EMPTY GAP]
[PAGINATION]
[SCROLLBAR]

==================================================
IMPORTANT
=========

Do NOT give partial CSS fixes only.

Analyze:

* actual DOM structure
* overflow parent containers
* DataTable wrappers
* flex/grid parent behavior

Then fix properly from root cause.

==================================================
FINAL GOAL
==========

Task table should behave like:

* Linear dashboard
* modern SaaS admin panel
* premium responsive analytics table

Clean.
Compact.
No overflow bugs.
Professional layout.
