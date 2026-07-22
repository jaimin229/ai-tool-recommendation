<?php
// ── Phase 2: Login handler — before any HTML ─────────────────
require_once 'config.php';

// Already logged in → redirect
if (!empty($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'index.php'));
    exit();
}

$error   = '';
$success = '';

// Show registration success message
if (isset($_GET['registered'])) {
    $success = 'Account created successfully! Please sign in.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        // Fetch user by email
        $stmt = $conn->prepare("SELECT id, username, password_hash, role FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Regenerate session ID to prevent fixation
            session_regenerate_id(true);

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            // Role-based redirect
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = 'Invalid email or password.';
            // Small delay to prevent timing attacks
            usleep(300000);
        }
    }
}
?>
<?php include 'header.php'; ?>

<style>
  .auth-label {
    display: block; font-size: 0.78rem; font-weight: 600;
    color: rgba(255,255,255,0.55); text-transform: uppercase;
    letter-spacing: 0.07em; margin-bottom: 8px;
  }
  .divider {
    display: flex; align-items: center; gap: 12px;
    color: rgba(255,255,255,0.2); font-size: 0.75rem;
  }
  .divider::before, .divider::after {
    content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.08);
  }
  .social-btn {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px; border-radius: 12px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.7); font-size: 0.85rem; font-weight: 500;
    cursor: pointer; transition: all 0.2s; width: 100%; font-family: 'Inter', sans-serif;
  }
  .social-btn:hover { background: rgba(255,255,255,0.08); color: #fff; border-color: rgba(255,255,255,0.2); }
  .alert-error {
    background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.25);
    border-radius: 12px; padding: 12px 16px; color: #fca5a5;
    font-size: 0.85rem; margin-bottom: 1.25rem;
  }
  .alert-success {
    background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25);
    border-radius: 12px; padding: 12px 16px; color: #6ee7b7;
    font-size: 0.85rem; margin-bottom: 1.25rem;
  }
</style>

<main class="min-h-screen flex items-center justify-center pt-24 pb-16 px-4">
  <div class="w-full max-w-5xl mx-auto flex flex-col lg:flex-row items-center gap-12 lg:gap-20">

    <!-- Left branding -->
    <div class="flex-1 text-center lg:text-left pointer-events-none select-none hidden lg:block">
      <div class="inline-flex items-center gap-2 mb-6 glass rounded-full px-4 py-2">
        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse inline-block"></span>
        <span class="text-xs font-semibold text-white/60 uppercase tracking-widest">Welcome Back</span>
      </div>
      <h2 class="text-4xl xl:text-5xl font-black text-white leading-tight mb-4">
        Discover the<br><span class="text-gradient">Future of AI</span>
      </h2>
      <p class="text-white/45 text-base leading-relaxed max-w-sm">
        Access your workspace and resume your journey through the most comprehensive AI tool directory.
      </p>
      <div class="mt-8 space-y-3">
        <?php foreach(['2,000+ AI Tools curated for you','Real-time recommendations','Personalised AI workflow builder'] as $feat): ?>
        <div class="flex items-center gap-3 text-white/50 text-sm">
          <div class="w-5 h-5 rounded-full bg-gradient-to-br from-cyan-400 to-purple-500 flex-shrink-0 flex items-center justify-center">
            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
          </div><?= htmlspecialchars($feat) ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Login glass form -->
    <div class="w-full max-w-md">
      <div class="glass-deep rounded-3xl p-8 shadow-2xl shadow-black/50">

        <div class="text-center mb-7">
          <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cyan-400 to-purple-500 flex items-center justify-center text-white font-black text-lg mx-auto mb-4 shadow-lg shadow-purple-500/30">AI</div>
          <h1 class="text-2xl font-black text-white mb-1">Sign In</h1>
          <p class="text-white/40 text-sm">Enter your credentials to continue</p>
        </div>

        <!-- Flash messages -->
        <?php if ($error): ?>
        <div class="alert-error">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert-success">✓ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Social login (UI only) -->
        <div class="grid grid-cols-2 gap-3 mb-6">
          <button type="button" class="social-btn">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
              <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
              <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
              <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
              <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>Google
          </button>
          <button type="button" class="social-btn">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/>
            </svg>GitHub
          </button>
        </div>

        <div class="divider mb-6">or continue with email</div>

        <!-- ── Login form (POST) ────────────────────────────── -->
        <form action="login.php" method="POST" id="loginForm">
          <div class="mb-4">
            <label for="emailInput" class="auth-label">Email Address</label>
            <input type="email" id="emailInput" name="email" required
                   class="glass-input"
                   placeholder="name@example.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
          <div class="mb-5">
            <div class="flex items-center justify-between mb-2">
              <label for="passwordInput" class="auth-label" style="margin-bottom:0">Password</label>
              <a href="#" class="text-xs text-white/40 hover:text-white/70 transition-colors no-underline">Forgot password?</a>
            </div>
            <input type="password" id="passwordInput" name="password" required
                   class="glass-input" placeholder="••••••••">
          </div>
          <div class="flex items-center gap-2 mb-6">
            <input type="checkbox" id="rememberMe" name="remember" class="w-4 h-4 rounded" style="accent-color:#6366f1;">
            <label for="rememberMe" class="text-white/45 text-sm cursor-pointer">Remember me for 30 days</label>
          </div>
          <button type="submit" id="login-submit-btn" class="btn-glow w-full py-3 rounded-2xl text-base font-bold tracking-wide relative z-10">
            Sign In →
          </button>
        </form>

        <p class="text-center text-white/35 text-sm mt-6">
          Don't have an account?
          <a href="register.php" class="text-white font-semibold hover:text-cyan-400 transition-colors no-underline ml-1">Create one</a>
        </p>
      </div>
    </div>
  </div>
</main>

<script>
(function() {
  document.querySelectorAll('.glass-input').forEach(input => {
    input.addEventListener('focus', () => { if (window.AAS3D) { window.AAS3D.targetColor = new THREE.Color(0x22d3ee); window.AAS3D.torusSpeed.y = -0.01; } });
    input.addEventListener('blur',  () => { if (window.AAS3D) { window.AAS3D.targetColor = new THREE.Color(0x6366f1); window.AAS3D.torusSpeed.y = -0.004; } });
  });
  document.getElementById('loginForm').addEventListener('submit', function() {
    document.getElementById('login-submit-btn').textContent = 'Signing in…';
    if (window.AAS3D) { window.AAS3D.icoSpeed.x = 0.02; window.AAS3D.icoSpeed.y = 0.025; }
  });
})();
</script>

<?php include 'footer.php'; ?>
