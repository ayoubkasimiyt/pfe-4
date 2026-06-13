<?php
declare(strict_types=1);

require __DIR__ . '/config/db.php';

$email     = 'admin@mailflow.com';
$password  = 'Admin1234!';
$hash      = password_hash($password, PASSWORD_DEFAULT);

// Delete old record with same email if any, then insert fresh
$pdo->prepare('DELETE FROM users WHERE email = ?')->execute([$email]);

$pdo->prepare("
    INSERT INTO users (first_name, last_name, email, password, role, plan)
    VALUES (?, ?, ?, ?, 'admin', 'Free')
")->execute(['Admin', 'MailFlow', $email, $hash]);

echo '<h2 style="font-family:sans-serif;color:green">✅ Admin account created!</h2>';
echo '<p style="font-family:sans-serif">Email: <strong>' . htmlspecialchars($email) . '</strong></p>';
echo '<p style="font-family:sans-serif">Password: <strong>' . htmlspecialchars($password) . '</strong></p>';
echo '<br><a href="admin.php" style="font-family:sans-serif">→ Go to Admin Login</a>';
echo '<br><br><strong style="font-family:sans-serif;color:red">⚠️ Delete this file after logging in!</strong>';
