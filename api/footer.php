<footer style="border-top:1px solid var(--border);padding:24px 0;">
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$li = isset($_SESSION['user_id']);
$un = $_SESSION['username'] ?? '';
$ia = ($_SESSION['role'] ?? '') === 'admin';
?>
<div class="w">
  <div class="f" style="flex-wrap:wrap;justify-content:space-between;align-items:center;gap:12px;">
    <nav class="f" style="flex-wrap:wrap;align-items:center;gap:4px;">
      <a href="index.php" style="color:var(--muted);font-size:13px;transition:color .15s;" onmouseover="this.style.color='var(--secondary)'" onmouseout="this.style.color='var(--muted)'">Home</a>
      <span style="color:var(--muted);font-size:13px;">·</span>
      <a href="tools.php" style="color:var(--muted);font-size:13px;transition:color .15s;" onmouseover="this.style.color='var(--secondary)'" onmouseout="this.style.color='var(--muted)'">Tools</a>
      <span style="color:var(--muted);font-size:13px;">·</span>
      <a href="recommend.php" style="color:var(--muted);font-size:13px;transition:color .15s;" onmouseover="this.style.color='var(--secondary)'" onmouseout="this.style.color='var(--muted)'">Recommend</a>
<?php if ($li): ?>
      <span style="color:var(--muted);font-size:13px;">·</span>
      <a href="profile.php" style="color:var(--muted);font-size:13px;transition:color .15s;" onmouseover="this.style.color='var(--secondary)'" onmouseout="this.style.color='var(--muted)'">Profile</a>
<?php if ($ia): ?>
      <span style="color:var(--muted);font-size:13px;">·</span>
      <a href="admin_dashboard.php" style="color:var(--muted);font-size:13px;transition:color .15s;" onmouseover="this.style.color='var(--secondary)'" onmouseout="this.style.color='var(--muted)'">Admin</a>
<?php endif; ?>
<?php endif; ?>
    </nav>
    <span class="t-m" style="font-size:12px;">&copy; 2024 AI Portal. All rights reserved.</span>
  </div>
</div>
</footer>

</body>
</html>
