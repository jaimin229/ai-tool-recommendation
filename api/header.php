<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI Tool Recommendation Portal</title>
<meta name="description" content="Discover and compare AI tools for your workflow.">
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#0a0a0b;--card:#141415;--card-hover:#1c1c1d;--elevated:#1e1e1f;--border:#232325;--border-hover:#2f2f31;--text:#ededef;--secondary:#9b9ba0;--muted:#5c5c62;--accent:#3b82f6;--accent-hover:#2563eb;--success:#22c55e;--warning:#eab308;--error:#ef4444;--radius:8px;--radius-lg:12px}
html{scroll-behavior:smooth;height:100%}
body{font-family:'Inter',system-ui,-apple-system,sans-serif;background:var(--bg);color:var(--text);min-height:100%;line-height:1.5;-webkit-font-smoothing:antialiased}
a{color:inherit;text-decoration:none}
.w{max-width:1280px;margin:0 auto;padding:0 24px}
.f{display:flex;align-items:center}
.f1{flex:1}
.g2{gap:8px}.g3{gap:12px}.g4{gap:16px}.g6{gap:24px}
.c{background:var(--card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px}
.c-sm{padding:16px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:9px 20px;border-radius:var(--radius);font-size:14px;font-weight:600;cursor:pointer;border:none;font-family:inherit;line-height:1.4;transition:all .15s}
.btn-primary{background:var(--accent);color:#fff}.btn-primary:hover{background:var(--accent-hover)}
.btn-secondary{background:transparent;color:var(--secondary);border:1px solid var(--border)}.btn-secondary:hover{color:var(--text);border-color:var(--border-hover);background:var(--card-hover)}
.btn-danger{background:rgba(239,68,68,.1);color:#fca5a5;border:1px solid rgba(239,68,68,.2)}.btn-danger:hover{background:rgba(239,68,68,.18)}
.btn-sm{padding:6px 14px;font-size:13px}
.btn-lg{padding:12px 28px;font-size:15px}
.btn-block{width:100%}
.i{background:var(--card);border:1px solid var(--border);color:var(--text);border-radius:var(--radius);padding:10px 14px;width:100%;outline:none;font-family:inherit;font-size:14px;transition:border-color .15s}.i::placeholder{color:var(--muted)}.i:focus{border-color:var(--accent)}.i-sm{padding:8px 12px;font-size:13px}
textarea.i{resize:vertical;min-height:100px}
.lbl{display:block;font-size:13px;font-weight:600;color:var(--secondary);margin-bottom:6px}
.tag{display:inline-flex;align-items:center;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:600}
.tag-accent{background:var(--accent-subtle, rgba(59,130,246,.1));color:#93c5fd;border:1px solid rgba(59,130,246,.2)}
.tag-green{background:rgba(34,197,94,.1);color:#86efac;border:1px solid rgba(34,197,94,.2)}
.tag-amber{background:rgba(234,179,8,.1);color:#fde047;border:1px solid rgba(234,179,8,.2)}
.tag-red{background:rgba(239,68,68,.1);color:#fca5a5;border:1px solid rgba(239,68,68,.2)}
.tag-gray{background:rgba(255,255,255,.04);color:var(--muted);border:1px solid var(--border)}
.avatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;background:var(--accent);color:#fff}
.avatar-sm{width:28px;height:28px;font-size:12px}
.avatar-lg{width:64px;height:64px;font-size:24px;border-radius:16px}
.st{color:#fbbf24;font-size:13px;letter-spacing:1px}.st-lg{font-size:16px}.st-em{color:rgba(255,255,255,.08)}
.t-m{color:var(--muted)}.t-s{color:var(--secondary)}
.nav-lk{color:var(--muted);font-size:14px;font-weight:500;padding:8px 14px;border-radius:var(--radius);transition:all .15s}.nav-lk:hover{color:var(--text);background:rgba(255,255,255,.04)}
.sf{opacity:0;transform:translateY(12px);animation:fu .4s ease forwards}
@keyframes fu{to{opacity:1;transform:translateY(0)}}
.s1{animation-delay:.05s}.s2{animation-delay:.1s}.s3{animation-delay:.15s}.s4{animation-delay:.2s}.s5{animation-delay:.25s}.s6{animation-delay:.3s}
.alert{padding:12px 16px;border-radius:var(--radius);font-size:14px;margin-bottom:16px}
.alert-s{background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.15);color:#86efac}
.alert-e{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.15);color:#fca5a5}
::-webkit-scrollbar{width:6px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:rgba(255,255,255,.08);border-radius:999px}
.g{display:grid}.gc1{grid-template-columns:repeat(1,1fr)}.gc2{grid-template-columns:repeat(2,1fr)}.gc3{grid-template-columns:repeat(3,1fr)}
@media(max-width:768px){.gc2{grid-template-columns:1fr}.gc3{grid-template-columns:1fr}}
@media(min-width:769px)and(max-width:1024px){.gc3{grid-template-columns:repeat(2,1fr)}}
.btn:disabled{opacity:.5;cursor:not-allowed;pointer-events:none}
</style>
</head>
<body>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$li = isset($_SESSION['user_id']);
$un = $_SESSION['username'] ?? '';
$ia = ($_SESSION['role'] ?? '') === 'admin';
?>

<nav style="position:fixed;top:0;width:100%;z-index:50;background:rgba(10,10,11,.85);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);">
  <div class="w">
    <div class="f" style="justify-content:space-between;height:56px;">
      <a href="index.php" class="f g2">
        <div class="avatar avatar-sm" style="border-radius:6px;background:var(--accent);">AI</div>
        <span style="font-weight:700;font-size:14px;">AI Portal</span>
      </a>
      <div class="f g1" style="display:none;" id="deskNav">
        <a href="index.php" class="nav-lk">Home</a>
        <a href="tools.php" class="nav-lk">Tools</a>
        <a href="recommend.php" class="nav-lk">Recommend</a>
<?php if ($li): ?>
        <a href="profile.php" class="nav-lk">Profile</a>
<?php if ($ia): ?>
        <a href="admin_dashboard.php" class="nav-lk">Admin</a>
<?php endif; ?>
        <div class="f g2" style="margin-left:12px;padding-left:12px;border-left:1px solid var(--border);">
          <span style="font-size:14px;font-weight:500;color:var(--secondary);"><?= htmlspecialchars($un) ?></span>
          <a href="logout.php" class="btn btn-sm btn-secondary">Sign Out</a>
        </div>
<?php else: ?>
        <a href="login.php" class="nav-lk">Login</a>
        <a href="register.php" class="btn btn-sm btn-primary" style="margin-left:4px;">Get Started</a>
<?php endif; ?>
      </div>
      <button id="mbBtn" class="btn btn-sm" style="background:transparent;border:1px solid var(--border);padding:8px;display:none;">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
    </div>
    <div id="mbMenu" style="display:none;padding-bottom:12px;border-top:1px solid var(--border);">
      <div class="f" style="flex-direction:column;gap:2px;padding-top:8px;">
        <a href="index.php" class="nav-lk">Home</a>
        <a href="tools.php" class="nav-lk">Tools</a>
        <a href="recommend.php" class="nav-lk">Recommend</a>
<?php if ($li): ?>
        <a href="profile.php" class="nav-lk">Profile</a>
<?php if ($ia): ?>
        <a href="admin_dashboard.php" class="nav-lk">Admin</a>
<?php endif; ?>
        <div style="padding-top:8px;margin-top:8px;border-top:1px solid var(--border);">
          <span style="font-size:14px;padding:8px 14px;display:block;color:var(--secondary);"><?= htmlspecialchars($un) ?></span>
          <a href="logout.php" class="btn btn-sm btn-secondary" style="margin:4px 14px;">Sign Out</a>
        </div>
<?php else: ?>
        <a href="login.php" class="nav-lk">Login</a>
        <a href="register.php" class="btn btn-sm btn-primary" style="margin:4px 14px;display:inline-block;">Get Started</a>
<?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<div style="height:56px;"></div>

<script>
(function(){var b=document.getElementById('mbBtn'),m=document.getElementById('mbMenu'),d=document.getElementById('deskNav');
function u(){var w=window.innerWidth;if(w<768){d.style.display='none';b.style.display=''}else{d.style.display='flex';b.style.display='none';m.style.display='none'}}
u();window.addEventListener('resize',u);
b&&b.addEventListener('click',function(){m.style.display=m.style.display==='none'?'block':'none'})})();
</script>