<?php
declare(strict_types=1);
session_start();
$bootLoggedIn = isset($_SESSION['user_id']);
session_write_close();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MailFlow — Email Marketing Platform</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- TOAST CONTAINER -->
<div class="toast-container" id="toastContainer"></div>

<!-- AUTH SCREEN -->
<section class="auth-screen" id="authScreen">
  <div class="auth-brand">
    <div class="auth-logo">✉</div>
    <span>MailFlow</span>
  </div>
  <div class="auth-bg-orb auth-bg-orb-a"></div>
  <div class="auth-bg-orb auth-bg-orb-b"></div>
  <div class="auth-stage auth-stage-saas">
    <header class="auth-topbar">
      <div class="auth-brand auth-brand-top">
        <div class="auth-logo">&#9993;</div>
        <span>MailFlow</span>
      </div>
    </header>
    <div class="auth-layout">
      <div class="auth-hero-panel">
        <div class="auth-kicker">Email marketing platform</div>
        <h1 class="auth-hero-title">Grow your business with smart email marketing</h1>
        <p class="auth-hero-description">MailFlow helps teams launch newsletters, automate campaigns, track engagement, and turn subscribers into loyal customers from one polished command center.</p>
        <div class="auth-hero-actions">
          <button class="btn btn-primary btn-lg auth-hero-cta" type="button" onclick="showAuthView('signup')">Get Started</button>
        </div>
        <p class="auth-hero-note">Start your first campaign in minutes</p>
      </div>
      <aside class="auth-card auth-authpanel">
        <div class="auth-panel-glow"></div>
        <div class="auth-panel-tabs">
          <button class="auth-panel-tab active" id="cardLoginBtn" type="button" onclick="showAuthView('login')">Login</button>
          <button class="auth-panel-tab" id="cardSignupBtn" type="button" onclick="showAuthView('signup')">Sign Up</button>
        </div>
        <div class="auth-card-head" id="loginTab">
          <div>
            <div class="auth-card-kicker">Welcome back</div>
            <h2>Login to MailFlow</h2>
          </div>
          <div class="auth-card-badge">Secure</div>
        </div>
        <form class="auth-form auth-form-static" id="loginForm" onsubmit="handleLogin(event)">
          <div class="form-group auth-field">
            <label>Email Address</label>
            <input type="email" id="loginEmail" placeholder="you@example.com" required>
          </div>
          <div class="form-group auth-field">
            <label>Password</label>
            <input type="password" id="loginPassword" placeholder="Enter your password" required>
          </div>
          <button class="btn btn-primary btn-lg auth-submit" type="submit">Login</button>
          <p class="auth-switch">Don&apos;t have an account? <button type="button" onclick="showAuthView('signup')">Create one now</button></p>
        </form>

        <div class="auth-card-head auth-card-hidden" id="signupTab">
          <div>
            <div class="auth-card-kicker">Start today</div>
            <h2>Create your account</h2>
          </div>
          <div class="auth-card-badge">Fast setup</div>
        </div>
        <form class="auth-form auth-card-hidden" id="signupForm" onsubmit="handleSignup(event)">
          <div class="form-row">
            <div class="form-group auth-field">
              <label>First Name</label>
              <input type="text" id="signupFirstName" placeholder="John" required>
            </div>
            <div class="form-group auth-field">
              <label>Last Name</label>
              <input type="text" id="signupLastName" placeholder="Doe" required>
            </div>
          </div>
          <div class="form-group auth-field">
            <label>Email Address</label>
            <input type="email" id="signupEmail" placeholder="you@example.com" required>
          </div>
          <div class="form-group auth-field">
            <label>Password</label>
            <input type="password" id="signupPassword" placeholder="At least 6 characters" minlength="6" required>
          </div>
          <button class="btn btn-primary btn-lg auth-submit" type="submit">Create Account</button>
          <p class="auth-switch">Already have an account? <button type="button" onclick="showAuthView('login')">Login here</button></p>
        </form>

        <div class="auth-message-wrap">
          <div class="auth-message" id="authMessage"></div>
        </div>
      </aside>
    </div>
  </div>
</section>

