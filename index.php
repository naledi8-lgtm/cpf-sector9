<?php
session_start();

// Redirect logged-in users to their respective dashboards
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: dashboard.php');
        exit;
    } elseif ($_SESSION['user_role'] === 'patroller') {
        header('Location: patroller_dashboard.php');
        exit;
    }
    // If 'member' or other, stay on homepage
}

require_once 'db.php';

// ---------- STATS ----------
$stmt = $pdo->query('SELECT COUNT(*) as total FROM users');
$totalMembers = $stmt->fetch()['total'];

$stmt = $pdo->query('SELECT COUNT(*) as total FROM announcements');
$totalTopics = $stmt->fetch()['total'];

$stmt = $pdo->query('SELECT COUNT(*) as total FROM patrollers');
$totalPatrollers = $stmt->fetch()['total'];

// ---------- MEETINGS ----------
$meetings = $pdo->query('SELECT * FROM meetings WHERE date_time > NOW() ORDER BY date_time LIMIT 3')->fetchAll();

// ---------- ANNOUNCEMENTS ----------
$announcements = $pdo->query('SELECT * FROM announcements ORDER BY date DESC LIMIT 3')->fetchAll();

// ---------- COMMITTEE (static) ----------
$committee = [
    ['name' => 'Mr. Sibusiso', 'role' => 'Chairperson', 'email' => 'Sibusiso@cpf9.org', 'phone' => '+27 82 111 2222'],
    ['name' => 'Mr. Kwagile Rahube', 'role' => 'Secretary', 'email' => 'Kwagile@cpf9.org', 'phone' => '+27 71 555 6666'],
    ['name' => 'Ms. Tsholofelo Mosome', 'role' => 'Treasurer', 'email' => 'Tsholofelo@cpf9.org', 'phone' => '+27 82 777 8888'],
    ['name' => 'Unknown', 'role' => 'Patrol Coordinator', 'email' => 'unknown@cpf9.org', 'phone' => '+27 73 999 0000'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPF Sector 9 – Community Patrol Force</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- ======================== HEADER ======================== -->
<header class="forum-header">
    <div class="container">
        <div class="logo-area">
            <img src="images/cpf-logo.jpeg" alt="CPF Sector 9 Logo" class="logo-img" style="height:35px; width:auto;">
            <span class="logo-text">CPF <span>Sector 9</span></span>
        </div>
        <div class="header-actions">
            <a href="#join-form" class="btn btn-primary"><i class="fas fa-handshake"></i> Join Us</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= $_SESSION['user_role'] === 'admin' ? 'dashboard.php' : 'patroller_dashboard.php' ?>" class="btn btn-outline"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="logout.php" class="btn btn-outline"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="register.php" class="btn btn-outline"><i class="fas fa-user-plus"></i> Register</a>
                <a href="#" class="btn btn-outline" onclick="document.getElementById('loginModal').style.display='flex'"><i class="fas fa-sign-in-alt"></i> Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- ======================== HERO ======================== -->
<section class="forum-hero">
    <div class="container">
        <h1>Connecting Community, Safety &amp; Trust</h1>
        <p>Welcome to the CPF Sector 9 community hub. Ask questions, share tips, and stay informed.</p>
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search or ask a question..." id="globalSearch" onkeyup="searchSite(this.value)">
        </div>
    </div>
</section>

<!-- ======================== CATEGORIES ======================== -->
<section class="forum-categories">
    <div class="container">
        <div class="category-grid">
            <a href="#announcements" class="category-card">
                <div class="category-icon"><i class="fas fa-bullhorn"></i></div>
                <h3>Announcements</h3>
                <p>Official updates from the patrol force committee</p>
            </a>
            <a href="#meetings" class="category-card">
                <div class="category-icon"><i class="fas fa-calendar-check"></i></div>
                <h3>Events &amp; Meetings</h3>
                <p>Upcoming community gatherings and trainings</p>
            </a>
            <a href="#join-form" class="category-card">
                <div class="category-icon"><i class="fas fa-handshake"></i></div>
                <h3>Join the Force</h3>
                <p>Become a volunteer and make a difference</p>
            </a>
            <a href="#committee" class="category-card">
                <div class="category-icon"><i class="fas fa-users-cog"></i></div>
                <h3>Committee</h3>
                <p>Meet the leadership team</p>
            </a>
            <a href="#" class="category-card">
                <div class="category-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>About Us</h3>
                <p>Our mission, values, and history</p>
            </a>
        </div>
    </div>
</section>

<!-- ======================== RECENT ACTIVITY ======================== -->
<section class="recent-activity" id="announcements">
    <div class="container">
        <div class="activity-grid">
            <!-- LEFT: Announcements -->
            <div class="activity-col">
                <h2><i class="fas fa-bullhorn" style="color:var(--yellow);"></i> Latest News</h2>
                <?php if ($announcements): ?>
                    <?php foreach ($announcements as $ann): ?>
                        <div class="activity-item">
                            <div class="activity-title"><?= htmlspecialchars($ann['title']) ?></div>
                            <div class="activity-meta"><?= date('M d, Y', strtotime($ann['date'])) ?></div>
                            <div class="activity-excerpt"><?= nl2br(htmlspecialchars($ann['content'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-msg">No announcements yet. Check back soon.</p>
                <?php endif; ?>
            </div>

            <!-- RIGHT: Upcoming Meetings -->
            <div class="activity-col" id="meetings">
                <h2><i class="fas fa-calendar-alt" style="color:var(--yellow);"></i> Upcoming Events</h2>
                <?php if ($meetings): ?>
                    <?php foreach ($meetings as $m): ?>
                        <div class="activity-item">
                            <div class="activity-title"><?= htmlspecialchars($m['title']) ?></div>
                            <div class="activity-meta"><i class="fas fa-clock"></i> <?= date('M d, Y H:i', strtotime($m['date_time'])) ?></div>
                            <div class="activity-meta"><i class="fas fa-map-pin"></i> <?= htmlspecialchars($m['location']) ?></div>
                            <?php if ($m['description']): ?>
                                <div class="activity-excerpt"><?= nl2br(htmlspecialchars($m['description'])) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-msg">No upcoming events scheduled.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ======================== COMMITTEE ======================== -->
<section class="committee-section" id="committee">
    <div class="container">
        <h2>Our <span>Committee</span></h2>
        <div class="committee-grid">
            <?php foreach ($committee as $member): ?>
                <div class="committee-card">
                    <div class="avatar"><i class="fas fa-user"></i></div>
                    <h4><?= htmlspecialchars($member['name']) ?></h4>
                    <div class="role"><?= htmlspecialchars($member['role']) ?></div>
                    <div class="contact-info"><i class="fas fa-envelope"></i> <?= htmlspecialchars($member['email']) ?></div>
                    <div class="contact-info"><i class="fas fa-phone"></i> <?= htmlspecialchars($member['phone']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ======================== JOIN FORM ======================== -->
<section id="join-form" class="join-section">
    <div class="container">
        <div class="join-card">
            <h2>Become a <span>Patroller</span></h2>
            <p>Join our team and help keep Sector 9 safe.</p>
            <form id="publicApplyForm" action="api.php?action=applyPatroller" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" placeholder="e.g. Thabo Mokoena" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" placeholder="you@example.com" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone *</label>
                        <input type="tel" name="phone" placeholder="+27 82 123 4567" required>
                    </div>
                    <div class="form-group">
                        <label>Suburb / Area</label>
                        <input type="text" name="area" placeholder="e.g. Sunnyside">
                    </div>
                </div>
                <div class="form-group">
                    <label>Why do you want to join? *</label>
                    <textarea name="reason" rows="3" placeholder="Tell us about yourself..." required></textarea>
                </div>
                <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Submit Application</button>
                <div id="applyFeedback" style="margin-top:12px; font-weight:600; text-align:center;"></div>
            </form>
        </div>
    </div>
</section>

<!-- ======================== FOOTER ======================== -->
<footer class="forum-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-info">
                <img src="images/cpf-logo.jpeg" alt="CPF Sector 9" style="height:30px; margin-bottom:10px;">
                <p>&copy; <?= date('Y') ?> CPF Sector 9 – Community Patrol Force. All rights reserved.</p>
                <p><i class="fas fa-phone"></i> (012) 345-6789 &nbsp;|&nbsp; <i class="fas fa-envelope"></i> cpf@cpf9.org</p>
                <p><i class="fab fa-whatsapp"></i> <a href="https://wa.me/2772000000" target="_blank" style="color:var(--yellow);">Chat with us on WhatsApp</a></p>
            </div>
            <div class="footer-links">
                <a href="#announcements">News</a>
                <a href="#meetings">Events</a>
                <a href="#committee">Committee</a>
                <a href="#join-form">Join</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</footer>

<!-- ======================== LOGIN MODAL ======================== -->
<div id="loginModal" class="modal-overlay" style="display:none;">
    <div class="modal">
        <h3>Login to CPF Sector 9</h3>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="your@email.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="document.getElementById('loginModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn-submit">Login</button>
            </div>
        </form>
        <p style="margin-top:12px; font-size:0.85rem; color:var(--gray-600);">
            Default admin: admin@cpf9.org / admin123
        </p>
        <p style="font-size:0.85rem;">
            <a href="register.php" style="color:var(--blue-primary);">Don't have an account? Register</a>
        </p>
    </div>
</div>

<!-- ======================== JAVASCRIPT ======================== -->
<script>
    // Apply form submission via AJAX
    document.getElementById('publicApplyForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        const res = await fetch('api.php?action=applyPatroller', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        const feedback = document.getElementById('applyFeedback');
        if (result.success) {
            feedback.innerHTML = '<span style="color:#155724;">✅ Application submitted! We\'ll contact you soon.</span>';
            this.reset();
        } else {
            feedback.innerHTML = '<span style="color:#721c24;">❌ Error: ' + (result.error || 'Unknown error') + '</span>';
        }
    });

    // Search functionality – scroll to categories when typing
    function searchSite(value) {
        if (value.length > 2) {
            document.querySelector('.forum-categories').scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Close modal when clicking outside
    document.getElementById('loginModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
</script>

</body>
</html>