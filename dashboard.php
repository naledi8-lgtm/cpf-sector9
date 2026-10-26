<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – CPF Sector 9</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-shield-halved"></i>
        <div>
            <h1>CPF <span>Sector 9</span></h1>
            <small>Admin Panel</small>
        </div>
    </div>
    <ul class="sidebar-nav">
        <li class="nav-label">Main</li>
        <li><a class="active" data-tab="dashboard" onclick="switchTab('dashboard')"><i class="fas fa-gauge-high"></i> Dashboard</a></li>
        <li><a data-tab="users" onclick="switchTab('users')"><i class="fas fa-users"></i> Users</a></li>
        <li><a data-tab="patrollers" onclick="switchTab('patrollers')"><i class="fas fa-user-shield"></i> Patrollers</a></li>
        <li><a data-tab="admins" onclick="switchTab('admins')"><i class="fas fa-user-tie"></i> Admins</a></li>
        <li class="nav-label">Content</li>
        <li><a data-tab="announcements" onclick="switchTab('announcements')"><i class="fas fa-bullhorn"></i> Announcements</a></li>
        <li><a data-tab="meetings" onclick="switchTab('meetings')"><i class="fas fa-calendar-check"></i> Meetings</a></li>
        <li class="nav-label" style="margin-top:12px;">
            <a href="logout.php" style="color:rgba(255,255,255,0.4);"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
    <div class="sidebar-footer"><i class="fas fa-shield" style="color:var(--yellow);opacity:0.4;"></i> CPF Sector 9</div>
</aside>

<div class="main">
    <header class="main-header">
        <div class="left">
            <button class="hamburger-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <span class="page-title" id="pageTitle">Dashboard</span>
        </div>
        <div class="right">
            <div class="admin-badge"><i class="fas fa-crown"></i> <span id="adminName"><?= htmlspecialchars($user_name) ?></span></div>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </header>

    <div class="page-content">
        <!-- Stats -->
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card"><span class="stat-icon"><i class="fas fa-users"></i></span><div class="stat-number" id="statUsers">0</div><div class="stat-label">Total Users</div></div>
            <div class="stat-card"><span class="stat-icon"><i class="fas fa-user-shield"></i></span><div class="stat-number" id="statPatrollers">0</div><div class="stat-label">Patrollers</div></div>
            <div class="stat-card"><span class="stat-icon"><i class="fas fa-user-tie"></i></span><div class="stat-number" id="statAdmins">0</div><div class="stat-label">Admins</div></div>
            <div class="stat-card"><span class="stat-icon"><i class="fas fa-bullhorn"></i></span><div class="stat-number" id="statAnnouncements">0</div><div class="stat-label">Announcements</div></div>
            <!-- Existing stats -->
<div class="stat-card"><span class="stat-icon"><i class="fas fa-users"></i></span><div class="stat-number" id="statUsers">0</div><div class="stat-label">Total Users</div></div>
<div class="stat-card"><span class="stat-icon"><i class="fas fa-user-shield"></i></span><div class="stat-number" id="statPatrollers">0</div><div class="stat-label">Patrollers</div></div>
<div class="stat-card"><span class="stat-icon"><i class="fas fa-user-tie"></i></span><div class="stat-number" id="statAdmins">0</div><div class="stat-label">Admins</div></div>
<div class="stat-card"><span class="stat-icon"><i class="fas fa-bullhorn"></i></span><div class="stat-number" id="statAnnouncements">0</div><div class="stat-label">Announcements</div></div>

<!-- Add new row with Members, Topics, Replies -->
<div class="stat-card" style="border-left:4px solid var(--yellow);">
    <span class="stat-icon"><i class="fas fa-user-friends"></i></span>
    <div class="stat-number" id="statMembers">0</div>
    <div class="stat-label">Members</div>
</div>
<div class="stat-card" style="border-left:4px solid var(--yellow);">
    <span class="stat-icon"><i class="fas fa-comments"></i></span>
    <div class="stat-number" id="statTopics">0</div>
    <div class="stat-label">Topics</div>
</div>
<div class="stat-card" style="border-left:4px solid var(--yellow);">
    <span class="stat-icon"><i class="fas fa-reply"></i></span>
    <div class="stat-number" id="statReplies">0</div>
    <div class="stat-label">Replies</div>