<!-- CAMPAIGN MODAL -->
<div class="modal-overlay" id="campaignModal">
  <div class="modal" style="max-width:760px">
    <div class="modal-header">
      <div class="modal-title" id="campaignModalTitle">Create Campaign</div>
      <button class="modal-close" onclick="closeCampaignModal()">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="editCampaignId">
      <div id="campaignStepSetup">
        <div class="campaign-form-section">
          <div class="campaign-form-title">Campaign Setup</div>
          <div class="form-row">
            <div class="form-group">
              <label>Campaign Name *</label>
              <input type="text" id="campName" placeholder="e.g. Summer Sale 2025" oninput="updateCampaignStepActions()">
            </div>
            <div class="form-group">
              <label>Status</label>
              <select id="campStatus">
                <option value="draft">Draft</option>
                <option value="scheduled">Scheduled</option>
                <option value="sent">Sent</option>
              </select>
            </div>
          </div>
        </div>

        <div class="campaign-form-section">
          <div class="campaign-form-title">Sender</div>
          <div class="form-row">
            <div class="form-group">
              <label>Sender Name</label>
              <input type="text" id="campSenderName" placeholder="Your Name" oninput="syncEmailBuilderFromSender('name');updateCampaignStepActions()">
            </div>
            <div class="form-group">
              <label>Sender Gmail</label>
              <input type="email" id="campSenderEmail" placeholder="you@gmail.com" oninput="syncEmailBuilderFromSender('email');updateCampaignStepActions()">
            </div>
          </div>
        </div>

        <div class="campaign-form-section audience-section">
          <div class="campaign-form-title">Audience / Send Data</div>
          <div class="form-group">
            <label>Select Contacts</label>
            <input type="text" id="campContactSearch" placeholder="Search saved contacts..." oninput="renderCampaignContacts()">
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px">
              <button class="btn btn-secondary btn-sm" type="button" onclick="selectAllCampaignContacts()">Select All</button>
              <button class="btn btn-secondary btn-sm" type="button" onclick="clearCampaignContacts()">Clear Selection</button>
            </div>
            <div class="field-help"><span id="audienceCount">0</span> contacts selected from saved contacts.</div>
          </div>
          <div class="campaign-contacts-list" id="campaignContactsList"></div>
          <div class="form-row">
            <div class="form-group">
              <label>Lien (url)</label>
              <input type="text" class="campaign-link-input" id="campImageUrl" placeholder="" oninput="syncCampaignImageUrl()">
            </div>
            <div class="form-group">
              <label>Upload Product Image</label>
              <input type="file" id="campImageUpload" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" onchange="handleCampaignImageUpload(event)">
              <div class="field-help">Upload the final product image here.</div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Subject</label>
              <input type="text" id="campSubject" placeholder="Your campaign subject">
            </div>
            <div class="form-group">
              <label>Preview Text</label>
              <input type="text" id="campPreviewText" placeholder="Short preview before opening the email">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>From Name</label>
              <input type="text" id="campFromName" placeholder="Your brand or sender name" oninput="syncSenderFields('name')">
            </div>
          </div>
          <div class="campaign-image-inline-preview" id="campImageInlinePreview"></div>
        </div>
      </div>
      <div style="display:none">
        <input type="email" id="campFromEmail" placeholder="you@example.com">
        <input type="text" id="campEmailTitle" placeholder="Main headline for your email">
        <textarea id="campEmailBody" placeholder="Write your message to clients here..."></textarea>
        <input type="text" id="campButtonText" placeholder="Shop Now">
        <input type="url" id="campButtonLink" placeholder="https://example.com">
        <input type="range" id="campImageWidth" min="220" max="680" step="10" value="520">
        <select id="campImageAlign">
          <option value="left">Left</option>
          <option value="center" selected>Center</option>
          <option value="right">Right</option>
        </select>
        <input type="hidden" id="campTemplate">
        <input type="hidden" id="campSelectedImage">
        <input type="hidden" id="campImageAlt">
      </div>

      <div class="form-group" id="schedDateGroup" style="display:none">
        <label>Schedule Date & Time</label>
        <input type="datetime-local" id="campScheduleDate">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeCampaignModal()">Cancel</button>
      <button class="btn btn-primary" onclick="saveCampaign()" id="campSaveBtn">Save Campaign</button>
    </div>
  </div>
