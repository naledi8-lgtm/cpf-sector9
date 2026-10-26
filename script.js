// Toast notification system
function showNotification(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.padding = '14px 28px';
    toast.style.borderRadius = '8px';
    toast.style.fontWeight = '600';
    toast.style.boxShadow = '0 8px 30px rgba(0,0,0,0.15)';
    toast.style.zIndex = '9999';
    toast.style.color = '#fff';
    if (type === 'success') toast.style.background = '#28a745';
    else if (type === 'error') toast.style.background = '#dc3545';
    else toast.style.background = '#ffc107';
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// API Call
async function apiCall(action, method = 'GET', data = null) {
    try {
        const url = `api.php?action=${action}`;
        const options = { method, headers: { 'Content-Type': 'application/json' } };
        if (data) options.body = JSON.stringify(data);
        const res = await fetch(url, options);
        return await res.json();
    } catch (e) {
        console.error('API Error:', e);
        showNotification('Network error. Please refresh.', 'error');
        return { error: 'Network error' };
    }
}

// Load Dashboard Data
async function loadDashboard() {
    try {
        console.log('Loading dashboard data...');
        
        const [users, patrollers, admins, announcements, meetings] = await Promise.all([
            apiCall('getUsers'),
            apiCall('getPatrollers'),
            apiCall('getAdmins'),
            apiCall('getAnnouncements'),
            apiCall('getMeetings')
        ]);

        console.log('Users:', users);
        console.log('Patrollers:', patrollers);
        console.log('Admins:', admins);
        console.log('Announcements:', announcements);
        console.log('Meetings:', meetings);

        // Update stats
        document.getElementById('statUsers').textContent = Array.isArray(users) ? users.length : 0;
        document.getElementById('statPatrollers').textContent = Array.isArray(patrollers) ? patrollers.length : 0;
        document.getElementById('statAdmins').textContent = Array.isArray(admins) ? admins.length : 0;
        document.getElementById('statAnnouncements').textContent = Array.isArray(announcements) ? announcements.length : 0;
        document.getElementById('statMembers').textContent = Array.isArray(users) ? users.length : 0;
document.getElementById('statTopics').textContent = Array.isArray(announcements) ? announcements.length : 0;
document.getElementById('statReplies').textContent = Array.isArray(patrollers) ? patrollers.length : 0;

        const pendingCount = Array.isArray(patrollers) ? patrollers.filter(p => p.status === 'pending').length : 0;
        const activeCount = Array.isArray(patrollers) ? patrollers.filter(p => p.status === 'active').length : 0;
        const meetingsCount = Array.isArray(meetings) ? meetings.length : 0;

        document.getElementById('pendingPatrollers').textContent = pendingCount;
        document.getElementById('activePatrollers').textContent = activeCount;
        document.getElementById('upcomingMeetings').textContent = meetingsCount;

        // Render tables (simple for now)
        renderSimpleTable('usersTable', users, ['name', 'email', 'role', 'status']);
        renderSimpleTable('patrollersTable', patrollers, ['name', 'email', 'phone', 'status']);
        renderSimpleTable('adminsTable', admins, ['name', 'email', 'role']);
        renderSimpleTable('announcementsTable', announcements, ['title', 'content', 'date']);
        renderSimpleTable('meetingsTable', meetings, ['title', 'date_time', 'location']);

    } catch (e) {
        console.error('Error loading dashboard:', e);
        showNotification('Error loading data. Check console for details.', 'error');
    }
}

// Simple table renderer
function renderSimpleTable(tbodyId, data, columns) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    
    if (!data || data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${columns.length + 1}">No records found.</td></tr>`;
        return;
    }

    tbody.innerHTML = data.map(item => {
        const row = columns.map(col => {
            let value = item[col] || '';
            if (col === 'status') {
                return `<td><span class="badge-status ${value}">${value}</span></td>`;
            } else if (col === 'date_time') {
                return `<td>${new Date(value).toLocaleString()}</td>`;
            } else if (col === 'content') {
                return `<td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${value}</td>`;
            }
            return `<td>${value}</td>`;
        }).join('');
        return `<tr>${row}<td><button class="btn-edit" onclick="alert('Edit ID: ${item.id}')"><i class="fas fa-edit"></i></button> <button class="btn-delete" onclick="alert('Delete ID: ${item.id}')"><i class="fas fa-trash"></i></button></td></tr>`;
    }).join('');
}

// Modal helpers
function openModal(id) {
    document.getElementById(id).classList.add('open');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}

// Tab switching
function switchTab(tab) {
    document.querySelectorAll('.dashboard-tabs button').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tab === tab);
    });
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.classList.toggle('active', panel.id === 'panel-' + tab);
    });
    document.querySelectorAll('.sidebar-nav a[data-tab]').forEach(a => {
        a.classList.toggle('active', a.dataset.tab === tab);
    });
    const labels = { dashboard: 'Dashboard', users: 'Manage Users', patrollers: 'Manage Patrollers', admins: 'Manage Admins', announcements: 'Announcements', meetings: 'Community Meetings' };
    document.querySelector('.main-header .page-title').innerHTML = `CPF Sector 9 <span>| ${labels[tab] || 'Dashboard'}</span>`;
}

// Close modal on overlay click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('open');
        document.querySelectorAll('.modal form').forEach(f => f.reset());
        document.querySelectorAll('.modal input[type="hidden"]').forEach(h => h.value = '');
    }
});

// Simple CRUD Functions (placeholders – expand later)
async function saveUser(e) {
    e.preventDefault();
    showNotification('User saved (demo)', 'success');
    closeModal('userModal');
}

async function savePatroller(e) {
    e.preventDefault();
    showNotification('Patroller saved (demo)', 'success');
    closeModal('patrollerModal');
}

async function saveAdmin(e) {
    e.preventDefault();
    showNotification('Admin saved (demo)', 'success');
    closeModal('adminModal');
}

async function saveAnnouncement(e) {
    e.preventDefault();
    showNotification('Announcement published (demo)', 'success');
    closeModal('announcementModal');
}

async function saveMeeting(e) {
    e.preventDefault();
    showNotification('Meeting scheduled (demo)', 'success');
    closeModal('meetingModal');
}

// Make functions globally accessible
window.openModal = openModal;
window.closeModal = closeModal;
window.switchTab = switchTab;
window.saveUser = saveUser;
window.savePatroller = savePatroller;
window.saveAdmin = saveAdmin;
window.saveAnnouncement = saveAnnouncement;
window.saveMeeting = saveMeeting;
window.loadDashboard = loadDashboard;