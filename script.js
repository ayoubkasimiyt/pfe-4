let campaigns = [];
let contacts = [];
let profile = {
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  company: '',
  website: '',
  bio: '',
  plan: 'Free',
  avatar_path: ''
};

let currentFilter = 'all';
let checkoutPlan = '';
let isYearly = false;
let charts = {};
let billingHistory = [];
let selectedCampaignContacts = [];
let emailLibraryImages = [];
let selectedTemplateCategory = 'all';
let campaignModalStep = 'setup';
let campaignModalBaseTitle = 'Create Campaign';

const emailTemplates = [
  {
    id: 'promotion-flash',
    category: 'Promotion',
    name: 'Flash Offer',
    kicker: 'Limited-time promotion',
    title: 'Big savings for your subscribers',
    body: 'Introduce your offer clearly, highlight the value, and guide readers toward one strong call to action.',
    button_text: 'Shop the Offer',
    accent: 'var(--accent-4)'
  },
  {
    id: 'newsletter-digest',
    category: 'Newsletter',
    name: 'Weekly Digest',
    kicker: 'Curated weekly update',
    title: 'Everything your audience should know this week',
    body: 'Use this format for updates, links, insights, and one featured highlight that keeps the email focused.',
    button_text: 'Read the Highlights',
    accent: 'var(--accent)'
  },
  {
    id: 'welcome-onboard',
    category: 'Welcome Email',
    name: 'Warm Welcome',
    kicker: 'First impression email',
    title: 'Welcome aboard, we are glad you are here',
    body: 'Set expectations, introduce your brand voice, and help new subscribers take their first simple step.',
    button_text: 'Get Started',
    accent: 'var(--accent-3)'
  },
  {
    id: 'launch-reveal',
    category: 'Product Launch',
    name: 'Launch Reveal',
    kicker: 'New release announcement',
    title: 'Your newest launch is ready to discover',
    body: 'Build anticipation with a clear headline, polished product image, and one link that drives action.',
    button_text: 'See the Launch',
    accent: 'var(--accent-2)'
  }
];

const statusConfig = {
  sent: { color: '#34d399', bg: 'rgba(52,211,153,.12)', label: 'Sent' },
  draft: { color: '#94a3b8', bg: 'rgba(148,163,184,.12)', label: 'Draft' },
  scheduled: { color: '#fb923c', bg: 'rgba(251,146,60,.12)', label: 'Scheduled' }
};

const typeIcons = ['📧', '📰', '🎉', '🛒', '💼', '📊', '🎯', '💌'];

window.addEventListener('DOMContentLoaded', async () => {
  applyTheme(localStorage.getItem('mf_theme') || 'dark');
  bindStaticEvents();
  await bootstrap();
});

function bindStaticEvents() {
  const campStatus = document.getElementById('campStatus');
  if (campStatus) {
    campStatus.addEventListener('change', toggleScheduleField);
  }

  document.addEventListener('click', (event) => {
    const menu = document.getElementById('profileMenuDrop');
    const toggle = document.getElementById('profileToggle');
    if (menu && toggle && !menu.contains(event.target) && !toggle.contains(event.target)) {
      menu.classList.remove('open');
    }
  });
}

async function bootstrap() {
  try {
    const response = await api('session');
    if (!response.authenticated) {
      showAuthPage();
      return;
    }

    hydrateState(response);
    initializeApp();
  } catch (error) {
    showAuthPage();
    showToast(error.message || 'Unable to load the application right now.', 'error');
  }
}

function hydrateState(payload) {
  profile = payload.user || profile;
  campaigns = Array.isArray(payload.campaigns) ? payload.campaigns : [];
  contacts = Array.isArray(payload.contacts) ? payload.contacts : [];
  emailLibraryImages = Array.isArray(payload.email_images) ? payload.email_images : [];
  billingHistory = Array.isArray(payload.billing_history) ? payload.billing_history : [];
}

function initializeApp() {
  showAppPage();
  applyProfile();
  renderCampaignContacts();
  renderEmailTemplateGallery();
  renderEmailImageLibrary();
  updateEmailDesignPreview();
  updateAudienceCount();
  renderCampaigns();
  renderContacts();
  renderAnalytics();
  updateHomeStats();
  updateUsage();
  updateBadges();
  renderBillingHistory();
}

async function reloadAppData() {
  const response = await api('session');
  if (!response.authenticated) {
    handleForcedLogout();
    return;
  }

  hydrateState(response);
  initializeApp();
}

async function api(action, method = 'GET', data = null) {
  const options = {
    method,
    credentials: 'same-origin',
    headers: {}
  };

  if (data !== null) {
    options.headers['Content-Type'] = 'application/json';
    options.body = JSON.stringify(data);
  }

  const response = await fetch(`api.php?action=${encodeURIComponent(action)}`, options);
  const payload = await response.json().catch(() => ({}));

  if (!response.ok || payload.success === false) {
    throw new Error(payload.message || 'Request failed.');
  }

  return payload;
}

function showAuthPage() {
  document.getElementById('authScreen')?.classList.remove('hidden');
  document.querySelector('.app-shell')?.classList.add('locked');
}

function showAppPage() {
  document.getElementById('authScreen')?.classList.add('hidden');
  document.querySelector('.app-shell')?.classList.remove('locked');
}

function showAuthView(view) {
  const isSignup = view === 'signup';
  document.getElementById('loginTab')?.classList.toggle('auth-card-hidden', isSignup);
  document.getElementById('signupTab')?.classList.toggle('auth-card-hidden', !isSignup);
  document.getElementById('loginForm')?.classList.toggle('auth-card-hidden', isSignup);
  document.getElementById('signupForm')?.classList.toggle('auth-card-hidden', !isSignup);
  document.getElementById('navLoginBtn')?.classList.toggle('active', !isSignup);
  document.getElementById('navSignupBtn')?.classList.toggle('active', isSignup);
  document.getElementById('cardLoginBtn')?.classList.toggle('active', !isSignup);
  document.getElementById('cardSignupBtn')?.classList.toggle('active', isSignup);
  const targetForm = document.getElementById(isSignup ? 'signupForm' : 'loginForm');
  targetForm?.scrollIntoView({ behavior: 'smooth', block: 'center' });
  targetForm?.querySelector('input')?.focus();
  setAuthMessage('');
}

function setAuthMessage(text, type = 'error') {
  const el = document.getElementById('authMessage');
  if (!el) return;
  el.textContent = text;
  el.classList.toggle('success', type === 'success');
}

async function handleLogin(event) {
  event.preventDefault();

  const email = document.getElementById('loginEmail').value.trim();
  const password = document.getElementById('loginPassword').value;

  try {
    const response = await api('login', 'POST', { email, password });
    hydrateState(response);
    setAuthMessage('Login successful.', 'success');
    initializeApp();
  } catch (error) {
    setAuthMessage(error.message);
  }
}

async function handleSignup(event) {
  event.preventDefault();

  const first_name = document.getElementById('signupFirstName').value.trim();
  const last_name = document.getElementById('signupLastName').value.trim();
  const email = document.getElementById('signupEmail').value.trim();
  const password = document.getElementById('signupPassword').value;

  try {
    const response = await api('register', 'POST', {
      first_name,
      last_name,
      email,
      password
    });

    hydrateState(response);
    setAuthMessage('Account created successfully.', 'success');
    initializeApp();
  } catch (error) {
    setAuthMessage(error.message);
  }
}

async function handleLogout() {
  try {
    await api('logout', 'POST', {});
  } catch (error) {
    // Ignore logout errors and clear the UI anyway.
  }

  handleForcedLogout();
}

function handleForcedLogout() {
  campaigns = [];
  contacts = [];
  profile = {
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    company: '',
    website: '',
    bio: '',
    plan: 'Free',
    avatar_path: ''
  };

  document.getElementById('loginForm')?.reset();
  document.getElementById('signupForm')?.reset();
  showAuthView('login');
  showAuthPage();
}

