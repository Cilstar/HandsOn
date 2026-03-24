<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HandsOn</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="../index.php" class="logo" style="justify-content: center; margin-bottom: 20px;">
                    <div class="logo-icon">🛠️</div>
                    <span>HandsOn</span>
                </a>
                <h2>Welcome Back</h2>
                <p>Login to access your account</p>
            </div>
            
            <form id="login-form" onsubmit="handleLogin(event)">
                <div class="form-group">
                    <label class="form-label">Email or Phone</label>
                    <input type="text" name="email" class="form-control" placeholder="Enter email or phone" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>
            
            <p class="text-center mt-2">
                Don't have an account? <a href="register.php">Sign Up</a>
            </p>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
    
    <script>
        async function handleLogin(e) {
            e.preventDefault();
            
            const form = e.target;
            const email = form.email.value;
            const password = form.password.value;
            
            const btn = form.querySelector('button');
            btn.disabled = true;
            btn.textContent = 'Logging in...';
            
            try {
                await login(email, password);
            } catch (error) {
                showAlert(error.message, 'error');
                btn.disabled = false;
                btn.textContent = 'Login';
            }
        }
        
        // Check if already logged in
        document.addEventListener('DOMContentLoaded', async function() {
            await checkAuth();
            if (currentUser) {
                window.location.href = 'workers.php';
            }
        });
    </script>
</body>
</html>
