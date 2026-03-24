<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - HandsOn</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="index.php" class="logo" style="justify-content: center; margin-bottom: 20px;">
                    <div class="logo-icon">🛠️</div>
                    <span>HandsOn</span>
                </a>
                <h2>Create Account</h2>
                <p>Join HandsOn to find or provide services</p>
            </div>
            
            <div class="auth-tabs">
                <div class="auth-tab active" onclick="switchTab('customer')">Customer</div>
                <div class="auth-tab" onclick="switchTab('worker')">Worker</div>
            </div>
            
            <form id="register-form" onsubmit="handleRegister(event)">
                <input type="hidden" name="role" id="user-role" value="customer">
                
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" placeholder="2547xxxxxxx" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" minlength="6" required>
                </div>
                
                <!-- Worker specific fields -->
                <div id="worker-fields" style="display: none;">
                    <div class="form-group">
                        <label class="form-label">Service Category</label>
                        <select name="category" class="form-control">
                            <option value="plumber">Plumber</option>
                            <option value="electrician">Electrician</option>
                            <option value="carpenter">Carpenter</option>
                            <option value="cleaner">Cleaner</option>
                            <option value="painter">Painter</option>
                            <option value="technician">Technician</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Experience</label>
                        <select name="experience" class="form-control">
                            <option value="1-2_years">1-2 years</option>
                            <option value="3-5_years">3-5 years</option>
                            <option value="5_plus_years">5+ years</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Create Account</button>
            </form>
            
            <p class="text-center mt-2">
                Already have an account? <a href="login.php">Login</a>
            </p>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
    
    <script>
        function switchTab(role) {
            document.querySelectorAll('.auth-tab').forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');
            
            document.getElementById('user-role').value = role;
            
            const workerFields = document.getElementById('worker-fields');
            if (role === 'worker') {
                workerFields.style.display = 'block';
            } else {
                workerFields.style.display = 'none';
            }
        }
        
        async function handleRegister(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            
            const userData = {
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                password: formData.get('password'),
                role: formData.get('role')
            };
            
            if (userData.role === 'worker') {
                userData.category = formData.get('category');
                userData.experience = formData.get('experience');
            }
            
            const btn = form.querySelector('button');
            btn.disabled = true;
            btn.textContent = 'Creating account...';
            
            try {
                await register(userData);
            } catch (error) {
                showAlert(error.message, 'error');
                btn.disabled = false;
                btn.textContent = 'Create Account';
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
