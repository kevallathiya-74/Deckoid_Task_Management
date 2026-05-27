Fix the Publishing Report color synchronization bug permanently WITHOUT using sockets/WebSockets.

CURRENT BUG:
- Admin changes task color → reflects on staff side ✅
- Staff changes task color → does NOT reflect on admin side ❌

REQUIRED RESULT:
- Admin and Staff both use the SAME database data source.
- When ANYONE changes a task color:
  → save instantly into database
  → update updated_at timestamp
  → auto refresh on other panel within 1–2 seconds
  → no page refresh required

IMPLEMENT THIS USING DATABASE + AJAX POLLING ONLY.

==================================================
STEP 1 — SINGLE SOURCE OF TRUTH
==================================================

Use ONE table for publishing task cell status.

Example columns:

id
report_id
row_id
column_key
task_text
status_color
updated_by
updated_at

status_color values:
- white
- production
- approval
- publishing

DO NOT store color only in frontend state.

==================================================
STEP 2 — COLOR CYCLE SYSTEM
==================================================

Double click behavior:

white → production (yellow)
production → approval (orange)
approval → publishing (green)
publishing → white

Cycle must repeat infinitely.

==================================================
STEP 3 — SAVE CHANGES IMMEDIATELY
==================================================

When user/admin double clicks:

1. Update UI instantly
2. Send AJAX request immediately

Example:

POST /api/publishing/cell/update

Payload:
{
  cell_id,
  status_color,
  updated_by
}

Backend must:
- validate user permission
- update database
- update updated_at timestamp
- return latest saved data

==================================================
STEP 4 — REMOVE ROLE-BASED SAVE DIFFERENCE
==================================================

Currently admin save works but staff save fails.

Fix this:
- admin and staff MUST use same save logic
- same controller
- same service
- same database update function

DO NOT create separate logic for staff/admin.

==================================================
STEP 5 — AUTO SYNC WITHOUT SOCKET
==================================================

Create lightweight polling system.

Every 2 seconds:

GET /api/publishing/changes?last_sync=timestamp

Return ONLY changed cells.

Example response:
{
  changes: [
    {
      cell_id: 12,
      status_color: "approval",
      updated_at: "2026-05-27 18:30:00"
    }
  ]
}

==================================================
STEP 6 — APPLY LIVE UI UPDATE
==================================================

When polling receives changes:

- update ONLY changed cells
- do NOT reload full table
- do NOT refresh page
- do NOT rerender entire component

Use direct DOM/state patching.

==================================================
STEP 7 — FIX STAFF SAVE BUG
==================================================

Current issue:
Staff side likely:
- updates local UI only
OR
- API endpoint failing
OR
- DB table mismatch
OR
- permission middleware blocking update

Find exact root cause and permanently fix it.

==================================================
STEP 8 — ADD DEBUG LOGGING
==================================================

FRONTEND:
console.log("Saving color", payload)
console.log("Polling changes", response)

BACKEND:
Log::info('Publishing color updated', [
  'cell_id' => $cellId,
  'status' => $status,
  'user_id' => auth()->id()
]);

==================================================
STEP 9 — REQUIRED TESTING
==================================================

TEST 1:
Staff changes yellow
→ admin updates in 1–2 sec

TEST 2:
Staff changes orange
→ admin updates in 1–2 sec

TEST 3:
Staff changes green
→ admin updates in 1–2 sec

TEST 4:
Admin changes color
→ staff updates in 1–2 sec

TEST 5:
Refresh both browsers
→ latest colors remain saved

==================================================
STEP 10 — PERFORMANCE RULES
==================================================

DO NOT:
- use WebSockets
- use socket.io
- reload full page
- rerender full table
- duplicate polling requests

DO:
- use optimized polling
- fetch only changed rows
- use updated_at sync strategy
- update only changed cells

==================================================
FINAL EXPECTED RESULT
==================================================

Publishing Report should behave like a collaborative live dashboard:

- Admin and Staff stay synchronized
- Changes reflect automatically
- Database is always source of truth
- No refresh needed
- No sockets used
- Stable and optimized
- Works instantly both directions