<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="MailPro — The most powerful email marketing platform. Create campaigns, automate workflows, and grow your business with real-time analytics.">
  <meta name="keywords" content="email marketing, campaigns, newsletter, automation, analytics, SaaS, MailPro">
  <meta name="author" content="MailPro">
  <title>MailPro — Powerful Email Marketing for Business Growth</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    /* ─── RESET & ROOT ─── */
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
      --blue:        #4f7cff;
      --blue-light:  #6b93ff;
      --blue-dark:   #3360e0;
      --purple:      #a78bfa;
      --purple-dark: #7c3aed;
      --pink:        #f472b6;
      --cyan:        #22d3ee;
      --green:       #34d399;
      --orange:      #fb923c;
      --bg:          #050816;
      --bg2:         #0a0f2e;
      --bg3:         #0f1535;
      --card:        rgba(255,255,255,0.04);
      --card-hover:  rgba(255,255,255,0.07);
      --border:      rgba(255,255,255,0.08);
      --border-blue: rgba(79,124,255,0.35);
      --text:        #eef2ff;
      --text-muted:  #8892b0;
      --text-dim:    #4a5568;
      --grad1:       linear-gradient(135deg, #4f7cff 0%, #a78bfa 100%);
      --grad2:       linear-gradient(135deg, #a78bfa 0%, #f472b6 100%);
      --grad3:       linear-gradient(135deg, #22d3ee 0%, #4f7cff 100%);
      --grad4:       linear-gradient(135deg, #34d399 0%, #22d3ee 100%);
      --glow-blue:   0 0 60px rgba(79,124,255,0.25);
      --glow-purple: 0 0 60px rgba(167,139,250,0.2);
      --shadow-lg:   0 25px 80px rgba(0,0,0,0.6);
      --shadow-card: 0 8px 32px rgba(0,0,0,0.4);
      --radius-sm:   10px;
      --radius:      16px;
      --radius-lg:   24px;
      --radius-xl:   32px;
    }

    html { scroll-behavior: smooth; font-size: 16px; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      overflow-x: hidden;
      line-height: 1.6;
    }

    /* ─── SCROLLBAR ─── */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: var(--bg); }
    ::-webkit-scrollbar-thumb { background: var(--blue); border-radius: 3px; }

    /* ─── ANIMATED BG CANVAS ─── */
    #bgCanvas {
      position: fixed; inset: 0; z-index: 0;
      pointer-events: none; opacity: 0.6;
    }

    /* ─── GRADIENT ORBS ─── */
    .orb {
      position: fixed; border-radius: 50%;
      filter: blur(100px); pointer-events: none;
      z-index: 0; will-change: transform;
    }
    .orb-1 {
      width: 700px; height: 700px;
      background: radial-gradient(circle, rgba(79,124,255,0.18) 0%, transparent 70%);
      top: -200px; left: -200px;
      animation: orbFloat1 14s ease-in-out infinite alternate;
    }
    .orb-2 {
      width: 600px; height: 600px;
      background: radial-gradient(circle, rgba(167,139,250,0.15) 0%, transparent 70%);
      top: 30%; right: -150px;
      animation: orbFloat2 18s ease-in-out infinite alternate;
    }
    .orb-3 {
      width: 500px; height: 500px;
      background: radial-gradient(circle, rgba(34,211,238,0.1) 0%, transparent 70%);
      bottom: -100px; left: 30%;
      animation: orbFloat3 22s ease-in-out infinite alternate;
    }
    @keyframes orbFloat1 {
      from { transform: translate(0,0) scale(1); }
      to   { transform: translate(60px,80px) scale(1.2); }
    }
    @keyframes orbFloat2 {
      from { transform: translate(0,0) scale(1); }
      to   { transform: translate(-40px,60px) scale(1.15); }
    }
    @keyframes orbFloat3 {
      from { transform: translate(0,0) scale(1); }
      to   { transform: translate(40px,-50px) scale(1.1); }
    }

    /* ─── PARTICLES ─── */
    .particle {
      position: fixed;
      border-radius: 50%;
      pointer-events: none;
      z-index: 0;
      animation: particleDrift linear infinite;
      opacity: 0;
    }
    @keyframes particleDrift {
      0%   { transform: translateY(110vh) translateX(0) rotate(0deg); opacity: 0; }
      5%   { opacity: 1; }
      90%  { opacity: 0.7; }
      100% { transform: translateY(-10vh) translateX(var(--drift)) rotate(360deg); opacity: 0; }
    }

    /* ─── FLOATING ICONS ─── */
    .float-icons { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
    .float-icon {
      position: absolute;
      font-size: 18px;
      color: rgba(79,124,255,0.12);
      animation: floatRise linear infinite;
      will-change: transform;
    }
    @keyframes floatRise {
      0%   { transform: translateY(110vh) rotate(0deg);   opacity: 0; }
      8%   { opacity: 1; }
      92%  { opacity: 0.5; }
      100% { transform: translateY(-15vh) rotate(360deg); opacity: 0; }
    }

    /* ─── UTILITY ─── */
    .container { max-width: 1240px; margin: 0 auto; padding: 0 28px; position: relative; z-index: 2; }
    .section { padding: 110px 0; position: relative; z-index: 2; }
    .text-center { text-align: center; }

    .badge {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 6px 16px; border-radius: 50px;
      font-size: 11px; font-weight: 700; letter-spacing: .12em;
      text-transform: uppercase;
    }
    .badge-blue {
      background: rgba(79,124,255,0.12);
      border: 1px solid rgba(79,124,255,0.25);
      color: #7ba3ff;
    }

    .section-tag {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 7px 18px; border-radius: 50px;
      font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase;
      background: rgba(79,124,255,0.1); border: 1px solid rgba(79,124,255,0.2);
      color: var(--blue-light); margin-bottom: 18px;
    }
    .section-tag i { font-size: 10px; }

    h2.section-title {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: clamp(34px, 4.5vw, 56px);
      font-weight: 800; line-height: 1.1;
      margin-bottom: 18px; letter-spacing: -.02em;
    }
    .section-desc {
      font-size: 17px; color: var(--text-muted);
      max-width: 580px; margin: 0 auto 64px; line-height: 1.8;
    }

    .grad-text {
      background: var(--grad1);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .grad-text-2 {
      background: var(--grad2);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    /* ─── BUTTONS ─── */
    .btn {
      display: inline-flex; align-items: center; gap: 9px;
      padding: 14px 30px; border-radius: 50px;
      font-weight: 700; font-size: 15px; cursor: pointer;
      transition: all .3s cubic-bezier(.4,0,.2,1);
      border: none; font-family: 'Inter', sans-serif;
      text-decoration: none; position: relative; overflow: hidden;
    }
    .btn::after {
      content: '';
      position: absolute; inset: 0;
      background: rgba(255,255,255,0);
      transition: background .3s;
    }
    .btn:hover::after { background: rgba(255,255,255,0.08); }

    .btn-primary {
      background: var(--grad1); color: #fff;
      box-shadow: 0 8px 32px rgba(79,124,255,0.45), 0 2px 8px rgba(0,0,0,0.3);
    }
    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 16px 48px rgba(79,124,255,0.6), 0 4px 16px rgba(0,0,0,0.4);
    }
    .btn-primary:active { transform: translateY(-1px); }

    .btn-ghost {
      background: rgba(255,255,255,0.06);
      border: 1px solid var(--border);
      color: var(--text);
      backdrop-filter: blur(10px);
    }
    .btn-ghost:hover {
      background: rgba(255,255,255,0.1);
      border-color: rgba(79,124,255,0.4);
      transform: translateY(-2px);
    }

    .btn-lg { padding: 17px 38px; font-size: 16px; border-radius: 50px; }
    .btn-icon {
      width: 44px; height: 44px; padding: 0;
      border-radius: 50%; justify-content: center;
    }

    /* ─── NAVBAR ─── */
    #navbar {
      position: fixed; top: 0; left: 0; right: 0;
      z-index: 1000; padding: 18px 0;
      transition: all .4s cubic-bezier(.4,0,.2,1);
    }
    #navbar.scrolled {
      background: rgba(5,8,22,0.88);
      backdrop-filter: blur(28px) saturate(180%);
      border-bottom: 1px solid var(--border);
      padding: 12px 0;
      box-shadow: 0 4px 32px rgba(0,0,0,0.4);
    }
    .nav-inner {
      display: flex; align-items: center;
      justify-content: space-between; gap: 24px;
    }
    .nav-logo {
      display: flex; align-items: center; gap: 11px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 22px; font-weight: 800;
      color: var(--text); text-decoration: none;
      letter-spacing: -.02em;
    }
    .nav-logo-icon {
      width: 42px; height: 42px; border-radius: 13px;
      background: var(--grad1);
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; color: #fff;
      box-shadow: 0 6px 20px rgba(79,124,255,0.45);
      transition: transform .3s, box-shadow .3s;
    }
    .nav-logo:hover .nav-logo-icon {
      transform: rotate(-8deg) scale(1.05);
      box-shadow: 0 10px 30px rgba(79,124,255,0.6);
    }

    .nav-links {
      display: flex; align-items: center;
      gap: 2px; list-style: none;
    }
    .nav-links a {
      padding: 8px 16px; border-radius: var(--radius-sm);
      color: var(--text-muted); font-size: 14px; font-weight: 500;
      text-decoration: none; transition: all .2s;
      position: relative;
    }
    .nav-links a::after {
      content: ''; position: absolute;
      bottom: 4px; left: 50%; transform: translateX(-50%);
      width: 0; height: 2px; border-radius: 1px;
      background: var(--grad1); transition: width .3s;
    }
    .nav-links a:hover { color: var(--text); }
    .nav-links a:hover::after { width: 20px; }

    .nav-actions { display: flex; align-items: center; gap: 10px; }
    .btn-nav-login {
      padding: 9px 20px; border-radius: var(--radius-sm);
      color: var(--text-muted); font-size: 14px; font-weight: 500;
      background: transparent; border: 1px solid var(--border);
      cursor: pointer; text-decoration: none; transition: all .2s;
      font-family: 'Inter', sans-serif;
    }
    .btn-nav-login:hover { color: var(--text); border-color: var(--blue); }
    .btn-nav-signup {
      padding: 9px 22px; border-radius: var(--radius-sm);
      color: #fff; font-size: 14px; font-weight: 700;
      background: var(--grad1); border: none;
      box-shadow: 0 4px 18px rgba(79,124,255,0.4);
      cursor: pointer; text-decoration: none; transition: all .2s;
      font-family: 'Inter', sans-serif;
    }
    .btn-nav-signup:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(79,124,255,0.55); }

    /* Hamburger */
    .hamburger {
      display: none; flex-direction: column; gap: 5px;
      cursor: pointer; padding: 8px; background: none; border: none;
    }
    .hamburger span {
      display: block; width: 24px; height: 2px;
      background: var(--text); border-radius: 2px; transition: all .3s;
    }
    .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
    .hamburger.open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
    .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

    /* Mobile Menu */
    .mobile-menu {
      display: none; position: fixed;
      top: 70px; left: 0; right: 0;
      background: rgba(5,8,22,0.97);
      backdrop-filter: blur(30px);
      border-bottom: 1px solid var(--border);
      padding: 24px 28px 28px;
      z-index: 998; flex-direction: column; gap: 6px;
    }
    .mobile-menu.open { display: flex; }
    .mobile-menu a {
      padding: 13px 16px; border-radius: var(--radius-sm);
      color: var(--text-muted); font-size: 15px; font-weight: 500;
      text-decoration: none; transition: all .2s;
    }
    .mobile-menu a:hover { color: var(--text); background: var(--card); }
    .mobile-menu-divider { height: 1px; background: var(--border); margin: 8px 0; }

    /* ─── HERO ─── */
    .hero {
      min-height: 100vh;
      display: flex; align-items: center;
      padding: 130px 0 90px;
      position: relative; z-index: 2;
    }
    .hero-inner {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px; align-items: center;
    }

    /* Hero content */
    .hero-content {}
    .hero-badge-wrap { margin-bottom: 28px; }
    .hero-badge {
      display: inline-flex; align-items: center; gap: 10px;
      padding: 9px 20px; border-radius: 50px;
      background: rgba(79,124,255,0.1);
      border: 1px solid rgba(79,124,255,0.22);
      font-size: 13px; color: #9db4ff; font-weight: 600;
    }
    .hero-badge-dot {
      width: 8px; height: 8px; border-radius: 50%;
      background: var(--blue);
      box-shadow: 0 0 8px var(--blue);
      animation: pulse 2s ease-in-out infinite;
    }
    @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.85)} }

    .hero h1 {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: clamp(42px, 5.5vw, 76px);
      font-weight: 900; line-height: 1.04;
      letter-spacing: -.03em; margin-bottom: 24px;
    }
    .hero p {
      font-size: 18px; color: var(--text-muted);
      line-height: 1.8; margin-bottom: 40px; max-width: 500px;
    }
    .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 48px; }

    /* Trust bar */
    .hero-trust {
      display: flex; align-items: center; gap: 16px;
      padding-top: 28px; border-top: 1px solid var(--border);
    }
    .trust-avatars { display: flex; }
    .trust-avatar {
      width: 36px; height: 36px; border-radius: 50%;
      border: 2.5px solid var(--bg);
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; margin-left: -10px;
    }
    .trust-avatar:first-child { margin-left: 0; }
    .trust-text { font-size: 13px; color: var(--text-muted); }
    .trust-text strong { color: var(--text); font-weight: 700; }

    /* Hero stars */
    .hero-stars { display: flex; align-items: center; gap: 12px; margin-left: 16px; }
    .stars { display: flex; gap: 2px; }
    .stars i { font-size: 12px; color: #fbbf24; }
    .stars-text { font-size: 12px; color: var(--text-muted); }

    /* ─── HERO VISUAL ─── */
    .hero-visual {
      position: relative;
      display: flex; align-items: center; justify-content: center;
    }

    /* Main dashboard mockup */
    .hero-dashboard {
      width: 100%;
      background: linear-gradient(160deg, rgba(13,18,58,0.95) 0%, rgba(5,8,22,0.9) 100%);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 26px;
      box-shadow: var(--shadow-lg), var(--glow-blue);
      animation: heroFloat 8s ease-in-out infinite;
      backdrop-filter: blur(20px);
      position: relative; z-index: 2;
    }
    @keyframes heroFloat {
      0%,100% { transform: translateY(0) rotate(0deg); }
      33%      { transform: translateY(-14px) rotate(.3deg); }
      66%      { transform: translateY(-6px) rotate(-.2deg); }
    }

    .db-title-bar {
      display: flex; align-items: center; gap: 7px; margin-bottom: 22px;
    }
    .db-dot { width: 12px; height: 12px; border-radius: 50%; }
    .db-title { font-size: 13px; color: var(--text-muted); margin-left: 8px; font-weight: 600; letter-spacing: .03em; }

    .db-stats {
      display: grid; grid-template-columns: repeat(2,1fr); gap: 12px; margin-bottom: 20px;
    }
    .db-stat {
      background: rgba(255,255,255,0.04);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm); padding: 16px;
      transition: border-color .3s;
    }
    .db-stat:hover { border-color: rgba(79,124,255,0.3); }
    .db-stat-val {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 24px; font-weight: 800;
      background: var(--grad1);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
      background-clip: text;
      line-height: 1;
    }
    .db-stat-lbl {
      font-size: 10px; color: var(--text-muted);
      margin-top: 5px; text-transform: uppercase; letter-spacing: .08em;
    }
    .db-stat-change {
      display: inline-flex; align-items: center; gap: 4px;
      font-size: 10px; color: var(--green); margin-top: 4px;
    }

    /* Chart area */
    .db-chart {
      background: rgba(255,255,255,0.03);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      padding: 16px; margin-bottom: 16px;
    }
    .db-chart-head {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 14px;
    }
    .db-chart-title { font-size: 12px; color: var(--text-muted); font-weight: 600; }
    .db-chart-badge {
      padding: 3px 10px; border-radius: 20px; font-size: 10px;
      background: rgba(79,124,255,0.12); color: var(--blue-light);
      border: 1px solid rgba(79,124,255,0.2); font-weight: 700;
    }

    .chart-bars {
      display: flex; align-items: flex-end; gap: 6px; height: 72px;
    }
    .chart-bar {
      flex: 1; border-radius: 5px 5px 0 0;
      background: var(--grad1); opacity: .75;
      animation: barGrow .9s cubic-bezier(.4,0,.2,1) both;
      transform-origin: bottom;
    }
    @keyframes barGrow { from{transform:scaleY(0)} to{transform:scaleY(1)} }

    /* Campaign row */
    .db-campaign {
      display: flex; align-items: center; gap: 12px;
      padding: 12px 14px; border-radius: var(--radius-sm);
      background: rgba(79,124,255,0.07);
      border: 1px solid rgba(79,124,255,0.15);
      transition: all .3s;
      cursor: default;
    }
    .db-campaign:hover { background: rgba(79,124,255,0.12); transform: translateX(3px); }
    .db-camp-icon {
      width: 38px; height: 38px; border-radius: 10px;
      background: var(--grad1);
      display: flex; align-items: center; justify-content: center;
      font-size: 15px; color: #fff; flex-shrink: 0;
    }
    .db-camp-name { font-size: 13px; font-weight: 700; }
    .db-camp-sub  { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
    .db-camp-live {
      margin-left: auto; padding: 4px 12px; border-radius: 20px;
      background: rgba(52,211,153,0.12); color: var(--green);
      font-size: 10px; font-weight: 800; border: 1px solid rgba(52,211,153,0.2);
      letter-spacing: .05em;
    }

    /* Floating mini cards */
    .hero-mini-card {
      position: absolute;
      background: rgba(13,18,58,0.92);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 14px 18px;
      backdrop-filter: blur(20px);
      box-shadow: 0 12px 40px rgba(0,0,0,0.5);
      display: flex; align-items: center; gap: 12px;
      font-size: 13px; font-weight: 600;
      z-index: 3;
      transition: transform .3s;
    }
    .mini-card-1 {
      top: -22px; right: -18px;
      animation: miniFloat1 6s ease-in-out infinite;
    }
    .mini-card-2 {
      bottom: 10px; left: -28px;
      animation: miniFloat2 8s ease-in-out infinite;
    }
    .mini-card-3 {
      top: 38%; right: -36px;
      animation: miniFloat1 10s ease-in-out infinite reverse;
    }
    @keyframes miniFloat1 {
      0%,100% { transform: translateY(0) rotate(-2deg); }
      50%      { transform: translateY(-10px) rotate(2deg); }
    }
    @keyframes miniFloat2 {
      0%,100% { transform: translateY(0) rotate(1deg); }
      50%      { transform: translateY(-8px) rotate(-2deg); }
    }
    .mini-icon {
      width: 36px; height: 36px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; flex-shrink: 0;
    }
    .mini-label { font-size: 11px; color: var(--text-muted); margin-bottom: 2px; }
    .mini-val   { font-size: 14px; font-weight: 800; }



    /* ─── FOOTER ─── */
    footer {
      border-top: 1px solid var(--border);
      padding: 64px 0 32px;
      background: rgba(5,8,22,0.7);
      position: relative; z-index: 2;
    }
    .footer-grid {
      display: grid; grid-template-columns: 2.2fr 1fr 1fr 1fr;
      gap: 48px; margin-bottom: 52px;
    }
    .footer-brand-desc {
      font-size: 14px; color: var(--text-muted);
      margin-top: 14px; line-height: 1.75; max-width: 280px;
    }
    .footer-socials { display: flex; gap: 10px; margin-top: 22px; }
    .footer-social {
      width: 40px; height: 40px; border-radius: var(--radius-sm);
      background: var(--card); border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      color: var(--text-muted); font-size: 15px;
      text-decoration: none; transition: all .25s;
    }
    .footer-social:hover {
      background: rgba(79,124,255,0.15);
      color: var(--blue-light);
      border-color: rgba(79,124,255,0.3);
      transform: translateY(-3px);
    }
    .footer-col h4 {
      font-size: 12px; font-weight: 800; letter-spacing: .1em;
      text-transform: uppercase; color: var(--text); margin-bottom: 18px;
    }
    .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 11px; }
    .footer-col ul a {
      font-size: 14px; color: var(--text-muted);
      text-decoration: none; transition: color .2s;
    }
    .footer-col ul a:hover { color: var(--text); }
    .footer-bottom {
      border-top: 1px solid var(--border); padding-top: 26px;
      display: flex; justify-content: space-between; align-items: center;
      font-size: 13px; color: var(--text-muted); flex-wrap: wrap; gap: 12px;
    }
    .footer-bottom-links { display: flex; gap: 20px; }
    .footer-bottom-links a { color: var(--text-muted); text-decoration: none; transition: color .2s; }
    .footer-bottom-links a:hover { color: var(--text); }

    /* ─── SCROLL REVEAL ─── */
    .reveal {
      opacity: 0; transform: translateY(40px);
      transition: opacity .7s ease, transform .7s ease;
    }
    .reveal.up { opacity: 1; transform: translateY(0); }
    .reveal-left  { opacity: 0; transform: translateX(-40px); transition: opacity .7s ease, transform .7s ease; }
    .reveal-right { opacity: 0; transform: translateX(40px);  transition: opacity .7s ease, transform .7s ease; }
    .reveal-left.up, .reveal-right.up { opacity: 1; transform: translateX(0); }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 1100px) {
      .features-grid { grid-template-columns: repeat(2,1fr); }
      .features-big  { grid-template-columns: 1fr; }
      .stats-grid    { grid-template-columns: repeat(2,1fr); }
    }
    @media (max-width: 900px) {
      .hero-inner { grid-template-columns: 1fr; text-align: center; gap: 50px; }
      .hero-visual { order: -1; max-width: 520px; margin: 0 auto; }
      .mini-card-1, .mini-card-2, .mini-card-3 { display: none; }
      .hero p { margin: 0 auto 40px; }
      .hero-actions { justify-content: center; }
      .hero-trust { justify-content: center; }
      .pricing-grid { grid-template-columns: 1fr; max-width: 440px; margin: 0 auto; }
      .testimonials-grid { grid-template-columns: 1fr; }
      .steps-grid { grid-template-columns: repeat(2,1fr); }
      .steps-line { display: none; }
      .contact-grid { grid-template-columns: 1fr; }
      .footer-grid { grid-template-columns: 1fr 1fr; }
      .nav-links, .nav-actions { display: none; }
      .hamburger { display: flex; }
      .cta-wrap { padding: 60px 32px; }
    }
    @media (max-width: 640px) {
      .features-grid { grid-template-columns: 1fr; }
      .stats-grid { grid-template-columns: repeat(2,1fr); }
      .steps-grid { grid-template-columns: 1fr; }
      .footer-grid { grid-template-columns: 1fr; }
      .footer-bottom { flex-direction: column; text-align: center; }
      .brands-row { gap: 28px; }
      .cta-wrap { padding: 48px 24px; }
      .pricing-grid { max-width: 100%; }
    }
  </style>
