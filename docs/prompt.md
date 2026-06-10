Master Prompt: Fix Todo List Staff Filtering + Improve UI Layout

I need to fix the Todo List page in my Task Management System.

Issue 1: Staff Filtering (Main Fix)

Currently, when Admin selects a staff member from the "Assign Staff" dropdown, the system still shows todos of all staff members together.

Required Behavior:

When Admin selects a specific staff member:
Show ONLY that selected staff member's todos.
Hide todos assigned to other staff members.
Filtering must work for:
Pinned Tasks section.
Normal Tasks section.
Any other task lists on the page.
When no staff is selected:
Show all tasks.
Filtering should happen instantly without page reload (AJAX/Live filtering preferred).
After creating a new todo:
Refresh the filtered list correctly.
If a staff member is selected, continue showing only that staff member's tasks.
Reset button should:
Clear selected staff filter.
Show all tasks again.
Example

If Admin selects:

Darsh

Then show:

✅ Todo 1 (Darsh)
✅ Todo 2 (Darsh)

Do NOT show:

❌ Todo 3 (Rahul)
❌ Todo 4 (Jay)

If Admin selects:

Rahul

Then show only Rahul's tasks.

Issue 2: UI Size Too Large

The Todo page looks oversized and wastes space.

Please make the UI more compact and professional.

Reduce Sizes
Task Input
Height: 40px–44px
Smaller padding
Smaller font size
Staff Dropdown
Height: 40px–44px
Width slightly reduced
Compact select styling
Task Type Dropdown
Height: 40px–44px
Compact appearance
Add Todo Button
Height: 40px–44px
Reduce horizontal padding
Smaller icon and text
Keep responsive
Task Cards

Reduce:

Card padding
Margins
Font sizes
Section spacing

Current cards feel too tall.

Make them cleaner and dashboard-like.

Issue 3: Better Alignment

Align all controls in a single row:

Task Input
Staff Select
Task Type
Add Todo Button

Requirements:

Perfect vertical alignment
Same height for all controls
Consistent spacing
Responsive layout

Desktop layout:

[ Task Input ] [ Staff Select ] [ Task Type ] [ Add Todo ]

Mobile layout:

Task Input
Staff Select
Task Type
Add Todo
Issue 4: Task Card Improvements

For each task card:

Reduce card height.
Show:
Task Name
Assigned Staff
Date/Time
Keep Delete icon aligned right.
Use flexbox alignment.
Improve visual hierarchy.

Example:

Task Name                    10/06/2026 12:05 PM
Assigned: Darsh                    [Delete]
Issue 5: Admin & Staff Experience
Admin
Can view all tasks.
Can filter by selected staff.
Can create tasks for any staff.
Staff User
Should automatically see only their own tasks.
Must never see other staff members' tasks.
No staff filter dropdown required for staff users.
Technical Requirements
Laravel/PHP backend filtering.
Proper query:
if ($staffId) {
    $todos = Todo::where('assigned_to', $staffId)->get();
} else {
    $todos = Todo::all();
}
Use AJAX for filter updates.
Maintain pagination if available.
No duplicate tasks.
No N+1 queries.
Responsive Bootstrap/Tailwind layout.
Clean and professional UI similar to modern admin dashboards.
Expected Result
Admin selects a staff member → only that staff member's todos appear.
Reset shows all tasks.
Staff users see only their own tasks.
Compact professional UI.
Better alignment and spacing.
Faster and cleaner Todo page.