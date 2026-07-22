<?php
// ── Phase 2: Register handler — before any HTML ──────────────
require_once 'config.php';

$errors   = [];
$success  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    // ── Validation ────────────────────────────────────────────
    if (empty($username) || strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    // ── Check email uniqueness ────────────────────────────────
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'An account with this email already exists.';
        }
        $stmt->close();
    }

    // ── Insert user ───────────────────────────────────────────
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare(
            "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'student')"
        );
        $stmt->bind_param('sss', $username, $email, $hash);
        if ($stmt->execute()) {
            // Auto-login after register
            $_SESSION['user_id']  = $stmt->insert_id;
            $_SESSION['username'] = $username;
            $_SESSION['role']     = 'student';
            $stmt->close();
            header("Location: login.php?registered=1");
            exit();
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
        $stmt->close();
    }
}
?>
<?php include 'header.php'; ?>

<style>
  .auth-label {
    display: block;
    font-size: 0.78rem;
    font-weight: 600;
    color: rgba(255,255,255,0.55);
    text-transform: uppercase;
    letter-spacing: 0.07em;
    margin-bottom: 8px;
  }
  .divider {
    display: flex; align-items: center; gap: 12px;
    color: rgba(255,255,255,0.2); font-size: 0.75rem;
  }
  .divider::before, .divider::after {
    content: ''; flex: 1; height: 1px;
    background: rgba(255,255,255,0.08);
  }
  .social-btn {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px; border-radius: 12px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem; font-weight: 500; cursor: pointer;
    transition: all 0.2s; width: 100%; font-family: 'Inter', sans-serif;
  }
  .social-btn:hover { background: rgba(255,255,255,0.08); color: #fff; border-color: rgba(255,255,255,0.2); }
  .strength-bar { height: 4px; border-radius: 99px; transition: all 0.4s ease; }
  .alert-error {
    background: rgba(239,68,68,0.12);
    border: 1px solid rgba(239,68,68,0.25);
    border-radius: 12px; padding: 12px 16px;
    color: #fca5a5; font-size: 0.85rem;
    margin-bottom: 1.25rem;
  }
  .alert-success {
    background: rgba(16,185,129,0.12);
    border: 1px solid rgba(16,185,129,0.25);
    border-radius: 12px; padding: 12px 16px;
    color: #6ee7b7; font-size: 0.85rem;
    margin-bottom: 1.25rem;
  }
</style>

<main class="min-h-screen flex items-center justify-center pt-24 pb-16 px-4">
  <div class="w-full max-w-5xl mx-auto flex flex-col lg:flex-row items-center gap-12 lg:gap-20">

    <!-- Left branding -->
    <div class="flex-1 text-center lg:text-left pointer-events-none select-none hidden lg:block">
      <div class="inline-flex items-center gap-2 mb-6 glass rounded-full px-4 py-2">
        <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse inline-block"></span>
        <span class="text-xs font-semibold text-white/60 uppercase tracking-widest">Join the Community</span>
      </div>
      <h2 class="text-4xl xl:text-5xl font-black text-white leading-tight mb-4">
        Build the<br><span class="text-gradient">Future with AI</span>
      </h2>
      <p class="text-white/45 text-base leading-relaxed max-w-sm">
        Join builders, researchers, and creators who discover their next favourite AI tool here.
      </p>
      <div class="mt-8 space-y-3">
        <?php foreach(['Free forever — no credit card needed','Personalised tool recommendations','Exclusive early access to new tools'] as $b): ?>
        <div class="flex items-center gap-3 text-white/50 text-sm">
          <div class="w-5 h-5 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 flex-shrink-0 flex items-center justify-center">
            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
          </div><?= htmlspecialchars($b) ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Register glass form -->
    <div class="w-full max-w-md">
      <div class="glass-deep rounded-3xl p-8 shadow-2xl shadow-black/50">

        <div class="text-center mb-7">
          <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center text-white font-black text-lg mx-auto mb-4 shadow-lg shadow-purple-500/30">✨</div>
          <h1 class="text-2xl font-black text-white mb-1">Create Account</h1>
          <p class="text-white/40 text-sm">Start your AI journey today</p>
        </div>

        <!-- Error / success alerts -->
        <?php if (!empty($errors)): ?>
        <div class="alert-error">
          <?php foreach ($errors as $err): ?>
            <div>⚠ <?= htmlspecialchars($err) ?></div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Social signup (UI only) -->
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

        <div class="divider mb-6">or register with email</div>

        <!-- ── Register form (POST) ───────────────────────── -->
        <form action="register.php" method="POST" id="registerForm">
          <div class="mb-4">
            <label for="usernameInput" class="auth-label">Username</label>
            <input type="text" id="usernameInput" name="username" required
                   class="glass-input"
                   placeholder="Choose a username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
          </div>
          <div class="mb-4">
            <label for="emailInput" class="auth-label">Email Address</label>
            <input type="email" id="emailInput" name="email" required
                   class="glass-input"
                   placeholder="name@example.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
          <div class="mb-2">
            <label for="passwordInput" class="auth-label">Password</label>
            <input type="password" id="passwordInput" name="password" required
                   class="glass-input" placeholder="Create a strong password"
                   oninput="updateStrength(this.value)">
          </div>

          <!-- Strength meter -->
          <div class="flex gap-1.5 mb-5" id="strength-bars">
            <div class="strength-bar flex-1 bg-white/10" id="bar1"></div>
            <div class="strength-bar flex-1 bg-white/10" id="bar2"></div>
            <div class="strength-bar flex-1 bg-white/10" id="bar3"></div>
            <div class="strength-bar flex-1 bg-white/10" id="bar4"></div>
          </div>

          <div class="flex items-start gap-2 mb-6">
            <input type="checkbox" id="terms" required class="w-4 h-4 mt-0.5 rounded flex-shrink-0" style="accent-color:#8b5cf6;">
            <label for="terms" class="text-white/40 text-xs cursor-pointer leading-relaxed">
              I agree to the <a href="#" class="text-white/70 hover:text-white no-underline">Terms of Service</a> and <a href="#" class="text-white/70 hover:text-white no-underline">Privacy Policy</a>
            </label>
          </div>

          <button type="submit" id="register-submit-btn" class="btn-glow w-full py-3 rounded-2xl text-base font-bold tracking-wide relative z-10">
            Create Account ✨
          </button>
        </form>

        <p class="text-center text-white/35 text-sm mt-6">
          Already have an account?
          <a href="login.php" class="text-white font-semibold hover:text-cyan-400 transition-colors no-underline ml-1">Sign in</a>
        </p>
      </div>
    </div>
  </div>
</main>

<script>
function updateStrength(value) {
  const colors = ['#ef4444','#f59e0b','#22d3ee','#10b981'];
  let score = 0;
  if (value.length >= 6)          score++;
  if (/[A-Z]/.test(value))        score++;
  if (/[0-9]/.test(value))        score++;
  if (/[^A-Za-z0-9]/.test(value)) score++;
  ['bar1','bar2','bar3','bar4'].forEach((id, i) => {
    document.getElementById(id).style.background = i < score ? colors[score-1] : 'rgba(255,255,255,0.08)';
  });
  if (window.AAS3D && score > 0) {
    window.AAS3D.targetColor = new THREE.Color([0xef4444,0xf59e0b,0x22d3ee,0x10b981][score-1]);
  }
}
document.querySelectorAll('.glass-input').forEach(input => {
  input.addEventListener('focus', () => { if (window.AAS3D) { window.AAS3D.icoSpeed.x = 0.008; window.AAS3D.icoSpeed.y = 0.010; } });
  input.addEventListener('blur',  () => { if (window.AAS3D) { window.AAS3D.icoSpeed.x = 0.003; window.AAS3D.icoSpeed.y = 0.004; } });
});
document.getElementById('registerForm').addEventListener('submit', function() {
  document.getElementById('register-submit-btn').textContent = 'Creating account…';
  if (window.AAS3D) { window.AAS3D.targetColor = new THREE.Color(0xa855f7); window.AAS3D.icoSpeed.x = 0.03; }
});
</script>

<?php include 'footer.php'; ?>