</head>
<body>

<!-- Orbs -->
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<!-- Floating Icons -->
<div class="float-icons" id="floatIcons"></div>

<!-- ═══ NAVBAR ═══ -->
<nav id="navbar">
  <div class="container">
    <div class="nav-inner">
      <a href="landing.php" class="nav-logo">
        <div class="nav-logo-icon"><i class="fas fa-paper-plane"></i></div>
        MailPro
      </a>

      <ul class="nav-links">
        <li><a href="#home">Home</a></li>
        <li><a href="#features">Features</a></li>
        <li><a href="#pricing">Pricing</a></li>
        <li><a href="#testimonials">Testimonials</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>

      <div class="nav-actions">
        <a href="login.php" class="btn-nav-login">Login</a>
        <a href="signup.php" class="btn-nav-signup">Sign Up Free</a>
      </div>

      <button class="hamburger" id="hamburger" aria-label="Toggle menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
  <a href="#home">Home</a>
  <a href="#features">Features</a>
  <a href="#pricing">Pricing</a>
  <a href="#testimonials">Testimonials</a>
  <a href="#contact">Contact</a>
  <div class="mobile-menu-divider"></div>
  <a href="login.php" style="color:var(--blue-light);font-weight:700">Login</a>
  <a href="signup.php" style="background:var(--grad1);color:#fff;padding:14px;border-radius:10px;font-weight:700;text-align:center;margin-top:4px">Sign Up Free →</a>
