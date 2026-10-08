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
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #f59e0b 100%);
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
            
            <p style="text-align: center; margin-top: 15px;">
                <a href="#" onclick="showHowItWorks(); return false;" style="color: #7c3aed; text-decoration: underline; font-weight: 600;">How It Works</a>
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
            try {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File too large. Maximum size is 2MB.');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('avatar-preview').style.display = 'block';
                        document.getElementById('avatar-preview-img').src = e.target.result;
                        document.getElementById('avatar-data').value = e.target.result;
                    };
                    reader.onerror = function() {
                        alert('Error reading image file. Please try another image.');
                    };
                    reader.readAsDataURL(file);
                }
            } catch (err) {
                console.log('Image preview error:', err);
                // Use default avatar on error
                document.getElementById('avatar-data').value = '';
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
            { id: 201, name: 'James Ochieng', email: 'james.ochieng@email.com', phone: '254755555555', password: 'provider123', service: 'plumber', category: 'plumber', rating: 4.8, jobs: 45, hourlyRate: 500 },
            { id: 202, name: 'Francis Otieno', email: 'francis.otieno@email.com', phone: '254766666666', password: 'provider123', service: 'electrician', category: 'electrician', rating: 4.6, jobs: 38, hourlyRate: 600 },
            { id: 203, name: 'Grace Wanjiku', email: 'grace.wanjiku@email.com', phone: '254777777777', password: 'provider123', service: 'cleaner', category: 'cleaner', rating: 4.9, jobs: 62, hourlyRate: 300 },
            { id: 204, name: 'Simon Omondi', email: 'simon.omondi@email.com', phone: '254788888888', password: 'provider123', service: 'mechanic', category: 'mechanic', rating: 4.7, jobs: 51, hourlyRate: 700 },
            { id: 205, name: 'Peter Mwangi', email: 'peter.mwangi@email.com', phone: '254799999999', password: 'provider123', service: 'plumber', category: 'plumber', rating: 4.5, jobs: 32, hourlyRate: 450 },
            { id: 206, name: 'Vincent Kimani', email: 'vincent.kimani@email.com', phone: '254711111122', password: 'provider123', service: 'electrician', category: 'electrician', rating: 4.4, jobs: 28, hourlyRate: 550 },
            { id: 207, name: 'Mary Kemunto', email: 'mary.kemunto@email.com', phone: '254711111133', password: 'provider123', service: 'cleaner', category: 'cleaner', rating: 4.8, jobs: 55, hourlyRate: 350 },
            { id: 208, name: 'Dennis Ochieng', email: 'dennis.ochieng@email.com', phone: '254711111144', password: 'provider123', service: 'mechanic', category: 'mechanic', rating: 4.6, jobs: 42, hourlyRate: 650 }
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
        
        // How It Works modal
        window.showHowItWorks = function() {
            document.getElementById('how-it-works-modal').style.display = 'block';
        }
        
        window.closeHowItWorks = function() {
            document.getElementById('how-it-works-modal').style.display = 'none';
        }
        
        // Technical Spec modal functions
        window.showTechSpec = function() {
            document.getElementById('tech-spec-modal').style.display = 'block';
        }
        
        window.closeTechSpec = function() {
            document.getElementById('tech-spec-modal').style.display = 'none';
        }
    </script>
    
    <!-- How It Works Modal -->
    <div id="how-it-works-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 2000; overflow-y: auto;">
        <div style="background: white; max-width: 600px; margin: 40px auto; border-radius: 20px; padding: 30px; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: #7c3aed;">How HandsOn Works</h2>
                <button onclick="closeHowItWorks()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">✕</button>
            </div>
            
            <div style="display: grid; gap: 20px;">
                <div style="display: flex; gap: 15px; align-items: start; padding: 15px; background: #f9fafb; border-radius: 12px;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #7c3aed, #a855f7); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0;">1</div>
                    <div>
                        <h4 style="color: #374151; margin-bottom: 5px;">Create an Account</h4>
                        <p style="color: #6b7280; font-size: 0.9rem;">Sign up as a customer or service provider</p>
                    </div>
                </div>
                
                <div style="display: flex; gap: 15px; align-items: start; padding: 15px; background: #f9fafb; border-radius: 12px;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #7c3aed, #a855f7); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0;">2</div>
                    <div>
                        <h4 style="color: #374151; margin-bottom: 5px;">Find a Service Provider</h4>
                        <p style="color: #6b7280; font-size: 0.9rem;">Browse the interactive map to find nearby verified plumbers, electricians, or other skilled workers</p>
                    </div>
                </div>
                
                <div style="display: flex; gap: 15px; align-items: start; padding: 15px; background: #f9fafb; border-radius: 12px;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #7c3aed, #a855f7); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0;">3</div>
                    <div>
                        <h4 style="color: #374151; margin-bottom: 5px;">View Profiles & Reviews</h4>
                        <p style="color: #6b7280; font-size: 0.9rem;">Check verified profiles, ratings, and reviews from other customers</p>
                    </div>
                </div>
                
                <div style="display: flex; gap: 15px; align-items: start; padding: 15px; background: #f9fafb; border-radius: 12px;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #7c3aed, #a855f7); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0;">4</div>
                    <div>
                        <h4 style="color: #374151; margin-bottom: 5px;">Book a Service</h4>
                        <p style="color: #6b7280; font-size: 0.9rem;">Request a service and discuss the details with the provider</p>
                    </div>
                </div>
                
                <div style="display: flex; gap: 15px; align-items: start; padding: 15px; background: #f9fafb; border-radius: 12px;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #7c3aed, #a855f7); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0;">5</div>
                    <div>
                        <h4 style="color: #374151; margin-bottom: 5px;">Pay & Review</h4>
                        <p style="color: #6b7280; font-size: 0.9rem;">Make secure payment via M-Pesa and rate your experience</p>
                    </div>
                </div>
            </div>
            
            <h4 style="color: #7c3aed; margin-top: 20px; margin-bottom: 10px;">Key Features</h4>
            <ul style="margin-left: 20px; color: #374151; line-height: 1.8;">
                <li>📍 Location-based worker identification using GPS</li>
                <li>✓ Verified skilled worker profiles</li>
                <li>🔧 Core trades: plumbing, electrical, carpentry, cleaning</li>
                <li>📱 Mobile payment support (M-Pesa)</li>
                <li>⭐ Job-matching by proximity, ratings, availability</li>
            </ul>
            
            <p style="margin-top: 25px; padding: 15px; background: linear-gradient(135deg, #7c3aed 0%, #f59e0b 100%); color: white; border-radius: 10px; text-align: center; font-weight: 600;">
                Pilot Area: Roysambu, Nairobi
            </p>
            
            <p style="text-align: center; margin-top: 15px;">
                <a href="#" onclick="showTechSpec(); return false;" style="color: #7c3aed; text-decoration: underline; font-weight: 600;">Technical Details</a>
            </p>
        </div>
    </div>
    
    <!-- Technical Spec Modal -->
    <div id="tech-spec-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 2000; overflow-y: auto;">
        <div style="background: white; max-width: 600px; margin: 40px auto; border-radius: 20px; padding: 30px; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: #7c3aed;">Technical Specification</h2>
                <button onclick="closeTechSpec()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">✕</button>
            </div>
            
            <div style="color: #374151; line-height: 1.7;">
                <h4 style="color: #7c3aed;">System Architecture</h4>
                <ul style="margin-left: 20px;">
                    <li><strong>Frontend:</strong> HTML5, CSS3, JavaScript</li>
                    <li><strong>Backend:</strong> PHP with REST API</li>
                    <li><strong>Database:</strong> MySQL</li>
                    <li><strong>Maps:</strong> Leaflet.js with OpenStreetMap</li>
                    <li><strong>Payments:</strong> M-Pesa Integration</li>
                </ul>
                
                <h4 style="color: #7c3aed; margin-top: 20px;">Key Components</h4>
                <ul style="margin-left: 20px;">
                    <li>Location-based service matching</li>
                    <li>Real-time worker tracking</li>
                    <li>Secure user authentication</li>
                    <li>Rating and review system</li>
                    <li>Job booking management</li>
                    <li>Payment processing</li>
                </ul>
                
                <h4 style="color: #7c3aed; margin-top: 20px;">Database Tables</h4>
                <ul style="margin-left: 20px;">
                    <li>Users - user accounts</li>
                    <li>Service_Providers - worker profiles</li>
                    <li>Service_Categories - skill categories</li>
                    <li>Service_Requests - job bookings</li>
                    <li>Payments - transaction records</li>
                </ul>
                
                <p style="margin-top: 20px; padding: 12px; background: #f3f4f6; border-radius: 8px; text-align: center;">
                    <strong>Pilot:</strong> Roysambu, Nairobi
                </p>
            </div>
        </div>
    </div>
</body>
</html>
