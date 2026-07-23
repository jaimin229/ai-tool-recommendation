<?php
require_once 'config.php';
include 'header.php';
?>

<style>
.tc { text-align: center; }
.jc { justify-content: center; }
.fw { flex-wrap: wrap; }
.hero-h1 { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 800; line-height: 1.15; letter-spacing: -0.025em; }
.hero-sub { font-size: 1.0625rem; line-height: 1.7; max-width: 600px; }
.hero-search { max-width: 520px; }
.stats-wrap { max-width: 480px; margin: 56px auto 0; }
.stat-num { font-size: 1.75rem; font-weight: 800; }
.stat-lbl { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); }
.mb-20 { margin-bottom: 20px; }
.mt-24 { margin-top: 24px; }
.pt-60 { padding-top: 60px; }
.pt-64 { padding-top: 64px; }
.p-mx { margin: 16px auto 0; }
.form-mx { margin: 36px auto 0; }
</style>

<main class="w pt-60">

  <section class="tc pt-64">

    <?php if ($li): ?>
    <div class="f jc g3 mb-20">
      <span class="t-s">Welcome back, <strong><?= htmlspecialchars($un) ?></strong></span>
      <?php if ($ia): ?>
      <a href="admin_dashboard.php" class="tag tag-amber">Admin</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <h1 class="hero-h1 sf s1">AI Tool Recommendation Portal</h1>

    <p class="hero-sub t-s sf s2 p-mx">Discover, compare, and choose the best AI tools for your workflow. Powered by community reviews.</p>

    <form action="tools.php" method="GET" class="hero-search sf s3 form-mx">
      <div class="f g2">
        <input type="text" name="q" placeholder="Search AI tools..." class="i f1" autocomplete="off">
        <button type="submit" class="btn btn-primary">Search</button>
      </div>
    </form>

    <div class="f jc fw g3 sf s4 mt-24">
      <?php
      $tags = ['ChatGPT', 'Midjourney', 'GitHub Copilot', 'Stable Diffusion', 'Claude'];
      foreach ($tags as $tag):
      ?>
      <a href="tools.php?q=<?= urlencode($tag) ?>" class="tag tag-gray"><?= htmlspecialchars($tag) ?></a>
      <?php endforeach; ?>
    </div>

  </section>

  <?php
  $toolCount = $conn->query("SELECT COUNT(*) as c FROM ai_tools")->fetch_assoc()['c'] ?? 0;
  $catCount  = $conn->query("SELECT COUNT(*) as c FROM categories")->fetch_assoc()['c'] ?? 0;
  $userCount = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'] ?? 0;
  ?>

  <div class="g gc3 g4 stats-wrap sf s5">
    <div class="c tc">
      <div class="stat-num"><?= $toolCount ?></div>
      <div class="stat-lbl">AI Tools</div>
    </div>
    <div class="c tc">
      <div class="stat-num"><?= $catCount ?></div>
      <div class="stat-lbl">Categories</div>
    </div>
    <div class="c tc">
      <div class="stat-num"><?= $userCount ?></div>
      <div class="stat-lbl">Users</div>
    </div>
  </div>

</main>

<?php include 'footer.php'; ?>