</div>

<!-- CONTACT MODAL -->
<div class="modal-overlay" id="contactModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="contactModalTitle">Add Contact</div>
      <button class="modal-close" onclick="closeContactModal()">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="editContactId">
      
      <!-- Copy-Paste Fields -->
      <div id="contactPasteFields">
        <div class="form-group">
          <label>Paste Contact Emails *</label>
          <textarea id="contPasteArea" placeholder="jane@example.com&#10;john@example.com, bob@example.com" rows="6" style="min-height:120px"></textarea>
          <div class="field-help">One email per line, or separated by commas. Extracted valid emails will be subscribed.</div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label>Company</label>
            <input type="text" id="contCompany" placeholder="Company name">
          </div>
        </div>
        
        <div class="form-group">
          <label>Tags</label>
          <input type="text" id="contTags" placeholder="vip, newsletter, buyer">
          <div class="field-help">Comma-separated tags for the contact.</div>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select id="contStatus">
            <option value="active">Active</option>
            <option value="unsubscribed">Unsubscribed</option>
            <option value="bounced">Bounced</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeContactModal()">Cancel</button>
      <button class="btn btn-primary" onclick="saveContact()">Save Contact</button>
    </div>
  </div>
</div>

<!-- APP SHELL -->
<div class="app-shell locked">

<!-- SIDEBAR OVERLAY -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">✉</div>
    <span class="logo-text">MailFlow</span>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <div class="nav-item active" data-page="home" onclick="navigate('home')">
      <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Dashboard
    </div>
    <div class="nav-item" data-page="contacts" onclick="navigate('contacts')">
      <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
      Contacts
      <span class="nav-badge" id="contactBadge">0</span>
    </div>
    <div class="nav-item" data-page="campaigns" onclick="navigate('campaigns')">
      <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      Campaigns
      <span class="nav-badge" id="campaignBadge">0</span>
    </div>

    <div class="nav-section-label">Account</div>
    <div class="nav-item" data-page="pricing" onclick="navigate('pricing')">
      <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
      Pricing
    </div>
    <div class="nav-item" data-page="profile" onclick="navigate('profile')">
      <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Profile
    </div>
    <div class="nav-item" data-page="contact-us" onclick="navigate('contact-us')">
      <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      Contact Us
    </div>
  </nav>
</aside>

