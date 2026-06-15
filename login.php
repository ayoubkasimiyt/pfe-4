<?php
session_start();
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Login to your MailPro account and manage your email marketing campaigns.">
  <title>Login — MailPro</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
      --blue: #4f7cff; --blue-light: #6b93ff;
      --purple: #a78bfa; --green: #34d399; --pink: #f472b6;
      --bg: #050816; --bg2: #0a0f2e;
      --card: rgba(13,18,58,0.9);
      --border: rgba(255,255,255,0.08);
      --border-blue: rgba(79,124,255,0.35);
      --text: #eef2ff; --text-muted: #8892b0;
      --grad1: linear-gradient(135deg, #4f7cff, #a78bfa);
      --shadow: 0 30px 90px rgba(0,0,0,0.6);
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      overflow: hidden;
    }

    /* ─── SPLIT LAYOUT ─── */
    .auth-left {
      width: 44%;
      background: linear-gradient(160deg, rgba(79,124,255,.15) 0%, rgba(167,139,250,.1) 50%, rgba(5,8,22,1) 100%);
      border-right: 1px solid var(--border);
      display: flex; flex-direction: column;
      justify-content: space-between;
      padding: 48px 52px;
      position: relative; overflow: hidden;
    }
    .auth-left::before {
      content: '';
      position: absolute; inset: 0;
      background:
        radial-gradient(ellipse at 10% 20%, rgba(79,124,255,.22) 0%, transparent 60%),
        radial-gradient(ellipse at 90% 80%, rgba(167,139,250,.15) 0%, transparent 60%);
      pointer-events: none;
    }

    .left-logo {
      display: flex; align-items: center; gap: 12px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 24px; font-weight: 800;
      color: var(--text); text-decoration: none;
      position: relative; z-index: 1;
    }
    .left-logo-icon {
      width: 46px; height: 46px; border-radius: 14px;
      background: var(--grad1);
      display: flex; align-items: center; justify-content: center;
      font-size: 20px; color: #fff;
      box-shadow: 0 8px 24px rgba(79,124,255,.5);
    }

    .left-content {
      position: relative; z-index: 1;
    }
    .left-content h2 {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 36px; font-weight: 900;
      line-height: 1.15; margin-bottom: 18px;
      letter-spacing: -.02em;
    }
    .grad-text {
      background: var(--grad1);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .left-content p {
      font-size: 15px; color: var(--text-muted);
      line-height: 1.75; margin-bottom: 36px;
    }

    .left-stats { display: flex; flex-direction: column; gap: 16px; }
    .left-stat {
      display: flex; align-items: center; gap: 14px;
      padding: 14px 18px; border-radius: 14px;
      background: rgba(255,255,255,0.04);
      border: 1px solid var(--border);
      backdrop-filter: blur(10px);
      transition: all .3s;
    }
    .left-stat:hover { border-color: rgba(79,124,255,.3); transform: translateX(4px); }
    .left-stat-icon {
      width: 42px; height: 42px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; flex-shrink: 0;
    }
    .left-stat-val { font-size: 18px; font-weight: 800; line-height: 1; }
    .left-stat-lbl { font-size: 12px; color: var(--text-muted); margin-top: 3px; }

    .left-bottom {
      position: relative; z-index: 1;
      font-size: 13px; color: var(--text-muted);
    }

    /* ─── FLOATING ELEMENTS ─── */
    .left-float {
      position: absolute;
      animation: leftFloat 8s ease-in-out infinite;
    }
    .left-float-1 { top: 25%; right: -20px; animation-delay: 0s; }
    .left-float-2 { top: 55%; left: 30px; animation-delay: 3s; }
    @keyframes leftFloat {
      0%,100% { transform: translateY(0) rotate(-3deg); }
      50%      { transform: translateY(-12px) rotate(3deg); }
    }
    .float-envelope {
      font-size: 48px; opacity: .12;
      color: var(--blue-light);
    }

    /* ─── RIGHT PANEL ─── */
    .auth-right {
      flex: 1;
      display: flex; align-items: center; justify-content: center;
      padding: 40px 28px;
      background: rgba(5,8,22,0.5);
      position: relative;
    }
    .auth-right::before {
      content: '';
      position: absolute;
      width: 500px; height: 500px;
      background: radial-gradient(circle, rgba(167,139,250,.08), transparent 70%);
      bottom: -100px; right: -100px;
      pointer-events: none;
    }

    .auth-card {
      width: 100%; max-width: 440px;
      position: relative; z-index: 1;
    }

    .auth-card-header { margin-bottom: 36px; }
    .auth-card-header h1 {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 30px; font-weight: 900;
      letter-spacing: -.02em; margin-bottom: 8px;
    }
    .auth-card-header p {
      font-size: 14px; color: var(--text-muted);
    }

    /* Message */
    .auth-msg {
      padding: 13px 16px; border-radius: 12px;
      font-size: 13px; margin-bottom: 22px;
      display: none; align-items: center; gap: 10px;
    }
    .auth-msg.error {
      display: flex;
      background: rgba(248,113,113,.1);
      border: 1px solid rgba(248,113,113,.2);
      color: #fca5a5;
    }
    .auth-msg.success {
      display: flex;
      background: rgba(52,211,153,.1);
      border: 1px solid rgba(52,211,153,.2);
      color: #86efac;
    }

    /* Form */
    .form-group { margin-bottom: 20px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    label {
      display: block; font-size: 11px; font-weight: 700;
      color: var(--text-muted); text-transform: uppercase;
      letter-spacing: .08em; margin-bottom: 9px;
    }
    .input-wrap { position: relative; }
    .input-wrap .i-icon {
      position: absolute; left: 15px; top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted); font-size: 14px;
      transition: color .2s;
      pointer-events: none;
    }
    .input-wrap:focus-within .i-icon { color: var(--blue-light); }

    .input-wrap .eye-toggle {
      position: absolute; right: 15px; top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted); font-size: 14px;
      cursor: pointer; transition: color .2s;
      background: none; border: none; padding: 4px;
    }
    .input-wrap .eye-toggle:hover { color: var(--text); }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%; background: rgba(255,255,255,0.04);
      border: 1px solid var(--border); border-radius: 12px;
      padding: 13px 14px 13px 44px;
      color: var(--text); font-size: 14px;
      font-family: 'Inter', sans-serif;
      transition: all .25s; outline: none;
    }
    input.has-eye { padding-right: 44px; }
    input:focus {
      border-color: var(--blue);
      box-shadow: 0 0 0 3px rgba(79,124,255,.15);
      background: rgba(255,255,255,.06);
    }
    input::placeholder { color: rgba(136,146,176,0.5); }
    input.error-field { border-color: rgba(248,113,113,0.5); }

    /* Forgot */
    .forgot-row {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 22px;
    }
    .remember-wrap { display: flex; align-items: center; gap: 8px; }
    .remember-wrap input[type="checkbox"] {
      width: 16px; height: 16px; padding: 0;
      accent-color: var(--blue); cursor: pointer;
    }
    .remember-wrap label { margin: 0; font-size: 13px; text-transform: none; letter-spacing: 0; color: var(--text-muted); }
    .forgot-link {
      font-size: 13px; color: var(--blue-light);
      text-decoration: none; font-weight: 600;
      transition: color .2s;
    }
    .forgot-link:hover { color: var(--text); }

    /* Submit btn */
    .btn-submit {
      display: flex; align-items: center; justify-content: center; gap: 9px;
      width: 100%; padding: 15px; border-radius: 12px;
      border: none; cursor: pointer; font-family: 'Inter', sans-serif;
      font-size: 15px; font-weight: 800;
      background: var(--grad1); color: #fff;
      box-shadow: 0 8px 28px rgba(79,124,255,.4);
      transition: all .3s;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 14px 40px rgba(79,124,255,.55); }
    .btn-submit:active { transform: translateY(0); }
    .btn-submit:disabled { opacity: .65; cursor: not-allowed; transform: none; }

    /* Divider */
    .divider {
      display: flex; align-items: center; gap: 14px;
      margin: 24px 0; font-size: 12px; color: var(--text-muted);
    }
    .divider::before, .divider::after {
      content: ''; flex: 1; height: 1px; background: var(--border);
    }

    /* Social btn */
    .btn-social {
      display: flex; align-items: center; justify-content: center; gap: 11px;
      width: 100%; padding: 13px; border-radius: 12px;
      border: 1px solid var(--border);
      background: rgba(255,255,255,0.04);
      color: var(--text); font-size: 14px; font-weight: 600;
      cursor: pointer; font-family: 'Inter', sans-serif;
      transition: all .25s;
    }
    .btn-social:hover { background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.15); }

    /* Footer links */
    .auth-footer { margin-top: 28px; text-align: center; }
    .auth-footer p { font-size: 14px; color: var(--text-muted); }
    .auth-footer a { color: var(--blue-light); font-weight: 700; text-decoration: none; transition: color .2s; }
    .auth-footer a:hover { color: var(--text); }
    .back-home { display: block; margin-top: 14px; }
    .back-home a { font-size: 13px; color: var(--text-muted); }
    .back-home a:hover { color: var(--text); }

    /* Responsive */
    @media (max-width: 900px) {
      .auth-left { display: none; }
      .auth-right { padding: 40px 20px; }
      body { overflow: auto; }
    }
    @media (max-width: 480px) {
      .auth-right { padding: 24px 16px; align-items: flex-start; padding-top: 40px; }
      .form-row { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<!-- ─── LEFT PANEL ─── -->
<div class="auth-left">
  <a href="landing.php" class="left-logo">
    <div class="left-logo-icon"><i class="fas fa-paper-plane"></i></div>
    MailPro
  </a>

  <!-- Floating decorations -->
  <div class="left-float left-float-1"><i class="fas fa-envelope float-envelope"></i></div>
  <div class="left-float left-float-2"><i class="fas fa-chart-line float-envelope" style="font-size:38px;color:var(--purple)"></i></div>

  <div class="left-content">
    <h2>Welcome back to <span class="grad-text">MailPro</span></h2>
    <p>Sign in to access your dashboard and continue growing your business with powerful email campaigns.</p>
    <div class="left-stats">
      <div class="left-stat">
        <div class="left-stat-icon" style="background:rgba(79,124,255,.15);color:var(--blue)">
          <i class="fas fa-envelope"></i>
        </div>
        <div>
          <div class="left-stat-val">1.4M+</div>
          <div class="left-stat-lbl">Emails delivered today</div>
        </div>
      </div>
      <div class="left-stat">
        <div class="left-stat-icon" style="background:rgba(52,211,153,.15);color:var(--green)">
          <i class="fas fa-chart-line"></i>
        </div>
        <div>
          <div class="left-stat-val">47.3%</div>
          <div class="left-stat-lbl">Average open rate</div>
        </div>
      </div>
      <div class="left-stat">
        <div class="left-stat-icon" style="background:rgba(167,139,250,.15);color:var(--purple)">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="left-stat-val">10K+</div>
          <div class="left-stat-lbl">Active businesses</div>
        </div>
      </div>
    </div>
  </div>

  <div class="left-bottom">
    <i class="fas fa-lock" style="margin-right:6px;color:var(--blue)"></i>
    256-bit SSL encryption · SOC2 Certified · GDPR Compliant
  </div>
</div>

<!-- ─── RIGHT PANEL ─── -->
<div class="auth-right">
  <div class="auth-card">
    <div class="auth-card-header">
      <h1>Sign in to your account</h1>
      <p>Enter your credentials to access your MailPro dashboard.</p>
    </div>

    <div class="auth-msg" id="authMsg"></div>

    <form id="loginForm" onsubmit="handleLogin(event)" novalidate>
      <div class="form-group">
        <label>Email Address</label>
        <div class="input-wrap">
          <i class="fas fa-envelope i-icon"></i>
          <input type="email" id="loginEmail" placeholder="you@example.com" autocomplete="email" required>
        </div>
      </div>

      <div class="form-group">
        <label>Password</label>
        <div class="input-wrap">
          <i class="fas fa-lock i-icon"></i>
          <input type="password" id="loginPassword" class="has-eye" placeholder="Your password" autocomplete="current-password" required>
          <button type="button" class="eye-toggle" id="eyeToggle" aria-label="Toggle password">
            <i class="fas fa-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <div class="forgot-row">
        <div class="remember-wrap">
          <input type="checkbox" id="rememberMe" name="remember">
          <label for="rememberMe">Remember me</label>
        </div>
        <a href="#" class="forgot-link">Forgot password?</a>
      </div>

      <button type="submit" class="btn-submit" id="loginBtn">
        <i class="fas fa-sign-in-alt"></i> Sign In
      </button>
    </form>

    <div class="divider">or continue with</div>

    <button class="btn-social" type="button" id="googleBtn">
      <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
      Continue with Google
    </button>

    <div class="auth-footer">
      <p>Don't have an account? <a href="signup.php">Create one free →</a></p>
      <div class="back-home"><a href="landing.php"><i class="fas fa-arrow-left" style="margin-right:6px"></i>Back to home</a></div>
    </div>
  </div>
</div>

<script>
/* Eye toggle */
const eyeToggle  = document.getElementById('eyeToggle');
const eyeIcon    = document.getElementById('eyeIcon');
const loginPass  = document.getElementById('loginPassword');
eyeToggle.addEventListener('click', () => {
  const isPass = loginPass.type === 'password';
  loginPass.type = isPass ? 'text' : 'password';
  eyeIcon.className = isPass ? 'fas fa-eye-slash' : 'fas fa-eye';
});

/* Google btn (placeholder) */
document.getElementById('googleBtn').addEventListener('click', () => {
  showMsg('Google login is not configured yet.', 'error');
});

function showMsg(text, type) {
  const el = document.getElementById('authMsg');
  el.className = 'auth-msg ' + type;
  el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${text}`;
  el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/* Login handler */
async function handleLogin(e) {
  e.preventDefault();
  const btn      = document.getElementById('loginBtn');
  const email    = document.getElementById('loginEmail').value.trim();
  const password = document.getElementById('loginPassword').value;

  // Basic validation
  document.getElementById('loginEmail').classList.remove('error-field');
  document.getElementById('loginPassword').classList.remove('error-field');

  if (!email || !/\S+@\S+\.\S+/.test(email)) {
    document.getElementById('loginEmail').classList.add('error-field');
    showMsg('Please enter a valid email address.', 'error');
    return;
  }
  if (!password) {
    document.getElementById('loginPassword').classList.add('error-field');
    showMsg('Please enter your password.', 'error');
    return;
  }

  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
  btn.disabled  = true;

  try {
    const res  = await fetch('api.php?action=login', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password })
    });
    const data = await res.json().catch(() => ({}));

    if (!res.ok || data.success === false) {
      throw new Error(data.message || 'Invalid email or password.');
    }

    showMsg('Login successful! Redirecting...', 'success');
    setTimeout(() => { window.location.href = 'index.php'; }, 700);

  } catch (err) {
    showMsg(err.message, 'error');
    btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';
    btn.disabled  = false;
  }
}
</script>
</body>
</html>
