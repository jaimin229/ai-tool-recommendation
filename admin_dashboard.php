<?php
// ── Phase 3: Admin RBAC — before any HTML ───────────────────
require_once 'config.php';

// Strict Role-Based Access Control
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$modal_success = '';
$modal_error   = '';

// ── Handle "Add New Tool" POST ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_tool') {
    $tool_name   = trim($_POST['tool_name']   ?? '');
    $description = trim($_POST['description'] ?? '');
    $url         = trim($_POST['url']         ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $pricing     = $_POST['pricing'] ?? 'Freemium';
    $icon        = trim($_POST['icon'] ?? '🤖');
    $added_by    = $_SESSION['user_id'];

    $allowed_pricing = ['Free','Freemium','Paid'];
    if (empty($tool_name)) {
        $modal_error = 'Tool name is required.';
    } elseif (!in_array($pricing, $allowed_pricing)) {
        $modal_error = 'Invalid pricing option.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO ai_tools (tool_name, description, url, category_id, pricing, icon, added_by)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('sssissi', $tool_name, $description, $url, $category_id, $pricing, $icon, $added_by);
        if ($stmt->execute()) {
            $modal_success = "Tool \"" . htmlspecialchars($tool_name) . "\" added successfully!";
        } else {
            $modal_error = 'Failed to add tool. Please try again.';
        }
        $stmt->close();
    }
}

// ── Handle "Delete Tool" ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_tool') {
    $del_id = (int)($_POST['tool_id'] ?? 0);
    if ($del_id > 0) {
        $stmt = $conn->prepare("DELETE FROM ai_tools WHERE id = ?");
        $stmt->bind_param('i', $del_id);
        $stmt->execute();
        $stmt->close();
        header("Location: admin_dashboard.php?deleted=1");
        exit();
    }
}

// ── Fetch all tools (with category name JOIN) ─────────────────
$tools_result = $conn->query(
    "SELECT t.*, c.category_name, c.slug AS cat_slug
     FROM ai_tools t
     LEFT JOIN categories c ON t.category_id = c.id
     ORDER BY t.created_at DESC"
);

// ── Fetch categories for dropdown ────────────────────────────
$cats_result = $conn->query("SELECT id, category_name FROM categories ORDER BY category_name");

// ── Stats ─────────────────────────────────────────────────────
$stat_tools = $conn->query("SELECT COUNT(*) AS c FROM ai_tools")->fetch_assoc()['c'];
$stat_users = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$stat_cats  = $conn->query("SELECT COUNT(*) AS c FROM categories")->fetch_assoc()['c'];
$stat_avg   = $conn->query("SELECT ROUND(AVG(rating),1) AS a FROM ai_tools")->fetch_assoc()['a'] ?? '—';
?>
<?php include 'header.php'; ?>

