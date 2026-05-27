DECKOID TASK MANAGEMENT SYSTEM — COMPLETE PROJECT ANALYSIS, DEBUGGING, STABILIZATION, RESPONSIVE FIXES & PRODUCTION HARDENING MASTER PROMPT

PROJECT:
Deckoid_Task_Management

GOAL:
Analyze the ENTIRE project end-to-end and permanently fix:
- frontend bugs
- backend bugs
- database issues
- API failures
- responsive problems
- broken forms
- dropdown issues
- save/load failures
- dynamic UI issues
- table rendering issues
- modal issues
- performance bottlenecks
- role permission issues
- validation issues
- data sync issues

Then optimize the entire system for:
- production usage
- 10–50 concurrent users
- stable CRUD operations
- clean responsive UI
- maintainable scalable architecture

==================================================
CRITICAL INSTRUCTIONS
==================================================

DO NOT:
- redesign the project completely
- change theme colors
- break existing features
- remove current modules
- change sidebar/navbar structure
- remove existing database data
- change business logic unnecessarily

ONLY:
- fix bugs
- stabilize project
- improve UI consistency
- improve responsiveness
- optimize performance
- fix database/API connectivity
- improve maintainability

==================================================
PHASE 1 — COMPLETE PROJECT ANALYSIS
==================================================

Analyze FULL project structure:

FRONTEND:
- layouts
- sidebar
- navbar
- pages
- forms
- tables
- modals
- dropdowns
- responsive structure
- API integration
- validation
- dynamic rendering
- state handling

BACKEND:
- routes
- controllers
- middleware
- services
- validation
- auth
- RBAC
- database queries
- error handling
- file structure

DATABASE:
- schema
- foreign keys
- indexes
- constraints
- missing tables
- duplicate data
- relation issues
- null issues
- migration issues

==================================================
PHASE 2 — FIND ALL BUGS
==================================================

Find ALL issues including:

=================================
FRONTEND BUGS
=================================

1. Broken dropdowns
2. Dynamic form issues
3. Modal overflow
4. Broken scrolling
5. Responsive breaking
6. Horizontal overflow
7. Table misalignment
8. Mobile UI issues
9. Duplicate event listeners
10. Broken form cloning
11. State reset issues
12. Missing validation
13. UI flickering
14. Broken pagination
15. Broken filters
16. Select/multi-select issues
17. Improper z-index
18. Toast overlap
19. Broken sidebar responsiveness
20. Misaligned cards
21. Publishing Report UI issues
22. Daily Report layout issues
23. Todo page layout issues
24. Broken task creation modal
25. Dynamic task section issues
26. Button alignment issues
27. Grid inconsistency
28. Input focus issues
29. Browser compatibility issues
30. Accessibility issues

=================================
BACKEND BUGS
=================================

1. API save failures
2. Load failures
3. Broken CRUD
4. Invalid responses
5. Missing validation
6. Unhandled exceptions
7. Duplicate APIs
8. Missing middleware
9. Broken auth
10. Session issues
11. Race conditions
12. Broken assignment logic
13. Slow queries
14. Missing transactions
15. SQL errors
16. API timeout issues
17. Incorrect status codes
18. Missing logging
19. Broken role permissions
20. Invalid payload handling

=================================
DATABASE BUGS
=================================

1. Missing tables
2. Missing migrations
3. Broken relations
4. Duplicate records
5. Wrong column types
6. Missing indexes
7. Slow queries
8. Missing constraints
9. Cascade issues
10. Daily report table missing
11. Publishing report mapping issues
12. Todo relation issues
13. Foreign key failures
14. Null reference problems

==================================================
PHASE 3 — FIX DAILY REPORT MODULE
==================================================

CRITICAL:
Daily Report currently fails with:

SQLSTATE[42S02]
Base table or view not found:
daily_reports table doesn't exist

FIX PERMANENTLY.

=================================
REQUIRED FIXES
=================================

1. Create proper database tables:

daily_reports
daily_report_items

2. Add proper foreign keys:
- user_id
- report_id

3. Add migrations

4. Add indexes

5. Add timestamps

6. Fix save API

7. Fix load API

8. Fix edit API

9. Fix report summary API

10. Fix admin report fetch

=================================
STAFF SIDE
=================================

Staff can:
- create report
- edit own report
- load previous reports
- save reports
- add rows dynamically
- remove rows
- calculate totals automatically

=================================
ADMIN SIDE
=================================

Admin can:
- select user
- select date
- view reports
- filter reports
- view totals
- view historical reports

=================================
VALIDATION
=================================

Daily Task:
- text only

Number:
- numeric only

Prevent:
- invalid saves
- empty payloads
- duplicate reports

==================================================
PHASE 4 — FIX TASK MANAGEMENT MODULE
==================================================

CRITICAL ISSUES:
Dynamic tasks break dropdowns.

=================================
FIX:
=================================

1. Add Another Task functionality
2. Dynamic dropdown initialization
3. Multi-select initialization
4. Department dropdown
5. Assign Team Lead dropdown
6. Remove duplicated IDs
7. Modal scrolling
8. Event rebinding
9. Dynamic validation
10. Dynamic progress slider