</div>
        </div>


        <!-- Tabs -->
        <div class="dashboard-tabs" id="dashboardTabs">
            <button class="active" data-tab="dashboard" onclick="switchTab('dashboard')"><i class="fas fa-gauge-high"></i> Overview</button>
            <button data-tab="users" onclick="switchTab('users')"><i class="fas fa-users"></i> Users</button>
            <button data-tab="patrollers" onclick="switchTab('patrollers')"><i class="fas fa-user-shield"></i> Patrollers</button>
            <button data-tab="admins" onclick="switchTab('admins')"><i class="fas fa-user-tie"></i> Admins</button>
            <button data-tab="announcements" onclick="switchTab('announcements')"><i class="fas fa-bullhorn"></i> Announcements</button>
            <button data-tab="meetings" onclick="switchTab('meetings')"><i class="fas fa-calendar-check"></i> Meetings</button>
        </div>

        <!-- Panels -->
        <div class="tab-panel active" id="panel-dashboard">
            <h3 style="color:var(--blue-dark);">Welcome back, <span id="adminGreeting">Admin</span> 👋</h3>
            <p style="color:var(--gray-600);">Here's what's happening in CPF Sector 9.</p>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-top:24px;">
                <div style="background:var(--blue-light);padding:20px;border-radius:var(--radius);border-left:4px solid var(--yellow);">
                    <div style="font-size:0.8rem;color:var(--gray-600);">Pending Patrollers</div>
                    <div style="font-size:1.8rem;font-weight:800;color:var(--blue-dark);" id="pendingPatrollers">0</div>
                </div>
                <div style="background:var(--blue-light);padding:20px;border-radius:var(--radius);border-left:4px solid var(--yellow);">
                    <div style="font-size:0.8rem;color:var(--gray-600);">Upcoming Meetings</div>
                    <div style="font-size:1.8rem;font-weight:800;color:var(--blue-dark);" id="upcomingMeetings">0</div>
                </div>
                <div style="background:var(--blue-light);padding:20px;border-radius:var(--radius);border-left:4px solid var(--yellow);">
                    <div style="font-size:0.8rem;color:var(--gray-600);">Active Patrollers</div>
                    <div style="font-size:1.8rem;font-weight:800;color:var(--blue-dark);" id="activePatrollers">0</div>
                </div>
            </div>
        </div>

        <div class="tab-panel" id="panel-users">
            <div class="panel-header"><h3>Users</h3><button class="btn-add" onclick="openModal('userModal')"><i class="fas fa-plus"></i> Add User</button></div>
            <div class="table-wrapper"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead><tbody id="usersTable"><tr><td colspan="5">Loading...</td></tr></tbody></table></div>
        </div>

        <div class="tab-panel" id="panel-patrollers">
            <div class="panel-header"><h3>Patrollers</h3><button class="btn-add" onclick="openModal('patrollerModal')"><i class="fas fa-plus"></i> Add Patroller</button></div>
            <div class="table-wrapper"><table><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead><tbody id="patrollersTable"><tr><td colspan="5">Loading...</td></tr></tbody></table></div>
        </div>

        <div class="tab-panel" id="panel-admins">
            <div class="panel-header"><h3>Admins</h3><button class="btn-add" onclick="openModal('adminModal')"><i class="fas fa-plus"></i> Add Admin</button></div>
            <div class="table-wrapper"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead><tbody id="adminsTable"><tr><td colspan="4">Loading...</td></tr></tbody></table></div>
        </div>

        <div class="tab-panel" id="panel-announcements">
            <div class="panel-header"><h3>Announcements</h3><button class="btn-add" onclick="openModal('announcementModal')"><i class="fas fa-plus"></i> Add Announcement</button></div>
            <div class="table-wrapper"><table><thead><tr><th>Title</th><th>Content</th><th>Date</th><th>Actions</th></tr></thead><tbody id="announcementsTable"><tr><td colspan="4">Loading...</td></tr></tbody></table></div>
        </div>

        <div class="tab-panel" id="panel-meetings">
            <div class="panel-header"><h3>Community Meetings</h3><button class="btn-add" onclick="openModal('meetingModal')"><i class="fas fa-plus"></i> Add Meeting</button></div>
            <div class="table-wrapper"><table><thead><tr><th>Title</th><th>Date & Time</th><th>Location</th><th>Actions</th></tr></thead><tbody id="meetingsTable"><tr><td colspan="4">Loading...</td></tr></tbody></table></div>
        </div>
    </div>
</div>