<!-- MAIN -->
<main class="main-content">
  <header class="topbar">
    <!-- Topbar search -->
    <div class="topbar-search">
      <svg class="topbar-search-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" placeholder="Search anything..." id="topbarSearchInput" oninput="globalSearch(this.value)" onfocus="globalSearch(this.value)">
    </div>

    <div class="topbar-actions">
      <!-- Theme toggle -->
      <div class="theme-toggle" id="themeBtn" onclick="toggleTheme()" title="Toggle theme">🌙</div>

      <!-- Profile dropdown -->
      <div class="profile-wrap">
        <div class="profile-trigger" id="profileToggle" onclick="toggleProfileMenu(event)">
          <div class="pt-av" id="topbarAvatar">?</div>
          <span class="pt-name" id="topbarName">User</span>
          <span class="pt-caret">▾</span>
        </div>
        <div class="profile-menu" id="profileMenuDrop">
          <div class="pm-header">
            <div class="pm-name" id="pmName">User</div>
            <div class="pm-email" id="pmEmail">you@example.com</div>
          </div>
          <div class="pm-divider"></div>
          <div class="pm-item" onclick="navigate('profile');closeAllDropdowns()">👤 My Profile</div>
          <div class="pm-item" onclick="navigate('pricing');closeAllDropdowns()">💳 Billing</div>
          <div class="pm-divider"></div>
          <div class="pm-item danger" onclick="handleLogout()">🚪 Sign Out</div>
        </div>
      </div>
    </div>
  </header>

  <!-- ══════════════ HOME ══════════════ -->
  <section class="page active" id="page-home">
    <div class="hero-section">
      <div class="hero-greeting">👋 Welcome back</div>
      <h1 class="hero-title" id="heroName">Good morning!</h1>
      <p class="hero-sub">Your campaigns are performing great. Let's reach your audience with something amazing today.</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card" style="--grad:var(--gradient-1)">
        <div class="stat-icon" style="background:rgba(79,124,255,.14);color:#818cf8">📧</div>
        <div class="stat-value" id="homeTotalCampaigns">0</div>
        <div class="stat-label">Total Campaigns</div>
        <div class="stat-change up" id="homeCampChange">+0 this week</div>
      </div>
      <div class="stat-card" style="--grad:var(--gradient-2)">
        <div class="stat-icon" style="background:rgba(52,211,153,.14);color:#34d399">👥</div>
        <div class="stat-value" id="homeTotalContacts">0</div>
        <div class="stat-label">Total Contacts</div>
        <div class="stat-change up">↑ +0 this week</div>
      </div>
      <div class="stat-card" style="--grad:var(--gradient-3)">
        <div class="stat-icon" style="background:rgba(244,114,182,.14);color:#f472b6">🖱️</div>
        <div class="stat-value" id="homeClickRate">0%</div>
        <div class="stat-label">Avg Click Rate</div>
        <div class="stat-change down">↓ -0.8%</div>
      </div>
    </div>

    <div class="two-col">
      <div class="card">
        <div class="card-header">
          <div><div class="card-title">Quick Actions</div><div class="card-subtitle">Jump into the most common tasks</div></div>
        </div>
        <div class="quick-actions-grid">
          <div class="quick-action-card" onclick="openCampaignModal()">
            <div class="qa-icon" style="background:rgba(79,124,255,.15)">✉</div>
            <div class="qa-title">New Campaign</div>
            <div class="qa-desc">Draft and send a new email campaign</div>
          </div>
          <div class="quick-action-card" onclick="navigate('contacts');setTimeout(openContactModal,150)">
            <div class="qa-icon" style="background:rgba(52,211,153,.15)">👤</div>
            <div class="qa-title">Add Contact</div>
            <div class="qa-desc">Add a subscriber to your list</div>
          </div>
          <div class="quick-action-card" onclick="navigate('pricing')">
            <div class="qa-icon" style="background:rgba(244,114,182,.15)">📥</div>
            <div class="qa-title">Pricing</div>
            <div class="qa-desc">View plans and upgrade your account</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title">Recent Activity</div></div>
        <div id="recentActivity">
          <div class="activity-item">
            <div class="activity-dot" style="background:#818cf8"></div>
            <div class="activity-text"><strong>Welcome!</strong> Start creating your first campaign</div>
            <div class="activity-time">Now</div>
          </div>
        </div>
      </div>
    </div>

  </section>

  <!-- ══════════════ CAMPAIGNS ══════════════ -->
  <section class="page" id="page-campaigns">
    <div class="filter-bar">
      <div class="search-bar" style="max-width:260px">
        <svg class="search-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search campaigns..." id="campaignSearch" oninput="filterCampaigns()">
      </div>
      <button class="filter-chip active" data-filter="all" onclick="setFilter(this,'all')">All</button>
      <button class="filter-chip" data-filter="draft" onclick="setFilter(this,'draft')">Draft</button>
      <button class="filter-chip" data-filter="sent" onclick="setFilter(this,'sent')">Sent</button>
      <button class="filter-chip" data-filter="scheduled" onclick="setFilter(this,'scheduled')">Scheduled</button>
      <button class="btn btn-primary btn-sm" style="margin-left:auto" onclick="openCampaignModal()">+ Create</button>
    </div>
    <div class="campaigns-grid" id="campaignsGrid"></div>
  </section>

  <!-- ══════════════ ANALYTICS ══════════════ -->
  <section class="page" id="page-contacts">
    <div class="contacts-toolbar">
      <div class="search-bar">
        <svg class="search-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search contacts..." id="contactSearch" oninput="filterContacts()">
      </div>
      <label class="btn btn-secondary btn-sm" style="cursor:pointer">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        Import CSV
        <input type="file" accept=".csv" id="csvInput" style="display:none" onchange="importCSV(event)">
      </label>
      <button class="btn btn-primary btn-sm" onclick="openContactModal()">+ Add Contact</button>
      <div style="margin-left:auto;font-size:12px;color:var(--text-muted)">
        <span id="contactCountLabel">0</span> contacts
        <button class="btn btn-danger btn-sm" id="deleteSelectedBtn" onclick="deleteSelectedContacts()" style="display:none;margin-left:10px">Delete Selected</button>
      </div>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>
            <th>Name</th><th>Email</th><th>Company</th><th>Tags</th><th>Status</th><th>Added</th><th>Actions</th>
          </tr>
        </thead>
        <tbody id="contactsTable">
          <tr>
            <td><input type="checkbox"></td>
            <td>Jane Doe</td>
            <td>jane@example.com</td>
            <td>Acme Corp.</td>
            <td><span class="tag">VIP</span><span class="tag">newsletter</span></td>
            <td><span class="status-badge active">Active</span></td>
            <td>Today</td>
            <td>
              <button class="btn btn-secondary btn-sm" type="button" onclick="editContact(this)">Edit</button>
              <button class="btn btn-danger btn-sm" type="button" onclick="deleteContact(this)">Delete</button>
            </td>
          </tr>
      </table>
      <div id="noContactsMsg" class="no-results">No contacts yet. Add your first contact above!</div>
    </div>
  </section>

  <!-- ══════════════ PRICING ══════════════ -->
  <section class="page" id="page-contact-us">
    <div class="hero-section">
      <div class="hero-greeting">Support</div>
      <h1 class="hero-title">Contact Us</h1>
      <p class="hero-sub">Send us your question, feedback, or issue from inside MailFlow.</p>
    </div>

    <div class="two-col contact-us-layout">
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Send a Message</div>
            <div class="card-subtitle">We will save your request and keep it linked to your account.</div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Your Name</label>
            <input type="text" id="cuName" placeholder="Your full name">
          </div>
          <div class="form-group">
            <label>Your Email</label>
            <input type="email" id="cuEmail" placeholder="you@example.com">
          </div>
        </div>
        <div class="form-group">
          <label>Subject</label>
          <input type="text" id="cuSubject" placeholder="How can we help you?">
        </div>
        <div class="form-group">
          <label>Message</label>
          <textarea id="cuMessage" rows="7" placeholder="Write your message here..." style="min-height:180px"></textarea>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap">
          <button class="btn btn-primary" type="button" onclick="submitContactUs()">Send Message</button>
        </div>
      </div>

    </div>
  </section>

  <section class="page" id="page-pricing">
    <div style="text-align:center;margin-bottom:32px">
      <h2 style="font-family:'Syne',sans-serif;font-size:30px;font-weight:800;margin-bottom:8px">Simple, transparent pricing</h2>
      <p style="color:var(--text-secondary);font-size:14px">Start free, scale as you grow. No hidden fees.</p>
    </div>

    <!-- Billing toggle -->
    <div class="billing-toggle">
      <span class="bt-label active" id="monthlyLabel">Monthly</span>
      <div class="toggle" id="billingToggle" onclick="toggleBilling()">
        <div class="toggle-track"><div class="toggle-thumb"></div></div>
      </div>
      <span class="bt-label" id="yearlyLabel">Yearly <span class="bt-save">Save 20%</span></span>
    </div>

    <div class="pricing-grid">
      <div class="pricing-card">
        <div class="plan-name">Free</div>
        <div class="plan-price" id="freePrice">$0<span>/mo</span></div>
        <div class="plan-period">Forever free, no credit card needed</div>
        <ul class="plan-features">
          <li><span class="feature-check yes">✓</span>500 contacts</li>
          <li><span class="feature-check yes">✓</span>1,000 emails/month</li>
          
        </ul>
        <button class="btn btn-secondary" style="width:100%" onclick="selectPlan('Free')">Current Plan</button>
      </div>

      <div class="pricing-card featured">
        <div class="plan-badge">⭐ Most Popular</div>
        <div class="plan-name">Pro</div>
        <div class="plan-price" id="proPrice">$29<span>/mo</span></div>
        <div class="plan-period" id="proPeriod">Billed monthly</div>
        <ul class="plan-features">
          <li><span class="feature-check yes">✓</span>10,000 contacts</li>
          <li><span class="feature-check yes">✓</span>100,000 emails/month</li>
          <li><span class="feature-check yes">✓</span>Unlimited campaigns</li>
          
        </ul>
        <button class="btn btn-primary" style="width:100%" onclick="openCheckout('Pro')">Upgrade to Pro</button>
      </div>

      <div class="pricing-card">
        <div class="plan-name">Business</div>
        <div class="plan-price" id="bizPrice">$79<span>/mo</span></div>
        <div class="plan-period" id="bizPeriod">Billed monthly</div>
        <ul class="plan-features">
          <li><span class="feature-check yes">✓</span>100,000 contacts</li>
          <li><span class="feature-check yes">✓</span>1M emails/month</li>
        
        </ul>
        <button class="btn btn-secondary" style="width:100%" onclick="openCheckout('Business')">Get Business</button>
      </div>

      <div class="pricing-card">
        <div class="plan-name">Enterprise</div>
        <div class="plan-price">Custom</div>
        <div class="plan-period">Custom billing available</div>
        <ul class="plan-features">
          <li><span class="feature-check yes">✓</span>Unlimited contacts</li>
          <li><span class="feature-check yes">✓</span>Unlimited emails</li>
        
        </ul>
        <button class="btn btn-secondary" style="width:100%" onclick="openCheckout('Enterprise')">Contact Sales</button>
      </div>
    </div>

  </section>

  <!-- ══════════════ PAYMENT CHECKOUT ══════════════ -->
  <section class="page" id="page-payment">
    <div class="card" style="max-width:760px;margin:0 auto">
      <div class="card-title" style="margin-bottom:12px">Payment Information</div>
      <div style="color:var(--text-secondary);font-size:14px;margin-bottom:20px">Enter your card details to complete the <strong id="checkoutPlanLabel">Pro</strong> upgrade.</div>
      <form id="paymentForm" onsubmit="handlePaymentSubmit(event)">
        <div class="form-group">
          <label>Selected plan</label>
          <input type="text" id="checkoutPlan" readonly style="background:var(--bg-muted);cursor:not-allowed">
        </div>
        <div class="form-group">
          <label>Cardholder name</label>
          <input type="text" id="cardholderName" placeholder="Name on card" required>
        </div>
        <div class="form-group">
          <label>Card number</label>
          <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" required>
        </div>
        <div class="form-row">
          <div class="form-group" style="flex:1">
            <label>Expiry date</label>
            <input type="text" id="cardExpiry" placeholder="MM/YY" required>
          </div>
          <div class="form-group" style="flex:1">
            <label>CVC</label>
            <input type="text" id="cardCvc" placeholder="123" required>
          </div>
        </div>
        <div class="form-group">
          <label>Billing email</label>
          <input type="email" id="billingEmail" placeholder="you@example.com" required>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:14px">
          <button class="btn btn-primary" type="submit">Submit payment</button>
          <button class="btn btn-secondary" type="button" onclick="navigate('pricing')">Back to pricing</button>
        </div>
      </form>
    </div>
  </section>

  <!-- ══════════════ PROFILE ══════════════ -->
  <section class="page" id="page-profile">
    <div class="profile-header">
      <div class="profile-avatar" id="profileAvatar" onclick="changeAvatar()">U<div class="avatar-edit">✏</div></div>
      <input type="file" id="avatarInput" accept="image/png,image/jpeg,image/webp" style="display:none" onchange="handleAvatarSelected(event)">
      <div class="profile-info">
        <div class="name" id="profileName">User</div>
        <div class="email" id="profileEmail">you@example.com</div>
        <div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap">
          <span class="badge badge-blue" id="profilePlanBadge">Free Plan</span>
          <span class="badge badge-green"><span class="badge-dot"></span>Active</span>
        </div>
      </div>
      <button class="btn btn-danger btn-sm" style="margin-left:auto" onclick="handleLogout()">Sign Out</button>
    </div>

    <div class="profile-tabs">
      <div class="profile-tab active" onclick="switchProfileTab('tab-info',this)">Account Info</div>
      <div class="profile-tab" onclick="switchProfileTab('tab-billing',this)">Billing</div>
      <div class="profile-tab" onclick="switchProfileTab('tab-security',this)">Security</div>
    </div>

    <!-- Account Info -->
    <div class="tab-panel active" id="tab-info">
      <div class="card">
        <div class="card-title" style="margin-bottom:18px">Personal Information</div>
        <div class="form-row">
          <div class="form-group"><label>First Name</label><input type="text" id="pFirstName" value=""></div>
          <div class="form-group"><label>Last Name</label><input type="text" id="pLastName" value=""></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Email</label><input type="email" id="pEmail" value=""></div>
          <div class="form-group"><label>Phone</label><input type="tel" id="pPhone" placeholder="+1 (555) 000-0000"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Company</label><input type="text" id="pCompany" placeholder="Your company"></div>
          <div class="form-group"><label>Website</label><input type="url" id="pWebsite" placeholder="https://example.com"></div>
        </div>
        <div class="form-group"><label>Bio</label><textarea id="pBio" placeholder="Tell us about yourself..." rows="3"></textarea></div>
        <button class="btn btn-primary" onclick="saveProfile()">Save Changes</button>
      </div>
    </div>

    <!-- Billing -->
    <div class="tab-panel" id="tab-billing">
      <div class="current-plan-card">
        <div class="cp-label">Current Plan</div>
        <div class="cp-name" id="cpName">Free Plan</div>
        <div class="cp-desc" id="cpDesc">You are on the free plan. Upgrade to unlock more features.</div>
      </div>
      <div class="card">
        <div class="card-title" id="billingHistoryTitle" style="margin-bottom:16px">Billing History</div>
        <div class="billing-row"><div class="billing-icon">💳</div><div class="billing-desc"><div class="bname">Pro Plan — Monthly</div><div class="bdate">Jan 1, 2025</div></div><div class="billing-amount">$29.00</div></div>
        <div class="billing-row"><div class="billing-icon">💳</div><div class="billing-desc"><div class="bname">Pro Plan — Monthly</div><div class="bdate">Dec 1, 2024</div></div><div class="billing-amount">$29.00</div></div>
        <div class="billing-row"><div class="billing-icon">🎁</div><div class="billing-desc"><div class="bname">Free Plan (started)</div><div class="bdate">Nov 15, 2024</div></div><div class="billing-amount">$0.00</div></div>
      </div>
    </div>

    <!-- Security -->
    <div class="tab-panel" id="tab-security">
      <div class="card" style="margin-bottom:16px">
        <div class="card-title" style="margin-bottom:18px">Change Password</div>
        <div class="form-group"><label>Current Password</label><input type="password" id="currentPass" placeholder="Enter current password"></div>
        <div class="form-group">
          <label>New Password</label>
          <input type="password" id="newPass" placeholder="Enter new password" oninput="checkPasswordStrength(this.value)">
          <div class="password-strength"><div class="ps-fill" id="passStrength" style="width:0%;background:#f87171"></div></div>
          <div style="font-size:11px;color:var(--text-muted);margin-top:4px" id="passStrengthLabel">Enter a password</div>
        </div>
        <div class="form-group"><label>Confirm New Password</label><input type="password" id="confirmPass" placeholder="Confirm new password"></div>
        <button class="btn btn-primary" onclick="changePassword()">Update Password</button>
      </div>
      <div class="card">
        <div class="card-title" style="margin-bottom:18px">Account Settings</div>
        <div style="display:flex;align-items:center;justify-content:space-between">
          <div><div style="font-size:13px;font-weight:600;color:var(--text-primary)">Delete Account</div><div style="font-size:12px;color:var(--text-muted);margin-top:2px">Permanently delete your account and all data</div></div>
          <button class="btn btn-danger btn-sm" onclick="deleteAccount()">Delete Account</button>
        </div>
      </div>
    </div>


</main>
</div>

<script>
window.MAILFLOW_BOOT = {
  loggedIn: <?php echo $bootLoggedIn ? 'true' : 'false'; ?>
};
</script>
<script src="script.js"></script>
</body>
</html>