function navigate(page) {
  document.querySelectorAll('.page').forEach((section) => section.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach((item) => item.classList.remove('active'));
  document.getElementById(`page-${page}`)?.classList.add('active');
  document.querySelector(`.nav-item[data-page="${page}"]`)?.classList.add('active');

}

function toggleTheme() {
  const next = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
  applyTheme(next);
  localStorage.setItem('mf_theme', next);
}

function applyTheme(theme) {
  document.documentElement.setAttribute('data-theme', theme);
  const themeBtn = document.getElementById('themeBtn');
  if (themeBtn) themeBtn.textContent = theme === 'light' ? '☀️' : '🌙';
}

function applyProfile() {
  const fullName = `${profile.first_name || ''} ${profile.last_name || ''}`.trim() || 'User';
  const firstChar = fullName.charAt(0).toUpperCase();

  setText('topbarName', fullName);
  setText('pmName', fullName);
  setText('pmEmail', profile.email || 'you@example.com');
  setText('heroName', `Welcome, ${profile.first_name || 'there'}!`);
  setText('profileName', fullName);
  setText('profileEmail', profile.email || 'you@example.com');
  setText('profilePlanBadge', `${profile.plan || 'Free'} Plan`);
  setText('cpName', `${profile.plan || 'Free'} Plan`);
  setText('cpDesc', getPlanDescription(profile.plan || 'Free'));

  setValue('pFirstName', profile.first_name || '');
  setValue('pLastName', profile.last_name || '');
  setValue('pEmail', profile.email || '');
  setValue('pPhone', profile.phone || '');
  setValue('pCompany', profile.company || '');
  setValue('pWebsite', profile.website || '');
  setValue('pBio', profile.bio || '');
  setValue('billingEmail', profile.email || '');
  setValue('cuName', fullName === 'User' ? '' : fullName);
  setValue('cuEmail', profile.email || '');

  applyAvatar('topbarAvatar', profile.avatar_path, firstChar || 'U');
  applyAvatar('profileAvatar', profile.avatar_path, firstChar || 'U');
}

function fillContactUsFromProfile() {
  const fullName = `${profile.first_name || ''} ${profile.last_name || ''}`.trim();
  setValue('cuName', fullName);
  setValue('cuEmail', profile.email || '');
}

async function submitContactUs() {
  const payload = {
    name: document.getElementById('cuName')?.value.trim() || '',
    email: document.getElementById('cuEmail')?.value.trim() || '',
    subject: document.getElementById('cuSubject')?.value.trim() || '',
    message: document.getElementById('cuMessage')?.value.trim() || ''
  };

  if (!payload.name || !payload.email || !payload.subject || !payload.message) {
    showToast('Please complete all contact form fields.', 'error');
    return;
  }

  try {
    const response = await api('contact_us', 'POST', payload);
    setValue('cuSubject', '');
    setValue('cuMessage', '');
    showToast(response.message || 'Your message has been sent successfully.', 'success');
  } catch (error) {
    showToast(error.message || 'Unable to send your message right now.', 'error');
  }
}

function applyAvatar(id, avatarPath, fallbackLetter) {
  const el = document.getElementById(id);
  if (!el) return;

  const hasImage = Boolean(avatarPath);
  el.style.backgroundImage = hasImage ? `url("${encodeURI(avatarPath)}")` : '';
  el.style.backgroundSize = hasImage ? 'cover' : '';
  el.style.backgroundPosition = hasImage ? 'center' : '';
  el.style.backgroundRepeat = hasImage ? 'no-repeat' : '';

  if (el.firstChild && el.firstChild.nodeType === Node.TEXT_NODE) {
    el.firstChild.nodeValue = hasImage ? '' : fallbackLetter;
  }
}

function updateHomeStats() {
  const sentCampaigns = campaigns.filter((campaign) => campaign.status === 'sent');
  const totalSent = sentCampaigns.reduce((sum, campaign) => sum + Number(campaign.recipients_count || 0), 0);
  const totalOpens = sentCampaigns.reduce((sum, campaign) => sum + Number(campaign.opens_count || 0), 0);
  const totalClicks = sentCampaigns.reduce((sum, campaign) => sum + Number(campaign.clicks_count || 0), 0);
  const openRate = totalSent ? Math.round((totalOpens / totalSent) * 100) : 0;
  const clickRate = totalSent ? Math.round((totalClicks / totalSent) * 100) : 0;

  setText('homeTotalCampaigns', String(campaigns.length));
  setText('homeTotalContacts', String(contacts.length));
  setText('homeOpenRate', `${openRate}%`);
  setText('homeClickRate', `${clickRate}%`);
  setText('homeCampChange', `+${sentCampaigns.length} sent`);
}

function updateUsage() {
  const totalEmails = campaigns.reduce((sum, campaign) => sum + Number(campaign.recipients_count || 0), 0);
  setText('usageCampaigns', String(campaigns.length));
  setText('usageContacts', String(contacts.length));
  setText('usageEmails', String(totalEmails));

  const campBar = document.getElementById('usageCampaignsBar');
  const contactBar = document.getElementById('usageContactsBar');
  const emailBar = document.getElementById('usageEmailsBar');

  if (campBar) campBar.style.width = `${Math.min((campaigns.length / 3) * 100, 100)}%`;
  if (contactBar) contactBar.style.width = `${Math.min((contacts.length / 500) * 100, 100)}%`;
  if (emailBar) emailBar.style.width = `${Math.min((totalEmails / 1000) * 100, 100)}%`;
}

function getPlanDescription(plan) {
  switch ((plan || 'Free').toLowerCase()) {
    case 'pro':
      return 'You are on the Pro plan. Enjoy more contacts and better sending capacity.';
    case 'business':
      return 'You are on the Business plan. Your account is ready for higher email volume.';
    case 'enterprise':
      return 'You are on the Enterprise plan. Your billing is customized for your company.';
    default:
      return 'You are on the free plan. Upgrade to unlock more features.';
  }
}

function updateBadges() {
  setText('campaignBadge', String(campaigns.length));
  setText('contactBadge', String(contacts.length));
}

function renderBillingHistory() {
  const title = document.getElementById('billingHistoryTitle');
  if (!title) return;

  const card = title.closest('.card');
  if (!card) return;

  const rows = billingHistory.length
    ? billingHistory.map((item) => `
      <div class="billing-row">
        <div class="billing-icon">${escHtml(item.icon || '💳')}</div>
        <div class="billing-desc">
          <div class="bname">${escHtml(item.title)}</div>
          <div class="bdate">${escHtml(formatDate(item.created_at))}</div>
        </div>
        <div class="billing-amount">${escHtml(item.amount_label)}</div>
      </div>
    `).join('')
    : `<div class="billing-row"><div class="billing-icon">💳</div><div class="billing-desc"><div class="bname">No billing activity yet</div><div class="bdate">Your future plan changes will appear here.</div></div><div class="billing-amount">$0.00</div></div>`;

  card.innerHTML = `<div class="card-title" id="billingHistoryTitle" style="margin-bottom:16px">Billing History</div>${rows}`;
}

function renderCampaigns() {
  const grid = document.getElementById('campaignsGrid');
  if (!grid) return;

  const query = (document.getElementById('campaignSearch')?.value || '').trim().toLowerCase();
  const filtered = campaigns
    .filter((campaign) => currentFilter === 'all' || campaign.status === currentFilter)
    .filter((campaign) => {
      if (!query) return true;
      return campaign.name.toLowerCase().includes(query) || campaign.subject.toLowerCase().includes(query);
    });

  if (!filtered.length) {
    grid.innerHTML = `<div style="grid-column:1/-1">
      <div class="empty-state">
        <div class="empty-icon">✉️</div>
        <div class="empty-title">${campaigns.length ? 'No matching campaigns' : 'No campaigns yet'}</div>
        <div class="empty-desc">${campaigns.length ? 'Try another search or filter.' : 'Create your first campaign to get started.'}</div>
        <button class="btn btn-primary" onclick="openCampaignModal()">+ Create Campaign</button>
      </div>
    </div>`;
    return;
  }

  grid.innerHTML = filtered.map((campaign, index) => {
    const config = statusConfig[campaign.status] || statusConfig.draft;
    const openRate = campaign.recipients_count ? Math.round((campaign.opens_count / campaign.recipients_count) * 100) : 0;
    const clickRate = campaign.recipients_count ? Math.round((campaign.clicks_count / campaign.recipients_count) * 100) : 0;

    return `
      <div class="campaign-card" style="animation-delay:${index * 0.06}s">
        <div class="campaign-card-top">
          <div class="campaign-type-icon" style="background:rgba(79,124,255,.12)">${typeIcons[index % typeIcons.length]}</div>
          <div class="campaign-menu">
            <button class="btn btn-secondary btn-sm btn-icon" onclick="viewCampaign(${campaign.id})" title="View">👁️</button>
            <button class="btn btn-secondary btn-sm btn-icon" onclick="openCampaignModal(${campaign.id})" title="Edit">✏️</button>
            <button class="btn btn-danger btn-sm btn-icon" onclick="deleteCampaign(${campaign.id})" title="Delete">🗑️</button>
          </div>
        </div>
        <div class="campaign-name">${escHtml(campaign.name)}</div>
        <div class="campaign-subject">${escHtml(campaign.subject)}</div>
        <div style="display:flex;align-items:center;gap:8px;margin-top:10px;flex-wrap:wrap">
          <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:${config.bg};color:${config.color}">
            <span style="width:5px;height:5px;border-radius:50%;background:${config.color};display:inline-block"></span>${config.label}
          </span>
          <span style="font-size:11px;color:var(--text-muted)">${formatDate(campaign.created_at)}</span>
        </div>
        <div class="campaign-stats">
          <div class="cs-item"><div class="cs-val">${campaign.recipients_count || '—'}</div><div class="cs-key">Recipients</div></div>
          <div class="cs-item"><div class="cs-val" style="color:#fb923c">${openRate}%</div><div class="cs-key">Open Rate</div></div>
          <div class="cs-item"><div class="cs-val" style="color:#f472b6">${clickRate}%</div><div class="cs-key">Click Rate</div></div>
        </div>
      </div>
    `;
  }).join('');
}

function filterCampaigns() {
  renderCampaigns();
}

function setFilter(button, filter) {
  currentFilter = filter;
  document.querySelectorAll('.filter-chip').forEach((chip) => chip.classList.remove('active'));
  button?.classList.add('active');
  renderCampaigns();
}

function viewCampaign(id) {
  const selected = campaigns.find((campaign) => Number(campaign.id) === Number(id));
  if (!selected) {
    showToast('Campaign not found.', 'error');
    return;
  }

  openCampaignModal(selected.id);
  showToast('Campaign loaded.', 'info');
}

function openCampaignModal(id = null) {
  resetCampaignModal();

  if (id) {
    const campaign = campaigns.find((item) => Number(item.id) === Number(id));
    if (!campaign) return;

    campaignModalBaseTitle = 'Edit Campaign';
    document.getElementById('campaignModalTitle').textContent = campaignModalBaseTitle;
    document.getElementById('editCampaignId').value = campaign.id;
    setValue('campName', campaign.name);
    setValue('campSenderName', campaign.sender_name || '');
    setValue('campSenderEmail', campaign.sender_email || '');
    setValue('campSubject', campaign.subject || '');
    setValue('campPreviewText', campaign.preview_text || '');
    setValue('campFromName', campaign.sender_name || '');
    setValue('campFromEmail', campaign.sender_email || '');
    setValue('campTemplate', campaign.campaign_template || '');
    setValue('campSelectedImage', campaign.selected_image || '');
    setValue('campImageUrl', campaign.button_link || '');
    setValue('campImageAlt', campaign.image_alt || '');
    setValue('campImageWidth', campaign.image_width || 520);
    setValue('campImageAlign', campaign.image_align || 'center');
    setValue('campEmailTitle', campaign.email_title || '');
    setValue('campEmailBody', campaign.email_body || '');
    setValue('campButtonText', campaign.button_text || '');
    setValue('campButtonLink', campaign.button_link || '');
    setValue('campStatus', campaign.status || 'draft');
    setValue('campScheduleDate', normalizeDateTimeLocal(campaign.schedule_at));
    selectedCampaignContacts = getSelectedCampaignContactIds(campaign.recipients_raw || '');
  }

  renderCampaignContacts();
  renderEmailTemplateGallery();
  renderEmailImageLibrary();
  toggleScheduleField();
  updateAudienceCount();
  updateEmailDesignPreview();
  renderCampaignImageInlinePreview();
  document.getElementById('campaignModal')?.classList.add('open');
}

function closeCampaignModal() {
  document.getElementById('campaignModal')?.classList.remove('open');
}

function resetCampaignModal() {
  campaignModalStep = 'setup';
  campaignModalBaseTitle = 'Create Campaign';
  document.getElementById('campaignModalTitle').textContent = campaignModalBaseTitle;
  selectedCampaignContacts = [];
  selectedTemplateCategory = 'all';
  setValue('editCampaignId', '');
  setValue('campName', '');
  setValue('campSenderName', `${profile.first_name || ''} ${profile.last_name || ''}`.trim());
  setValue('campSenderEmail', profile.email || '');
  setValue('campSubject', '');
  setValue('campPreviewText', '');
  setValue('campFromName', `${profile.first_name || ''} ${profile.last_name || ''}`.trim());
  setValue('campFromEmail', profile.email || '');
  setValue('campTemplate', emailTemplates[0]?.id || '');
  setValue('campSelectedImage', '');
  setValue('campImageUrl', '');
  setValue('campImageAlt', '');
  setValue('campImageWidth', 520);
  setValue('campImageAlign', 'center');
  setValue('campEmailTitle', '');
  setValue('campEmailBody', '');
  setValue('campButtonText', '');
  setValue('campButtonLink', '');
  setValue('campContactSearch', '');
  setValue('campStatus', 'draft');
  setValue('campScheduleDate', '');
  const imageUpload = document.getElementById('campImageUpload');
  if (imageUpload) imageUpload.value = '';
  renderCampaignContacts();
  renderEmailTemplateGallery();
  renderEmailImageLibrary();
  applySelectedTemplateDefaults();
  updateEmailDesignPreview();
  updateAudienceCount();
  renderCampaignImageInlinePreview();
}

function showCampaignStep() {
  const title = document.getElementById('campaignModalTitle');
  if (title) title.textContent = campaignModalBaseTitle;
}

function isCampaignSetupComplete() {
  const name = document.getElementById('campName')?.value.trim() || '';
  const senderName = document.getElementById('campSenderName')?.value.trim() || '';
  const senderEmail = document.getElementById('campSenderEmail')?.value.trim() || '';
  return Boolean(name && senderName && senderEmail && selectedCampaignContacts.length);
}

function updateCampaignStepActions() {
  const continueBtn = document.getElementById('campToDesignBtn');
  if (continueBtn) continueBtn.style.display = 'none';
}

function openCampaignDesignStep() {
  showCampaignStep();
}

function toggleScheduleField() {
  const status = document.getElementById('campStatus')?.value;
  const group = document.getElementById('schedDateGroup');
  if (group) group.style.display = status === 'scheduled' ? 'block' : 'none';
}

async function saveCampaign() {
  const id = document.getElementById('editCampaignId').value;
  const name = document.getElementById('campName').value.trim();
  const subject = document.getElementById('campSubject').value.trim();
  const sender_name = (document.getElementById('campFromName')?.value || document.getElementById('campSenderName')?.value || '').trim();
  const sender_email = (document.getElementById('campFromEmail')?.value || document.getElementById('campSenderEmail')?.value || '').trim();
  const preview_text = document.getElementById('campPreviewText').value.trim();
  const campaign_template = document.getElementById('campTemplate').value.trim();
  const selected_image = document.getElementById('campSelectedImage').value.trim();
  const image_alt = document.getElementById('campImageAlt').value.trim();
  const image_width = Number(document.getElementById('campImageWidth').value || 520);
  const image_align = document.getElementById('campImageAlign').value;
  const email_title = document.getElementById('campEmailTitle').value.trim();
  const email_body = document.getElementById('campEmailBody').value.trim();
  const button_text = document.getElementById('campButtonText').value.trim();
  const button_link = document.getElementById('campButtonLink').value.trim();
  const content = buildCampaignEmailHtml({
    subject,
    preview_text,
    sender_name,
    sender_email,
    campaign_template,
    selected_image,
    image_alt,
    image_width,
    image_align,
    email_title,
    email_body,
    button_text,
    button_link
  });
  const recipients_raw = getSelectedCampaignRecipientEmails().join('\n');
  const audience_tags = '';
  const audience_notes = '';
  const status = document.getElementById('campStatus').value;
  const schedule_at = document.getElementById('campScheduleDate').value || null;

  if (!name) {
    showToast('Campaign name is required.', 'error');
    return;
  }

  if (!subject) {
    showToast('Subject is required.', 'error');
    return;
  }

  if (!sender_name || !sender_email) {
    showToast('From name and from email are required.', 'error');
    return;
  }

  if (!recipients_raw) {
    showToast('Select at least one contact from saved contacts.', 'error');
    return;
  }

  try {
    const response = await api('campaigns', 'POST', {
      id,
      name,
      subject,
      sender_name,
      sender_email,
      preview_text,
      campaign_template,
      selected_image,
      image_alt,
      image_width,
      image_align,
      email_title,
      email_body,
      button_text,
      button_link,
      content,
      recipients_raw,
      audience_tags,
      audience_notes,
      status,
      schedule_at
    });

    campaigns = response.campaigns || campaigns;
    closeCampaignModal();
    renderCampaigns();
    renderAnalytics();
    updateHomeStats();
    updateUsage();
    updateBadges();
    showToast(response.message || 'Campaign saved successfully.', 'success');
  } catch (error) {
    showToast(error.message, 'error');
  }
}

async function deleteCampaign(id) {
  if (!confirm('Delete this campaign?')) return;

  try {
    const response = await api('campaign_delete', 'POST', { id });
    campaigns = response.campaigns || campaigns.filter((campaign) => Number(campaign.id) !== Number(id));
    renderCampaigns();
    renderAnalytics();
    updateHomeStats();
    updateUsage();
    updateBadges();
    showToast(response.message || 'Campaign deleted.', 'success');
  } catch (error) {
    showToast(error.message, 'error');
  }
}

function renderAnalytics() {
  const sent = campaigns.filter((campaign) => campaign.status === 'sent');
  const totalSent = sent.reduce((sum, campaign) => sum + Number(campaign.recipients_count || 0), 0);
  const totalOpens = sent.reduce((sum, campaign) => sum + Number(campaign.opens_count || 0), 0);
  const totalClicks = sent.reduce((sum, campaign) => sum + Number(campaign.clicks_count || 0), 0);
  const delivered = Math.round(totalSent * 0.97);
  const clickRate = totalSent ? Math.round((totalClicks / totalSent) * 100) : 0;

  setText('aTotalCampaigns', String(campaigns.length));
  setText('aTotalSent', String(totalSent));
  setText('aClickRate', `${clickRate}%`);
  setText('mSent', String(totalSent));
  setText('mDelivered', String(delivered));
  setText('mClicked', String(totalClicks));

  const clickFill = document.getElementById('clickFill');
  if (clickFill) clickFill.style.width = `${clickRate}%`;

  renderAnalyticsTable();
  renderActivityTimeline(sent);
  renderCharts(sent);
}

function renderAnalyticsTable() {
  const tbody = document.getElementById('analyticsTable');
  const empty = document.getElementById('analyticsTableEmpty');
  if (!tbody || !empty) return;

  if (!campaigns.length) {
    tbody.innerHTML = '';
    empty.style.display = 'block';
    return;
  }

  empty.style.display = 'none';
  tbody.innerHTML = campaigns.map((campaign) => {
    const openRate = campaign.recipients_count ? Math.round((campaign.opens_count / campaign.recipients_count) * 100) : 0;
    const clickRate = campaign.recipients_count ? Math.round((campaign.clicks_count / campaign.recipients_count) * 100) : 0;
    return `
      <tr>
        <td>${escHtml(campaign.name)}</td>
        <td>${escHtml(campaign.status)}</td>
        <td>${campaign.recipients_count || 0}</td>
        <td>${openRate}%</td>
        <td>${clickRate}%</td>
        <td>${formatDate(campaign.created_at)}</td>
      </tr>
    `;
  }).join('');
}

function renderActivityTimeline(sentCampaigns) {
  const wrapper = document.getElementById('activityTimeline');
  if (!wrapper) return;

  if (!campaigns.length) {
    wrapper.innerHTML = `<div class="no-results">No campaign activity yet.</div>`;
    return;
  }

  wrapper.innerHTML = campaigns.slice(0, 5).map((campaign) => `
    <div class="activity-item">
      <div class="activity-dot" style="background:${(statusConfig[campaign.status] || statusConfig.draft).color}"></div>
      <div class="activity-text"><strong>${escHtml(campaign.name)}</strong> is ${escHtml(campaign.status)}</div>
      <div class="activity-time">${formatDate(campaign.created_at)}</div>
    </div>
  `).join('');
}

function renderCharts(sentCampaigns) {
  if (typeof Chart === 'undefined') return;

  const performanceCanvas = document.getElementById('performanceChart');
  const statusCanvas = document.getElementById('statusChart');
  if (!performanceCanvas || !statusCanvas) return;

  destroyChart('performanceChart');
  destroyChart('statusChart');

  charts.performanceChart = new Chart(performanceCanvas, {
    type: 'bar',
    data: {
      labels: sentCampaigns.map((campaign) => campaign.name).slice(0, 6),
      datasets: [
        {
          label: 'Recipients',
          data: sentCampaigns.map((campaign) => Number(campaign.recipients_count || 0)).slice(0, 6),
          backgroundColor: '#4f7cff'
        },
        {
          label: 'Opens',
          data: sentCampaigns.map((campaign) => Number(campaign.opens_count || 0)).slice(0, 6),
          backgroundColor: '#34d399'
        }
      ]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });

  charts.statusChart = new Chart(statusCanvas, {
    type: 'doughnut',
    data: {
      labels: ['Draft', 'Scheduled', 'Sent'],
      datasets: [{
        data: [
          campaigns.filter((campaign) => campaign.status === 'draft').length,
          campaigns.filter((campaign) => campaign.status === 'scheduled').length,
          campaigns.filter((campaign) => campaign.status === 'sent').length
        ],
        backgroundColor: ['#94a3b8', '#fb923c', '#34d399']
      }]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });
}

function destroyChart(key) {
  if (charts[key]) {
    charts[key].destroy();
    delete charts[key];
  }
}

function openContactModal() {
  document.getElementById('contactModalTitle').textContent = document.getElementById('editContactId').value ? 'Edit Contact' : 'Add Contact';
  document.getElementById('contactModal')?.classList.add('open');
}

function closeContactModal() {
  document.getElementById('contactModal')?.classList.remove('open');
  setValue('editContactId', '');
  setValue('contPasteArea', '');
  setValue('contCompany', '');
  setValue('contTags', '');
  setValue('contStatus', 'active');
}

async function saveContact() {
  const id = document.getElementById('editContactId').value;
  const emails = parseRecipients(document.getElementById('contPasteArea').value);
  const company = document.getElementById('contCompany').value.trim();
  const tags = document.getElementById('contTags').value.trim();
  const status = document.getElementById('contStatus').value;

  if (!emails.length) {
    showToast('Please add at least one valid email address.', 'error');
    return;
  }

  try {
    const response = await api('contacts', 'POST', {
      id,
      emails,
      company,
      tags,
      status
    });

    contacts = response.contacts || contacts;
    closeContactModal();
    renderContacts();
    updateHomeStats();
    updateUsage();
    updateBadges();
    showToast(response.message || 'Contact saved successfully.', 'success');
  } catch (error) {
    showToast(error.message, 'error');
  }
}

function renderContacts() {
  const tbody = document.getElementById('contactsTable');
  const empty = document.getElementById('noContactsMsg');
  if (!tbody || !empty) return;

  const query = (document.getElementById('contactSearch')?.value || '').trim().toLowerCase();
  const filtered = contacts.filter((contact) => {
    if (!query) return true;
    return (
      contact.email.toLowerCase().includes(query) ||
      (contact.company || '').toLowerCase().includes(query) ||
      (contact.tags_text || '').toLowerCase().includes(query)
    );
  });

  if (!filtered.length) {
    tbody.innerHTML = '';
    empty.style.display = 'block';
    setText('contactCountLabel', '0');
    return;
  }

  empty.style.display = 'none';
  setText('contactCountLabel', String(filtered.length));
  tbody.innerHTML = filtered.map((contact) => {
    const tagsHtml = (contact.tags || []).map((tag) => `<span class="tag">${escHtml(tag)}</span>`).join('');
    return `
      <tr data-contact-id="${contact.id}">
        <td><input type="checkbox" class="contact-checkbox" value="${contact.id}" onchange="updateDeleteBtn()"></td>
        <td>${escHtml(contact.name || contact.email.split('@')[0])}</td>
        <td>${escHtml(contact.email)}</td>
        <td>${escHtml(contact.company || '-')}</td>
        <td>${tagsHtml || '<span style="opacity:0.5">No tags</span>'}</td>
        <td><span class="status-badge ${escAttr((contact.status || 'active').toLowerCase())}">${escHtml(contact.status || 'active')}</span></td>
        <td>${formatDate(contact.created_at)}</td>
        <td>
          <button class="btn btn-secondary btn-sm" type="button" onclick="editContact(${contact.id})">Edit</button>
          <button class="btn btn-danger btn-sm" type="button" onclick="deleteContact(${contact.id})">Delete</button>
        </td>
      </tr>
    `;
  }).join('');
}

function filterContacts() {
  renderContacts();
}

function editContact(id) {
  const contact = contacts.find((item) => Number(item.id) === Number(id));
  if (!contact) return;

  setValue('editContactId', contact.id);
  setValue('contPasteArea', contact.email);
  setValue('contCompany', contact.company || '');
  setValue('contTags', contact.tags_text || '');
  setValue('contStatus', contact.status || 'active');
  document.getElementById('contactModalTitle').textContent = 'Edit Contact';
  openContactModal();
}

async function deleteContact(id) {
  if (!confirm('Delete this contact?')) return;

  try {
    const response = await api('contact_delete', 'POST', { ids: [id] });
    contacts = response.contacts || contacts.filter((contact) => Number(contact.id) !== Number(id));
    renderContacts();
    updateHomeStats();
    updateUsage();
    updateBadges();
    updateDeleteBtn();
    showToast(response.message || 'Contact deleted.', 'success');
  } catch (error) {
    showToast(error.message, 'error');
  }
}

function toggleSelectAll(checkbox) {
  document.querySelectorAll('.contact-checkbox').forEach((item) => {
    item.checked = checkbox.checked;
  });
  updateDeleteBtn();
}

function updateDeleteBtn() {
  const checked = Array.from(document.querySelectorAll('.contact-checkbox:checked')).map((input) => Number(input.value));
  const deleteBtn = document.getElementById('deleteSelectedBtn');
  if (deleteBtn) deleteBtn.style.display = checked.length ? 'inline-flex' : 'none';
}

async function deleteSelectedContacts() {
  const ids = Array.from(document.querySelectorAll('.contact-checkbox:checked')).map((input) => Number(input.value));
  if (!ids.length) return;
  if (!confirm('Delete selected contacts?')) return;

  try {
    const response = await api('contact_delete', 'POST', { ids });
    contacts = response.contacts || contacts.filter((contact) => !ids.includes(Number(contact.id)));
    const selectAll = document.getElementById('selectAll');
    if (selectAll) selectAll.checked = false;
    renderContacts();
    updateHomeStats();
    updateUsage();
    updateBadges();
    updateDeleteBtn();
    showToast(response.message || 'Selected contacts deleted.', 'success');
  } catch (error) {
    showToast(error.message, 'error');
  }
}

async function importCSV(event) {
  const file = event.target.files?.[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = async () => {
    try {
      const text = String(reader.result || '');
      const importedContacts = parseContactsCsv(text);
      if (!importedContacts.length) {
        showToast('No valid contacts were found in the CSV file.', 'error');
        return;
      }

      const response = await api('contacts', 'POST', { contacts: importedContacts });
      contacts = response.contacts || contacts;
      renderContacts();
      updateHomeStats();
      updateUsage();
      updateBadges();
      updateDeleteBtn();
      showToast(response.message || `${importedContacts.length} contacts imported successfully.`, 'success');
    } catch (error) {
      showToast(error.message || 'CSV import failed.', 'error');
    } finally {
      event.target.value = '';
    }
  };

  reader.onerror = () => {
    showToast('Unable to read the selected CSV file.', 'error');
    event.target.value = '';
  };

  reader.readAsText(file);
}

async function saveProfile() {
  const payload = {
    first_name: document.getElementById('pFirstName').value.trim(),
    last_name: document.getElementById('pLastName').value.trim(),
    email: document.getElementById('pEmail').value.trim(),
    phone: document.getElementById('pPhone').value.trim(),
    company: document.getElementById('pCompany').value.trim(),
    website: document.getElementById('pWebsite').value.trim(),
    bio: document.getElementById('pBio').value.trim()
  };

  try {
    const response = await api('profile_update', 'POST', payload);
    profile = { ...profile, ...payload, ...(response.user || {}) };
    applyProfile();
    showToast(response.message || 'Profile updated successfully.', 'success');
  } catch (error) {
    showToast(error.message, 'error');
  }
}

async function changePassword() {
  const current_password = document.getElementById('currentPass').value;
  const new_password = document.getElementById('newPass').value;
  const confirm_password = document.getElementById('confirmPass').value;

  if (new_password !== confirm_password) {
    showToast('Password confirmation does not match.', 'error');
    return;
  }

  try {
    const response = await api('password_change', 'POST', {
      current_password,
      new_password
    });
    document.getElementById('currentPass').value = '';
    document.getElementById('newPass').value = '';
    document.getElementById('confirmPass').value = '';
    checkPasswordStrength('');
    showToast(response.message || 'Password updated successfully.', 'success');
  } catch (error) {
    showToast(error.message, 'error');
  }
}

async function deleteAccount() {
  if (!confirm('Delete your account and all your data?')) return;

  try {
    await api('delete_account', 'POST', {});
    showToast('Account deleted successfully.', 'success');
    handleForcedLogout();
  } catch (error) {
    showToast(error.message, 'error');
  }
}

function toggleProfileMenu(event) {
  event?.stopPropagation();
  document.getElementById('profileMenuDrop')?.classList.toggle('open');
}

function closeAllDropdowns() {
  document.getElementById('profileMenuDrop')?.classList.remove('open');
}

function switchProfileTab(id, tab = null) {
  document.querySelectorAll('.tab-panel').forEach((panel) => panel.classList.remove('active'));
  document.querySelectorAll('.profile-tab').forEach((item) => item.classList.remove('active'));
  document.getElementById(id)?.classList.add('active');
  if (tab) tab.classList.add('active');
  else {
    document.querySelector(`.profile-tab[onclick*="${id}"]`)?.classList.add('active');
  }
}

function toggleSwitch(element) {
  element?.classList.toggle('on');
}

function saveNotifications() {
  showToast('Preferences saved on the interface.', 'success');
}

function globalSearch(query) {
  const value = query.trim().toLowerCase();
  if (!value) return;

  if (campaigns.some((campaign) => campaign.name.toLowerCase().includes(value) || campaign.subject.toLowerCase().includes(value))) {
    navigate('campaigns');
    setValue('campaignSearch', query);
    renderCampaigns();
    return;
  }

  navigate('contacts');
  setValue('contactSearch', query);
  renderContacts();
}

function toggleBilling() {
  isYearly = !isYearly;
  document.getElementById('billingToggle')?.classList.toggle('on', isYearly);
  document.getElementById('monthlyLabel')?.classList.toggle('active', !isYearly);
  document.getElementById('yearlyLabel')?.classList.toggle('active', isYearly);
  const proPrice = document.getElementById('proPrice');
  const bizPrice = document.getElementById('bizPrice');
  const proPeriod = document.getElementById('proPeriod');
  const bizPeriod = document.getElementById('bizPeriod');

  if (proPrice) proPrice.innerHTML = isYearly ? '$24<span>/mo</span>' : '$29<span>/mo</span>';
  if (bizPrice) bizPrice.innerHTML = isYearly ? '$66<span>/mo</span>' : '$79<span>/mo</span>';
  if (proPeriod) proPeriod.textContent = isYearly ? 'Billed annually ($288/yr)' : 'Billed monthly';
  if (bizPeriod) bizPeriod.textContent = isYearly ? 'Billed annually ($792/yr)' : 'Billed monthly';
}

function selectPlan(plan) {
  if ((profile.plan || 'Free') === plan) {
    showToast(`${plan} is already your current plan.`, 'info');
    return;
  }

  if (plan === 'Free') {
    updatePlan(plan);
    return;
  }

  openCheckout(plan);
}

function openCheckout(plan) {
  checkoutPlan = plan;
  setText('checkoutPlanLabel', plan);
  setValue('checkoutPlan', plan);
  navigate('payment');
}

function handlePaymentSubmit(event) {
  event.preventDefault();
  updatePlan(checkoutPlan || document.getElementById('checkoutPlan').value, {
    billing_email: document.getElementById('billingEmail').value.trim(),
    cardholder_name: document.getElementById('cardholderName').value.trim(),
    card_number: document.getElementById('cardNumber').value.trim(),
    card_expiry: document.getElementById('cardExpiry').value.trim(),
    card_cvc: document.getElementById('cardCvc').value.trim()
  });
}

async function updatePlan(plan, paymentData = null) {
  if (!plan) {
    showToast('Please select a plan first.', 'error');
    return;
  }

  try {
    const response = await api(paymentData ? 'checkout_plan' : 'change_plan', 'POST', {
      plan,
      payment: paymentData
    });

    profile = response.user || profile;
    billingHistory = response.billing_history || billingHistory;
    applyProfile();
    renderBillingHistory();

    if (paymentData) {
      document.getElementById('paymentForm')?.reset();
      navigate('profile');
      switchProfileTab('tab-billing');
    }

    showToast(response.message || 'Plan updated successfully.', 'success');
  } catch (error) {
    showToast(error.message, 'error');
  }
}

function checkPasswordStrength(value) {
  const fill = document.getElementById('passStrength');
  const label = document.getElementById('passStrengthLabel');
  if (!fill || !label) return;

  let score = 0;
  if (value.length >= 8) score += 25;
  if (/[A-Z]/.test(value)) score += 25;
  if (/[0-9]/.test(value)) score += 25;
  if (/[^A-Za-z0-9]/.test(value)) score += 25;

  fill.style.width = `${score}%`;
  fill.style.background = score >= 75 ? '#34d399' : score >= 50 ? '#fb923c' : '#f87171';
  label.textContent = value ? (score >= 75 ? 'Strong password' : score >= 50 ? 'Medium password' : 'Weak password') : 'Enter a password';
}

function changeAvatar() {
  document.getElementById('avatarInput')?.click();
}

async function handleAvatarSelected(event) {
  const file = event.target.files?.[0];
  if (!file) return;

  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
  if (!allowedTypes.includes(file.type)) {
    showToast('Please choose PNG, JPG, or WEBP image.', 'error');
    event.target.value = '';
    return;
  }

  if (file.size > 2 * 1024 * 1024) {
    showToast('Profile photo must be smaller than 2MB.', 'error');
    event.target.value = '';
    return;
  }

  const formData = new FormData();
  formData.append('avatar', file);

  try {
    const response = await fetch('api.php?action=upload_avatar', {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
    });
    const payload = await response.json().catch(() => ({}));

    if (!response.ok || payload.success === false) {
      throw new Error(payload.message || 'Avatar upload failed.');
    }

    profile = payload.user || profile;
    applyProfile();
    showToast(payload.message || 'Profile photo updated successfully.', 'success');
  } catch (error) {
    showToast(error.message || 'Avatar upload failed.', 'error');
  } finally {
    event.target.value = '';
  }
}

function updateAudienceCount() {
  setText('audienceCount', String(selectedCampaignContacts.length));
  updateCampaignStepActions();
}

function renderCampaignContacts() {
  const wrapper = document.getElementById('campaignContactsList');
  if (!wrapper) return;

  if (!contacts.length) {
    wrapper.innerHTML = `<div class="no-results">No saved contacts yet. Add contacts first.</div>`;
    updateAudienceCount();
    return;
  }

  const filteredContacts = getFilteredCampaignContacts();

  if (!filteredContacts.length) {
    wrapper.innerHTML = `<div class="no-results">No contacts match your search.</div>`;
    updateAudienceCount();
    return;
  }

  wrapper.innerHTML = filteredContacts.map((contact) => {
    const id = Number(contact.id);
    const checked = selectedCampaignContacts.includes(id);
    const sub = [contact.name || '', contact.company || ''].filter(Boolean).join(' • ');
    return `
      <label class="campaign-contact-item ${checked ? 'active' : ''}">
        <input type="checkbox" ${checked ? 'checked' : ''} onchange="toggleCampaignContact(${id}, this.checked)">
        <div class="campaign-contact-main">
          <div class="campaign-contact-title">${escHtml(contact.email)}</div>
          <div class="campaign-contact-sub">${escHtml(sub || 'Saved contact')}</div>
        </div>
      </label>
    `;
  }).join('');

  updateAudienceCount();
}

function getFilteredCampaignContacts() {
  const query = (document.getElementById('campContactSearch')?.value || '').trim().toLowerCase();
  return contacts.filter((contact) => {
    if (!query) return true;
    const email = String(contact.email || '').toLowerCase();
    const company = String(contact.company || '').toLowerCase();
    const name = String(contact.name || '').toLowerCase();
    return email.includes(query) || company.includes(query) || name.includes(query);
  });
}

function toggleCampaignContact(contactId, isChecked) {
  const id = Number(contactId);
  if (!Number.isFinite(id)) return;

  if (isChecked) {
    if (!selectedCampaignContacts.includes(id)) {
      selectedCampaignContacts.push(id);
    }
  } else {
    selectedCampaignContacts = selectedCampaignContacts.filter((item) => item !== id);
  }

  renderCampaignContacts();
}

function selectAllCampaignContacts() {
  const visibleIds = getFilteredCampaignContacts()
    .map((contact) => Number(contact.id))
    .filter((id) => Number.isFinite(id));

  visibleIds.forEach((id) => {
    if (!selectedCampaignContacts.includes(id)) {
      selectedCampaignContacts.push(id);
    }
  });

  renderCampaignContacts();
}

function clearCampaignContacts() {
  selectedCampaignContacts = [];
  renderCampaignContacts();
}

function syncSenderFields(type) {
  if (type === 'name') {
    setValue('campSenderName', document.getElementById('campFromName')?.value || '');
  } else if (type === 'email') {
    setValue('campSenderEmail', document.getElementById('campFromEmail')?.value || '');
  }
}

function syncEmailBuilderFromSender(type) {
  if (type === 'name') {
    setValue('campFromName', document.getElementById('campSenderName')?.value || '');
  } else if (type === 'email') {
    setValue('campFromEmail', document.getElementById('campSenderEmail')?.value || '');
  }
  updateEmailDesignPreview();
}

function syncCampaignImageUrl() {
  const url = document.getElementById('campImageUrl')?.value.trim() || '';
  setValue('campButtonLink', url);
}

async function handleCampaignImageUpload(event) {
  const file = event.target.files?.[0];
  if (!file) return;

  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
  if (!allowedTypes.includes(file.type)) {
    showToast('Please choose JPG, PNG, or WEBP image.', 'error');
    event.target.value = '';
    return;
  }

  if (file.size > 5 * 1024 * 1024) {
    showToast('Product image must be smaller than 5MB.', 'error');
    event.target.value = '';
    return;
  }

  const formData = new FormData();
  formData.append('email_image', file);

  try {
    const response = await fetch('api.php?action=upload_email_image', {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
    });
    const payload = await response.json().catch(() => ({}));

    if (!response.ok || payload.success === false) {
      throw new Error(payload.message || 'Image upload failed.');
    }

    emailLibraryImages = Array.isArray(payload.email_images) ? payload.email_images : emailLibraryImages;
    setValue('campSelectedImage', payload.image_path || '');
    setValue('campImageAlt', payload.image_name || file.name.replace(/\.[^.]+$/, ''));
    renderCampaignImageInlinePreview();
    renderEmailImageLibrary();
    updateEmailDesignPreview();
    showToast(payload.message || 'Product image uploaded successfully.', 'success');
  } catch (error) {
    showToast(error.message || 'Image upload failed.', 'error');
  } finally {
    event.target.value = '';
  }
}

function renderCampaignImageInlinePreview() {
  const wrapper = document.getElementById('campImageInlinePreview');
  if (!wrapper) return;

  const url = document.getElementById('campSelectedImage')?.value.trim() || '';
  if (!url) {
    wrapper.innerHTML = '';
    return;
  }

  wrapper.innerHTML = `
    <div class="campaign-image-inline-card">
      <img src="${escAttr(url)}" alt="${escAttr(document.getElementById('campImageAlt')?.value.trim() || 'Product image')}">
      <div class="campaign-image-inline-meta">Selected product image</div>
    </div>
  `;
}

function getTemplateCategories() {
  return ['all', ...new Set(emailTemplates.map((template) => template.category))];
}

function renderEmailTemplateGallery() {
  const cats = document.getElementById('emailTemplateCats');
  const grid = document.getElementById('emailTemplateGrid');
  if (!cats || !grid) return;

  cats.innerHTML = '';

  const selectedTemplateId = document.getElementById('campTemplate')?.value || '';
  const visibleTemplates = emailTemplates;

  grid.innerHTML = visibleTemplates.map((template) => `
    <button class="email-template-card ${selectedTemplateId === template.id ? 'active' : ''}" type="button" onclick="selectEmailTemplate('${escAttr(template.id)}')">
      <div class="email-template-preview">
        <div class="email-template-line short"></div>
        <div class="email-template-line mid"></div>
        <div class="email-template-line"></div>
        <div class="email-template-line short"></div>
      </div>
    </button>
  `).join('');
}

function setEmailTemplateCategory(category) {
  selectedTemplateCategory = category;
  renderEmailTemplateGallery();
}

function selectEmailTemplate(templateId) {
  setValue('campTemplate', templateId);
  renderEmailTemplateGallery();
  applySelectedTemplateDefaults();
  updateEmailDesignPreview();
}

function applySelectedTemplateDefaults() {
  const template = emailTemplates.find((item) => item.id === (document.getElementById('campTemplate')?.value || ''));
  if (!template) return;

  if (!document.getElementById('campEmailTitle')?.value.trim()) {
    setValue('campEmailTitle', template.title);
  }
  if (!document.getElementById('campEmailBody')?.value.trim()) {
    setValue('campEmailBody', template.body);
  }
  if (!document.getElementById('campButtonText')?.value.trim()) {
    setValue('campButtonText', template.button_text);
  }
  if (!document.getElementById('campSubject')?.value.trim()) {
    setValue('campSubject', template.title);
  }
}

function renderEmailImageLibrary() {
  const grid = document.getElementById('emailImageGrid');
  if (!grid) return;

  if (!emailLibraryImages.length) {
    grid.innerHTML = `<div class="no-results">No email images were found in assets/email-images or uploads/email-images.</div>`;
    return;
  }

  const selectedImage = document.getElementById('campSelectedImage')?.value || '';
  grid.innerHTML = emailLibraryImages.map((image) => `
    <button class="email-image-card ${selectedImage === image.path ? 'active' : ''}" type="button" onclick="selectEmailImage('${escAttr(image.path)}')">
      <div class="email-image-thumb"><img src="${escAttr(image.path)}" alt="${escAttr(image.name)}"></div>
      <div class="email-image-meta">
        <div class="email-image-name">${escHtml(image.name)}</div>
      </div>
    </button>
  `).join('');
}

function selectEmailImage(path) {
  setValue('campSelectedImage', path);
  if (!document.getElementById('campImageAlt')?.value.trim()) {
    const match = emailLibraryImages.find((image) => image.path === path);
    setValue('campImageAlt', match?.name || '');
  }
  renderCampaignImageInlinePreview();
  renderEmailImageLibrary();
  updateEmailDesignPreview();
}

function replaceSelectedEmailImage() {
  const current = document.getElementById('campSelectedImage')?.value || '';
  if (!emailLibraryImages.length) return;
  const currentIndex = Math.max(0, emailLibraryImages.findIndex((image) => image.path === current));
  const next = emailLibraryImages[(currentIndex + 1) % emailLibraryImages.length];
  selectEmailImage(next.path);
}

function clearSelectedEmailImage() {
  setValue('campSelectedImage', '');
  setValue('campImageAlt', '');
  renderCampaignImageInlinePreview();
  renderEmailImageLibrary();
  updateEmailDesignPreview();
}

function updateEmailDesignPreview() {
  const preview = document.getElementById('emailClientPreview');
  if (!preview) return;

  const state = getEmailDesignState();
  const template = state.template;

  preview.innerHTML = `
    <div class="email-preview-frame">
      <div class="email-preview-header">
        <div class="email-preview-subject email-preview-editable" contenteditable="true" spellcheck="false" data-field="campSubject" oninput="syncPreviewEditable(this)" onblur="normalizePreviewEditable()">${escHtml(state.subject)}</div>
        <div class="email-preview-meta">From <span class="email-preview-editable-inline" contenteditable="true" spellcheck="false" data-field="campFromName" oninput="syncPreviewEditable(this)" onblur="normalizePreviewEditable()">${escHtml(state.fromName)}</span> &lt;<span class="email-preview-editable-inline" contenteditable="true" spellcheck="false" data-field="campFromEmail" oninput="syncPreviewEditable(this)" onblur="normalizePreviewEditable()">${escHtml(state.fromEmail)}</span>&gt;</div>
        <div class="email-preview-meta email-preview-editable-inline" contenteditable="true" spellcheck="false" data-field="campPreviewText" oninput="syncPreviewEditable(this)" onblur="normalizePreviewEditable()">${escHtml(state.previewText)}</div>
      </div>
      <div class="email-preview-body">
        <div class="email-preview-kicker">${escHtml(template.kicker)}</div>
        <div class="email-preview-title email-preview-editable" contenteditable="true" spellcheck="false" data-field="campEmailTitle" oninput="syncPreviewEditable(this)" onblur="normalizePreviewEditable()">${escHtml(state.title)}</div>
        ${state.selectedImage
          ? `<div class="email-preview-image-wrap align-${escAttr(state.imageAlign)}"><img src="${escAttr(state.selectedImage)}" alt="${escAttr(state.imageAlt)}" style="width:${state.imageWidth}px"></div>`
          : `<button class="email-preview-image-empty" type="button" onclick="document.getElementById('emailImageGrid')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' })">Choose a product image below</button>`}
        <div class="email-preview-text email-preview-editable" contenteditable="true" data-field="campEmailBody" oninput="syncPreviewEditable(this)" onblur="normalizePreviewEditable()">${escHtml(state.body).replace(/\n/g, '<br>')}</div>
        ${state.buttonText ? `<button class="email-preview-cta email-preview-editable-button" type="button" contenteditable="true" spellcheck="false" data-field="campButtonText" oninput="syncPreviewEditable(this)" onblur="normalizePreviewEditable()" style="background:${template.accent}">${escHtml(state.buttonText)}</button>` : ''}
        <div class="email-preview-footer">Designed inside your MailFlow builder preview.</div>
      </div>
    </div>
  `;
}

function syncPreviewEditable(element) {
  if (!element?.dataset?.field) return;

  const field = element.dataset.field;
  const value = readPreviewEditableValue(element, field);
  setValue(field, value);

  if (field === 'campFromName') {
    syncSenderFields('name');
  } else if (field === 'campFromEmail') {
    syncSenderFields('email');
  }
}

function normalizePreviewEditable() {
  updateEmailDesignPreview();
}

function readPreviewEditableValue(element, field) {
  const raw = field === 'campEmailBody'
    ? (element.innerText || element.textContent || '')
    : (element.textContent || '');

  return raw
    .replace(/\u00a0/g, ' ')
    .replace(/\r/g, '')
    .replace(/\n{3,}/g, '\n\n')
    .trim();
}

function getEmailDesignState() {
  const template = emailTemplates.find((item) => item.id === (document.getElementById('campTemplate')?.value || '')) || emailTemplates[0];
  return {
    template,
    subject: document.getElementById('campSubject')?.value.trim() || 'Your campaign subject',
    previewText: document.getElementById('campPreviewText')?.value.trim() || 'Preview text for your email will appear here.',
    fromName: document.getElementById('campFromName')?.value.trim() || 'Your Brand',
    fromEmail: document.getElementById('campFromEmail')?.value.trim() || 'you@example.com',
    title: document.getElementById('campEmailTitle')?.value.trim() || template.title,
    body: document.getElementById('campEmailBody')?.value.trim() || template.body,
    buttonText: document.getElementById('campButtonText')?.value.trim() || template.button_text,
    buttonLink: document.getElementById('campButtonLink')?.value.trim() || '#',
    selectedImage: document.getElementById('campSelectedImage')?.value.trim() || '',
    imageAlt: document.getElementById('campImageAlt')?.value.trim() || (document.getElementById('campEmailTitle')?.value.trim() || template.title),
    imageWidth: Math.min(680, Math.max(220, Number(document.getElementById('campImageWidth')?.value || 520))),
    imageAlign: document.getElementById('campImageAlign')?.value || 'center'
  };
}

function buildCampaignEmailHtml(data) {
  const template = emailTemplates.find((item) => item.id === data.campaign_template) || emailTemplates[0];
  const safeTitle = escHtml(data.email_title || template.title);
  const safeBody = escHtml(data.email_body || template.body).replace(/\n/g, '<br>');
  const safeSubject = escHtml(data.subject || '');
  const safePreview = escHtml(data.preview_text || '');
  const safeFromName = escHtml(data.sender_name || '');
  const safeFromEmail = escHtml(data.sender_email || '');
  const safeButtonText = escHtml(data.button_text || '');
  const safeButtonLink = escAttr(data.button_link || '#');
  const safeImage = data.selected_image ? `<div style="text-align:${escAttr(data.image_align || 'center')};margin:18px 0;"><img src="${escAttr(data.selected_image)}" alt="${escAttr(data.image_alt || '')}" style="width:${Math.min(680, Math.max(220, Number(data.image_width || 520)))}px;max-width:100%;border-radius:16px;display:inline-block;"></div>` : '';
  const safeCta = safeButtonText ? `<a href="${safeButtonLink}" style="display:inline-block;margin-top:20px;padding:12px 18px;border-radius:12px;background:${template.accent};color:#ffffff;text-decoration:none;font-weight:800;font-size:13px;">${safeButtonText}</a>` : '';

  return `
    <div style="max-width:680px;margin:0 auto;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #e2e8f0;font-family:DM Sans,Arial,sans-serif;color:#0f172a;">
      <div style="padding:18px 22px 12px;border-bottom:1px solid #e2e8f0;">
        <div style="font-size:18px;font-weight:800;line-height:1.3;">${safeSubject}</div>
        <div style="font-size:12px;color:#64748b;margin-top:6px;">From ${safeFromName} &lt;${safeFromEmail}&gt;</div>
        <div style="font-size:12px;color:#64748b;margin-top:4px;">${safePreview}</div>
      </div>
      <div style="padding:24px 22px 28px;">
        <div style="font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#64748b;margin-bottom:12px;">${escHtml(template.kicker)}</div>
        <div style="font-family:Syne,Arial,sans-serif;font-size:30px;line-height:1.05;margin-bottom:14px;">${safeTitle}</div>
        ${safeImage}
        <div style="font-size:14px;line-height:1.75;color:#334155;">${safeBody}</div>
        ${safeCta}
      </div>
    </div>
  `.trim();
}

function getSelectedCampaignRecipientEmails() {
  return contacts
    .filter((contact) => selectedCampaignContacts.includes(Number(contact.id)))
    .map((contact) => String(contact.email || '').trim())
    .filter(Boolean);
}

function getSelectedCampaignContactIds(recipientsRaw) {
  const selectedEmails = parseRecipients(recipientsRaw);
  if (!selectedEmails.length) return [];

  return contacts
    .filter((contact) => selectedEmails.includes(String(contact.email || '').trim().toLowerCase()))
    .map((contact) => Number(contact.id))
    .filter((id) => Number.isFinite(id));
}

function parseRecipients(value) {
  const unique = new Set();
  String(value || '')
    .split(/[\n,;]+/)
    .map((item) => item.trim().toLowerCase())
    .filter((item) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(item))
    .forEach((item) => unique.add(item));

  return Array.from(unique);
}
function parseContactsCsv(text) {
  const rows = parseCsvRows(text);
  if (!rows.length) return [];

  const firstRow = rows[0].map((cell) => normalizeCsvHeader(cell));
  const hasHeader = firstRow.some((header) => ['email', 'company', 'tags', 'status'].includes(header));
  const dataRows = hasHeader ? rows.slice(1) : rows;
  const contactsMap = new Map();

  dataRows.forEach((row) => {
    const contact = hasHeader ? contactFromHeaderRow(firstRow, row) : contactFromPlainRow(row);
    if (!contact?.email) return;

    contactsMap.set(contact.email, contact);
  });

  return Array.from(contactsMap.values());
}

function parseCsvRows(text) {
  const rows = [];
  let row = [];
  let cell = '';
  let inQuotes = false;

  for (let i = 0; i < text.length; i += 1) {
    const char = text[i];
    const next = text[i + 1];

    if (char === '"') {
      if (inQuotes && next === '"') {
        cell += '"';
        i += 1;
      } else {
        inQuotes = !inQuotes;
      }
      continue;
    }

    if (char === ',' && !inQuotes) {
      row.push(cell.trim());
      cell = '';
      continue;
    }

    if ((char === '\n' || char === '\r') && !inQuotes) {
      if (char === '\r' && next === '\n') {
        i += 1;
      }
      row.push(cell.trim());
      if (row.some((item) => item !== '')) {
        rows.push(row);
      }
      row = [];
      cell = '';
      continue;
    }

    cell += char;
  }

  if (cell !== '' || row.length) {
    row.push(cell.trim());
    if (row.some((item) => item !== '')) {
      rows.push(row);
    }
  }

  return rows;
}

function normalizeCsvHeader(value) {
  const clean = String(value || '').trim().toLowerCase().replace(/[^a-z0-9]+/g, '');
  const aliases = {
    email: 'email',
    emailaddress: 'email',
    emailid: 'email',
    mail: 'email',
    company: 'company',
    companyname: 'company',
    organization: 'company',
    organisation: 'company',
    business: 'company',
    tags: 'tags',
    tag: 'tags',
    labels: 'tags',
    segments: 'tags',
    segment: 'tags',
    status: 'status',
    state: 'status'
  };

  return aliases[clean] || clean;
}

function contactFromHeaderRow(headers, row) {
  const contact = {
    email: '',
    company: '',
    tags: [],
    status: 'active'
  };

  headers.forEach((header, index) => {
    const value = String(row[index] || '').trim();
    if (!value) return;

    if (header === 'email') {
      const email = firstValidEmail(value);
      if (email) contact.email = email;
    } else if (header === 'company') {
      contact.company = value;
    } else if (header === 'tags') {
      contact.tags = splitTags(value);
    } else if (header === 'status') {
      contact.status = normalizeImportedStatus(value);
    }
  });

  return contact.email ? contact : null;
}

function contactFromPlainRow(row) {
  const values = row.map((cell) => String(cell || '').trim()).filter(Boolean);
  if (!values.length) return null;

  const email = firstValidEmail(values.join(' '));
  if (!email) return null;

  return {
    email,
    company: '',
    tags: [],
    status: 'active'
  };
}

function firstValidEmail(value) {
  return parseRecipients(value)[0] || '';
}

function splitTags(value) {
  return String(value || '')
    .split(/[;,]+/)
    .map((item) => item.trim())
    .filter(Boolean);
}

function normalizeImportedStatus(value) {
  const clean = String(value || '').trim().toLowerCase();
  if (['inactive', 'disabled'].includes(clean)) return 'inactive';
  if (['unsubscribed', 'unsubscribe', 'optout', 'optedout'].includes(clean)) return 'unsubscribed';
  if (['bounced', 'bounce'].includes(clean)) return 'bounced';
  return 'active';
}

function execFormat(command) {
  document.execCommand(command, false, null);
}

function execLink() {
  const url = prompt('Enter the URL');
  if (url) document.execCommand('createLink', false, url);
}

function updateProductPreview() {
  const preview = document.getElementById('productPreview');
  if (!preview) return;

  const name = document.getElementById('productName').value.trim() || 'Product name';
  const price = document.getElementById('productPrice').value.trim() || '$0.00';
  const image = document.getElementById('productImage').value.trim();
  const cta = document.getElementById('productCta').value.trim() || 'Shop Now';
  const desc = document.getElementById('productDesc').value.trim() || 'Add a short product description.';

  preview.innerHTML = `
    <div class="product-preview-image"${image ? ` style="background-image:url('${escAttr(image)}');background-size:cover;background-position:center"` : ''}>${image ? '' : 'Image preview'}</div>
    <div class="product-preview-body">
      <div class="product-preview-name">${escHtml(name)}</div>
      <div class="product-preview-desc">${escHtml(desc)}</div>
      <div class="product-preview-price">${escHtml(price)}</div>
      <div class="product-preview-button">${escHtml(cta)}</div>
    </div>
  `;
}

function insertProductBlock() {
  const name = document.getElementById('productName').value.trim();
  const price = document.getElementById('productPrice').value.trim();
  const image = document.getElementById('productImage').value.trim();
  const link = document.getElementById('productLink').value.trim();
  const cta = document.getElementById('productCta').value.trim() || 'Shop Now';
  const desc = document.getElementById('productDesc').value.trim();

  const editor = document.getElementById('campContent');
  if (!editor) return;

  editor.innerHTML += `
    <div style="border:1px solid #e5e7eb;border-radius:14px;padding:16px;margin:12px 0">
      ${image ? `<img src="${escAttr(image)}" alt="${escAttr(name || 'Product')}" style="max-width:100%;border-radius:10px;margin-bottom:12px">` : ''}
      <h3 style="margin:0 0 8px">${escHtml(name || 'Product')}</h3>
      ${desc ? `<p style="margin:0 0 8px">${escHtml(desc)}</p>` : ''}
      ${price ? `<p style="margin:0 0 12px;font-weight:700">${escHtml(price)}</p>` : ''}
      ${link ? `<a href="${escAttr(link)}" target="_blank" style="display:inline-block;padding:10px 16px;border-radius:10px;background:#4f7cff;color:#fff;text-decoration:none">${escHtml(cta)}</a>` : ''}
    </div>
  `;

  showToast('Product block added to the email body.', 'success');
}

function exportReport() {
  const lines = [
    'MailFlow Analytics Report',
    `Campaigns: ${campaigns.length}`,
    `Contacts: ${contacts.length}`
  ];

  const blob = new Blob([lines.join('\n')], { type: 'text/plain;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = 'mailflow-report.txt';
  link.click();
  URL.revokeObjectURL(url);
}

function closeMobileSidebar() {}

function showToast(message, type = 'info') {
  const container = document.getElementById('toastContainer');
  if (!container) return;

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.textContent = message;
  container.appendChild(toast);

  setTimeout(() => toast.classList.add('show'), 10);
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 250);
  }, 2800);
}

function setText(id, value) {
  const el = document.getElementById(id);
  if (el) el.textContent = value;
}

function setValue(id, value) {
  const el = document.getElementById(id);
  if (el) el.value = value;
}

function formatDate(value) {
  if (!value) return 'Today';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return 'Today';
  return date.toLocaleDateString();
}

function normalizeDateTimeLocal(value) {
  if (!value) return '';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return '';
  const pad = (num) => String(num).padStart(2, '0');
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

function escHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function escAttr(value) {
  return escHtml(value);
}