<style>
  .admin-glass-table { width:100%; border-collapse:separate; border-spacing:0 4px; text-align:left; }
  .admin-glass-table thead th {
    padding:14px 20px; font-size:0.7rem; font-weight:700;
    color:rgba(255,255,255,0.35); text-transform:uppercase;
    letter-spacing:0.1em; border-bottom:1px solid rgba(255,255,255,0.06); white-space:nowrap;
  }
  .admin-glass-table tbody tr {
    background:rgba(255,255,255,0.04); backdrop-filter:blur(8px);
    transition:background 0.25s, transform 0.2s;
  }
  .admin-glass-table tbody tr:hover { background:rgba(255,255,255,0.09); transform:translateX(3px); }
  .admin-glass-table tbody td {
    padding:16px 20px; font-size:0.88rem;
    color:rgba(255,255,255,0.75); border-bottom:1px solid rgba(255,255,255,0.04); vertical-align:middle;
  }
  .admin-glass-table tbody td:first-child { border-radius:12px 0 0 12px; }
  .admin-glass-table tbody td:last-child  { border-radius:0 12px 12px 0; }
  .badge-cat {
    display:inline-flex; align-items:center;
    padding:3px 10px; border-radius:999px;
    font-size:0.7rem; font-weight:700; letter-spacing:0.04em; text-transform:uppercase;
    border:1px solid rgba(255,255,255,0.12);
    background:rgba(255,255,255,0.06); color:rgba(255,255,255,0.7);
  }
  .stat-card {
    background:rgba(255,255,255,0.04); backdrop-filter:blur(20px);
    border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:1.5rem;
    position:relative; overflow:hidden; transition:transform 0.3s ease, border-color 0.3s;
  }
  .stat-card:hover { transform:translateY(-4px); border-color:rgba(255,255,255,0.16); }
  .stat-card-glow { position:absolute; inset:0; pointer-events:none; background:radial-gradient(circle at 80% 20%, var(--glow) 0%, transparent 60%); opacity:0.3; }
  .action-btn { width:32px; height:32px; border-radius:8px; border:0; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; transition:all 0.2s; font-size:0.85rem; }
  .action-btn-delete { background:rgba(239,68,68,0.1); color:#f87171; }
  .action-btn-delete:hover { background:rgba(239,68,68,0.2); box-shadow:0 0 12px rgba(239,68,68,0.3); }
  .admin-modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.55); backdrop-filter:blur(8px); z-index:1000; display:flex; align-items:center; justify-content:center; padding:1rem; opacity:0; pointer-events:none; transition:opacity 0.3s; }
  .admin-modal-overlay.open { opacity:1; pointer-events:auto; }
  .admin-modal { background:rgba(10,10,30,0.85); backdrop-filter:blur(30px); border:1px solid rgba(255,255,255,0.1); border-radius:24px; padding:2rem; width:100%; max-width:500px; box-shadow:0 30px 80px rgba(0,0,0,0.6); transform:scale(0.95); transition:transform 0.3s; }
  .admin-modal-overlay.open .admin-modal { transform:scale(1); }
  .auth-label { display:block; font-size:0.78rem; font-weight:600; color:rgba(255,255,255,0.55); text-transform:uppercase; letter-spacing:0.07em; margin-bottom:8px; }
  .form-select-glass { appearance:none; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.12); color:#fff; border-radius:12px; padding:13px 40px 13px 16px; width:100%; outline:none; font-family:'Inter',sans-serif; font-size:0.95rem; transition:all 0.3s; cursor:pointer; }
  .form-select-glass:focus { border-color:rgba(99,102,241,0.7); box-shadow:0 0 0 3px rgba(99,102,241,0.15); background-color:rgba(255,255,255,0.07); }
  .form-select-glass option { background:#1e1b4b; color:#fff; }
  .flash-success { background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.25); border-radius:12px; padding:12px 16px; color:#6ee7b7; font-size:0.85rem; margin-bottom:1rem; }
  .flash-error   { background:rgba(239,68,68,0.12);  border:1px solid rgba(239,68,68,0.25);  border-radius:12px; padding:12px 16px; color:#fca5a5; font-size:0.85rem; margin-bottom:1rem; }
</style>

<main class="pt-24 pb-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

  <!-- Dashboard header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl sm:text-3xl font-black text-white mb-1">Admin <span class="text-gradient">Control Panel</span></h1>
      <p class="text-white/40 text-sm">Logged in as <strong class="text-white/60"><?= htmlspecialchars($_SESSION['username']) ?></strong> ·
        <a href="logout.php" class="text-red-400/70 hover:text-red-400 no-underline transition-colors">Sign out</a>
      </p>
    </div>
    <button onclick="openModal()" class="btn-glow px-6 py-2.5 rounded-xl text-sm font-bold relative z-10 self-start sm:self-auto">
      + Add New Tool
    </button>
  </div>

  <!-- Page flash messages -->
  <?php if ($modal_success): ?><div class="flash-success">✓ <?= $modal_success ?></div><?php endif; ?>
  <?php if ($modal_error):   ?><div class="flash-error">⚠ <?= htmlspecialchars($modal_error) ?></div><?php endif; ?>
  <?php if (isset($_GET['deleted'])): ?><div class="flash-success">✓ Tool deleted successfully.</div><?php endif; ?>

  <!-- Stats row -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <?php
    $adminStats = [
      ['Total Tools',    $stat_tools, '🛠️', '--glow: rgba(34,211,238,0.4)'],
      ['Registered Users', $stat_users,'👥', '--glow: rgba(168,85,247,0.4)'],
      ['Categories',     $stat_cats,  '📂', '--glow: rgba(245,158,11,0.4)'],
      ['Avg Rating',     $stat_avg.' ★','⭐', '--glow: rgba(16,185,129,0.4)'],
    ];
    foreach ($adminStats as [$label, $value, $icon, $glow]):
    ?>
    <div class="stat-card" style="<?= $glow ?>">
      <div class="stat-card-glow"></div>
      <div class="text-2xl mb-3"><?= $icon ?></div>
      <div class="text-2xl font-black text-white"><?= htmlspecialchars((string)$value) ?></div>
      <div class="text-xs text-white/40 uppercase tracking-widest mt-1"><?= $label ?></div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Data Table -->
  <div class="glass-deep rounded-2xl overflow-hidden">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 p-5 border-b border-white/6">
      <div class="text-white font-semibold text-sm">All Tools <span class="text-white/30 font-normal">(<?= $stat_tools ?> entries)</span></div>
      <input type="text" id="adminSearch" placeholder="Filter table…"
             class="glass-input text-sm" style="width:200px; padding:8px 14px; border-radius:12px;"
             oninput="filterAdminTable(this.value)">
    </div>

    <div class="overflow-x-auto p-4">
      <table class="admin-glass-table" id="admin-tools-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tool Name</th>
            <th>Category</th>
            <th>Pricing</th>
            <th>Rating</th>
            <th>Added</th>
            <th class="text-right">Delete</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($tools_result && $tools_result->num_rows > 0):
            while ($tool = $tools_result->fetch_assoc()): ?>
          <tr class="admin-row" data-name="<?= strtolower(htmlspecialchars($tool['tool_name'])) ?>">
            <td class="text-white/30 text-xs font-mono">#<?= $tool['id'] ?></td>
            <td>
              <div class="font-semibold text-white"><?= htmlspecialchars($tool['tool_name']) ?></div>
              <?php if (!empty($tool['url'])): ?>
              <a href="<?= htmlspecialchars($tool['url']) ?>" target="_blank" class="text-xs text-cyan-400/60 hover:text-cyan-400 no-underline transition-colors">
                <?= htmlspecialchars(parse_url($tool['url'], PHP_URL_HOST) ?? $tool['url']) ?> ↗
              </a>
              <?php endif; ?>
            </td>
            <td><span class="badge-cat"><?= htmlspecialchars($tool['category_name'] ?? 'Uncategorised') ?></span></td>
            <td class="text-white/55"><?= htmlspecialchars($tool['pricing']) ?></td>
            <td class="text-yellow-400 font-bold"><?= htmlspecialchars($tool['rating'] ?? '—') ?></td>
            <td class="text-white/35 text-xs"><?= date('d M Y', strtotime($tool['created_at'])) ?></td>
            <td class="text-right">
              <form method="POST" action="admin_dashboard.php" onsubmit="return confirm('Delete this tool?');" style="display:inline;">
                <input type="hidden" name="action"  value="delete_tool">
                <input type="hidden" name="tool_id" value="<?= $tool['id'] ?>">
                <button type="submit" class="action-btn action-btn-delete" title="Delete">🗑</button>
              </form>
            </td>
          </tr>
          <?php endwhile;
          else: ?>
          <tr>
            <td colspan="7" class="text-center text-white/30 py-10">No tools found. Add your first tool!</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- Add Tool Modal -->
<div class="admin-modal-overlay" id="addToolModal">
  <div class="admin-modal">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-black text-white">Add AI Tool</h2>
      <button onclick="closeModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-white/40 hover:text-white hover:bg-white/10 transition-all text-lg border-0 bg-transparent cursor-pointer">✕</button>
    </div>

    <form action="admin_dashboard.php" method="POST">
      <input type="hidden" name="action" value="add_tool">

      <div class="mb-4">
        <label class="auth-label">Tool Name *</label>
        <input type="text" name="tool_name" required class="glass-input" placeholder="e.g. DevMind AI">
      </div>
      <div class="mb-4">
        <label class="auth-label">Website URL</label>
        <input type="url" name="url" class="glass-input" placeholder="https://…">
      </div>
      <div class="grid grid-cols-2 gap-3 mb-4">
        <div>
          <label class="auth-label">Category</label>
          <select name="category_id" class="form-select-glass">
            <option value="0">— Select —</option>
            <?php
              // Reset categories cursor
              $cats_result->data_seek(0);
              while ($cat = $cats_result->fetch_assoc()):
            ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div>
          <label class="auth-label">Pricing</label>
          <select name="pricing" class="form-select-glass">
            <option value="Free">Free</option>
            <option value="Freemium" selected>Freemium</option>
            <option value="Paid">Paid</option>
          </select>
        </div>
      </div>
      <div class="mb-4">
        <label class="auth-label">Icon (emoji)</label>
        <input type="text" name="icon" class="glass-input" placeholder="🤖" maxlength="5" value="🤖">
      </div>
      <div class="mb-6">
        <label class="auth-label">Description</label>
        <textarea name="description" class="glass-input" rows="3" style="resize:vertical;" placeholder="Brief description of the tool…"></textarea>
      </div>
      <div class="flex gap-3">
        <button type="button" onclick="closeModal()" class="flex-1 py-2.5 rounded-xl border border-white/10 text-white/50 hover:text-white hover:bg-white/5 transition-all text-sm font-medium cursor-pointer bg-transparent">Cancel</button>
        <button type="submit" class="btn-glow flex-1 py-2.5 rounded-xl text-sm font-bold relative z-10">Save Tool</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openModal() {
    document.getElementById('addToolModal').classList.add('open');
    if (window.AAS3D) { window.AAS3D.targetColor = new THREE.Color(0xa855f7); window.AAS3D.icoSpeed.x = 0.009; }
  }
  function closeModal() {
    document.getElementById('addToolModal').classList.remove('open');
    if (window.AAS3D) { window.AAS3D.targetColor = new THREE.Color(0x6366f1); window.AAS3D.icoSpeed.x = 0.003; }
  }
  document.getElementById('addToolModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
  function filterAdminTable(q) {
    document.querySelectorAll('.admin-row').forEach(row => {
      row.style.display = row.dataset.name.includes(q.toLowerCase()) ? '' : 'none';
    });
  }
  <?php if (!empty($modal_success) || isset($_GET['deleted'])): ?>
  // Auto-open re-confirmation stays visible; no modal needed
  <?php endif; ?>
</script>

<?php include 'footer.php'; ?>
