<?php include 'header.php'; ?>
<?php include 'config.php'; ?>

<div class="container-fluid p-0 overflow-hidden">
    <div class="row g-0 min-vh-100-nav">
        <!-- Visual Side -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-5 position-relative">
            <!-- Spatial UI Elements Container -->
            <div class="z-1 text-center text-lg-start">
                <h1 class="display-3 fw-bolder text-gradient mb-4" style="line-height: 1.1;">Discover the Future<br>of AI Tools</h1>
                <p class="fs-5 text-secondary" style="max-width: 500px;">Join the next generation of builders. Explore advanced spatial workflows and cutting-edge artificial intelligence.</p>
            </div>
            
            <!-- Abstract background glow effects mimicking 3D depth -->
            <div class="position-absolute top-0 start-0 w-100 h-100 opacity-50" style="pointer-events: none; background: radial-gradient(circle at 30% 40%, rgba(99, 102, 241, 0.3) 0%, transparent 50%), radial-gradient(circle at 70% 60%, rgba(236, 72, 153, 0.2) 0%, transparent 50%); mix-blend-mode: screen;"></div>
        </div>

        <!-- Form Side -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-md-5">
            <div class="glass-panel w-100 p-5 mx-auto" style="max-width: 480px;">
                <div class="text-center mb-5">
                    <h2 class="fw-bold mb-2">Create Account</h2>
                    <p class="text-secondary">Enter your details to get started</p>
                </div>
                
                <form action="#" method="POST">
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control rounded-4" id="usernameInput" placeholder="Username" required>
                        <label for="usernameInput">Username</label>
                    </div>
                    
                    <div class="form-floating mb-4">
                        <input type="email" class="form-control rounded-4" id="emailInput" placeholder="name@example.com" required>
                        <label for="emailInput">Email address</label>
                    </div>
                    
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control rounded-4" id="passwordInput" placeholder="Password" required>
                        <label for="passwordInput">Password</label>
                    </div>
                    
                    <button type="submit" class="btn btn-glow w-100 rounded-pill py-3 fs-5 fw-semibold mt-3">Register Now</button>
                    
                    <div class="text-center mt-4">
                        <p class="text-secondary">Already have an account? <a href="login.php" class="text-white text-decoration-none fw-semibold">Sign in here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