</div>

<!-- ═══ HERO ═══ -->
<section class="hero" id="home">
  <div class="container">
    <div class="hero-inner">

      <!-- Content -->
      <div class="hero-content">
        <div class="hero-badge-wrap">
          <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            #1 Email Marketing Platform · 10K+ Businesses
          </div>
        </div>

        <h1>Powerful Email Marketing for <span class="grad-text">Business Growth</span></h1>
        <p>Create, automate, and track email campaigns with ease. Reach the right audience at the right time and turn subscribers into loyal customers.</p>

        <div class="hero-actions">
          <a href="login.php" class="btn btn-primary btn-lg" id="getStartedBtn">
            <i class="fas fa-rocket"></i> Get Started Free
          </a>
          <a href="#features" class="btn btn-ghost btn-lg">
            <i class="fas fa-play-circle"></i> Learn More
          </a>
        </div>

        <div class="hero-trust">
          <div class="trust-avatars">
            <span class="trust-avatar" style="background:linear-gradient(135deg,#4f7cff,#a78bfa)">👩</span>
            <span class="trust-avatar" style="background:linear-gradient(135deg,#34d399,#22d3ee)">👨</span>
            <span class="trust-avatar" style="background:linear-gradient(135deg,#f472b6,#a78bfa)">👩</span>
            <span class="trust-avatar" style="background:linear-gradient(135deg,#fb923c,#f472b6)">👨</span>
            <span class="trust-avatar" style="background:linear-gradient(135deg,#a78bfa,#6366f1)">👩</span>
          </div>
          <div>
            <div class="trust-text">Trusted by <strong>10,000+</strong> businesses worldwide</div>
          </div>
          <div class="hero-stars">
            <div class="stars">
              <i class="fas fa-star"></i><i class="fas fa-star"></i>
              <i class="fas fa-star"></i><i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <span class="stars-text">4.9/5 rating</span>
          </div>
        </div>
      </div>

      <!-- Visual -->
      <div class="hero-visual">

        <!-- Floating Mini Cards -->
        <div class="hero-mini-card mini-card-1">
          <div class="mini-icon" style="background:rgba(52,211,153,.15);color:var(--green)">
            <i class="fas fa-check-circle"></i>
          </div>
          <div>
            <div class="mini-label">Campaign Sent!</div>
            <div class="mini-val">98.7% Delivered ✨</div>
          </div>
        </div>

        <div class="hero-mini-card mini-card-2">
          <div class="mini-icon" style="background:rgba(79,124,255,.15);color:var(--blue)">
            <i class="fas fa-chart-line"></i>
          </div>
          <div>
            <div class="mini-label">Open Rate</div>
            <div class="mini-val">+34% This Week 📈</div>
          </div>
        </div>

        <div class="hero-mini-card mini-card-3">
          <div class="mini-icon" style="background:rgba(167,139,250,.15);color:var(--purple)">
            <i class="fas fa-users"></i>
          </div>
          <div>
            <div class="mini-label">New Subscribers</div>
            <div class="mini-val">+247 Today 🎉</div>
          </div>
        </div>

        <!-- Dashboard -->
        <div class="hero-dashboard">
          <div class="db-title-bar">
            <div class="db-dot" style="background:#ff5f57"></div>
            <div class="db-dot" style="background:#febc2e"></div>
            <div class="db-dot" style="background:#28c840"></div>
            <span class="db-title">MailPro Dashboard</span>
          </div>

          <div class="db-stats">
            <div class="db-stat">
              <div class="db-stat-val">1.4M</div>
              <div class="db-stat-lbl">Emails Sent</div>
              <div class="db-stat-change"><i class="fas fa-arrow-up"></i> +12%</div>
            </div>
            <div class="db-stat">
              <div class="db-stat-val" style="background:var(--grad2);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">47.3%</div>
              <div class="db-stat-lbl">Open Rate</div>
              <div class="db-stat-change"><i class="fas fa-arrow-up"></i> +8%</div>
            </div>
            <div class="db-stat">
              <div class="db-stat-val" style="background:var(--grad4);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">13.1%</div>
              <div class="db-stat-lbl">Click Rate</div>
              <div class="db-stat-change"><i class="fas fa-arrow-up"></i> +5%</div>
            </div>
            <div class="db-stat">
              <div class="db-stat-val">294</div>
              <div class="db-stat-lbl">Campaigns</div>
              <div class="db-stat-change"><i class="fas fa-arrow-up"></i> +3</div>
            </div>
          </div>

          <div class="db-chart">
            <div class="db-chart-head">
              <div class="db-chart-title"><i class="fas fa-chart-bar" style="color:var(--blue);margin-right:6px"></i>Performance</div>
              <div class="db-chart-badge">This Week</div>
            </div>
            <div class="chart-bars">
              <div class="chart-bar" style="height:42%;animation-delay:.05s"></div>
              <div class="chart-bar" style="height:60%;animation-delay:.1s"></div>
              <div class="chart-bar" style="height:38%;animation-delay:.15s"></div>
              <div class="chart-bar" style="height:78%;animation-delay:.2s;background:var(--grad2);opacity:.8"></div>
              <div class="chart-bar" style="height:55%;animation-delay:.25s"></div>
              <div class="chart-bar" style="height:88%;animation-delay:.3s;background:var(--grad2);opacity:.9"></div>
              <div class="chart-bar" style="height:70%;animation-delay:.35s"></div>
            </div>
          </div>

          <div class="db-campaign">
            <div class="db-camp-icon"><i class="fas fa-envelope"></i></div>
            <div>
              <div class="db-camp-name">Summer Sale Campaign</div>
              <div class="db-camp-sub">Sent to 9,840 subscribers</div>
            </div>
            <div class="db-camp-live">● LIVE</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>



