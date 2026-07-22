<?php
// Phase 1: Session + DB — must be before any HTML
require_once 'config.php';
?>
<?php include 'header.php'; ?>

<!-- ═══════════════════════════════════════════════ -->
<!-- PHASE 2: HERO SECTION over 3D canvas            -->
<!-- ═══════════════════════════════════════════════ -->

<main class="relative">

  <!-- Hero Section -->
  <section class="min-h-screen flex flex-col items-center justify-center px-4 pt-16" id="hero-section">

    <!-- Pointer-events-none wrapper for purely decorative text layer -->
    <div class="pointer-events-none text-center mb-12 select-none">

      <!-- Eyebrow tag -->
      <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-2 mb-8 pointer-events-auto">
        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse inline-block"></span>
        <span class="text-xs font-semibold text-white/70 uppercase tracking-widest">Next-Gen AI Discovery</span>
      </div>

      <!-- Main headline -->
      <h1 class="text-5xl sm:text-7xl lg:text-8xl font-black leading-none tracking-tight mb-6">
        <span class="text-gradient">AI Tool</span><br>
        <span class="text-white">Recommendation</span><br>
        <span class="text-gradient-cyan">Portal</span>
      </h1>

      <!-- Sub-headline -->
      <p class="text-lg sm:text-xl text-white/55 font-light max-w-xl mx-auto leading-relaxed">
        Discover, compare, and choose the best AI tools for your workflow. Powered by community intelligence and 3D spatial computing.
      </p>
    </div>

    <!-- Search form — pointer events re-activated -->
    <div class="pointer-events-auto w-full max-w-2xl mx-auto">
      <form action="tools.php" method="GET" id="hero-search-form"
            class="backdrop-blur-2xl bg-white/5 border border-white/10 rounded-3xl p-8 shadow-2xl shadow-black/40">

        <!-- Logged-in greeting -->
        <?php if (!empty($_SESSION['username'])): ?>
        <div class="mb-4 flex items-center gap-2 text-sm text-white/50">
          <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>
          Welcome back, <strong class="text-white/80"><?= htmlspecialchars($_SESSION['username']) ?></strong>!
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="admin_dashboard.php" class="ml-auto text-xs text-cyan-400 hover:text-cyan-300 no-underline transition-colors">Admin Panel →</a>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Label -->
        <label for="heroSearchInput" class="block text-white font-semibold text-lg mb-3">
          What do you need?
        </label>

        <!-- Input row -->
        <div class="flex gap-3 flex-col sm:flex-row">
          <input
            type="text"
            id="heroSearchInput"
            name="q"
            class="glass-input flex-1"
            placeholder="e.g. Image generation, Code assistant, Data analysis…"
            value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
            autocomplete="off"
          >
          <button type="submit"
                  class="btn-glow px-8 py-3 rounded-2xl text-base font-bold tracking-wide relative z-10 whitespace-nowrap"
                  id="hero-search-btn">
            Search Tools
          </button>
        </div>

        <!-- Quick tags -->
        <div class="flex flex-wrap gap-2 mt-5">
          <span class="text-white/40 text-xs font-medium self-center">Trending:</span>
          <?php
            $tags = ['ChatGPT', 'Midjourney', 'GitHub Copilot', 'Stable Diffusion', 'Claude'];
            foreach ($tags as $tag):
          ?>
          <button type="button"
                  onclick="document.getElementById('heroSearchInput').value='<?= htmlspecialchars($tag) ?>'; document.getElementById('hero-search-form').submit();"
                  class="text-xs px-3 py-1 rounded-full glass hover:bg-white/10 transition-all text-white/60 hover:text-white cursor-pointer border-0">
            <?= htmlspecialchars($tag) ?>
          </button>
          <?php endforeach; ?>
        </div>
      </form>
    </div>

    <!-- Stats row — pulled live from DB -->
    <?php
      $toolCount = $conn->query("SELECT COUNT(*) as c FROM ai_tools")->fetch_assoc()['c'] ?? '—';
      $catCount  = $conn->query("SELECT COUNT(*) as c FROM categories")->fetch_assoc()['c'] ?? '—';
      $userCount = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'] ?? '—';
    ?>
    <div class="pointer-events-auto mt-12 flex gap-8 sm:gap-16 text-center">
      <div>
        <div class="text-2xl sm:text-3xl font-black text-gradient"><?= $toolCount ?>+</div>
        <div class="text-xs text-white/40 uppercase tracking-widest mt-1">AI Tools</div>
      </div>
      <div>
        <div class="text-2xl sm:text-3xl font-black text-gradient"><?= $catCount ?>+</div>
        <div class="text-xs text-white/40 uppercase tracking-widest mt-1">Categories</div>
      </div>
      <div>
        <div class="text-2xl sm:text-3xl font-black text-gradient"><?= $userCount ?>+</div>
        <div class="text-xs text-white/40 uppercase tracking-widest mt-1">Users</div>
      </div>
    </div>

    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 pointer-events-none animate-bounce">
      <svg class="w-5 h-5 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </div>
  </section>

</main>

<!-- Trigger 3D pulse on search focus -->
<script>
  (function() {
    const searchInput = document.getElementById('heroSearchInput');
    if (!searchInput) return;
    searchInput.addEventListener('focus', () => {
      if (window.AAS3D) {
        window.AAS3D.icoSpeed.x = 0.01;
        window.AAS3D.icoSpeed.y = 0.012;
        window.AAS3D.targetColor = new THREE.Color(0x22d3ee);
      }
    });
    searchInput.addEventListener('blur', () => {
      if (window.AAS3D) {
        window.AAS3D.icoSpeed.x = 0.003;
        window.AAS3D.icoSpeed.y = 0.004;
        window.AAS3D.targetColor = new THREE.Color(0x6366f1);
      }
    });
  })();
</script>

<?php include 'footer.php'; ?>