<?php
// ── Phase 4: Dynamic tool directory — before any HTML ────────
require_once 'config.php';

// Category slug → JS name mapping for 3D canvas reactions
$cat_slug_map = [
    'code'    => 'code',
    'image'   => 'image',
    'data'    => 'data',
    'writing' => 'writing',
    'audio'   => 'audio',
    'agent'   => 'agent',
    'general' => 'agent',
];

// Search / filter from URL params
$search_q   = trim($_GET['q']      ?? '');
$search_cat = trim($_GET['cat']    ?? '');

// Build query with optional search + category filter
$where_clauses = [];
$params        = [];
$types         = '';

if ($search_q !== '') {
    $like = '%' . $search_q . '%';
    $where_clauses[] = '(t.tool_name LIKE ? OR t.description LIKE ?)';
    $params[] = $like;
    $params[] = $like;
    $types   .= 'ss';
}
if ($search_cat !== '' && $search_cat !== 'all') {
    $where_clauses[] = 'c.slug = ?';
    $params[] = $search_cat;
    $types   .= 's';
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$sql = "SELECT t.*, c.category_name, c.slug AS cat_slug
        FROM ai_tools t
        LEFT JOIN categories c ON t.category_id = c.id
        $where_sql
        ORDER BY t.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$tools_result = $stmt->get_result();

// Fetch categories for filter pills
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name");
?>
<?php include 'header.php'; ?>

<style>
  .tool-card[data-category="code"]    { --card-glow: rgba(34,211,238,0.30);  --badge-from: #22d3ee; --badge-to: #6366f1; }
  .tool-card[data-category="image"]   { --card-glow: rgba(168,85,247,0.30);  --badge-from: #a855f7; --badge-to: #ec4899; }
  .tool-card[data-category="data"]    { --card-glow: rgba(245,158,11,0.30);  --badge-from: #f59e0b; --badge-to: #ef4444; }
  .tool-card[data-category="writing"] { --card-glow: rgba(16,185,129,0.30);  --badge-from: #10b981; --badge-to: #3b82f6; }
  .tool-card[data-category="audio"]   { --card-glow: rgba(239,68,68,0.30);   --badge-from: #ef4444; --badge-to: #f97316; }
  .tool-card[data-category="agent"]   { --card-glow: rgba(99,102,241,0.35);  --badge-from: #6366f1; --badge-to: #8b5cf6; }
  .tool-card[data-category="general"] { --card-glow: rgba(99,102,241,0.25);  --badge-from: #6366f1; --badge-to: #8b5cf6; }

  .tool-card {
    background: rgba(0,0,0,0.40);
    backdrop-filter: blur(16px) saturate(160%);
    -webkit-backdrop-filter: blur(16px) saturate(160%);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 20px; padding: 1.5rem;
    transition: transform 0.45s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.45s ease, border-color 0.3s ease;
    cursor: pointer; display: flex; flex-direction: column; height: 100%;
    position: relative; overflow: hidden;
  }
  .tool-card::before {
    content: ''; position: absolute; inset: 0;
    background: radial-gradient(circle at 80% 20%, var(--card-glow, rgba(99,102,241,0.2)) 0%, transparent 60%);
    opacity: 0; transition: opacity 0.4s ease; pointer-events: none; border-radius: inherit;
  }
  .tool-card:hover { transform: translateY(-10px) scale(1.02); box-shadow: 0 25px 50px rgba(0,0,0,0.5), 0 0 40px var(--card-glow, rgba(99,102,241,0.25)); border-color: rgba(255,255,255,0.15); }
  .tool-card:hover::before { opacity: 1; }
  .category-badge { display: inline-flex; align-items: center; gap: 5px; background: linear-gradient(135deg, var(--badge-from, #6366f1), var(--badge-to, #a855f7)); color: #fff; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; padding: 4px 12px; border-radius: 999px; }
  .rating-stars { color: #fbbf24; font-size: 0.8rem; letter-spacing: 1px; }
  .search-bar-tools { background: rgba(255,255,255,0.04); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.09); border-radius: 16px; padding: 10px 18px; display: flex; align-items: center; gap: 10px; min-width: 280px; }
  .filter-pill { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.6); border-radius: 999px; padding: 6px 18px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s; font-family: 'Inter', sans-serif; text-decoration: none; display: inline-block; }
  .filter-pill:hover, .filter-pill.active { background: linear-gradient(135deg, #6366f1, #a855f7); border-color: transparent; color: #fff; box-shadow: 0 0 15px rgba(99,102,241,0.4); }
  .empty-state { text-align: center; padding: 5rem 2rem; color: rgba(255,255,255,0.3); }
  .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
</style>

<main class="pt-24 pb-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

  <!-- Page header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-10">
    <div>
      <h1 class="text-3xl sm:text-4xl font-black text-white mb-1">Explore <span class="text-gradient">AI Tools</span></h1>
      <p class="text-white/45 text-sm">Browse our curated collection of cutting-edge AI tools</p>
    </div>
    <!-- Search form (GET to retain in URL) -->
    <form method="GET" action="tools.php" class="search-bar-tools">
      <svg class="w-4 h-4 text-white/40 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
      </svg>
      <input type="text" name="q" id="toolsSearch"
             placeholder="Search tools…"
             value="<?= htmlspecialchars($search_q) ?>"
             class="bg-transparent border-0 outline-none text-white text-sm placeholder-white/35 w-full font-sans">
      <?php if ($search_cat): ?>
      <input type="hidden" name="cat" value="<?= htmlspecialchars($search_cat) ?>">
      <?php endif; ?>
    </form>
  </div>

  <!-- Category filter pills -->
  <div class="flex flex-wrap gap-2 mb-8">
    <a href="tools.php<?= $search_q ? '?q=' . urlencode($search_q) : '' ?>"
       class="filter-pill <?= $search_cat === '' ? 'active' : '' ?>">All Tools</a>
    <?php
      $categories->data_seek(0);
      while ($cat = $categories->fetch_assoc()):
        $slug   = htmlspecialchars($cat['slug'] ?? 'general');
        $active = ($search_cat === ($cat['slug'] ?? '')) ? 'active' : '';
        $href   = '?cat=' . urlencode($cat['slug'] ?? '') . ($search_q ? '&q=' . urlencode($search_q) : '');
    ?>
    <a href="<?= $href ?>" class="filter-pill <?= $active ?>">
      <?= htmlspecialchars($cat['category_name']) ?>
    </a>
    <?php endwhile; ?>
  </div>

  <!-- Tools Grid — dynamic from DB -->
  <?php if ($tools_result && $tools_result->num_rows > 0): ?>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="tools-grid">
    <?php while ($tool = $tools_result->fetch_assoc()):
      $cat_slug = $cat_slug_map[$tool['cat_slug'] ?? 'general'] ?? 'agent';
      $icon     = !empty($tool['icon']) ? $tool['icon'] : '🤖';
      $rating   = !empty($tool['rating']) ? (float)$tool['rating'] : 4.5;
      // Build star string
      $full_stars = min(5, (int)round($rating));
      $star_str   = str_repeat('★ ', $full_stars) . str_repeat('☆ ', 5 - $full_stars);
    ?>
    <div class="tool-card"
         data-category="<?= htmlspecialchars($cat_slug) ?>"
         data-name="<?= strtolower(htmlspecialchars($tool['tool_name'])) ?>">

      <!-- Top row: icon + badge -->
      <div class="flex items-start justify-between mb-4">
        <span class="text-2xl"><?= htmlspecialchars($icon) ?></span>
        <span class="category-badge"><?= htmlspecialchars($tool['category_name'] ?? 'General') ?></span>
      </div>

      <!-- Name & description -->
      <h3 class="text-lg font-bold text-white mb-2"><?= htmlspecialchars($tool['tool_name']) ?></h3>
      <p class="text-white/45 text-sm leading-relaxed flex-1 mb-5"><?= htmlspecialchars($tool['description'] ?? '') ?></p>

      <!-- Footer row -->
      <div class="flex items-center justify-between mt-auto pt-4 border-t border-white/8">
        <div>
          <div class="rating-stars"><?= rtrim($star_str) ?></div>
          <div class="text-white/35 text-xs mt-0.5"><?= htmlspecialchars((string)$rating) ?> · <?= htmlspecialchars($tool['pricing']) ?></div>
        </div>
        <?php if (!empty($tool['url'])): ?>
        <a href="<?= htmlspecialchars($tool['url']) ?>" target="_blank" rel="noopener noreferrer"
           class="btn-glow px-4 py-2 rounded-xl text-xs font-bold relative z-10 no-underline tool-card-btn"
           data-category="<?= htmlspecialchars($cat_slug) ?>">
          View Tool ↗
        </a>
        <?php else: ?>
        <button class="btn-glow px-4 py-2 rounded-xl text-xs font-bold relative z-10 tool-card-btn"
                data-category="<?= htmlspecialchars($cat_slug) ?>">
          View Tool
        </button>
        <?php endif; ?>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

  <?php else: ?>
  <!-- Empty state -->
  <div class="empty-state glass rounded-2xl">
    <div class="icon">🔍</div>
    <h3 class="text-white font-bold text-xl mb-2">No tools found</h3>
    <p class="text-sm">
      <?= $search_q ? 'No results for "' . htmlspecialchars($search_q) . '".' : 'No tools in this category yet.' ?>
      <a href="tools.php" class="text-cyan-400 hover:text-cyan-300 no-underline ml-1">Clear filters</a>
    </p>
  </div>
  <?php endif; ?>

  <?php $stmt->close(); ?>
</main>

<script>
(function() {
  const categoryColors = {
    code:    new THREE.Color(0x22d3ee),
    image:   new THREE.Color(0xa855f7),
    data:    new THREE.Color(0xf59e0b),
    writing: new THREE.Color(0x10b981),
    audio:   new THREE.Color(0xef4444),
    agent:   new THREE.Color(0x6366f1),
    general: new THREE.Color(0x6366f1),
  };
  const defaultColor = new THREE.Color(0x6366f1);

  document.querySelectorAll('.tool-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
      if (!window.AAS3D) return;
      const cat = card.dataset.category;
      window.AAS3D.targetColor  = categoryColors[cat] || defaultColor;
      window.AAS3D.icoSpeed.x   = 0.012;
      window.AAS3D.icoSpeed.y   = 0.015;
      window.AAS3D.torusSpeed.x = 0.014;
    });
    card.addEventListener('mouseleave', () => {
      if (!window.AAS3D) return;
      window.AAS3D.targetColor  = defaultColor;
      window.AAS3D.icoSpeed.x   = 0.003;
      window.AAS3D.icoSpeed.y   = 0.004;
      window.AAS3D.torusSpeed.x = 0.006;
    });
  });
})();
</script>

<?php include 'footer.php'; ?>