<!-- ═══ FOOTER ═══ -->
<footer>
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="landing.php" class="nav-logo" style="text-decoration:none">
          <div class="nav-logo-icon"><i class="fas fa-paper-plane"></i></div>
          MailPro
        </a>
        <p class="footer-brand-desc">The all-in-one email marketing platform for businesses that want to grow faster. Simple, powerful, and affordable.</p>
        <div class="footer-socials">
          <a href="#" class="footer-social" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="#" class="footer-social" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
          <a href="#" class="footer-social" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" class="footer-social" aria-label="GitHub"><i class="fab fa-github"></i></a>
          <a href="#" class="footer-social" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
        </div>
      </div>

      <div class="footer-col">
        <h4>Product</h4>
        <ul>
          <li><a href="#features">Features</a></li>
          <li><a href="#pricing">Pricing</a></li>
          <li><a href="#how">How It Works</a></li>
          <li><a href="#">Integrations</a></li>
          <li><a href="#">Changelog</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Company</h4>
        <ul>
          <li><a href="#">About Us</a></li>
          <li><a href="#">Blog</a></li>
          <li><a href="#">Careers</a></li>
          <li><a href="#contact">Contact</a></li>
          <li><a href="#">Press Kit</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Legal</h4>
        <ul>
          <li><a href="#">Privacy Policy</a></li>
          <li><a href="#">Terms of Service</a></li>
          <li><a href="#">Cookie Policy</a></li>
          <li><a href="#">GDPR</a></li>
          <li><a href="#">Security</a></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <span>© <?= date('Y') ?> MailPro. All rights reserved.</span>
      <div class="footer-bottom-links">
        <a href="#">Privacy</a>
        <a href="#">Terms</a>
        <a href="#">Cookies</a>
      </div>
      <span>Made with <i class="fas fa-heart" style="color:var(--pink)"></i> for email marketers</span>
    </div>
  </div>
