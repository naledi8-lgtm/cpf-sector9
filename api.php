<?php
require_once 'db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {
        // ---------- USERS ----------
        case 'getUsers':
            echo json_encode($pdo->query('SELECT * FROM users')->fetchAll());
            break;
        case 'addUser':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, role, status) VALUES (?, ?, ?, ?)');
            $stmt->execute([$data['name'], $data['email'], $data['role'], $data['status']]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
        case 'updateUser':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('UPDATE users SET name=?, email=?, role=?, status=? WHERE id=?');
            $stmt->execute([$data['name'], $data['email'], $data['role'], $data['status'], $data['id']]);
            echo json_encode(['success' => true]);
            break;
        case 'deleteUser':
            $id = $_GET['id'];
            $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        // ---------- PATROLLERS ----------
        case 'getPatrollers':
            echo json_encode($pdo->query('SELECT * FROM patrollers')->fetchAll());
            break;
        case 'addPatroller':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('INSERT INTO patrollers (name, email, phone, status) VALUES (?, ?, ?, ?)');
            $stmt->execute([$data['name'], $data['email'], $data['phone'], $data['status']]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
        case 'updatePatroller':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('UPDATE patrollers SET name=?, email=?, phone=?, status=? WHERE id=?');
            $stmt->execute([$data['name'], $data['email'], $data['phone'], $data['status'], $data['id']]);
            echo json_encode(['success' => true]);
            break;
        case 'deletePatroller':
            $id = $_GET['id'];
            $pdo->prepare('DELETE FROM patrollers WHERE id=?')->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        // ---------- ADMINS ----------
        case 'getAdmins':
            echo json_encode($pdo->query('SELECT * FROM admins')->fetchAll());
            break;
        case 'addAdmin':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('INSERT INTO admins (name, email, role) VALUES (?, ?, ?)');
            $stmt->execute([$data['name'], $data['email'], $data['role']]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
        case 'updateAdmin':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('UPDATE admins SET name=?, email=?, role=? WHERE id=?');
            $stmt->execute([$data['name'], $data['email'], $data['role'], $data['id']]);
            echo json_encode(['success' => true]);
            break;
        case 'deleteAdmin':
            $id = $_GET['id'];
            $pdo->prepare('DELETE FROM admins WHERE id=?')->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        // ---------- ANNOUNCEMENTS ----------
        case 'getAnnouncements':
            echo json_encode($pdo->query('SELECT * FROM announcements ORDER BY date DESC')->fetchAll());
            break;
        case 'addAnnouncement':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('INSERT INTO announcements (title, content, date) VALUES (?, ?, ?)');
            $stmt->execute([$data['title'], $data['content'], date('Y-m-d')]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
        case 'updateAnnouncement':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('UPDATE announcements SET title=?, content=?, date=? WHERE id=?');
            $stmt->execute([$data['title'], $data['content'], $data['date'], $data['id']]);
            echo json_encode(['success' => true]);
            break;
        case 'deleteAnnouncement':
            $id = $_GET['id'];
            $pdo->prepare('DELETE FROM announcements WHERE id=?')->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        // ---------- MEETINGS ----------
        case 'getMeetings':
            echo json_encode($pdo->query('SELECT * FROM meetings ORDER BY date_time')->fetchAll());
            break;
        case 'addMeeting':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('INSERT INTO meetings (title, date_time, location, description) VALUES (?, ?, ?, ?)');
            $stmt->execute([$data['title'], $data['date_time'], $data['location'], $data['description']]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
        case 'updateMeeting':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('UPDATE meetings SET title=?, date_time=?, location=?, description=? WHERE id=?');
            $stmt->execute([$data['title'], $data['date_time'], $data['location'], $data['description'], $data['id']]);
            echo json_encode(['success' => true]);
            break;
        case 'deleteMeeting':
            $id = $_GET['id'];
            $pdo->prepare('DELETE FROM meetings WHERE id=?')->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        // ---------- PUBLIC APPLICATION ----------
        case 'applyPatroller':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('INSERT INTO patrollers (name, email, phone, status) VALUES (?, ?, ?, "pending")');
            $stmt->execute([$data['name'], $data['email'], $data['phone']]);
            $hashed = password_hash('temp123', PASSWORD_DEFAULT);
            $stmt2 = $pdo->prepare('INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, "patroller", "pending")');
            $stmt2->execute([$data['name'], $data['email'], $hashed]);
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>