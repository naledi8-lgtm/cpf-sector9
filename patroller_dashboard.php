<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'patroller') {
    header('Location: index.php');
    exit;
}
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$stmt = $pdo->prepare('SELECT * FROM patrollers WHERE email = (SELECT email FROM users WHERE id = ?)');
$stmt->execute([$user_id]);
$patroller = $stmt->fetch();

if (!$patroller) {
    $patroller = ['name' => $user_name, 'email' => '', 'phone' => '', 'status' => 'unknown'];
}

$announcements = $pdo->query('SELECT * FROM announcements ORDER BY date DESC LIMIT 5')->fetchAll();
$meetings = $pdo->query('SELECT * FROM meetings WHERE date_time > NOW() ORDER BY date_time LIMIT 10')->fetchAll();

$otherPatrollers = $pdo->query('SELECT * FROM patrollers WHERE status = "active" AND email != "' . $patroller['email'] . '" LIMIT 20')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patroller Dashboard – CPF Sector 9</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .patroller-profile .label { font-weight: 600; color: var(--gray-700); width: 100px; display: inline-block; }
        .patroller-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 20px; }
        @media (max-width: 768px) { .patroller-grid { grid-template-columns: 1fr; } }
        .card-list { background: var(--white); border-radius: var(--radius); padding: 20px 24px; border: 1px solid var(--gray-200); box-shadow: var(--shadow-sm); margin-bottom: 24px; }
        .card-list h3 { font-size: 1.1rem; font-weight: 700; color: var(--blue-dark); margin-bottom: 12px; border-bottom: 2px solid var(--yellow); padding-bottom: 8px; }
        .card-list ul { list-style: none; padding: 0; }
        .card-list ul li { padding: 8px 0; border-bottom: 1px solid var(--gray-100); }
        .card-list ul li:last-child { border-bottom: none; }
        .card-list .empty { color: var(--gray-500); font-style: italic; }
    </style>
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-shield-halved"></i>
        <div><h1>CPF <span>Sector 9</span></h1><small>Patroller Portal</small></div>
    </div>
    <ul class="sidebar-nav">
        <li class="nav-label">Patroller</li>
        <li><a class="active" href="#"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="#profile"><i class="fas fa-user"></i> My Profile</a></li>
        <li><a href="#announcements"><i class="fas fa-bullhorn"></i> Announcements</a></li>
        <li><a href="#meetings"><i class="fas fa-calendar-alt"></i> Meetings</a></li>
        <li><a href="#patrollers"><i class="fas fa-user-shield"></i> Patrollers</a></li>
        <li class="nav-label" style="margin-top:12px;"><a href="logout.php" style="color:rgba(255,255,255,0.4);"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
    <div class="sidebar-footer"><i class="fas fa-shield" style="color:var(--yellow);opacity:0.4;"></i> CPF Sector 9</div>
</aside>

<div class="main">
    <header class="main-header">
        <div class="left">
            <button class="hamburger-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button>
            <span class="page-title">Patroller Dashboard</span>
        </div>
        <div class="right">
            <div class="admin-badge" style="background:var(--yellow);color:var(--blue-dark);"><i class="fas fa-user-shield"></i> <span><?= htmlspecialchars($user_name) ?></span></div>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </header>

    <div class="page-content">
        <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:24px;">
            <div>
                <h2 style="font-size:1.8rem; color:var(--blue-dark);">Welcome, <?= htmlspecialchars($user_name) ?> 👋</h2>
                <p style="color:var(--gray-600);">Status: <span class="badge-status <?= $patroller['status'] ?>"><?= ucfirst($patroller['status']) ?></span></p>
            </div>
            <div><span style="background:var(--blue-light); padding:6px 18px; border-radius:var(--radius-full); font-weight:600;"><i class="fas fa-calendar-day" style="color:var(--yellow);"></i> <?= date('l, F j, Y') ?></span></div>
        </div>

        <div class="patroller-grid">
            <div>
                <div class="card-list" id="announcements">
                    <h3><i class="fas fa-bullhorn" style="color:var(--yellow);"></i> Latest Announcements</h3>
                    <ul>
                        <?php if ($announcements): foreach ($announcements as $ann): ?>
                            <li><strong><?= htmlspecialchars($ann['title']) ?></strong><br><span style="font-size:0.85rem;color:var(--gray-600);"><?= date('M d, Y', strtotime($ann['date'])) ?></span><p style="margin-top:4px;font-size:0.9rem;"><?= nl2br(htmlspecialchars($ann['content'])) ?></p></li>
                        <?php endforeach; else: ?>
                            <li class="empty">No announcements yet.</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="card-list" id="meetings">
                    <h3><i class="fas fa-calendar-check" style="color:var(--yellow);"></i> Upcoming Meetings</h3>
                    <ul>
                        <?php if ($meetings): foreach ($meetings as $m): ?>
                            <li><strong><?= htmlspecialchars($m['title']) ?></strong><br><span style="font-size:0.85rem;color:var(--gray-600);"><i class="fas fa-calendar-alt"></i> <?= date('M d, Y H:i', strtotime($m['date_time'])) ?></span><br><span style="font-size:0.85rem;color:var(--gray-600);"><i class="fas fa-map-pin"></i> <?= htmlspecialchars($m['location']) ?></span><?php if ($m['description']): ?><p style="margin-top:4px;font-size:0.9rem;"><?= nl2br(htmlspecialchars($m['description'])) ?></p><?php endif; ?></li>
                        <?php endforeach; else: ?>
                            <li class="empty">No upcoming meetings.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div>
                <div class="card-list" id="profile">
                    <h3><i class="fas fa-user" style="color:var(--yellow);"></i> My Profile</h3>
                    <div style="margin-top:8px;">
                        <div><span class="label">Name:</span> <?= htmlspecialchars($patroller['name']) ?></div>
                        <div><span class="label">Email:</span> <?= htmlspecialchars($patroller['email']) ?></div>
                        <div><span class="label">Phone:</span> <?= htmlspecialchars($patroller['phone']) ?></div>
                        <div><span class="label">Status:</span> <span class="badge-status <?= $patroller['status'] ?>"><?= ucfirst($patroller['status']) ?></span></div>
                    </div>
                </div>

                <div class="card-list" id="patrollers">
                    <h3><i class="fas fa-users" style="color:var(--yellow);"></i> Active Patrollers</h3>
                    <ul>
                        <?php if ($otherPatrollers): foreach ($otherPatrollers as $p): ?>
                            <li><strong><?= htmlspecialchars($p['name']) ?></strong><br><span style="font-size:0.85rem;color:var(--gray-600);"><i class="fas fa-envelope"></i> <?= htmlspecialchars($p['email']) ?></span><br><span style="font-size:0.85rem;color:var(--gray-600);"><i class="fas fa-phone"></i> <?= htmlspecialchars($p['phone']) ?></span></li>
                        <?php endforeach; else: ?>
                            <li class="empty">No other active patrollers.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelector('.hamburger-toggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('open');
    });
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.querySelector('.hamburger-toggle');
        if (window.innerWidth <= 1024 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });
</script>
</body>
</html>