</footer>

<script>
/* ══════════════════════════════════════════════
   FLOATING ICONS
══════════════════════════════════════════════ */
(function() {
  const container = document.getElementById('floatIcons');
  const icons = ['fa-envelope','fa-paper-plane','fa-at','fa-inbox','fa-bell','fa-chart-bar','fa-star','fa-envelope-open','fa-mail-bulk'];
  for (let i = 0; i < 22; i++) {
    const el = document.createElement('i');
    el.className = `fas ${icons[i % icons.length]} float-icon`;
    el.style.left  = Math.random() * 100 + 'vw';
    el.style.bottom = '-60px';
    el.style.fontSize = (12 + Math.random() * 16) + 'px';
    el.style.animationDuration = (16 + Math.random() * 20) + 's';
    el.style.animationDelay   = (Math.random() * 18) + 's';
    el.style.opacity = 0;
    container.appendChild(el);
  }
})();

/* ══════════════════════════════════════════════
   PARTICLES
══════════════════════════════════════════════ */
(function() {
  const colors = ['rgba(79,124,255,.6)','rgba(167,139,250,.5)','rgba(34,211,238,.5)','rgba(52,211,153,.4)'];
  for (let i = 0; i < 14; i++) {
    const p = document.createElement('div');
    p.className = 'particle';
    const size = 2 + Math.random() * 4;
    p.style.cssText = `
      width:${size}px; height:${size}px;
      left:${Math.random()*100}vw;
      background:${colors[i % colors.length]};
      animation-duration:${20+Math.random()*25}s;
      animation-delay:${Math.random()*15}s;
      --drift:${(Math.random()-.5)*120}px;
    `;
    document.body.appendChild(p);
  }
})();