=================================
ENSURE:
=================================

Every new task form behaves EXACTLY like Task 1.

==================================================
PHASE 5 — FIX PUBLISHING REPORT MODULE
==================================================

=================================
FIX UI ISSUES
=================================

1. Broken table alignment
2. Overflow issues
3. Assignment column issues
4. Scrollbar UI
5. Table responsiveness
6. Cell sizing
7. Dynamic week generation
8. Editing experience
9. Sticky headers
10. Dynamic row addition
11. Text wrapping
12. Input alignment
13. Mobile rendering

=================================
FEATURE REQUIREMENTS
=================================

1. Categories:
- Posts
- Reels
- Facebook Ads

2. Dynamic weeks:
- Week 1
- Week 2
- Week 3

3. Save/load tables

4. Edit tables

5. Delete tables

6. Multi-member assignment

7. Staff visibility

8. Admin-only controls

=================================
UI REQUIREMENTS
=================================

Fix:
- huge cells
- broken spacing
- ugly scrollbars
- misaligned columns
- assignment pill UI
- action button alignment

Make UI:
- clean
- premium
- consistent
- responsive
- modern SaaS style

==================================================
PHASE 6 — FIX TODO MODULE
==================================================

=================================
FEATURES
=================================

1. Todo creation
2. Assign staff
3. Pending/completed
4. Pin task
5. Daily pinned tasks
6. Sticky pinned section
7. Responsive layout
8. Separate pinned/non-pinned sections

=================================
FIX:
=================================

- task duplication
- assignment issues
- broken status updates
- slow rendering
- mobile responsiveness

==================================================
PHASE 7 — RESPONSIVE FIXES
==================================================

Fix ENTIRE project responsiveness.

=================================
DESKTOP
=================================

- aligned grids
- proper widths
- modal centering
- table optimization

=================================
TABLET
=================================

- wrapped layouts
- optimized spacing
- proper stacking

=================================
MOBILE
=================================

- sidebar collapse
- stacked cards
- responsive tables
- scroll optimization
- proper touch spacing

=================================
FIX:
=================================

1. overflow-x issues
2. clipped dropdowns
3. modal height issues
4. hidden buttons
5. horizontal scrolling
6. responsive typography
7. card breaking
8. broken flex/grid layouts

==================================================
PHASE 8 — PERFORMANCE OPTIMIZATION
==================================================

Optimize for:
10–50 concurrent users.

=================================
FRONTEND
=================================

1. Lazy rendering
2. Debouncing
3. Memoization
4. Remove unnecessary rerenders
5. Optimize event listeners
6. Optimize large tables
7. Reduce DOM load

=================================
BACKEND
=================================

1. Query optimization
2. Eager loading
3. DB indexing
4. API optimization
5. Proper caching
6. Transactions
7. Queue heavy operations

=================================
DATABASE
=================================

1. Add indexes
2. Optimize joins
3. Remove N+1 queries
4. Normalize schema
5. Add constraints

==================================================
PHASE 9 — SECURITY FIXES
==================================================

Implement:

1. CSRF protection
2. XSS prevention
3. SQL injection prevention
4. Input sanitization
5. Proper validation
6. Secure sessions
7. RBAC validation
8. Route protection
9. API authorization
10. Rate limiting

==================================================
PHASE 10 — ERROR HANDLING
==================================================

Implement:

1. Global error handling
2. API error middleware
3. User-friendly toasts
4. Backend logging
5. Validation errors
6. DB exception handling
7. Network failure handling

==================================================
PHASE 11 — TESTING
==================================================

TEST ENTIRE PROJECT:

=================================
TASKS
=================================

- create
- edit
- delete
- multi-task
- assignment
- filters

=================================
PUBLISHING REPORT
=================================

- create week
- save
- load
- edit
- assign members
- responsive tables

=================================
TODO
=================================

- pin task
- assignment
- completion
- filtering

=================================
DAILY REPORT
=================================

- save
- load
- edit
- admin summary
- date filtering

=================================
RESPONSIVE
=================================

- desktop
- tablet
- mobile

=================================
VERIFY:
=================================

- no console errors
- no SQL errors
- no broken API
- no duplicate events
- no missing dropdowns
- no broken layouts
- no undefined variables
- no migration failures
- no infinite rerenders

==================================================
FINAL REQUIREMENTS
==================================================

1. Production-ready architecture
2. Clean reusable components
3. Clean APIs
4. Stable DB structure
5. Fully responsive
6. Fast performance
7. Modern SaaS UI
8. Proper validation
9. Clean code organization
10. No broken existing features

==================================================
VERY IMPORTANT
==================================================

After fixing:
- test every module manually
- verify all CRUD operations
- verify all save/load operations
- verify admin/staff role behavior
- verify database persistence
- verify responsive behavior

The final project must be:
- stable
- scalable
- maintainable
- responsive
- production-ready
- bug-free
- visually consistent
- optimized for real users