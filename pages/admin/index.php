<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HandsOn</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #7c3aed;
            --secondary: #f59e0b;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .admin-container { display: flex; min-height: 100vh; }
        
        .admin-sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            color: white;
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .admin-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .admin-logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #7c3aed 0%, #f59e0b 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .admin-logo h2 { font-size: 1.3rem; }
        
        .admin-menu { list-style: none; padding: 0; }
        
        .admin-menu-item { margin-bottom: 8px; }
        
        .admin-menu-link {
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
        
        .admin-menu-link:hover, .admin-menu-link.active {
            background: linear-gradient(135deg, #7c3aed 0%, #f59e0b 100%);
            color: white;
        }
        
        .admin-main { flex: 1; margin-left: 260px; padding: 30px; }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .admin-title { color: white; font-size: 1.8rem; }
        
        .admin-user {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
        }
        
        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        .admin-user-details {
            display: flex;
            flex-direction: column;
        }
        
        .admin-user-name {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .admin-user-email {
            font-size: 0.75rem;
            opacity: 0.8;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .stat-card-header { display: flex; justify-content: space-between; margin-bottom: 16px; }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .stat-icon.purple { background: #ede9fe; color: #7c3aed; }
        .stat-icon.green { background: #d1fae5; color: #10b981; }
        .stat-icon.yellow { background: #fef3c7; color: #f59e0b; }
        .stat-icon.blue { background: #dbeafe; color: #3b82f6; }
        
        .stat-value { font-size: 2rem; font-weight: 700; color: #1f2937; }
        .stat-label { color: #6b7280; font-size: 0.9rem; }
        .stat-change { font-size: 0.85rem; margin-top: 8px; }
        .stat-change.positive { color: #10b981; }
        
        .data-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .data-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .data-card-title { font-size: 1.2rem; font-weight: 600; }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: white;
        }
        
        .btn-primary:hover { transform: translateY(-2px); }
        
        .data-table { width: 100%; border-collapse: collapse; }
        
        .data-table th {
            text-align: left;
            padding: 12px;
            background: #f9fafb;
            color: #6b7280;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        
        .data-table td { padding: 16px 12px; border-bottom: 1px solid #e5e7eb; }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-badge.active { background: #d1fae5; color: #059669; }
        .status-badge.pending { background: #fef3c7; color: #d97706; }
        .status-badge.completed { background: #dbeafe; color: #2563eb; }
        
        /* Charts */
        .chart-container {
            height: 280px;
            background: linear-gradient(135deg, #f9fafb 0%, #e5e7eb 100%);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .chart-bars {
            display: flex;
            align-items: flex-end;
            gap: 20px;
            height: 150px;
            margin-bottom: 20px;
        }
        
        .chart-bar {
            width: 40px;
            background: linear-gradient(180deg, #7c3aed 0%, #a855f7 100%);
            border-radius: 8px 8px 0 0;
            transition: all 0.3s;
            position: relative;
        }
        
        .chart-bar:hover { transform: scaleY(1.05); }
        
        .chart-bar::after {
            content: attr(data-value);
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-weight: 600;
            font-size: 0.85rem;
            color: #7c3aed;
        }
        
        .chart-labels {
            display: flex;
            gap: 20px;
            font-size: 0.8rem;
            color: #6b7280;
        }
        
        .chart-labels span { width: 40px; text-align: center; }
        
        /* Pie Chart */
        .pie-chart {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: conic-gradient(
                #3b82f6 0deg 120deg,
                #f59e0b 120deg 200deg,
                #10b981 200deg 260deg,
                #ef4444 260deg 360deg
            );
            position: relative;
            margin: 0 auto 20px;
        }
        
        .pie-chart::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
        }
        
        .pie-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        
        .pie-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
        }
        
        .pie-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        
        /* Activity */
        .activity-list { list-style: none; padding: 0; }
        
        .activity-item {
            display: flex;
            gap: 12px;
            padding: 16px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .activity-item:last-child { border-bottom: none; }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .activity-content { flex: 1; }
        .activity-title { font-weight: 500; margin-bottom: 4px; }
        .activity-time { font-size: 0.8rem; color: #6b7280; }
        
        /* Content Sections */
        .content-section { display: none; }
        .content-section.active { display: block; }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <div class="admin-logo-icon">🛠️</div>
                <h2>HandsOn Admin</h2>
            </div>
            
            <ul class="admin-menu">
                <li class="admin-menu-item">
                    <a href="#" class="admin-menu-link active" onclick="showSection('dashboard')">
                        <span>📊</span> Dashboard
                    </a>
                </li>
                <li class="admin-menu-item">
                    <a href="#" class="admin-menu-link" onclick="showSection('users')">
                        <span>👥</span> All Users
                    </a>
                </li>
                <li class="admin-menu-item">
                    <a href="#" class="admin-menu-link" onclick="showSection('providers')">
                        <span>🔧</span> Service Providers
                    </a>
                </li>
                <li class="admin-menu-item">
                    <a href="#" class="admin-menu-link" onclick="showSection('customers')">
                        <span>👤</span> Customers
                    </a>
                </li>
                <li class="admin-menu-item">
                    <a href="#" class="admin-menu-link" onclick="showSection('bookings')">
                        <span>📋</span> Bookings
                    </a>
                </li>
                <li class="admin-menu-item">
                    <a href="#" class="admin-menu-link" onclick="showSection('finances')">
                        <span>💰</span> Finances
                    </a>
                </li>
                <li class="admin-menu-item" style="margin-top: 40px;">
                    <a href="#" class="admin-menu-link" onclick="logout()">
                        <span>🚪</span> Logout
                    </a>
                </li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1 class="admin-title" id="page-title">Dashboard Overview</h1>
                <div class="admin-user" id="admin-user-info">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=7c3aed&color=fff&size=40" alt="Admin" class="admin-avatar" id="admin-avatar">
                    <div class="admin-user-details">
                        <span class="admin-user-name" id="admin-name">Admin</span>
                        <span class="admin-user-email" id="admin-email">omondisylvester999@gmail.com</span>
                    </div>
                </div>
            </div>
            
            <!-- Dashboard Section -->
            <div id="dashboard" class="content-section active">
                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <div class="stat-value">8</div>
                                <div class="stat-label">Total Users</div>
                            </div>
                            <div class="stat-icon purple">👥</div>
                        </div>
                        <div class="stat-change positive">2 service providers per category</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <div class="stat-value">8</div>
                                <div class="stat-label">Service Providers</div>
                            </div>
                            <div class="stat-icon green">🔧</div>
                        </div>
                        <div class="stat-change positive">2 per service type</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <div class="stat-value">0</div>
                                <div class="stat-label">Customers</div>
                            </div>
                            <div class="stat-icon blue">👤</div>
                        </div>
                        <div class="stat-change positive">Registered customers</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <div class="stat-value">KSh 0</div>
                                <div class="stat-label">Total Revenue</div>
                            </div>
                            <div class="stat-icon yellow">💰</div>
                        </div>
                        <div class="stat-change positive">No transactions yet</div>
                    </div>
                </div>
                
                <!-- Charts -->
                <div class="stats-grid">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h3 class="data-card-title">Bookings by Category</h3>
                        </div>
                        <div class="chart-container">
                            <div class="chart-bars">
                                <div class="chart-bar" style="height: 60px;" data-value="2"></div>
                                <div class="chart-bar" style="height: 60px;" data-value="2"></div>
                                <div class="chart-bar" style="height: 60px;" data-value="2"></div>
                                <div class="chart-bar" style="height: 60px;" data-value="2"></div>
                            </div>
                            <div class="chart-labels">
                                <span>Plumber</span>
                                <span>Electric</span>
                                <span>Cleaner</span>
                                <span>Mechanic</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="data-card">
                        <div class="data-card-header">
                            <h3 class="data-card-title">Service Distribution</h3>
                        </div>
                        <div class="chart-container">
                            <div class="pie-chart"></div>
                            <div class="pie-legend">
                                <div class="pie-legend-item">
                                    <div class="pie-dot" style="background: #3b82f6;"></div>
                                    <span>Plumber (33%)</span>
                                </div>
                                <div class="pie-legend-item">
                                    <div class="pie-dot" style="background: #f59e0b;"></div>
                                    <span>Electrician (22%)</span>
                                </div>
                                <div class="pie-legend-item">
                                    <div class="pie-dot" style="background: #10b981;"></div>
                                    <span>Cleaner (17%)</span>
                                </div>
                                <div class="pie-legend-item">
                                    <div class="pie-dot" style="background: #ef4444;"></div>
                                    <span>Mechanic (28%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="data-card">
                    <div class="data-card-header">
                        <h3 class="data-card-title">Recent Activity</h3>
                    </div>
                    <ul class="activity-list">
                        <li class="activity-item">
                            <div class="activity-icon" style="background: #d1fae5; color: #059669;">✓</div>
                            <div class="activity-content">
                                <div class="activity-title">Admin login successful</div>
                                <div class="activity-time">Just now</div>
                            </div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-icon" style="background: #ede9fe; color: #7c3aed;">🔧</div>
                            <div class="activity-content">
                                <div class="activity-title">8 Service Providers registered</div>
                                <div class="activity-time">2 per service category</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Service Providers Section -->
            <div id="providers" class="content-section">
                <div class="data-card">
                    <div class="data-card-header">
                        <h3 class="data-card-title">🔧 Service Providers (2 per service)</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Service</th>
                                <th>Phone</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>James Ochieng</td>
                                <td>🔧 Plumber</td>
                                <td>+254 712 345 678</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Peter Mwangi</td>
                                <td>🔧 Plumber</td>
                                <td>+254 723 456 789</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Francis Otieno</td>
                                <td>⚡ Electrician</td>
                                <td>+254 734 567 890</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Vincent Kimani</td>
                                <td>⚡ Electrician</td>
                                <td>+254 745 678 901</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>Grace Wanjiku</td>
                                <td>🧹 Cleaner</td>
                                <td>+254 756 789 012</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>Faith Atieno</td>
                                <td>🧹 Cleaner</td>
                                <td>+254 767 890 123</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>Simon Omondi</td>
                                <td>🚗 Mechanic</td>
                                <td>+254 778 901 234</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td>Dennis Ochieng</td>
                                <td>🚗 Mechanic</td>
                                <td>+254 789 012 345</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Users Section -->
            <div id="users" class="content-section">
                <div class="data-card">
                    <div class="data-card-header">
                        <h3 class="data-card-title">👥 All Users</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Sylvester Omondi</td>
                                <td>omondisylvester999@gmail.com</td>
                                <td>👑 Admin</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>James Ochieng</td>
                                <td>james@email.com</td>
                                <td>🔧 Provider</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Peter Mwangi</td>
                                <td>peter@email.com</td>
                                <td>🔧 Provider</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Francis Otieno</td>
                                <td>francis@email.com</td>
                                <td>⚡ Provider</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>Vincent Kimani</td>
                                <td>vincent@email.com</td>
                                <td>⚡ Provider</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>Grace Wanjiku</td>
                                <td>grace@email.com</td>
                                <td>🧹 Provider</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>Faith Atieno</td>
                                <td>faith@email.com</td>
                                <td>🧹 Provider</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td>Simon Omondi</td>
                                <td>simon@email.com</td>
                                <td>🚗 Provider</td>
                                <td><span class="status-badge active">Active</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Customers Section -->
            <div id="customers" class="content-section">
                <div class="data-card">
                    <div class="data-card-header">
                        <h3 class="data-card-title">👤 Customers</h3>
                    </div>
                    <p style="text-align: center; color: #6b7280; padding: 40px;">
                        No customers registered yet. Customers will appear here after they sign up.
                    </p>
                </div>
            </div>
            
            <!-- Bookings Section -->
            <div id="bookings" class="content-section">
                <div class="data-card">
                    <div class="data-card-header">
                        <h3 class="data-card-title">📋 Bookings</h3>
                    </div>
                    <p style="text-align: center; color: #6b7280; padding: 40px;">
                        No bookings yet. Bookings will appear here when customers request services.
                    </p>
                </div>
            </div>
            
            <!-- Finances Section -->
            <div id="finances" class="content-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <div class="stat-value">KSh 0</div>
                                <div class="stat-label">Total Revenue</div>
                            </div>
                            <div class="stat-icon yellow">💰</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <div class="stat-value">KSh 0</div>
                                <div class="stat-label">This Month</div>
                            </div>
                            <div class="stat-icon green">📈</div>
                        </div>
                    </div>
                </div>
                
                <div class="data-card">
                    <div class="data-card-header">
                        <h3 class="data-card-title">💰 Transaction History</h3>
                    </div>
                    <p style="text-align: center; color: #6b7280; padding: 40px;">
                        No transactions yet. Revenue will appear here after first bookings.
                    </p>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Logout function
        function logout() {
            localStorage.removeItem('user');
            localStorage.removeItem('token');
            localStorage.removeItem('selectedService');
            // Use absolute path from web root
            window.location.href = '/handsonapplication/landing.php';
        }
        
        // Admin profile data
        const ADMIN_PROFILE = {
            name: 'Sylvester Omondi',
            email: 'omondisylvester999@gmail.com',
            avatar: ''
        };
        
        // Generate avatar URL
        function getAvatarUrl(name, bgColor) {
            return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=' + (bgColor || '7c3aed') + '&color=fff&size=40';
        }
        
        // Load admin profile
        document.addEventListener('DOMContentLoaded', function() {
            const userData = JSON.parse(localStorage.getItem('user'));
            if (userData) {
                const name = userData.name || ADMIN_PROFILE.name;
                const email = userData.email || ADMIN_PROFILE.email;
                document.getElementById('admin-name').textContent = name;
                document.getElementById('admin-email').textContent = email;
                
                // Set avatar
                if (userData.avatar) {
                    document.getElementById('admin-avatar').src = userData.avatar;
                } else {
                    document.getElementById('admin-avatar').src = getAvatarUrl(name, '7c3aed');
                }
            }
        });
        
        // Make admin user info clickable for profile settings
        document.getElementById('admin-user-info').style.cursor = 'pointer';
        document.getElementById('admin-user-info').onclick = function() {
            alert('Profile settings coming soon! You can update your name and profile picture here.');
        };
        
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(function(sec) {
                sec.classList.remove('active');
            });
            
            // Remove active class from menu
            document.querySelectorAll('.admin-menu-link').forEach(function(link) {
                link.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(sectionId).classList.add('active');
            
            // Add active to clicked menu
            event.target.closest('.admin-menu-link').classList.add('active');
            
            // Update title
            var titles = {
                'dashboard': 'Dashboard Overview',
                'users': 'All Users',
                'providers': 'Service Providers',
                'customers': 'Customers',
                'bookings': 'Bookings',
                'finances': 'Finances'
            };
            document.getElementById('page-title').textContent = titles[sectionId];
        }
    </script>
</body>
</html>