/* ══════════════════════════════════════════════
   NAVBAR SCROLL
══════════════════════════════════════════════ */
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 40);
}, { passive: true });

/* ══════════════════════════════════════════════
   HAMBURGER
══════════════════════════════════════════════ */
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');
hamburger.addEventListener('click', () => {
  hamburger.classList.toggle('open');
  mobileMenu.classList.toggle('open');
});
document.querySelectorAll('#mobileMenu a').forEach(a => {
  a.addEventListener('click', () => {
    hamburger.classList.remove('open');
    mobileMenu.classList.remove('open');
  });
});

/* ══════════════════════════════════════════════
   SMOOTH SCROLL
══════════════════════════════════════════════ */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const id = a.getAttribute('href').slice(1);
    if (!id) return;
    const target = document.getElementById(id);
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});


/* ══════════════════════════════════════════════
   ACTIVE NAV LINK ON SCROLL
══════════════════════════════════════════════ */
const sections  = document.querySelectorAll('section[id], div[id]');
const navLinks  = document.querySelectorAll('.nav-links a');
const sectionObs = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      navLinks.forEach(link => link.style.color = '');
      const active = document.querySelector(`.nav-links a[href="#${e.target.id}"]`);
      if (active) active.style.color = 'var(--text)';
    }
  });
}, { threshold: 0.4 });
sections.forEach(s => sectionObs.observe(s));

/* ══════════════════════════════════════════════
   HERO DASHBOARD LIVE COUNTER TICK
══════════════════════════════════════════════ */
setInterval(() => {
  const vals = document.querySelectorAll('.db-stat-val');
  if (vals[0]) {
    const base = 1.4 + (Math.random() * 0.02 - 0.01);
    vals[0].textContent = base.toFixed(1) + 'M';
  }
}, 3500);
</script>
</body>
</html>
