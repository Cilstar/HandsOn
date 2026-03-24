<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HandsOn - Find Skilled Workers Near You</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f59e0b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .landing-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .landing-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 10px;
        }
        
        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #7c3aed 0%, #f59e0b 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }
        
        .logo-text {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #7c3aed 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .tagline {
            text-align: center;
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 1rem;
        }
        
        /* Auth Tabs */
        .auth-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            background: #f3f4f6;
            padding: 4px;
            border-radius: 12px;
        }
        
        .auth-tab {
            flex: 1;
            padding: 12px;
            border: none;
            background: transparent;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s ease;
        }
        
        .auth-tab.active {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: white;
        }
        
        /* Forms */
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
        }
        
        .form-control {
            width: 100%;
            padding: 14px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            border-color: #7c3aed;
            outline: none;
        }
        
        .btn {
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            font-size: 1rem;
            width: 100%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
        }
        
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
        
        .divider span {
            padding: 0 15px;
            color: #9ca3af;
            font-size: 0.9rem;
        }
        
        /* Services Preview */
        .services-preview {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 20px;
        }
        
        .service-item {
            text-align: center;
            padding: 10px;
            background: #f9fafb;
            border-radius: 12px;
        }
        
        .service-icon {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        
        .service-name {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
        }
        
        .error-msg {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: none;
        }
        
        .error-msg.show {
            display: block;
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <div class="landing-card">
            <div class="logo">
                <div class="logo-icon">🛠️</div>
                <span class="logo-text">HandsOn</span>
            </div>
            
            <p class="tagline">Find trusted service providers near you</p>
            
            <div id="error-msg" class="error-msg"></div>
            
            <div class="auth-tabs">
                <button class="auth-tab active" onclick="showForm('login')">Login</button>
                <button class="auth-tab" onclick="showForm('register')">Sign Up</button>
            </div>
            
            <!-- Login Form -->
            <form id="login-form" onsubmit="handleLogin(event)">
                <div class="form-group">
                    <label class="form-label">Email or Phone</label>
                    <input type="text" name="email" class="form-control" placeholder="Enter email or phone" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <!-- Register Form -->
            <form id="register-form" style="display: none;" onsubmit="handleRegister(event)">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
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
                    <label class="form-label">I am a...</label>
                    <select name="role" class="form-control" id="role-select" onchange="toggleServiceCategory()">
                        <option value="customer">Customer</option>
                        <option value="provider">Service Provider</option>
                    </select>
                </div>
                
                <div class="form-group" id="service-category-group" style="display: none;">
                    <label class="form-label">Service Category</label>
                    <select name="category" class="form-control">
                        <option value="plumber">🔧 Plumber</option>
                        <option value="electrician">⚡ Electrician</option>
                        <option value="cleaner">🧹 Cleaner</option>
                        <option value="mechanic">🚗 Mechanic</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Profile Picture (optional)</label>
                    <input type="file" name="avatar" class="form-control" accept="image/*" onchange="previewAvatar(event)">
                    <input type="hidden" name="avatar_data" id="avatar-data">
                    <div id="avatar-preview" style="margin-top: 10px; display: none;">
                        <img id="avatar-preview-img" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Min 6 characters" minlength="6" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Account</button>
            </form>
            
            <div class="divider">
                <span>or continue with</span>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button class="btn" style="background: white; border: 2px solid #e5e7eb; color: #374151;" onclick="signInWithGoogle()">
                    <img src="https://www.google.com/favicon.ico" width="20" style="vertical-align: middle; margin-right: 8px;"/>
                    Google
                </button>
            </div>
            
            <p style="text-align: center; color: #6b7280; font-size: 0.9rem;">
                Continue as guest to browse providers
            </p>
            

            

            

        </div>
    </div>
    
    <script>
        function showForm(form) {
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            
            document.getElementById('login-form').style.display = form === 'login' ? 'block' : 'none';
            document.getElementById('register-form').style.display = form === 'register' ? 'block' : 'none';
        }
        
        function toggleServiceCategory() {
            const role = document.getElementById('role-select').value;
            const categoryGroup = document.getElementById('service-category-group');
            categoryGroup.style.display = role === 'provider' ? 'block' : 'none';
        }
        
        // Preview avatar before upload
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').style.display = 'block';
                    document.getElementById('avatar-preview-img').src = e.target.result;
                    document.getElementById('avatar-data').value = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
        
        // Generate avatar URL
        function getAvatarUrl(name, color) {
            return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=' + (color || '7c3aed') + '&color=fff&size=80';
        }
        
        function showError(msg) {
            const errorEl = document.getElementById('error-msg');
            errorEl.textContent = msg;
            errorEl.classList.add('show');
        }
        
        function hideError() {
            document.getElementById('error-msg').classList.remove('show');
        }
        
        // Pre-configured customer accounts
        const CUSTOMER_ACCOUNTS = [
            { id: 101, name: 'John Kamau', email: 'john.kamau@email.com', phone: '254711111111', password: 'customer123', service: 'plumber' },
            { id: 102, name: 'Mary Wanjiku', email: 'mary.wanjiku@email.com', phone: '254722222222', password: 'customer123', service: 'electrician' },
            { id: 103, name: 'David Otieno', email: 'david.otieno@email.com', phone: '254733333333', password: 'customer123', service: 'cleaner' },
            { id: 104, name: 'Sarah Akinyi', email: 'sarah.akinyi@email.com', phone: '254744444444', password: 'customer123', service: 'mechanic' },
            { id: 105, name: 'Michael Ochieng', email: 'michael.ochieng@email.com', phone: '254755555555', password: 'customer123', service: 'plumber' },
            { id: 106, name: 'Grace Kemunto', email: 'grace.kemunto@email.com', phone: '254766666666', password: 'customer123', service: 'electrician' },
            { id: 107, name: 'Peter Mwangi', email: 'peter.mwangi@email.com', phone: '254777777777', password: 'customer123', service: 'cleaner' },
            { id: 108, name: 'Faith Atieno', email: 'faith.atieno@email.com', phone: '254788888888', password: 'customer123', service: 'mechanic' }
        ];
        
        // Pre-configured service provider accounts
        const PROVIDER_ACCOUNTS = [
            { id: 201, name: 'James Ochieng', email: 'james.ochieng@email.com', phone: '254755555555', password: 'provider123', service: 'plumber', category: 'plumber', rating: 4.8, jobs: 45, hourlyRate: 800 },
            { id: 202, name: 'Francis Otieno', email: 'francis.otieno@email.com', phone: '254766666666', password: 'provider123', service: 'electrician', category: 'electrician', rating: 4.6, jobs: 38, hourlyRate: 1000 },
            { id: 203, name: 'Grace Wanjiku', email: 'grace.wanjiku@email.com', phone: '254777777777', password: 'provider123', service: 'cleaner', category: 'cleaner', rating: 4.9, jobs: 62, hourlyRate: 500 },
            { id: 204, name: 'Simon Omondi', email: 'simon.omondi@email.com', phone: '254788888888', password: 'provider123', service: 'mechanic', category: 'mechanic', rating: 4.7, jobs: 51, hourlyRate: 1200 },
            { id: 205, name: 'Peter Mwangi', email: 'peter.mwangi@email.com', phone: '254799999999', password: 'provider123', service: 'plumber', category: 'plumber', rating: 4.5, jobs: 32, hourlyRate: 750 },
            { id: 206, name: 'Vincent Kimani', email: 'vincent.kimani@email.com', phone: '254711111122', password: 'provider123', service: 'electrician', category: 'electrician', rating: 4.4, jobs: 28, hourlyRate: 900 },
            { id: 207, name: 'Mary Kemunto', email: 'mary.kemunto@email.com', phone: '254711111133', password: 'provider123', service: 'cleaner', category: 'cleaner', rating: 4.8, jobs: 55, hourlyRate: 550 },
            { id: 208, name: 'Dennis Ochieng', email: 'dennis.ochieng@email.com', phone: '254711111144', password: 'provider123', service: 'mechanic', category: 'mechanic', rating: 4.6, jobs: 42, hourlyRate: 1100 }
        ];
        
        // Admin account
        const ADMIN_ACCOUNT = { id: 1, name: 'Sylvester Omondi', email: 'omondisylvester999@gmail.com', phone: '0702857848', password: 'cilstar2022', role: 'admin' };
        
        function handleLogin(e) {
            e.preventDefault();
            hideError();
            
            const form = e.target;
            const email = form.email.value;
            const password = form.password.value;
            
            // Check admin credentials
            if (email === ADMIN_ACCOUNT.email && password === ADMIN_ACCOUNT.password) {
                localStorage.setItem('token', 'admin-token-123');
                localStorage.setItem('user', JSON.stringify(ADMIN_ACCOUNT));
                window.location.href = 'pages/admin/index.php';
                return;
            }
            
            // Check customer accounts
            const customer = CUSTOMER_ACCOUNTS.find(c => c.email === email && c.password === password);
            if (customer) {
                const avatarUrl = getAvatarUrl(customer.name, '10b981');
                const userData = { id: customer.id, name: customer.name, email: customer.email, phone: customer.phone, role: 'customer', service: customer.service, avatar: avatarUrl };
                localStorage.setItem('token', 'customer-token-' + customer.id);
                localStorage.setItem('user', JSON.stringify(userData));
                window.location.href = 'index.php?service=' + customer.service;
                return;
            }
            
            // Check provider accounts
            const provider = PROVIDER_ACCOUNTS.find(p => p.email === email && p.password === password);
            if (provider) {
                const providerColors = { 'plumber': '3b82f6', 'electrician': 'f59e0b', 'cleaner': '10b981', 'mechanic': 'ef4444' };
                const avatarUrl = getAvatarUrl(provider.name, providerColors[provider.category] || '7c3aed');
                const userData = { id: provider.id, name: provider.name, email: provider.email, phone: provider.phone, role: 'provider', category: provider.category, avatar: avatarUrl };
                localStorage.setItem('token', 'provider-token-' + provider.id);
                localStorage.setItem('user', JSON.stringify(userData));
                window.location.href = 'pages/provider-dashboard.php';
                return;
            }
            
            // Show error if no match
            showError('Invalid email or password. Please check your credentials.');
        }
        
        function handleRegister(e) {
            e.preventDefault();
            hideError();
            
            const form = e.target;
            const role = form.role.value;
            const avatarData = form.avatar_data.value;
            
            const newUser = {
                id: Date.now(),
                name: form.name.value,
                email: form.email.value,
                phone: form.phone.value,
                role: role,
                avatar: avatarData || getAvatarUrl(form.name.value)
            };
            
            // If provider, add category
            if (role === 'provider') {
                newUser.category = form.category.value;
            }
            
            localStorage.setItem('token', 'new-user-token-' + newUser.id);
            localStorage.setItem('user', JSON.stringify(newUser));
            
            if (newUser.role === 'provider') {
                window.location.href = 'pages/provider-dashboard.php';
            } else {
                window.location.href = 'index.php';
            }
        }
        

        
        // Check if already logged in
        const token = localStorage.getItem('token');
        const user = localStorage.getItem('user');
        
        if (token && user) {
            const userData = JSON.parse(user);
            if (userData.role === 'admin') {
                window.location.href = 'pages/admin/index.php';
            } else if (userData.role === 'provider') {
                window.location.href = 'pages/provider-dashboard.php';
            } else {
                const service = userData.service || '';
                window.location.href = 'index.php' + (service ? '?service=' + service : '');
            }
        }
        
        // Add logout function to global scope
        window.logout = function() {
            localStorage.removeItem('user');
            localStorage.removeItem('token');
            localStorage.removeItem('selectedService');
            window.location.href = '/handsonapplication/landing.php';
        };
        
        // Google Sign-In (simulated)
        function signInWithGoogle() {
            // In a real app, this would use Google Identity Services
            // For demo, we'll simulate a Google login
            const googleUser = {
                id: Date.now(),
                name: 'Google User',
                email: 'user@gmail.com',
                phone: '254700000000',
                role: 'customer',
                provider: 'google',
                avatar: getAvatarUrl('Google User', '4285f4')
            };
            
            localStorage.setItem('token', 'google-token-' + Date.now());
            localStorage.setItem('user', JSON.stringify(googleUser));
            window.location.href = 'index.php';
        }
    </script>
</body>
</html>
