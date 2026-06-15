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
  <meta name="description" content="Create your free MailPro account and start growing your business with email marketing.">
  <title>Sign Up Free — MailPro</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
      --blue: #4f7cff; --blue-light: #6b93ff;
      --purple: #a78bfa; --green: #34d399;
      --orange: #fb923c; --pink: #f472b6;
      --bg: #050816;
      --border: rgba(255,255,255,0.08);
      --text: #eef2ff; --text-muted: #8892b0;
      --grad1: linear-gradient(135deg, #4f7cff, #a78bfa);
      --grad2: linear-gradient(135deg, #a78bfa, #f472b6);
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      overflow-x: hidden;
    }

    /* ─── SPLIT LAYOUT ─── */
    .auth-left {
      width: 44%;
      background: linear-gradient(160deg, rgba(167,139,250,.12) 0%, rgba(79,124,255,.08) 50%, rgba(5,8,22,1) 100%);
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
        radial-gradient(ellipse at 80% 10%, rgba(167,139,250,.2) 0%, transparent 55%),
        radial-gradient(ellipse at 10% 90%, rgba(79,124,255,.12) 0%, transparent 55%);
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

    .left-content { position: relative; z-index: 1; }
    .left-content h2 {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 34px; font-weight: 900;
      line-height: 1.2; margin-bottom: 16px;
      letter-spacing: -.02em;
    }
    .grad-text {
      background: var(--grad2);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .left-content > p {
      font-size: 15px; color: var(--text-muted);
      line-height: 1.75; margin-bottom: 32px;
    }

    .perks { display: flex; flex-direction: column; gap: 14px; }
    .perk {
      display: flex; align-items: flex-start; gap: 14px;
      padding: 16px 18px; border-radius: 14px;
      background: rgba(255,255,255,0.04);
      border: 1px solid var(--border);
      transition: all .3s;
    }
    .perk:hover { border-color: rgba(167,139,250,.3); transform: translateX(4px); }
    .perk-icon {
      width: 40px; height: 40px; border-radius: 11px;
      display: flex; align-items: center; justify-content: center;
      font-size: 17px; flex-shrink: 0; margin-top: 2px;
    }
    .perk-title { font-size: 14px; font-weight: 700; margin-bottom: 4px; }
    .perk-desc  { font-size: 12px; color: var(--text-muted); line-height: 1.5; }

    /* Floating deco */
    .left-float {
      position: absolute; pointer-events: none;
      animation: floatDeco 7s ease-in-out infinite;
    }
    .left-float-a { top: 20%; right: -10px; animation-delay: 1s; }
    .left-float-b { bottom: 22%; left: 40px; animation-delay: 4s; animation-duration: 10s; }
    @keyframes floatDeco {
      0%,100% { transform: translateY(0) rotate(5deg); }
      50%      { transform: translateY(-14px) rotate(-5deg); }
    }
    .deco-icon { font-size: 52px; opacity: .09; }

    .left-bottom { position: relative; z-index: 1; font-size: 13px; color: var(--text-muted); }

    /* ─── RIGHT PANEL ─── */
    .auth-right {
      flex: 1;
      display: flex; align-items: center; justify-content: center;
      padding: 48px 28px;
      position: relative; overflow-y: auto;
    }
    .auth-right::before {
      content: '';
      position: absolute;
      width: 500px; height: 500px;
      background: radial-gradient(circle, rgba(79,124,255,.07), transparent 70%);
      top: -100px; right: -100px;
      pointer-events: none;
    }

    .auth-card {
      width: 100%; max-width: 460px;
      position: relative; z-index: 1;
    }

    .auth-card-header { margin-bottom: 32px; }
    .auth-card-header h1 {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 28px; font-weight: 900;
      letter-spacing: -.02em; margin-bottom: 8px;
    }
    .auth-card-header p { font-size: 14px; color: var(--text-muted); }

    /* Free badge */
    .free-badge {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 6px 14px; border-radius: 50px;
      background: rgba(52,211,153,.1); border: 1px solid rgba(52,211,153,.2);
      color: var(--green); font-size: 12px; font-weight: 700;
      margin-bottom: 22px;
    }
    .free-badge span {
      width: 7px; height: 7px; border-radius: 50%;
      background: var(--green); animation: pulse 2s infinite;
    }
    @keyframes pulse { 0%,100%{opacity:1}50%{opacity:.4} }

    /* Message */
    .auth-msg {
      padding: 13px 16px; border-radius: 12px;
      font-size: 13px; margin-bottom: 20px;
      display: none; align-items: center; gap: 10px;
    }
    .auth-msg.error   { display:flex; background:rgba(248,113,113,.1); border:1px solid rgba(248,113,113,.2); color:#fca5a5; }
    .auth-msg.success { display:flex; background:rgba(52,211,153,.1);  border:1px solid rgba(52,211,153,.2);  color:#86efac; }

    /* Form */
    .form-group { margin-bottom: 18px; }
    .form-row   { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    label {
      display: block; font-size: 11px; font-weight: 700;
      color: var(--text-muted); text-transform: uppercase;
      letter-spacing: .08em; margin-bottom: 9px;
    }
    .input-wrap { position: relative; }
    .input-wrap .i-icon {
      position: absolute; left: 15px; top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted); font-size: 14px; pointer-events: none;
      transition: color .2s;
    }
    .input-wrap:focus-within .i-icon { color: var(--blue-light); }
    .input-wrap .eye-toggle {
      position: absolute; right: 15px; top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted); font-size: 14px;
      cursor: pointer; background: none; border: none; padding: 4px;
      transition: color .2s;
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
    input::placeholder { color: rgba(136,146,176,.5); }
    input.error-field  { border-color: rgba(248,113,113,.5); }

    /* Strength bar */
    .strength-bar-wrap { margin-top: 8px; }
    .strength-track {
      height: 3px; border-radius: 3px;
      background: rgba(255,255,255,.08); overflow: hidden;
    }
    .strength-fill {
      height: 100%; border-radius: 3px;
      transition: width .4s, background .4s;
      width: 0%;
    }
    .strength-text { font-size: 11px; color: var(--text-muted); margin-top: 5px; }

    /* Terms */
    .terms-row {
      display: flex; align-items: flex-start; gap: 10px;
      margin-bottom: 22px;
    }
    .terms-row input[type="checkbox"] {
      width: 16px; height: 16px; padding: 0; margin-top: 2px;
      accent-color: var(--blue); cursor: pointer; flex-shrink: 0;
    }
    .terms-row label {
      margin: 0; font-size: 13px; color: var(--text-muted);
      text-transform: none; letter-spacing: 0; font-weight: 400;
      line-height: 1.5;
    }
    .terms-row a { color: var(--blue-light); font-weight: 600; text-decoration: none; }
    .terms-row a:hover { text-decoration: underline; }

    /* Submit */
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
    .btn-submit:disabled { opacity:.65; cursor:not-allowed; transform:none; }

    /* Divider */
    .divider {
      display: flex; align-items: center; gap: 14px;
      margin: 22px 0; font-size: 12px; color: var(--text-muted);
    }
    .divider::before, .divider::after { content:''; flex:1; height:1px; background:var(--border); }

    .btn-social {
      display: flex; align-items: center; justify-content: center; gap: 11px;
      width: 100%; padding: 13px; border-radius: 12px;
      border: 1px solid var(--border); background: rgba(255,255,255,.04);
      color: var(--text); font-size: 14px; font-weight: 600;
      cursor: pointer; font-family: 'Inter', sans-serif; transition: all .25s;
    }
    .btn-social:hover { background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.15); }

    .auth-footer { margin-top: 24px; text-align: center; }
    .auth-footer p { font-size: 14px; color: var(--text-muted); }
    .auth-footer a { color: var(--blue-light); font-weight: 700; text-decoration: none; }
    .auth-footer a:hover { color: var(--text); }
    .back-home { margin-top: 14px; }
    .back-home a { font-size: 13px; color: var(--text-muted); }

    @media (max-width: 900px) {
      .auth-left { display: none; }
      .auth-right { padding: 40px 20px; }
      body { overflow: auto; }
    }
    @media (max-width: 480px) {
      .auth-right { padding: 24px 16px; padding-top: 36px; }
      .form-row { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="auth-left">
  <a href="landing.php" class="left-logo">
    <div class="left-logo-icon"><i class="fas fa-paper-plane"></i></div>
    MailPro
  </a>

  <div class="left-float left-float-a">
    <i class="fas fa-rocket deco-icon" style="color:var(--purple)"></i>
  </div>
  <div class="left-float left-float-b">
    <i class="fas fa-envelope deco-icon" style="color:var(--blue)"></i>
  </div>

  <div class="left-content">
    <h2>Start growing with <span class="grad-text">MailPro</span> today</h2>
    <p>Join 10,000+ businesses using MailPro to drive more revenue through email marketing.</p>

    <div class="perks">
      <div class="perk">
        <div class="perk-icon" style="background:rgba(79,124,255,.15);color:var(--blue)">
          <i class="fas fa-envelope-open-text"></i>
        </div>
        <div>
          <div class="perk-title">Drag-and-Drop Builder</div>
          <div class="perk-desc">Create stunning emails in minutes with 200+ professional templates.</div>
        </div>
      </div>
      <div class="perk">
        <div class="perk-icon" style="background:rgba(52,211,153,.15);color:var(--green)">
          <i class="fas fa-robot"></i>
        </div>
        <div>
          <div class="perk-title">Smart Automation</div>
          <div class="perk-desc">Set up automated workflows and grow your business on autopilot.</div>
        </div>
      </div>
      <div class="perk">
        <div class="perk-icon" style="background:rgba(167,139,250,.15);color:var(--purple)">
          <i class="fas fa-chart-line"></i>
        </div>
        <div>
          <div class="perk-title">Real-Time Analytics</div>
          <div class="perk-desc">Track every open, click, and conversion with detailed reports.</div>
        </div>
      </div>
    </div>
  </div>

  <div class="left-bottom">
    <i class="fas fa-check-circle" style="color:var(--green);margin-right:6px"></i>
    Free forever plan · No credit card required · Cancel anytime
  </div>
</div>

<!-- RIGHT PANEL -->
<div class="auth-right">
  <div class="auth-card">
    <div class="auth-card-header">
      <div class="free-badge"><span></span> Free Account — No Credit Card</div>
      <h1>Create your account</h1>
      <p>Start sending beautiful email campaigns in minutes.</p>
    </div>

    <div class="auth-msg" id="authMsg"></div>

    <form id="signupForm" onsubmit="handleSignup(event)" novalidate>
      <div class="form-row">
        <div class="form-group">
          <label>First Name</label>
          <div class="input-wrap">
            <i class="fas fa-user i-icon"></i>
            <input type="text" id="firstName" placeholder="John" autocomplete="given-name" required>
          </div>
        </div>
        <div class="form-group">
          <label>Last Name</label>
          <div class="input-wrap">
            <i class="fas fa-user i-icon"></i>
            <input type="text" id="lastName" placeholder="Doe" autocomplete="family-name" required>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>Email Address</label>
        <div class="input-wrap">
          <i class="fas fa-envelope i-icon"></i>
          <input type="email" id="signupEmail" placeholder="you@example.com" autocomplete="email" required>
        </div>
      </div>

      <div class="form-group">
        <label>Password</label>
        <div class="input-wrap">
          <i class="fas fa-lock i-icon"></i>
          <input type="password" id="signupPassword" class="has-eye" placeholder="Min. 6 characters" autocomplete="new-password" required>
          <button type="button" class="eye-toggle" id="eyeToggle1"><i class="fas fa-eye" id="eyeIcon1"></i></button>
        </div>
        <div class="strength-bar-wrap">
          <div class="strength-track"><div class="strength-fill" id="strengthFill"></div></div>
          <div class="strength-text" id="strengthText">Enter a password</div>
        </div>
      </div>

      <div class="form-group">
        <label>Confirm Password</label>
        <div class="input-wrap">
          <i class="fas fa-lock i-icon"></i>
          <input type="password" id="confirmPassword" class="has-eye" placeholder="Repeat your password" autocomplete="new-password" required>
          <button type="button" class="eye-toggle" id="eyeToggle2"><i class="fas fa-eye" id="eyeIcon2"></i></button>
        </div>
      </div>

      <div class="terms-row">
        <input type="checkbox" id="terms" required>
        <label for="terms">
          I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. I confirm I'm 18 or older.
        </label>
      </div>

      <button type="submit" class="btn-submit" id="signupBtn">
        <i class="fas fa-rocket"></i> Create Free Account
      </button>
    </form>

    <div class="divider">or continue with</div>

    <button class="btn-social" type="button">
      <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
      Sign up with Google
    </button>

    <div class="auth-footer">
      <p>Already have an account? <a href="login.php">Sign in →</a></p>
      <div class="back-home"><a href="landing.php"><i class="fas fa-arrow-left" style="margin-right:6px"></i>Back to home</a></div>
    </div>
  </div>
</div>

<script>
/* ─── Eye toggles ─── */
function makeEye(btnId, iconId, inputId) {
  document.getElementById(btnId).addEventListener('click', () => {
    const inp  = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    const show = inp.type === 'password';
    inp.type   = show ? 'text' : 'password';
    icon.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
  });
}
makeEye('eyeToggle1','eyeIcon1','signupPassword');
makeEye('eyeToggle2','eyeIcon2','confirmPassword');

/* ─── Password strength ─── */
const passInput   = document.getElementById('signupPassword');
const fillEl      = document.getElementById('strengthFill');
const textEl      = document.getElementById('strengthText');

const levels = [
  { score:0, label:'Enter a password',   color:'transparent', w:'0%'   },
  { score:1, label:'Too weak',           color:'#ef4444',     w:'20%'  },
  { score:2, label:'Weak',               color:'#fb923c',     w:'40%'  },
  { score:3, label:'Fair',               color:'#facc15',     w:'60%'  },
  { score:4, label:'Strong',             color:'#34d399',     w:'80%'  },
  { score:5, label:'Very strong 💪',     color:'#22d3ee',     w:'100%' },
];
passInput.addEventListener('input', () => {
  const v = passInput.value;
  let score = 0;
  if (v.length >= 6)  score++;
  if (v.length >= 10) score++;
  if (/[A-Z]/.test(v)) score++;
  if (/[0-9]/.test(v)) score++;
  if (/[^A-Za-z0-9]/.test(v)) score++;
  const lvl = levels[Math.min(score, 5)];
  fillEl.style.width      = lvl.w;
  fillEl.style.background = lvl.color;
  textEl.textContent      = lvl.label;
});

/* ─── Message ─── */
function showMsg(text, type) {
  const el = document.getElementById('authMsg');
  el.className = 'auth-msg ' + type;
  el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${text}`;
  el.scrollIntoView({ behavior:'smooth', block:'nearest' });
}

function clearErrors() {
  document.querySelectorAll('.error-field').forEach(e => e.classList.remove('error-field'));
}

/* ─── Signup handler ─── */
async function handleSignup(e) {
  e.preventDefault();
  clearErrors();

  const btn             = document.getElementById('signupBtn');
  const first_name      = document.getElementById('firstName').value.trim();
  const last_name       = document.getElementById('lastName').value.trim();
  const email           = document.getElementById('signupEmail').value.trim();
  const password        = document.getElementById('signupPassword').value;
  const confirmPassword = document.getElementById('confirmPassword').value;
  const termsChecked    = document.getElementById('terms').checked;

  /* ─ Validate ─ */
  if (!first_name) {
    document.getElementById('firstName').classList.add('error-field');
    showMsg('Please enter your first name.', 'error'); return;
  }
  if (!last_name) {
    document.getElementById('lastName').classList.add('error-field');
    showMsg('Please enter your last name.', 'error'); return;
  }
  if (!email || !/\S+@\S+\.\S+/.test(email)) {
    document.getElementById('signupEmail').classList.add('error-field');
    showMsg('Please enter a valid email address.', 'error'); return;
  }
  if (password.length < 6) {
    document.getElementById('signupPassword').classList.add('error-field');
    showMsg('Password must be at least 6 characters.', 'error'); return;
  }
  if (password !== confirmPassword) {
    document.getElementById('confirmPassword').classList.add('error-field');
    showMsg('Passwords do not match.', 'error'); return;
  }
  if (!termsChecked) {
    showMsg('Please accept the Terms of Service and Privacy Policy.', 'error'); return;
  }

  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating account...';
  btn.disabled  = true;

  try {
    const res  = await fetch('api.php?action=register', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ first_name, last_name, email, password })
    });
    const data = await res.json().catch(() => ({}));

    if (!res.ok || data.success === false) {
      throw new Error(data.message || 'Registration failed. Please try again.');
    }

    showMsg('Account created successfully! Redirecting to your dashboard...', 'success');
    setTimeout(() => { window.location.href = 'index.php'; }, 800);

  } catch (err) {
    showMsg(err.message, 'error');
    btn.innerHTML = '<i class="fas fa-rocket"></i> Create Free Account';
    btn.disabled  = false;
  }
}
</script>
</body>
</html>
