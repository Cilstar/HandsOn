<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Dashboard - HandsOn</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #7c3aed;
            --secondary: #f59e0b;
            --success: #10b981;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f3f4f6;
        }
        
        .provider-dashboard { display: flex; min-height: 100vh; }
        
        .provider-sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            color: white;
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .provider-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .provider-logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #7c3aed 0%, #f59e0b 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .provider-info {
            text-align: center;
            padding: 20px;
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            margin-bottom: 20px;
        }
        
        .provider-avatar-small {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed 0%, #f59e0b 100%);
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            border: 4px solid white;
        }
        
        .provider-name { font-weight: 600; font-size: 1.1rem; margin-bottom: 4px; }
        
        .provider-category-badge {
            display: inline-block;
            padding: 4px 12px;
            background: linear-gradient(135deg, #7c3aed 0%, #f59e0b 100%);
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .provider-rating { margin-top: 8px; color: #fbbf24; }
        
        .provider-menu { list-style: none; padding: 0; }
        
        .provider-menu-item { margin-bottom: 8px; }
        
        .provider-menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .provider-menu-link:hover, .provider-menu-link.active {
            background: linear-gradient(135deg, #7c3aed 0%, #f59e0b 100%);
            color: white;
        }
        
        .provider-main { flex: 1; margin-left: 260px; padding: 30px; }
        
        .provider-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .provider-title { color: #1f2937; font-size: 1.8rem; }
        
        .provider-status-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .status-switch {
            position: relative;
            width: 60px;
            height: 30px;
        }
        
        .status-switch input { opacity: 0; width: 0; height: 0; }
        
        .status-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 30px;
        }
        
        .status-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .status-slider { background: #10b981; }
        input:checked + .status-slider:before { transform: translateX(30px); }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .stat-box-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 12px;
        }
        
        .stat-box-value { font-size: 1.8rem; font-weight: 700; color: #1f2937; }
        .stat-box-label { color: #6b7280; font-size: 0.9rem; }
        
        .jobs-section {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .jobs-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .jobs-title { font-size: 1.2rem; font-weight: 600; }
        
        .job-card {
            display: flex;
            gap: 16px;
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 12px;
        }
        
        .job-card:hover { border-color: #7c3aed; }
        
        .job-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed 0%, #f59e0b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .job-info { flex: 1; }
        .job-customer { font-weight: 600; margin-bottom: 4px; }
        .job-service { color: #7c3aed; font-size: 0.9rem; margin-bottom: 4px; }
        .job-location { color: #6b7280; font-size: 0.85rem; }
        .job-amount { font-weight: 700; color: #10b981; font-size: 1.1rem; }
        
        .job-actions { display: flex; gap: 8px; margin-top: 12px; }
        
        .btn-accept {
            background: #10b981;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
        }
        
        .btn-decline {
            background: #fee2e2;
            color: #dc2626;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .review-card {
            display: flex;
            gap: 12px;
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .review-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed 0%, #f59e0b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .review-content { flex: 1; }
        .review-header { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .review-name { font-weight: 600; }
        .review-stars { color: #fbbf24; }
        .review-text { color: #6b7280; font-size: 0.9rem; }
    </style>
    
    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
</head>
<body>
    <div class="provider-dashboard">
        <!-- Sidebar -->
        <aside class="provider-sidebar">
            <div class="provider-logo">
                <div class="provider-logo-icon">🛠️</div>
                <h2>HandsOn</h2>
            </div>
            
            <div class="provider-info">
                <div class="provider-avatar-small" id="provider-avatar">
                    <img src="" alt="Profile" id="provider-avatar-img" style="width:100%;height:100%;border-radius:50%;object-fit:cover;display:none;">
                </div>
                <div class="provider-name" id="provider-name">Loading...</div>
                <div class="provider-category-badge" id="provider-category">🔧 Loading...</div>
                <div class="provider-rating" id="provider-rating"></div>
                <div class="provider-rating">⭐ 4.8 (12 reviews)</div>
            </div>
            
            <ul class="provider-menu">
                <li class="provider-menu-item">
                    <a href="#" class="provider-menu-link active" onclick="showProviderSection('dashboard')">
                        <span>📊</span> Dashboard
                    </a>
                </li>
                <li class="provider-menu-item">
                    <a href="#" class="provider-menu-link" onclick="showProviderSection('jobs')">
                        <span>📋</span> My Jobs
                    </a>
                </li>
                <li class="provider-menu-item">
                    <a href="#" class="provider-menu-link" onclick="showProviderSection('earnings')">
                        <span>💰</span> Earnings
                    </a>
                </li>
                <li class="provider-menu-item">
                    <a href="#" class="provider-menu-link" onclick="showProviderSection('reviews')">
                        <span>⭐</span> Reviews
                    </a>
                </li>
                <li class="provider-menu-item">
                    <a href="#" class="provider-menu-link" onclick="showProviderSection('profile')">
                        <span>👤</span> My Profile
                    </a>
                </li>
                <li class="provider-menu-item" style="margin-top: 40px;">
                    <a href="#" class="provider-menu-link" onclick="logout()">
                        <span>🚪</span> Logout
                    </a>
                </li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="provider-main">
            <div class="provider-header">
                <h1 class="provider-title">Welcome back, Simon! 👋</h1>
                <div class="provider-status-toggle">
                    <span>Available for jobs</span>
                    <label class="status-switch">
                        <input type="checkbox" checked>
                        <span class="status-slider"></span>
                    </label>
                </div>
            </div>
            
            <!-- Dashboard Section -->
            <div id="dashboard-section">
            <!-- Stats -->
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-box-icon" style="background: #dbeafe; color: #3b82f6;">💰</div>
                    <div class="stat-box-value" id="stat-earnings">KSh 0</div>
                    <div class="stat-box-label">This Month</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-icon" style="background: #d1fae5; color: #10b981;">✓</div>
                    <div class="stat-box-value" id="stat-jobs">0</div>
                    <div class="stat-box-label">Jobs Completed</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-icon" style="background: #fef3c7; color: #f59e0b;">⭐</div>
                    <div class="stat-box-value" id="stat-rating">4.8</div>
                    <div class="stat-box-label">Average Rating</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-icon" style="background: #ede9fe; color: #7c3aed;">👥</div>
                    <div class="stat-box-value" id="stat-customers">0</div>
                    <div class="stat-box-label">Repeat Customers</div>
                </div>
            </div>
            
            <!-- Available Jobs -->
            <div class="jobs-section">
                <div class="jobs-header">
                    <h3 class="jobs-title">🔔 Available Jobs Near You</h3>
                </div>
                
                <p style="text-align: center; color: #6b7280; padding: 30px;">
                    No jobs available yet. Customers will send you requests when they need your services.
                </p>
            </div>
            </div>
            
            <!-- My Jobs Section -->
            <div id="jobs-section" style="display: none;">
                <div class="jobs-section">
                    <div class="jobs-header">
                        <h3 class="jobs-title">📋 My Jobs</h3>
                    </div>
                    <p style="text-align: center; color: #6b7280; padding: 30px;">
                        No jobs yet. Accept job requests from customers to start working.
                    </p>
                </div>
            </div>
            
            <!-- Earnings Section -->
            <div id="earnings-section" style="display: none;">
                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-box-icon" style="background: #dbeafe; color: #3b82f6;">💰</div>
                        <div class="stat-box-value">KSh 0</div>
                        <div class="stat-box-label">Total Earnings</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-icon" style="background: #d1fae5; color: #10b981;">📈</div>
                        <div class="stat-box-value">KSh 0</div>
                        <div class="stat-box-label">Pending Payment</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-icon" style="background: #fef3c7; color: #f59e0b;">✓</div>
                        <div class="stat-box-value">KSh 0</div>
                        <div class="stat-box-label">Paid Out</div>
                    </div>
                </div>
                <div class="jobs-section">
                    <div class="jobs-header">
                        <h3 class="jobs-title">💳 Payment History</h3>
                    </div>
                    <p style="text-align: center; color: #6b7280; padding: 30px;">
                        No payment history yet. Complete jobs to start earning.
                    </p>
                </div>
            </div>
            
            <!-- Reviews Section -->
            <div id="reviews-section" style="display: none;">
                <div class="jobs-section">
                    <div class="jobs-header">
                        <h3 class="jobs-title">⭐ My Reviews</h3>
                    </div>
                    <p style="text-align: center; color: #6b7280; padding: 30px;">
                        No reviews yet. Complete jobs to receive reviews from customers.
                    </p>
                </div>
            </div>
            
            <!-- Profile Section -->
            <div id="profile-section" style="display: none;">
                <div class="jobs-section">
                    <div class="jobs-header">
                        <h3 class="jobs-title">👤 My Profile</h3>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <!-- Profile Photo -->
                        <div class="card" style="background: white; padding: 25px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                            <h4 style="margin-bottom: 16px;">Profile Photo</h4>
                            <div style="text-align: center; margin-bottom: 16px;">
                                <div class="provider-avatar-small" id="profile-avatar" style="width: 100px; height: 100px; margin: 0 auto;">
                                    <img id="profile-avatar-img" src="" alt="Profile" style="width:100%;height:100%;border-radius:50%;object-fit:cover;display:none;">
                                </div>
                            </div>
                            <label style="display: block; background: #7c3aed; color: white; padding: 12px; border-radius: 8px; text-align: center; cursor: pointer; margin-bottom: 8px;">
                                📷 Upload Photo
                                <input type="file" id="profile-photo-input" accept="image/*" style="display: none;" onchange="handlePhotoUpload(this)">
                            </label>
                            <p style="color: #6b7280; font-size: 0.85rem; text-align: center;">JPG, PNG, GIF or WebP. Max 2MB</p>
                        </div>
                        
                        <!-- Profile Details -->
                        <div class="card" style="background: white; padding: 25px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                            <h4 style="margin-bottom: 16px;">Profile Details</h4>
                            
                            <div class="form-group" style="margin-bottom: 16px;">
                                <label style="display: block; margin-bottom: 6px; font-weight: 600;">Full Name</label>
                                <input type="text" id="profile-name" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 16px;">
                                <label style="display: block; margin-bottom: 6px; font-weight: 600;">Phone Number</label>
                                <input type="tel" id="profile-phone" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 16px;">
                                <label style="display: block; margin-bottom: 6px; font-weight: 600;">Bio</label>
                                <textarea id="profile-bio" class="form-control" rows="3" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; resize: vertical;" placeholder="Tell customers about your services..."></textarea>
                            </div>
                            
                            <button onclick="saveProfile()" style="width: 100%; background: #10b981; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600;">💾 Save Changes</button>
                        </div>
                    </div>
                    
                    <!-- Location Settings -->
                    <div class="card" style="background: white; padding: 25px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: 20px;">
                        <h4 style="margin-bottom: 16px;">📍 Location Settings</h4>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 6px; font-weight: 600;">Latitude</label>
                                <input type="number" step="0.0001" id="profile-latitude" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
                            </div>
                            
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 6px; font-weight: 600;">Longitude</label>
                                <input type="number" step="0.0001" id="profile-longitude" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
                            </div>
                        </div>
                        
                        <button onclick="getCurrentLocation()" style="margin-top: 10px; background: #3b82f6; color: white; border: none; padding: 10px 16px; border-radius: 8px; cursor: pointer;">📍 Get Current Location</button>
                        <button onclick="saveLocation()" style="margin-top: 10px; margin-left: 10px; background: #10b981; color: white; border: none; padding: 10px 16px; border-radius: 8px; cursor: pointer;">💾 Save Location</button>
                    </div>
                </div>
            </div>
            

        </main>
    </div>
    
    <script>
        // Generate avatar URL
        function getProviderAvatarUrl(name, category) {
            const colors = { 'Plumber': '3b82f6', 'Electrician': 'f59e0b', 'Cleaner': '10b981', 'Mechanic': 'ef4444' };
            const color = colors[category] || '7c3aed';
            return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=' + color + '&color=fff&size=80';
        }
        
        // Provider data with ratings - all showing 0 since no real work done
        const PROVIDER_DATA = {
            'james.ochieng@email.com': { name: 'James Ochieng', category: 'Plumber', categoryIcon: '🔧', rating: '⭐⭐⭐⭐', ratingNum: '0.0', jobs: 0, earnings: 'KSh 0', customers: 0 },
            'francis.otieno@email.com': { name: 'Francis Otieno', category: 'Electrician', categoryIcon: '⚡', rating: '⭐⭐⭐⭐', ratingNum: '0.0', jobs: 0, earnings: 'KSh 0', customers: 0 },
            'grace.wanjiku@email.com': { name: 'Grace Wanjiku', category: 'Cleaner', categoryIcon: '🧹', rating: '⭐⭐⭐⭐', ratingNum: '0.0', jobs: 0, earnings: 'KSh 0', customers: 0 },
            'simon.omondi@email.com': { name: 'Simon Omondi', category: 'Mechanic', categoryIcon: '🚗', rating: '⭐⭐⭐⭐', ratingNum: '0.0', jobs: 0, earnings: 'KSh 0', customers: 0 },
            'peter.mwangi@email.com': { name: 'Peter Mwangi', category: 'Plumber', categoryIcon: '🔧', rating: '⭐⭐⭐⭐', ratingNum: '0.0', jobs: 0, earnings: 'KSh 0', customers: 0 },
            'vincent.kimani@email.com': { name: 'Vincent Kimani', category: 'Electrician', categoryIcon: '⚡', rating: '⭐⭐⭐⭐', ratingNum: '0.0', jobs: 0, earnings: 'KSh 0', customers: 0 },
            'mary.kemunto@email.com': { name: 'Mary Kemunto', category: 'Cleaner', categoryIcon: '🧹', rating: '⭐⭐⭐⭐', ratingNum: '0.0', jobs: 0, earnings: 'KSh 0', customers: 0 },
            'dennis.ochieng@email.com': { name: 'Dennis Ochieng', category: 'Mechanic', categoryIcon: '🚗', rating: '⭐⭐⭐⭐', ratingNum: '0.0', jobs: 0, earnings: 'KSh 0', customers: 0 }
        };
        
        // Load provider data on page load
        document.addEventListener('DOMContentLoaded', function() {
            const userData = JSON.parse(localStorage.getItem('user'));
            if (userData && userData.email) {
                const providerInfo = PROVIDER_DATA[userData.email];
                if (providerInfo) {
                    document.getElementById('provider-name').textContent = providerInfo.name;
                    document.getElementById('provider-category').textContent = providerInfo.categoryIcon + ' ' + providerInfo.category;
                    document.getElementById('provider-rating').textContent = providerInfo.rating + ' (' + providerInfo.jobs + ' jobs)';
                    
                    // Set profile picture
                    const avatarImg = document.getElementById('provider-avatar-img');
                    if (avatarImg) {
                        avatarImg.src = getProviderAvatarUrl(providerInfo.name, providerInfo.category);
                        avatarImg.style.display = 'block';
                    }
                    
                    // Update stats
                    document.getElementById('stat-jobs').textContent = providerInfo.jobs;
                    document.getElementById('stat-earnings').textContent = providerInfo.earnings;
                    document.getElementById('stat-rating').textContent = providerInfo.ratingNum;
                    document.getElementById('stat-customers').textContent = providerInfo.customers;
                } else {
                    // Default values for newly registered providers - all zeros
                    document.getElementById('provider-name').textContent = userData.name || 'Provider';
                    document.getElementById('provider-category').textContent = (userData.category ? getCategoryIcon(userData.category) + ' ' + userData.category : 'Service Provider');
                    document.getElementById('provider-rating').textContent = '⭐⭐⭐⭐ (0 jobs)';
                    
                    // Set default avatar for new providers
                    const avatarImg = document.getElementById('provider-avatar-img');
                    if (avatarImg) {
                        avatarImg.src = getProviderAvatarUrl(userData.name || 'Provider', '7c3aed');
                        avatarImg.style.display = 'block';
                    }
                    
                    // Reset stats to 0
                    document.getElementById('stat-jobs').textContent = '0';
                    document.getElementById('stat-earnings').textContent = 'KSh 0';
                    document.getElementById('stat-rating').textContent = '0.0';
                    document.getElementById('stat-customers').textContent = '0';
                }
            }
        });
        
        function getCategoryIcon(category) {
            const icons = { 'plumber': '🔧', 'electrician': '⚡', 'cleaner': '🧹', 'mechanic': '🚗' };
            return icons[category] || '🔧';
        }
        
        // Show/hide provider dashboard sections
        function showProviderSection(section) {
            // Hide all sections
            document.getElementById('dashboard-section').style.display = 'none';
            document.getElementById('jobs-section').style.display = 'none';
            document.getElementById('earnings-section').style.display = 'none';
            document.getElementById('reviews-section').style.display = 'none';
            document.getElementById('profile-section').style.display = 'none';
            
            // Remove active class from all menu links
            document.querySelectorAll('.provider-menu-link').forEach(link => link.classList.remove('active'));
            
            // Show selected section
            document.getElementById(section + '-section').style.display = 'block';
            
            // Add active class to clicked menu item (find by onclick)
            const menuLinks = document.querySelectorAll('.provider-menu-link');
            menuLinks.forEach(link => {
                if (link.getAttribute('onclick') && link.getAttribute('onclick').includes(section)) {
                    link.classList.add('active');
                }
            });
            
            // Load profile data if profile section
            if (section === 'profile') {
                loadProfileData();
            }
            
            // Load jobs if jobs section
            if (section === 'jobs') {
                loadProviderJobs();
            }
            
            // Update page title
            const titles = {
                'dashboard': 'Welcome back, ',
                'jobs': 'My Jobs',
                'earnings': 'My Earnings',
                'reviews': 'My Reviews',
                'profile': 'My Profile'
            };
            
            const userData = JSON.parse(localStorage.getItem('user'));
            const providerInfo = PROVIDER_DATA[userData?.email];
            const name = providerInfo ? providerInfo.name : (userData?.name || 'Provider');
            
            if (section === 'dashboard') {
                document.querySelector('.provider-title').textContent = 'Welcome back, ' + name.split(' ')[0] + '! 👋';
            } else {
                document.querySelector('.provider-title').textContent = titles[section];
            }
        }
        
        // Logout function
        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            localStorage.removeItem('selectedService');
            // Use absolute path from web root
            window.location.href = '/handsonapplication/landing.php';
        }
        
        // Load profile data
        async function loadProfileData() {
            try {
                const userData = JSON.parse(localStorage.getItem('user'));
                if (!userData) return;
                
                // Get user info from API
                const data = await apiCall('auth/user.php');
                
                if (data.user) {
                    document.getElementById('profile-name').value = data.user.name || '';
                    document.getElementById('profile-phone').value = data.user.phone || '';
                    
                    // Set profile photo
                    const avatarImg = document.getElementById('profile-avatar-img');
                    if (data.user.profile_photo) {
                        avatarImg.src = data.user.profile_photo;
                        avatarImg.style.display = 'block';
                    } else {
                        avatarImg.style.display = 'none';
                    }
                }
                
                if (data.worker_profile) {
                    document.getElementById('profile-bio').value = data.worker_profile.bio || '';
                    document.getElementById('profile-latitude').value = data.worker_profile.latitude || '';
                    document.getElementById('profile-longitude').value = data.worker_profile.longitude || '';
                }
            } catch (error) {
                console.error('Failed to load profile:', error);
            }
        }
        
        // Load provider jobs from localStorage
        function loadProviderJobs() {
            // Get all bookings from localStorage
            var allBookings = JSON.parse(localStorage.getItem('bookings') || '[]');
            
            // Get current user info
            var userData = JSON.parse(localStorage.getItem('user') || '{}');
            
            // Filter jobs for this provider (in real app, filter by worker_id)
            // For demo, show all bookings as provider jobs
            var jobsSection = document.getElementById('jobs-section');
            
            if (allBookings.length > 0) {
                var jobsHtml = '<div style="display: grid; gap: 16px;">';
                
                allBookings.forEach(function(job) {
                    var statusClass = job.status === 'pending' ? 'pending' : (job.status === 'accepted' ? 'active' : (job.status === 'rejected' ? 'rejected' : 'completed'));
                    var statusText = job.status === 'pending' ? 'Pending' : (job.status === 'accepted' ? 'Accepted' : (job.status === 'rejected' ? 'Rejected' : 'Completed'));
                    var statusColor = job.status === 'pending' ? '#d97706' : (job.status === 'accepted' ? '#10b981' : (job.status === 'rejected' ? '#ef4444' : '#6b7280'));
                    
                    // Show customer info
                    jobsHtml += '<div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">' +
                        '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">' +
                        '<h4 style="margin: 0; color: #1f2937;">' + (job.title || 'Service Request') + '</h4>' +
                        '<span style="background: ' + statusColor + '; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem;">' + statusText + '</span>' +
                        '</div>' +
                        '<p style="color: #6b7280; margin: 8px 0;"><strong>Customer:</strong> ' + (job.customer_name || 'N/A') + '</p>' +
                        '<p style="color: #6b7280; margin: 8px 0;">' + (job.description || 'No description') + '</p>' +
                        '<div style="display: flex; gap: 20px; font-size: 0.9rem; color: #6b7280;">' +
                        '<span>📍 ' + (job.address || 'N/A') + '</span>' +
                        '<span>📅 ' + (job.scheduled_date || 'Not set') + '</span>' +
                        '</div>';
                    
                    // Show Accept/Reject buttons only for pending jobs
                    if (job.status === 'pending') {
                        jobsHtml += '<div style="margin-top: 12px;">' +
                        '<button onclick="updateJobStatus(' + job.id + ', \'accepted\')" style="background: #10b981; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; margin-right: 8px;">✓ Accept</button>' +
                        '<button onclick="updateJobStatus(' + job.id + ', \'rejected\')" style="background: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer;">✗ Reject</button>' +
                        '</div>';
                    }
                    
                    jobsHtml += '</div>';
                });
                
                jobsHtml += '</div>';
                
                jobsSection.innerHTML = '<div class="jobs-section">' +
                    '<div class="jobs-header">' +
                    '<h3 class="jobs-title">📋 My Jobs (' + allBookings.length + ')</h3>' +
                    '</div>' +
                    jobsHtml +
                    '</div>';
            } else {
                jobsSection.innerHTML = '<div class="jobs-section">' +
                    '<div class="jobs-header">' +
                    '<h3 class="jobs-title">📋 My Jobs</h3>' +
                    '</div>' +
                    '<p style="text-align: center; color: #6b7280; padding: 30px;">' +
                    'No jobs yet. Accept job requests from customers to start working.' +
                    '</p>' +
                    '</div>';
            }
        }
        
        // Update job status (Accept/Reject)
        window.updateJobStatus = function(jobId, newStatus) {
            var bookings = JSON.parse(localStorage.getItem('bookings') || '[]');
            
            // Find and update the job
            for (var i = 0; i < bookings.length; i++) {
                if (bookings[i].id === jobId) {
                    bookings[i].status = newStatus;
                    bookings[i].updated_at = new Date().toISOString();
                    break;
                }
            }
            
            // Save back to localStorage
            localStorage.setItem('bookings', JSON.stringify(bookings));
            
            // Show confirmation
            var message = newStatus === 'accepted' ? 'Job accepted! The customer has been notified.' : 'Job rejected.';
            showAlert(message, newStatus === 'accepted' ? 'success' : 'warning');
            
            // Reload the jobs list
            setTimeout(loadProviderJobs, 1000);
        };
        
        // Handle photo upload
        async function handlePhotoUpload(input) {
            if (!input.files || !input.files[0]) return;
            
            const file = input.files[0];
            
            // Check file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File too large. Maximum size is 2MB.');
                return;
            }
            
            // Check file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.');
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const avatarImg = document.getElementById('profile-avatar-img');
                avatarImg.src = e.target.result;
                avatarImg.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
        
        // Save profile
        async function saveProfile() {
            try {
                const name = document.getElementById('profile-name').value;
                const phone = document.getElementById('profile-phone').value;
                const bio = document.getElementById('profile-bio').value;
                
                const formData = new FormData();
                formData.append('name', name);
                formData.append('phone', phone);
                formData.append('bio', bio);
                
                // Check if there's a new photo
                const photoInput = document.getElementById('profile-photo-input');
                if (photoInput.files && photoInput.files[0]) {
                    formData.append('profile_photo', photoInput.files[0]);
                }
                
                const result = await updateProfile(formData);
                
                if (result && result.user) {
                    // Update local storage
                    const userData = JSON.parse(localStorage.getItem('user'));
                    userData.name = result.user.name;
                    userData.profile_photo = result.profile_photo;
                    localStorage.setItem('user', JSON.stringify(userData));
                    
                    alert('Profile saved successfully!');
                }
            } catch (error) {
                alert('Failed to save profile: ' + error.message);
            }
        }
        
        // Get current location
        function getCurrentLocation() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser');
                return;
            }
            
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('profile-latitude').value = position.coords.latitude.toFixed(6);
                document.getElementById('profile-longitude').value = position.coords.longitude.toFixed(6);
            }, function(error) {
                alert('Failed to get location: ' + error.message);
            });
        }
        
        // Save location
        async function saveLocation() {
            try {
                const latitude = document.getElementById('profile-latitude').value;
                const longitude = document.getElementById('profile-longitude').value;
                
                if (!latitude || !longitude) {
                    alert('Please enter or get your location');
                    return;
                }
                
                const result = await updateProfile({
                    latitude: parseFloat(latitude),
                    longitude: parseFloat(longitude)
                });
                
                if (result) {
                    alert('Location saved successfully!');
                }
            } catch (error) {
                alert('Failed to save location: ' + error.message);
            }
        }
    </script>
</body>
</html>