<!-- Modals -->
<div id="userModal" class="modal-overlay">
    <div class="modal">
        <h3 id="userModalTitle">Add User</h3>
        <form id="userForm" onsubmit="saveUser(event)">
            <input type="hidden" id="userFormId">
            <div class="form-group"><label>Full Name *</label><input type="text" id="userFormName" placeholder="e.g. Jane Doe" required></div>
            <div class="form-group"><label>Email *</label><input type="email" id="userFormEmail" placeholder="jane@example.com" required></div>
            <div class="form-group"><label>Role</label><select id="userFormRole"><option value="member">Member</option><option value="patroller">Patroller</option><option value="admin">Admin</option></select></div>
            <div class="form-group"><label>Status</label><select id="userFormStatus"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="modal-actions"><button type="button" class="btn-cancel" onclick="closeModal('userModal')">Cancel</button><button type="submit" class="btn-submit">Save</button></div>
        </form>
    </div>
</div>

<div id="patrollerModal" class="modal-overlay">
    <div class="modal">
        <h3 id="patrollerModalTitle">Add Patroller</h3>
        <form id="patrollerForm" onsubmit="savePatroller(event)">
            <input type="hidden" id="patrollerFormId">
            <div class="form-group"><label>Full Name *</label><input type="text" id="patrollerFormName" placeholder="e.g. John Smith" required></div>
            <div class="form-group"><label>Email *</label><input type="email" id="patrollerFormEmail" placeholder="john@example.com" required></div>
            <div class="form-group"><label>Phone *</label><input type="tel" id="patrollerFormPhone" placeholder="+27 82 123 4567" required></div>
            <div class="form-group"><label>Status</label><select id="patrollerFormStatus"><option value="active">Active</option><option value="inactive">Inactive</option><option value="pending">Pending</option></select></div>
            <div class="modal-actions"><button type="button" class="btn-cancel" onclick="closeModal('patrollerModal')">Cancel</button><button type="submit" class="btn-submit">Save</button></div>
        </form>
    </div>
</div>

<div id="adminModal" class="modal-overlay">
    <div class="modal">
        <h3 id="adminModalTitle">Add Admin</h3>
        <form id="adminForm" onsubmit="saveAdmin(event)">
            <input type="hidden" id="adminFormId">
            <div class="form-group"><label>Full Name *</label><input type="text" id="adminFormName" placeholder="e.g. Sarah Mbeki" required></div>
            <div class="form-group"><label>Email *</label><input type="email" id="adminFormEmail" placeholder="sarah@example.com" required></div>
            <div class="form-group"><label>Role</label><select id="adminFormRole"><option value="super-admin">Super Admin</option><option value="admin">Admin</option><option value="moderator">Moderator</option></select></div>
            <div class="modal-actions"><button type="button" class="btn-cancel" onclick="closeModal('adminModal')">Cancel</button><button type="submit" class="btn-submit">Save</button></div>
        </form>
    </div>
</div>

<div id="announcementModal" class="modal-overlay">
    <div class="modal">
        <h3 id="announcementModalTitle">Add Announcement</h3>
        <form id="announcementForm" onsubmit="saveAnnouncement(event)">
            <input type="hidden" id="announcementFormId">
            <div class="form-group"><label>Title *</label><input type="text" id="announcementFormTitle" placeholder="e.g. Community Cleanup" required></div>
            <div class="form-group"><label>Content *</label><textarea id="announcementFormContent" rows="3" placeholder="Details..." required></textarea></div>
            <div class="modal-actions"><button type="button" class="btn-cancel" onclick="closeModal('announcementModal')">Cancel</button><button type="submit" class="btn-submit">Publish</button></div>
        </form>
    </div>
</div>

<div id="meetingModal" class="modal-overlay">
    <div class="modal">
        <h3 id="meetingModalTitle">Add Meeting</h3>
        <form id="meetingForm" onsubmit="saveMeeting(event)">
            <input type="hidden" id="meetingFormId">
            <div class="form-group"><label>Title *</label><input type="text" id="meetingFormTitle" placeholder="Monthly Sector Meeting" required></div>
            <div class="form-group"><label>Date & Time *</label><input type="datetime-local" id="meetingFormDateTime" required></div>
            <div class="form-group"><label>Location *</label><input type="text" id="meetingFormLocation" placeholder="Community Hall, 123 Main St" required></div>
            <div class="form-group"><label>Description</label><textarea id="meetingFormDesc" rows="2" placeholder="Agenda..."></textarea></div>
            <div class="modal-actions"><button type="button" class="btn-cancel" onclick="closeModal('meetingModal')">Cancel</button><button type="submit" class="btn-submit">Schedule</button></div>
        </form>
    </div>
</div>

<script src="script.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard loaded');
        loadDashboard();
    });

    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
    }
</script>
</body>
</html>