<?php
declare(strict_types=1);

session_start();

require __DIR__ . '/config/db.php';

// ── Bootstrap admin account ──────────────────────────────────────────────────
// Runs once: creates the admin user if it doesn't exist yet.
(static function (PDO $pdo): void {
    $email    = 'admin@mailflow.com';
    $password = 'Admin1234!';

    $exists = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $exists->execute([$email]);

    if (!$exists->fetch()) {
        $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, password, role, plan)
            VALUES ('Admin', 'MailFlow', ?, ?, 'admin', 'Free')
        ")->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
    } else {
        // Make sure the existing account has admin role
        $pdo->prepare("UPDATE users SET role = 'admin' WHERE email = ? LIMIT 1")
            ->execute([$email]);
    }
})($pdo);
// ─────────────────────────────────────────────────────────────────────────────

$pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('user','admin') NOT NULL DEFAULT 'user' AFTER password");
$pdo->exec("
    CREATE TABLE IF NOT EXISTS orders (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        campaign_id INT UNSIGNED DEFAULT NULL,
        amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        status ENUM('pending','paid','cancelled','completed') NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT fk_orders_campaign FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

function adminRedirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function setAdminFlash(string $type, string $message): void
{
    $_SESSION['admin_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function getAdminFlash(): ?array
{
    if (!isset($_SESSION['admin_flash']) || !is_array($_SESSION['admin_flash'])) {
        return null;
    }

    $flash = $_SESSION['admin_flash'];
    unset($_SESSION['admin_flash']);
    return $flash;
}

function clearCurrentSession(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function renderAdminLogin(?string $error = null): never
{
    $errorHtml = $error
        ? '<div class="login-error">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>'
        : '';
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MailFlow Admin — Login</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:Segoe UI,Arial,sans-serif;background:radial-gradient(circle at top left,rgba(79,124,255,.18),transparent 24%),linear-gradient(135deg,#0b1020,#10192d 55%,#0c1324);color:#eef3ff;min-height:100vh;display:flex;align-items:center;justify-content:center}
  .login-wrap{width:100%;max-width:420px;padding:20px}
  .login-brand{display:flex;align-items:center;gap:12px;font-weight:800;font-size:22px;margin-bottom:32px;justify-content:center}
  .login-brand-mark{width:46px;height:46px;border-radius:14px;background:linear-gradient(135deg,#4f7cff,#a78bfa);display:flex;align-items:center;justify-content:center;box-shadow:0 10px 22px rgba(79,124,255,.28);font-size:20px}
  .login-card{padding:36px;border-radius:24px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);box-shadow:0 24px 60px rgba(0,0,0,.4);backdrop-filter:blur(20px)}
  .login-title{font-size:26px;font-weight:800;margin-bottom:6px}
  .login-sub{color:#94a3b8;font-size:14px;margin-bottom:28px}
  .field{margin-bottom:18px}
  .field label{display:block;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;margin-bottom:8px}
  .field input{width:100%;padding:13px 16px;border-radius:14px;border:1px solid rgba(255,255,255,.1);background:rgba(255,255,255,.06);color:#eef3ff;font-size:14px;font-family:inherit;outline:none;transition:border-color .2s,box-shadow .2s}
  .field input:focus{border-color:#4f7cff;box-shadow:0 0 0 3px rgba(79,124,255,.18)}
  .login-btn{width:100%;padding:14px;border-radius:14px;border:none;background:linear-gradient(135deg,#4f7cff,#7c8fff);color:#fff;font-size:15px;font-weight:700;cursor:pointer;margin-top:6px;transition:opacity .2s,transform .2s}
  .login-btn:hover{opacity:.9;transform:translateY(-1px)}
  .login-error{padding:13px 15px;border-radius:14px;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.25);color:#fca5a5;font-size:13px;margin-bottom:20px}
  .back-link{display:block;text-align:center;margin-top:20px;font-size:13px;color:#94a3b8}
  .back-link a{color:#818cf8;font-weight:700;text-decoration:none}
  .back-link a:hover{color:#c7d2fe}
</style>
</head>
<body>
<div class="login-wrap">
  <div class="login-brand">
    <div class="login-brand-mark">✉</div>
    MailFlow
  </div>
  <div class="login-card">
    <div class="login-title">Admin Access</div>
    <div class="login-sub">Sign in with your admin account to continue.</div>
    {$errorHtml}
    <form method="post" action="admin.php">
      <input type="hidden" name="admin_action" value="admin_login">
      <div class="field">
        <label>Email Address</label>
        <input type="email" name="admin_email" placeholder="admin@example.com" required autofocus>
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="admin_password" placeholder="Your password" required>
      </div>
      <button class="login-btn" type="submit">Sign In to Dashboard</button>
    </form>
    <div class="back-link"><a href="index.php">← Back to MailFlow</a></div>
  </div>
</div>
</body>
</html>
HTML;
    exit;
}

function handleAdminLogin(PDO $pdo): void
{
    if (
        ($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' ||
        (trim((string) ($_POST['admin_action'] ?? ''))) !== 'admin_login'
    ) {
        return;
    }

    $email    = strtolower(trim((string) ($_POST['admin_email'] ?? '')));
    $password = (string) ($_POST['admin_password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        renderAdminLogin('Email and password are required.');
    }

    $stmt = $pdo->prepare('SELECT id, password, role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, (string) $user['password'])) {
        renderAdminLogin('Invalid email or password.');
    }

    if (($user['role'] ?? 'user') !== 'admin') {
        renderAdminLogin('This account does not have admin privileges.');
    }

    $_SESSION['user_id'] = (int) $user['id'];
    session_regenerate_id(true);
    header('Location: admin.php');
    exit;
}

function ensureAdminSession(PDO $pdo): array
{
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        renderAdminLogin();
    }

    $statement = $pdo->prepare('
        SELECT id, first_name, last_name, email, role
        FROM users
        WHERE id = ?
        LIMIT 1
    ');
    $statement->execute([$userId]);
    $user = $statement->fetch();

    if (!$user || ($user['role'] ?? 'user') !== 'admin') {
        clearCurrentSession();
        renderAdminLogin('Access denied. Admin accounts only.');
    }

    return $user;
}

function ensureAdminCsrfToken(): string
{
    if (empty($_SESSION['admin_csrf'])) {
        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['admin_csrf'];
}

function verifyAdminCsrf(): void
{
    $token = (string) ($_POST['csrf_token'] ?? '');
    $sessionToken = (string) ($_SESSION['admin_csrf'] ?? '');

    if ($token === '' || $sessionToken === '' || !hash_equals($sessionToken, $token)) {
        setAdminFlash('error', 'Invalid security token. Please try again.');
        adminRedirect('admin.php');
    }
}

function adminAllowedRole(string $role): bool
{
    return in_array($role, ['user', 'admin'], true);
}

function adminAllowedCampaignStatus(string $status): bool
{
    return in_array($status, ['draft', 'scheduled', 'sent'], true);
}

function adminAllowedOrderStatus(string $status): bool
{
    return in_array($status, ['pending', 'paid', 'cancelled', 'completed'], true);
}

function adminPositiveId(string $key): int
{
    $value = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);

    return $value ?: 0;
}

function handleAdminPost(PDO $pdo, array $adminUser): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    verifyAdminCsrf();

    $action = trim((string) ($_POST['admin_action'] ?? ''));

    switch ($action) {
        case 'logout':
            clearCurrentSession();
            adminRedirect('index.php');

        case 'delete_user':
            $userId = adminPositiveId('user_id');
            if ($userId <= 0) {
                setAdminFlash('error', 'Invalid user selected.');
                adminRedirect('admin.php#users');
            }
            if ($userId === (int) $adminUser['id']) {
                setAdminFlash('error', 'You cannot delete your own admin account.');
                adminRedirect('admin.php#users');
            }

            $deleteUser = $pdo->prepare('DELETE FROM users WHERE id = ? LIMIT 1');
            $deleteUser->execute([$userId]);
            setAdminFlash('success', 'User deleted successfully.');
            adminRedirect('admin.php#users');

        case 'change_user_role':
            $userId = adminPositiveId('user_id');
            $role = trim((string) ($_POST['role'] ?? ''));
            if ($userId <= 0 || !adminAllowedRole($role)) {
                setAdminFlash('error', 'Invalid role update request.');
                adminRedirect('admin.php#users');
            }
            if ($userId === (int) $adminUser['id'] && $role !== 'admin') {
                setAdminFlash('error', 'You cannot remove your own admin role.');
                adminRedirect('admin.php#users');
            }

            $updateRole = $pdo->prepare('UPDATE users SET role = ?, updated_at = NOW() WHERE id = ? LIMIT 1');
            $updateRole->execute([$role, $userId]);
            setAdminFlash('success', 'User role updated successfully.');
            adminRedirect('admin.php#users');

        case 'delete_campaign':
            $campaignId = adminPositiveId('campaign_id');
            if ($campaignId <= 0) {
                setAdminFlash('error', 'Invalid campaign selected.');
                adminRedirect('admin.php#campaigns');
            }

            $deleteCampaign = $pdo->prepare('DELETE FROM campaigns WHERE id = ? LIMIT 1');
            $deleteCampaign->execute([$campaignId]);
            setAdminFlash('success', 'Campaign deleted successfully.');
            adminRedirect('admin.php#campaigns');

        case 'update_campaign_status':
            $campaignId = adminPositiveId('campaign_id');
            $status = trim((string) ($_POST['status'] ?? ''));
            if ($campaignId <= 0 || !adminAllowedCampaignStatus($status)) {
                setAdminFlash('error', 'Invalid campaign update request.');
                adminRedirect('admin.php#campaigns');
            }

            $updateCampaign = $pdo->prepare('UPDATE campaigns SET status = ?, updated_at = NOW() WHERE id = ? LIMIT 1');
            $updateCampaign->execute([$status, $campaignId]);
            setAdminFlash('success', 'Campaign status updated successfully.');
            adminRedirect('admin.php#campaigns');

        case 'delete_contact':
            $contactId = adminPositiveId('contact_id');
            if ($contactId <= 0) {
                setAdminFlash('error', 'Invalid contact selected.');
                adminRedirect('admin.php#contacts');
            }

            $deleteContact = $pdo->prepare('DELETE FROM contacts WHERE id = ? LIMIT 1');
            $deleteContact->execute([$contactId]);
            setAdminFlash('success', 'Contact deleted successfully.');
            adminRedirect('admin.php#contacts');

        case 'update_order_status':
            $orderId = adminPositiveId('order_id');
            $status = trim((string) ($_POST['status'] ?? ''));
            if ($orderId <= 0 || !adminAllowedOrderStatus($status)) {
                setAdminFlash('error', 'Invalid order update request.');
                adminRedirect('admin.php#orders');
            }

            $updateOrder = $pdo->prepare('UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ? LIMIT 1');
            $updateOrder->execute([$status, $orderId]);
            setAdminFlash('success', 'Order status updated successfully.');
            adminRedirect('admin.php#orders');

        case 'delete_order':
            $orderId = adminPositiveId('order_id');
            if ($orderId <= 0) {
                setAdminFlash('error', 'Invalid order selected.');
                adminRedirect('admin.php#orders');
            }

            $deleteOrder = $pdo->prepare('DELETE FROM orders WHERE id = ? LIMIT 1');
            $deleteOrder->execute([$orderId]);
            setAdminFlash('success', 'Order deleted successfully.');
            adminRedirect('admin.php#orders');
    }
}

handleAdminLogin($pdo);
$adminUser = ensureAdminSession($pdo);
$csrfToken = ensureAdminCsrfToken();
handleAdminPost($pdo, $adminUser);
$flash = getAdminFlash();

$stats = [
    'users' => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'contacts' => (int) $pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn(),
    'campaigns' => (int) $pdo->query('SELECT COUNT(*) FROM campaigns')->fetchColumn(),
    'orders' => (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'emails_sent' => (int) $pdo->query('SELECT COALESCE(SUM(recipients_count), 0) FROM campaigns')->fetchColumn(),
];

$users = $pdo->query('
    SELECT id, first_name, last_name, email, role, plan, created_at
    FROM users
    ORDER BY id DESC
')->fetchAll();

$campaigns = $pdo->query('
    SELECT c.id, c.name, c.subject, c.status, c.recipients_count, c.created_at,
           u.email AS user_email
    FROM campaigns c
    INNER JOIN users u ON u.id = c.user_id
    ORDER BY c.id DESC
')->fetchAll();

$contacts = $pdo->query('
    SELECT c.id, c.email, c.company, c.status, c.created_at,
           u.email AS user_email
    FROM contacts c
    INNER JOIN users u ON u.id = c.user_id
    ORDER BY c.id DESC
')->fetchAll();

$orders = $pdo->query('
    SELECT o.id, o.amount, o.status, o.created_at,
           u.email AS user_email,
           c.name AS campaign_name
    FROM orders o
    INNER JOIN users u ON u.id = o.user_id
    LEFT JOIN campaigns c ON c.id = o.campaign_id
    ORDER BY o.id DESC
')->fetchAll();

function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MailFlow Admin Dashboard</title>
<style>
  :root{
    --bg:#0b1020;
    --bg-panel:#12192d;
    --bg-panel-soft:#192238;
    --text:#eef3ff;
    --muted:#94a3b8;
    --border:rgba(255,255,255,.08);
    --accent:#4f7cff;
    --accent-2:#34d399;
    --danger:#ef4444;
    --warning:#f59e0b;
    --radius:20px;
    --shadow:0 22px 50px rgba(0,0,0,.28);
  }
  *{box-sizing:border-box}
  html{scroll-behavior:smooth}
  body{margin:0;font-family:Segoe UI,Arial,sans-serif;background:radial-gradient(circle at top left,rgba(79,124,255,.18),transparent 24%),linear-gradient(135deg,#0b1020,#10192d 55%,#0c1324);color:var(--text)}
  a{text-decoration:none;color:inherit}
  button,select{font:inherit}
  .admin-shell{display:grid;grid-template-columns:280px 1fr;min-height:100vh}
  .admin-sidebar{padding:28px 22px;border-right:1px solid var(--border);background:rgba(10,15,28,.82);backdrop-filter:blur(20px)}
  .brand{display:flex;align-items:center;gap:12px;font-weight:800;font-size:22px;margin-bottom:28px}
  .brand-mark{width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,#4f7cff,#a78bfa);display:flex;align-items:center;justify-content:center;box-shadow:0 10px 22px rgba(79,124,255,.28)}
  .admin-user{padding:18px;border:1px solid var(--border);border-radius:18px;background:rgba(255,255,255,.03);margin-bottom:24px}
  .admin-user strong{display:block;font-size:16px}
  .admin-user span{display:block;margin-top:6px;color:var(--muted);font-size:13px;word-break:break-word}
  .admin-nav{display:grid;gap:8px}
  .admin-nav a{padding:12px 14px;border-radius:14px;color:#dbe7ff;background:transparent;transition:.2s}
  .admin-nav a:hover{background:rgba(79,124,255,.14)}
  .admin-side-actions{margin-top:24px;display:grid;gap:10px}
  .admin-side-actions a{padding:11px 14px;border-radius:14px;background:rgba(255,255,255,.04);border:1px solid var(--border);font-size:14px}
  .admin-main{padding:28px}
  .admin-topbar{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:24px}
  .admin-title h1{margin:0;font-size:34px;font-weight:800}
  .admin-title p{margin:8px 0 0;color:var(--muted)}
  .admin-actions{display:flex;gap:10px;flex-wrap:wrap}
  .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 16px;border-radius:14px;border:1px solid var(--border);background:rgba(255,255,255,.04);color:var(--text);cursor:pointer}
  .btn-primary{background:linear-gradient(135deg,#4f7cff,#7c8fff);border:none}
  .btn-danger{background:rgba(239,68,68,.12);border-color:rgba(239,68,68,.24);color:#fecaca}
  .stats-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:16px;margin-bottom:26px}
  .stat-card{padding:22px;border-radius:22px;background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.03));border:1px solid var(--border);box-shadow:var(--shadow)}
  .stat-card span{display:block;font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em}
  .stat-card strong{display:block;margin-top:14px;font-size:34px;font-weight:800}
  .admin-section{margin-bottom:24px;padding:22px;border-radius:24px;background:rgba(255,255,255,.04);border:1px solid var(--border);box-shadow:var(--shadow)}
  .section-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:18px}
  .section-head h2{margin:0;font-size:24px}
  .section-head p{margin:6px 0 0;color:var(--muted);font-size:14px}
  .flash{margin-bottom:20px;padding:14px 16px;border-radius:16px;font-size:14px}
  .flash.success{background:rgba(52,211,153,.12);border:1px solid rgba(52,211,153,.22);color:#bbf7d0}
  .flash.error{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.22);color:#fecaca}
  .table-wrap{overflow:auto}
  table{width:100%;border-collapse:collapse;min-width:760px}
  th,td{padding:14px 12px;border-bottom:1px solid var(--border);text-align:left;font-size:14px;vertical-align:top}
  th{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em}
  tr:last-child td{border-bottom:none}
  .badge{display:inline-flex;align-items:center;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:700}
  .badge.admin{background:rgba(79,124,255,.14);color:#c7d2fe}
  .badge.user{background:rgba(148,163,184,.14);color:#cbd5e1}
  .badge.status-draft,.badge.status-pending{background:rgba(245,158,11,.15);color:#fde68a}
  .badge.status-scheduled,.badge.status-completed{background:rgba(79,124,255,.15);color:#bfdbfe}
  .badge.status-sent,.badge.status-paid{background:rgba(52,211,153,.14);color:#bbf7d0}
  .badge.status-cancelled{background:rgba(239,68,68,.15);color:#fecaca}
  .inline-actions{display:flex;gap:8px;flex-wrap:wrap}
  .inline-form{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
  .inline-form select{padding:10px 12px;border-radius:12px;border:1px solid var(--border);background:var(--bg-panel-soft);color:var(--text)}
  .muted{color:var(--muted)}
  .empty{padding:18px;border:1px dashed var(--border);border-radius:16px;color:var(--muted);text-align:center}
  @media (max-width: 1200px){
    .stats-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
  }
  @media (max-width: 900px){
    .admin-shell{grid-template-columns:1fr}
    .admin-sidebar{border-right:none;border-bottom:1px solid var(--border)}
    .admin-main{padding:20px}
  }
  @media (max-width: 640px){
    .stats-grid{grid-template-columns:1fr}
    .admin-topbar{flex-direction:column}
  }
</style>
</head>
<body>
  <div class="admin-shell">
    <aside class="admin-sidebar">
      <div class="brand">
        <div class="brand-mark">MF</div>
        <div>MailFlow Admin</div>
      </div>

      <div class="admin-user">
        <strong><?php echo esc(trim(($adminUser['first_name'] ?? '') . ' ' . ($adminUser['last_name'] ?? '')) ?: 'Admin'); ?></strong>
        <span><?php echo esc((string) $adminUser['email']); ?></span>
        <span>Role: admin</span>
      </div>

      <nav class="admin-nav">
        <a href="#overview">Overview</a>
        <a href="#users">Users</a>
        <a href="#campaigns">Campaigns</a>
        <a href="#contacts">Contacts</a>
        <a href="#orders">Orders</a>
      </nav>

      <div class="admin-side-actions">
        <a href="index.php">Back to Website</a>
      </div>
    </aside>

    <main class="admin-main">
      <div class="admin-topbar" id="overview">
        <div class="admin-title">
          <h1>Admin Dashboard</h1>
          <p>Private control center for MailFlow users, campaigns, contacts, and orders.</p>
        </div>
        <div class="admin-actions">
          <a class="btn" href="index.php">Open App</a>
          <form method="post" action="api.php?action=logout" style="margin:0">
            <button class="btn btn-danger" type="submit">Sign Out</button>
          </form>
        </div>
      </div>

      <?php if ($flash): ?>
        <div class="flash <?php echo esc((string) $flash['type']); ?>"><?php echo esc((string) $flash['message']); ?></div>
      <?php endif; ?>

      <div class="stats-grid">
        <div class="stat-card"><span>Total Users</span><strong><?php echo number_format($stats['users']); ?></strong></div>
        <div class="stat-card"><span>Total Contacts</span><strong><?php echo number_format($stats['contacts']); ?></strong></div>
        <div class="stat-card"><span>Total Campaigns</span><strong><?php echo number_format($stats['campaigns']); ?></strong></div>
        <div class="stat-card"><span>Total Orders</span><strong><?php echo number_format($stats['orders']); ?></strong></div>
        <div class="stat-card"><span>Emails Sent</span><strong><?php echo number_format($stats['emails_sent']); ?></strong></div>
      </div>

      <section class="admin-section" id="users">
        <div class="section-head">
          <div>
            <h2>Manage Users</h2>
            <p>View accounts, remove users, and switch roles between user and admin.</p>
          </div>
        </div>
        <?php if ($users): ?>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Plan</th>
                  <th>Role</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $user): ?>
                  <tr>
                    <td><?php echo (int) $user['id']; ?></td>
                    <td><?php echo esc(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'User'); ?></td>
                    <td><?php echo esc((string) $user['email']); ?></td>
                    <td><?php echo esc((string) ($user['plan'] ?? 'Free')); ?></td>
                    <td><span class="badge <?php echo esc((string) $user['role']); ?>"><?php echo esc((string) $user['role']); ?></span></td>
                    <td><?php echo esc((string) $user['created_at']); ?></td>
                    <td>
                      <div class="inline-actions">
                        <form class="inline-form" method="post">
                          <input type="hidden" name="csrf_token" value="<?php echo esc($csrfToken); ?>">
                          <input type="hidden" name="admin_action" value="change_user_role">
                          <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                          <select name="role">
                            <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>user</option>
                            <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>admin</option>
                          </select>
                          <button class="btn" type="submit">Save Role</button>
                        </form>
                        <form method="post" onsubmit="return confirm('Delete this user?');">
                          <input type="hidden" name="csrf_token" value="<?php echo esc($csrfToken); ?>">
                          <input type="hidden" name="admin_action" value="delete_user">
                          <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                          <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="empty">No users found.</div>
        <?php endif; ?>
      </section>

      <section class="admin-section" id="campaigns">
        <div class="section-head">
          <div>
            <h2>Manage Campaigns</h2>
            <p>Review campaign ownership, update status, and remove campaigns when needed.</p>
          </div>
        </div>
        <?php if ($campaigns): ?>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Subject</th>
                  <th>User</th>
                  <th>Status</th>
                  <th>Recipients</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($campaigns as $campaign): ?>
                  <tr>
                    <td><?php echo (int) $campaign['id']; ?></td>
                    <td><?php echo esc((string) $campaign['name']); ?></td>
                    <td><?php echo esc((string) $campaign['subject']); ?></td>
                    <td><?php echo esc((string) $campaign['user_email']); ?></td>
                    <td><span class="badge status-<?php echo esc((string) $campaign['status']); ?>"><?php echo esc((string) $campaign['status']); ?></span></td>
                    <td><?php echo number_format((int) $campaign['recipients_count']); ?></td>
                    <td><?php echo esc((string) $campaign['created_at']); ?></td>
                    <td>
                      <div class="inline-actions">
                        <form class="inline-form" method="post">
                          <input type="hidden" name="csrf_token" value="<?php echo esc($csrfToken); ?>">
                          <input type="hidden" name="admin_action" value="update_campaign_status">
                          <input type="hidden" name="campaign_id" value="<?php echo (int) $campaign['id']; ?>">
                          <select name="status">
                            <?php foreach (['draft', 'scheduled', 'sent'] as $status): ?>
                              <option value="<?php echo esc($status); ?>" <?php echo ($campaign['status'] === $status) ? 'selected' : ''; ?>><?php echo esc($status); ?></option>
                            <?php endforeach; ?>
                          </select>
                          <button class="btn" type="submit">Save Status</button>
                        </form>
                        <form method="post" onsubmit="return confirm('Delete this campaign?');">
                          <input type="hidden" name="csrf_token" value="<?php echo esc($csrfToken); ?>">
                          <input type="hidden" name="admin_action" value="delete_campaign">
                          <input type="hidden" name="campaign_id" value="<?php echo (int) $campaign['id']; ?>">
                          <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="empty">No campaigns found.</div>
        <?php endif; ?>
      </section>

      <section class="admin-section" id="contacts">
        <div class="section-head">
          <div>
            <h2>Manage Contacts</h2>
            <p>Review stored contacts across all user accounts and remove invalid entries.</p>
          </div>
        </div>
        <?php if ($contacts): ?>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Email</th>
                  <th>Company</th>
                  <th>User</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($contacts as $contact): ?>
                  <tr>
                    <td><?php echo (int) $contact['id']; ?></td>
                    <td><?php echo esc((string) $contact['email']); ?></td>
                    <td><?php echo esc((string) ($contact['company'] ?: '-')); ?></td>
                    <td><?php echo esc((string) $contact['user_email']); ?></td>
                    <td><?php echo esc((string) $contact['status']); ?></td>
                    <td><?php echo esc((string) $contact['created_at']); ?></td>
                    <td>
                      <form method="post" onsubmit="return confirm('Delete this contact?');">
                        <input type="hidden" name="csrf_token" value="<?php echo esc($csrfToken); ?>">
                        <input type="hidden" name="admin_action" value="delete_contact">
                        <input type="hidden" name="contact_id" value="<?php echo (int) $contact['id']; ?>">
                        <button class="btn btn-danger" type="submit">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="empty">No contacts found.</div>
        <?php endif; ?>
      </section>

      <section class="admin-section" id="orders">
        <div class="section-head">
          <div>
            <h2>Manage Orders</h2>
            <p>Track plan purchases and update order status for billing follow-up.</p>
          </div>
        </div>
        <?php if ($orders): ?>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User</th>
                  <th>Campaign</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $order): ?>
                  <tr>
                    <td><?php echo (int) $order['id']; ?></td>
                    <td><?php echo esc((string) $order['user_email']); ?></td>
                    <td><?php echo esc((string) ($order['campaign_name'] ?: '-')); ?></td>
                    <td>MAD <?php echo number_format((float) $order['amount'], 2); ?></td>
                    <td><span class="badge status-<?php echo esc((string) $order['status']); ?>"><?php echo esc((string) $order['status']); ?></span></td>
                    <td><?php echo esc((string) $order['created_at']); ?></td>
                    <td>
                      <div class="inline-actions">
                        <form class="inline-form" method="post">
                          <input type="hidden" name="csrf_token" value="<?php echo esc($csrfToken); ?>">
                          <input type="hidden" name="admin_action" value="update_order_status">
                          <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
                          <select name="status">
                            <?php foreach (['pending', 'paid', 'cancelled', 'completed'] as $status): ?>
                              <option value="<?php echo esc($status); ?>" <?php echo ($order['status'] === $status) ? 'selected' : ''; ?>><?php echo esc($status); ?></option>
                            <?php endforeach; ?>
                          </select>
                          <button class="btn" type="submit">Save Status</button>
                        </form>
                        <form method="post" onsubmit="return confirm('Delete this order?');">
                          <input type="hidden" name="csrf_token" value="<?php echo esc($csrfToken); ?>">
                          <input type="hidden" name="admin_action" value="delete_order">
                          <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
                          <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="empty">No orders found yet. New paid plan checkouts will appear here.</div>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>
</html>
