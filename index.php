<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HandsOn - Find Skilled Workers Near You</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        :root {
            --primary: #7c3aed;
            --secondary: #f59e0b;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { font-family: 'Poppins', sans-serif; overflow: hidden; }
        
        /* Header */
        .app-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(135deg, #7c3aed 0%, #f59e0b 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1003;
            box-shadow: 0 2px 15px rgba(0,0,0,0.2);
        }
        
        .app-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
        }
        
        .app-logo-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        
        .app-logo-text { font-size: 1.3rem; font-weight: 700; }
        
        .header-actions { display: flex; align-items: center; gap: 10px; }
        
        .logout-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
        }
        
        .logout-btn:hover { background: rgba(255,255,255,0.4); }
        
        .header-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
        }
        
        .header-btn:hover { background: rgba(255,255,255,0.3); }
        
        .user-avatar-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            border: 2px solid white;
            cursor: pointer;
            font-size: 1.2rem;
            overflow: hidden;
        }
        
        .user-avatar-btn img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Map */
        #map-container {
            position: fixed;
            top: 60px;
            left: 0;
            right: 0;
            bottom: 0;
        }
        
        #map { width: 100%; height: 100%; }
        
        /* Search */
        .search-panel {
            position: absolute;
            top: 80px;
            left: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 500px;
        }
        
        .search-box-new {
            background: white;
            border-radius: 16px;
            padding: 14px 18px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.15);
            display: flex;
            gap: 10px;
        }
        
        .search-box-new input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 1rem;
        }
        
        .search-btn {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
        }
        
        /* Service Pills */
        .service-pills {
            position: absolute;
            top: 150px;
            left: 0;
            right: 0;
            z-index: 999;
            display: flex;
            gap: 12px;
            padding: 0 20px;
            justify-content: center;
        }
        
        .service-pill {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: white;
            border: none;
            border-radius: 16px;
            padding: 12px 20px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            min-width: 90px;
            animation: pulse 3s infinite;
        }
        
        .service-pill:nth-child(2) { animation-delay: 0.2s; }
        .service-pill:nth-child(3) { animation-delay: 0.4s; }
        .service-pill:nth-child(4) { animation-delay: 0.6s; }
        .service-pill:nth-child(5) { animation-delay: 0.8s; }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .service-pill:hover, .service-pill.active {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: white;
            transform: scale(1.1);
        }
        
        .service-pill .icon { font-size: 1.8rem; margin-bottom: 6px; }
        .service-pill .label { font-size: 0.8rem; font-weight: 600; }
        
        /* Controls */
        .map-controls {
            position: absolute;
            right: 20px;
            bottom: 120px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .map-control-btn {
            width: 50px;
            height: 50px;
            background: white;
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            cursor: pointer;
            font-size: 1.3rem;
        }
        
        /* Bottom Sheet */
        .bottom-sheet {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-radius: 30px 30px 0 0;
            padding: 20px;
            z-index: 1001;
            transform: translateY(calc(100% - 100px));
            transition: transform 0.4s;
            max-height: 65vh;
            overflow-y: auto;
        }
        
        .bottom-sheet.expanded { transform: translateY(0); }
        
        .bottom-sheet-handle {
            width: 50px;
            height: 5px;
            background: #d1d5db;
            border-radius: 3px;
            margin: 0 auto 20px;
        }
        
        /* Provider Cards */
        .provider-card {
            display: flex;
            gap: 14px;
            padding: 14px;
            background: #f9fafb;
            border-radius: 14px;
            margin-bottom: 12px;
            cursor: pointer;
        }
        
        .provider-card:hover { background: #f3f4f6; }
        
        .provider-card.selected {
            border: 2px solid #7c3aed;
            background: #ede9fe;
        }
        
        .provider-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .provider-info { flex: 1; }
        .provider-name { font-weight: 700; margin-bottom: 4px; }
        .provider-category { color: #7c3aed; font-weight: 600; font-size: 0.9rem; margin-bottom: 4px; }
        .provider-details { display: flex; gap: 12px; color: #6b7280; font-size: 0.8rem; }
        
        .provider-price {
            font-weight: 800;
            font-size: 1.2rem;
            color: #7c3aed;
        }
        
        .view-profile-btn {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            white-space: nowrap;
            transition: all 0.3s;
        }
        
        .view-profile-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3);
        }
        
        .book-now-btn {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            white-space: nowrap;
            transition: all 0.3s;
        }
        
        .book-now-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
        }
        
        .provider-card-buttons {
            display: flex;
            gap: 8px;
            margin-top: 8px;
            position: relative;
            z-index: 10;
        }
        
        .provider-card-buttons button {
            position: relative;
            z-index: 100;
            cursor: pointer;
            pointer-events: auto;
        }
        
        .marker-pin {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="app-header">
        <a href="landing.php" class="app-logo">
            <div class="app-logo-icon">🛠️</div>
            <span class="app-logo-text">HandsOn</span>
        </a>
        
        <div class="header-actions" id="auth-buttons">
            <button class="header-btn" onclick="showAbout()">About</button>
            <button class="header-btn" onclick="showFeatures()">Features</button>
            <button class="header-btn" onclick="window.location.href='landing.php'">Login</button>
        </div>
        
        <div class="header-actions" id="user-menu" style="display: none;">
            <button class="header-btn" onclick="showAbout()">About</button>
            <button class="header-btn" onclick="showFeatures()">Features</button>
            <button class="user-avatar-btn" onclick="showProfile()">
                <img id="header-user-avatar" src="" alt="User" style="display:none;">
            </button>
            <button class="logout-btn" onclick="logout()">Logout</button>
        </div>
    </header>
    
    <!-- Map -->
    <div id="map-container">
        <div id="map"></div>
        
        <!-- Search -->
        <div class="search-panel">
            <div class="search-box-new">
                <span style="font-size: 1.2rem;">🔍</span>
                <input type="text" id="search-destination" placeholder="What service do you need?" list="service-suggestions">
                <datalist id="service-suggestions">
                    <option value="Plumber - Fix leaking pipe">
                    <option value="Plumber - Install water heater">
                    <option value="Plumber - Unblock drain">
                    <option value="Electrician - Fix wiring">
                    <option value="Electrician - Install lights">
                    <option value="Electrician - Repair socket">
                    <option value="Cleaner - House cleaning">
                    <option value="Cleaner - Office cleaning">
                    <option value="Cleaner - Deep cleaning">
                    <option value="Mechanic - Car repair">
                    <option value="Mechanic - Oil change">
                    <option value="Mechanic - Tire replacement">
                </datalist>
                <button class="search-btn" onclick="searchServices()">Find</button>
            </div>
        </div>
        
        <!-- Service Pills -->
        <div class="service-pills">
            <button class="service-pill active" data-category="all" onclick="filterByCategory('all')">
                <span class="icon">👀</span>
                <span class="label">All</span>
            </button>
            <button class="service-pill" data-category="plumber" onclick="filterByCategory('plumber')">
                <span class="icon">🔧</span>
                <span class="label">Plumber</span>
            </button>
            <button class="service-pill" data-category="electrician" onclick="filterByCategory('electrician')">
                <span class="icon">⚡</span>
                <span class="label">Electrician</span>
            </button>
            <button class="service-pill" data-category="cleaner" onclick="filterByCategory('cleaner')">
                <span class="icon">🧹</span>
                <span class="label">Cleaner</span>
            </button>
            <button class="service-pill" data-category="mechanic" onclick="filterByCategory('mechanic')">
                <span class="icon">🚗</span>
                <span class="label">Mechanic</span>
            </button>
        </div>
        
        <!-- Controls -->
        <div class="map-controls">
            <button class="map-control-btn" onclick="map.zoomIn()">➕</button>
            <button class="map-control-btn" onclick="map.zoomOut()">➖</button>
            <button class="map-control-btn" onclick="recenterMap()">📍</button>
        </div>
        
        <!-- Bottom Sheet - Hidden by default, only shows when dragged up -->
        <div class="bottom-sheet" id="bottom-sheet" style="display: none;">
            <div class="bottom-sheet-handle" onclick="toggleSheet()"></div>
            
            <div id="sheet-content">
                <h3 style="margin-bottom: 16px;">📍 Nearby Service Providers</h3>
                <p id="provider-count" style="color: #6b7280; margin-bottom: 16px;">Loading...</p>
                <div id="providers-list"></div>
            </div>
        </div>
        
        <!-- About Modal -->
        <div id="about-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 2000; overflow-y: auto;">
            <div style="background: white; max-width: 700px; margin: 40px auto; border-radius: 20px; padding: 30px; max-height: 90vh; overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="color: #7c3aed;">About HandsOn</h2>
                    <button onclick="closeAbout()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">✕</button>
                </div>
                
                <div style="color: #374151;">
                    <h3 style="color: #7c3aed; margin-bottom: 10px;">HandsOn: A Digital Marketplace for On-Demand Plumbing and Skilled Trade Services in Africa</h3>
                    
                    <h4 style="margin-top: 20px; margin-bottom: 8px;">Background</h4>
                    <p style="margin-bottom: 12px; line-height: 1.6;">Kenya's economic transformation is increasingly driven by digital innovation, youth empowerment, and inclusive growth. Despite progress in sectors such as finance, transport, and communication, most of Africa's workforce remains in the informal sector. Approximately 85 percent of employment in Africa is informal, with skilled trades such as plumbing, carpentry, and electrical work dominated by informal workers who lack access to structured digital platforms.</p>
                    
                    <h4 style="margin-top: 20px; margin-bottom: 8px;">The Problem</h4>
                    <p style="margin-bottom: 8px; line-height: 1.6;">Finding skilled labor such as plumbers or electricians is a manual process. Customers rely on personal referrals, local advertisements, or social media posts. Weaknesses include:</p>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li>Unreliable referrals based on personal experience</li>
                        <li>Lack of verification of worker identity or competence</li>
                        <li>Inconsistent pricing with no standardized rates</li>
                        <li>Limited visibility for skilled workers</li>
                        <li>Lack of digital records for reputation building</li>
                    </ul>
                    
                    <h4 style="margin-top: 20px; margin-bottom: 8px;">Our Solution</h4>
                    <p style="margin-bottom: 12px; line-height: 1.6;">HandsOn is a location-based digital platform that connects customers with nearby verified skilled workers through real-time location tracking. Users can:</p>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li>View available skilled workers on an interactive map</li>
                        <li>Access verified profiles and reviews</li>
                        <li>Upload problem images for better diagnosis</li>
                        <li>Make secure digital payments (via M-Pesa)</li>
                        <li>Rate and review services for transparency</li>
                    </ul>
                    
                    <h4 style="margin-top: 20px; margin-bottom: 8px;">Objectives</h4>
                    <p style="margin-bottom: 8px; line-height: 1.6;">To design and implement a location-based digital marketplace that connects customers with verified skilled workers (beginning with plumbers) in real time, promoting professionalism, employment, and inclusive economic growth.</p>
                    
                    <h4 style="margin-top: 20px; margin-bottom: 8px;">Alignment with Development Goals</h4>
                    <p style="line-height: 1.6;">This project aligns with the African Union's Agenda 2063 and the United Nations Sustainable Development Goals (SDGs) — particularly Goal 8 (Decent Work and Economic Growth) and Goal 9 (Industry, Innovation, and Infrastructure).</p>
                    
                    <p style="margin-top: 20px; padding: 15px; background: linear-gradient(135deg, #7c3aed 0%, #f59e0b 100%); color: white; border-radius: 10px; text-align: center;">
                        Pilot Area: Roysambu, Nairobi
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Features Modal -->
        <div id="features-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 2000; overflow-y: auto;">
            <div style="background: white; max-width: 700px; margin: 40px auto; border-radius: 20px; padding: 30px; max-height: 90vh; overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="color: #7c3aed;">System Features</h2>
                    <button onclick="closeFeatures()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">✕</button>
                </div>
                
                <div style="color: #374151;">
                    <h3 style="color: #7c3aed; margin-bottom: 15px;">Functional Requirements</h3>
                    
                    <h4 style="margin-top: 15px; margin-bottom: 8px;">For Customers</h4>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li>Register and login to the platform</li>
                        <li>Request services from skilled workers</li>
                        <li>View worker profiles, ratings, and reviews</li>
                        <li>Book services and schedule appointments</li>
                        <li>Make secure digital payments (M-Pesa)</li>
                        <li>Track job status in real-time</li>
                        <li>Rate and review services</li>
                    </ul>
                    
                    <h4 style="margin-top: 15px; margin-bottom: 8px;">For Service Providers</h4>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li>Create and manage service provider profile</li>
                        <li>Set service categories and hourly rates</li>
                        <li>Receive service requests from customers</li>
                        <li>Accept or reject job requests</li>
                        <li>Manage subscription plans</li>
                        <li>Receive payments digitally</li>
                        <li>Build reputation through reviews</li>
                    </ul>
                    
                    <h4 style="margin-top: 15px; margin-bottom: 8px;">For Administrators</h4>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li>Manage user accounts</li>
                        <li>Manage service categories</li>
                        <li>View and manage bookings</li>
                        <li>Process payments and subscriptions</li>
                        <li>Generate reports and analytics</li>
                    </ul>
                    
                    <h3 style="color: #7c3aed; margin-top: 25px; margin-bottom: 15px;">Non-Functional Requirements</h3>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li>Secure authentication and data protection</li>
                        <li>User-friendly and responsive interface</li>
                        <li>Scalable to handle many users</li>
                        <li>High system availability and reliability</li>
                        <li>Fast response time (under 3 seconds)</li>
                        <li>Support for mobile and desktop browsers</li>
                    </ul>
                    
                    <h3 style="color: #7c3aed; margin-top: 25px; margin-bottom: 15px;">System Architecture</h3>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li><strong>User Interface:</strong> Web-based responsive interface</li>
                        <li><strong>Application Server:</strong> PHP backend with REST API</li>
                        <li><strong>Service Matching:</strong> Location-based worker matching engine</li>
                        <li><strong>Payment Module:</strong> M-Pesa integration</li>
                        <li><strong>Database:</strong> MySQL for persistent storage</li>
                        <li><strong>Notifications:</strong> SMS/Email notifications</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global logout function
        window.logout = function() {
            localStorage.removeItem('user');
            localStorage.removeItem('token');
            localStorage.removeItem('selectedService');
            window.location.href = '/handsonapplication/landing.php';
        }
        
        // About modal functions
        window.showAbout = function() {
            document.getElementById('about-modal').style.display = 'block';
        }
        
        window.closeAbout = function() {
            document.getElementById('about-modal').style.display = 'none';
        }
        
        // Features modal functions
        window.showFeatures = function() {
            document.getElementById('features-modal').style.display = 'block';
        }
        
        window.closeFeatures = function() {
            document.getElementById('features-modal').style.display = 'none';
        }
        
        // Generate avatar URL
        function getUserAvatarUrl(name, color) {
            return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=' + (color || '7c3aed') + '&color=fff&size=40';
        }
        
        // Load user avatar in header
        function loadUserAvatar() {
            const userData = JSON.parse(localStorage.getItem('user'));
            if (userData) {
                const avatarImg = document.getElementById('header-user-avatar');
                if (userData.avatar) {
                    avatarImg.src = userData.avatar;
                    avatarImg.style.display = 'block';
                } else {
                    avatarImg.src = getUserAvatarUrl(userData.name || 'User', '7c3aed');
                    avatarImg.style.display = 'block';
                }
            }
        };
    </script>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        let map, markers = [], providers = [], currentCategory = 'all', selectedProvider = null, userLocation = null, userMarker = null;
        
        const kenyanNames = {
            plumber: ['James Ochieng', 'Peter Mwangi', 'John Kariuki', 'David Otieno', 'Michael Njoroge'],
            electrician: ['Francis Otieno', 'Vincent Kimani', 'Emmanuel Kariuki', 'Samuel Mwangi', 'George Otieno'],
            cleaner: ['Grace Wanjiku', 'Faith Atieno', 'Mary Kemunto', 'Sarah Akinyi', 'Esther Wambui'],
            mechanic: ['Simon Omondi', 'Dennis Ochieng', 'Benson Otieno', 'Felix Kariuki', 'Victor Mwangi']
        };
        
        const CATEGORIES = {
            plumber: { icon: '🔧', name: 'Plumber', color: '#3b82f6' },
            electrician: { icon: '⚡', name: 'Electrician', color: '#f59e0b' },
            cleaner: { icon: '🧹', name: 'Cleaner', color: '#10b981' },
            mechanic: { icon: '🚗', name: 'Mechanic', color: '#ef4444' }
        };
        
        // Init
        document.addEventListener('DOMContentLoaded', function() {
            // Check auth
            const user = localStorage.getItem('user');
            if (user) {
                const userData = JSON.parse(user);
                document.getElementById('auth-buttons').style.display = 'none';
                document.getElementById('user-menu').style.display = 'flex';
            }
            
            // Check for service category from URL
            const urlParams = new URLSearchParams(window.location.search);
            const serviceParam = urlParams.get('service');
            if (serviceParam && ['plumber', 'electrician', 'cleaner', 'mechanic'].includes(serviceParam)) {
                currentCategory = serviceParam;
                // Update UI to show selected category
                document.querySelectorAll('.service-pill').forEach(function(p) {
                    p.classList.remove('active');
                    if (p.dataset.category === serviceParam) p.classList.add('active');
                });
            }
            
            // Load user avatar
            loadUserAvatar();
            
            // Init map - Center on Roysambu, Nairobi
            map = L.map('map').setView([-1.2225, 36.8947], 14);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);
            
            // Get location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos) {
                    userLocation = [pos.coords.latitude, pos.coords.longitude];
                    map.setView(userLocation, 14);
                    addUserMarker();
                });
            }
            
            loadProviders();
        });
        
        function addUserMarker() {
            if (!userLocation) return;
            var userIcon = L.divIcon({
                html: '<div style="width:28px;height:28px;background:linear-gradient(135deg,#7c3aed,#f59e0b);border:4px solid white;border-radius:50%;box-shadow:0 3px 15px rgba(0,0,0,0.3);"></div>',
                iconSize: [28, 28], iconAnchor: [14, 14]
            });
            userMarker = L.marker(userLocation, {icon: userIcon}).addTo(map).bindPopup('Your Location');
        }
        
        function recenterMap() {
            if (userLocation) map.setView(userLocation, 14);
        }
        
        function loadProviders() {
            // Try to load from API first, but fallback to mock data for demo
            console.log('Loading providers...');
            
            // For demo purposes, directly load mock data to ensure providers show up
            // In production, this would call: getWorkers({}).then(...)
            loadMockProviders();
            
            /* Original API call - uncomment for production:
            getWorkers({}).then(function(workers) {
                console.log('API returned workers:', workers);
                if (workers && workers.length > 0) {
                    // Check if workers have valid location data
                    var validWorkers = workers.filter(function(w) { return w.latitude && w.longitude; });
                    if (validWorkers.length > 0) {
                        providers = validWorkers.map(function(w) {
                            return {
                                id: w.user_id,
                                name: w.name,
                                category: w.category,
                                rating: w.rating_avg || '0.0',
                                reviews: w.review_count || 0,
                                hourlyRate: w.hourly_rate || 0,
                                distance: w.distance || 0,
                                location: [parseFloat(w.latitude), parseFloat(w.longitude)],
                                phone: w.phone,
                                bio: w.bio,
                                photo: w.photo || w.profile_photo,
                                is_verified: w.is_verified
                            };
                        });
                        displayProviders(providers);
                        addMarkers(providers);
                    } else {
                        loadMockProviders();
                    }
                } else {
                    loadMockProviders();
                }
            }).catch(function(error) {
                console.error('Failed to load workers:', error);
                loadMockProviders();
            });
            */
        }
        
        function loadMockProviders() {
            // Roysambu, Nairobi coordinates
            var base = userLocation || [-1.2225, 36.8947];
            providers = [];
            // Focus on plumbing services for Roysambu testing
            // Use all 8 provider accounts from landing.php
            var allProviders = [
                { id: 201, name: 'James Ochieng', category: 'plumber', rating: 4.8, reviews: 45, hourlyRate: 500 },
                { id: 202, name: 'Francis Otieno', category: 'electrician', rating: 4.6, reviews: 38, hourlyRate: 600 },
                { id: 203, name: 'Grace Wanjiku', category: 'cleaner', rating: 4.9, reviews: 62, hourlyRate: 300 },
                { id: 204, name: 'Simon Omondi', category: 'mechanic', rating: 4.7, reviews: 51, hourlyRate: 700 },
                { id: 205, name: 'Peter Mwangi', category: 'plumber', rating: 4.5, reviews: 32, hourlyRate: 450 },
                { id: 206, name: 'Vincent Kimani', category: 'electrician', rating: 4.4, reviews: 28, hourlyRate: 550 },
                { id: 207, name: 'Mary Kemunto', category: 'cleaner', rating: 4.8, reviews: 55, hourlyRate: 350 },
                { id: 208, name: 'Dennis Ochieng', category: 'mechanic', rating: 4.6, reviews: 42, hourlyRate: 650 }
            ];
            
            // Create providers with locations around Roysambu
            for (var i = 0; i < allProviders.length; i++) {
                var p = allProviders[i];
                providers.push({
                    id: p.id,
                    name: p.name,
                    category: p.category,
                    rating: p.rating,
                    reviews: p.reviews,
                    hourlyRate: p.hourlyRate,
                    distance: (Math.random() * 4 + 0.5).toFixed(1),
                    location: [base[0] + (Math.random() - 0.5) * 0.04, base[1] + (Math.random() - 0.5) * 0.04]
                });
            }
            
            console.log('Loaded mock providers:', providers.length);
            console.log('Displaying providers...');
            displayProviders(providers);
            addMarkers(providers);
            // Don't auto-expand bottom sheet on load
            // document.getElementById('bottom-sheet').classList.add('expanded');
        }
        
        function displayProviders(list) {
            var filtered = currentCategory === 'all' ? list : list.filter(p => p.category === currentCategory);
            document.getElementById('provider-count').textContent = filtered.length + ' providers available';
            
            if (filtered.length === 0) {
                document.getElementById('providers-list').innerHTML = '<p style="text-align:center;color:#6b7280;padding:30px;">No providers found</p>';
                return;
            }
            
            document.getElementById('providers-list').innerHTML = filtered.map(function(p) {
                var cardHtml = '<div class="provider-card ' + (selectedProvider && selectedProvider.id === p.id ? 'selected' : '') + '" onclick="selectProvider(' + p.id + ')">' +
                    '<div class="provider-avatar">' + (CATEGORIES[p.category] ? CATEGORIES[p.category].icon : '👤') + '</div>' +
                    '<div class="provider-info"><div class="provider-name">' + p.name + '</div>' +
                    '<div class="provider-category">' + (CATEGORIES[p.category] ? CATEGORIES[p.category].name : p.category) + '</div>' +
                    '<div class="provider-details"><span>⭐ ' + p.rating + '</span><span>📍 ' + p.distance + 'km</span></div></div>' +
                    '<div class="provider-price">KSh ' + p.hourlyRate + '/hr</div></div>' +
                    '</div>' +
                    '<div class="provider-card-buttons">' +
                    '<button type="button" class="book-now-btn" onclick="bookNow(' + p.id + ')">📅 Book Now</button>' +
                    '<button type="button" class="view-profile-btn" onclick="goToProfile(' + p.id + ')">View Profile</button>' +
                    '</div>';
                return cardHtml;
            }).join('');
            
            // Also update the nearby section
            displayNearbyProviders(filtered);
        }
        
        function displayNearbyProviders(list) {
            // Nearby section removed - providers shown via map markers only
            // This function kept for backward compatibility but does nothing
        }
        
        function selectProviderAndScroll(id) {
            // Don't expand bottom sheet when selecting provider
            // selectProvider(id);
            // document.getElementById('bottom-sheet').classList.add('expanded');
        }
        
        function addMarkers(list) {
            markers.forEach(function(m) { map.removeLayer(m); });
            markers = [];
            
            var filtered = currentCategory === 'all' ? list : list.filter(p => p.category === currentCategory);
            var color, icon;
            
            filtered.forEach(function(p) {
                color = CATEGORIES[p.category] ? CATEGORIES[p.category].color : '#7c3aed';
                icon = CATEGORIES[p.category] ? CATEGORIES[p.category].icon : '👤';
                
                var mIcon = L.divIcon({
                    html: '<div class="marker-pin" style="background:' + color + ';color:white;">' + icon + '</div>',
                    iconSize: [45, 45], iconAnchor: [22, 45]
                });
                
                var marker = L.marker(p.location, {icon: mIcon}).addTo(map).bindPopup(
                    '<div style="text-align:center;"><strong>' + p.name + '</strong><br>' +
                    '<span style="color:' + color + '">' + (CATEGORIES[p.category] ? CATEGORIES[p.category].name : p.category) + '</span><br>' +
                    '⭐ ' + p.rating + ' • KSh ' + p.hourlyRate + '/hr<br>' +
                    '<button onclick="bookNow(' + p.id + ')" style="margin-top:8px;padding:6px 16px;background:#f59e0b;color:white;border:none;border-radius:6px;cursor:pointer;margin-right:5px;font-weight:600;">📅 Book Now</button>' +
                    '<button onclick="window.location.href=\'pages/view-profile.php?id=' + p.id + '&name=' + encodeURIComponent(p.name) + '&category=' + p.category + '&rating=' + p.rating + '&hourlyRate=' + p.hourlyRate + '\'" style="margin-top:8px;padding:6px 16px;background:#10b981;color:white;border:none;border-radius:6px;cursor:pointer;">View Profile</button></div>'
                );
                markers.push(marker);
            });
        }
        
        function filterByCategory(cat) {
            currentCategory = cat;
            document.querySelectorAll('.service-pill').forEach(function(p) {
                p.classList.remove('active');
                if (p.dataset.category === cat) p.classList.add('active');
            });
            displayProviders(providers);
            addMarkers(providers);
        }
        
        function selectProvider(id) {
            var provider = providers.find(function(p) { return p.id === id; });
            if (!provider) return;
            selectedProvider = provider;
            displayProviders(providers);
            map.setView(provider.location, 15);
            // Don't expand bottom sheet when selecting provider
            // document.getElementById('bottom-sheet').classList.add('expanded');
        }
        
        function goToProfile(id) {
            var provider = providers.find(function(p) { return p.id === id; });
            if (!provider) return;
            
            // Go to worker profile page
            window.location.href = 'pages/view-profile.php?id=' + id + '&name=' + encodeURIComponent(provider.name) + '&category=' + provider.category + '&rating=' + provider.rating + '&hourlyRate=' + provider.hourlyRate + '&phone=' + (provider.phone || '') + '&bio=' + (provider.bio || '');
        }
        
        function bookNow(id) {
            var provider = providers.find(function(p) { return p.id === id; });
            if (!provider) return;
            
            // Go to booking page directly
            window.location.href = 'pages/create-job.php?worker=' + id + '&name=' + encodeURIComponent(provider.name) + '&category=' + provider.category + '&rating=' + provider.rating + '&hourlyRate=' + provider.hourlyRate;
        }
        
        function searchServices() {
            var dest = document.getElementById('search-destination').value;
            if (!dest) { alert('Please enter a service'); return; }
            
            // Map search terms to categories
            var searchTerm = dest.toLowerCase();
            var categoryMap = {
                'plumb': 'plumber', 'pipe': 'plumber', 'leak': 'plumber', 'water': 'plumber', 'heater': 'plumber', 'drain': 'plumber', 'unblock': 'plumber',
                'electric': 'electrician', 'wire': 'electrician', 'power': 'electrician', 'light': 'electrician', 'socket': 'electrician', 'wiring': 'electrician',
                'clean': 'cleaner', 'house': 'cleaner', 'office': 'cleaner', 'deep': 'cleaner',
                'car': 'mechanic', 'vehicle': 'mechanic', 'repair': 'mechanic', 'engine': 'mechanic', 'oil': 'mechanic', 'tire': 'mechanic'
            };
            
            var matchedCategory = 'all';
            
            // Check for exact category match first (e.g., "plumber" or "Plumber - Fix leaking pipe")
            if (searchTerm.indexOf('plumber') !== -1) {
                matchedCategory = 'plumber';
            } else if (searchTerm.indexOf('electrician') !== -1) {
                matchedCategory = 'electrician';
            } else if (searchTerm.indexOf('cleaner') !== -1) {
                matchedCategory = 'cleaner';
            } else if (searchTerm.indexOf('mechanic') !== -1) {
                matchedCategory = 'mechanic';
            } else {
                // Fall back to keyword matching
                for (var key in categoryMap) {
                    if (searchTerm.indexOf(key) !== -1) {
                        matchedCategory = categoryMap[key];
                        break;
                    }
                }
            }
            
            // Update the category filter
            filterByCategory(matchedCategory);
            
            // Don't auto-expand bottom sheet on search
            // document.getElementById('bottom-sheet').classList.add('expanded');
            
            // Save the search term for reference
            localStorage.setItem('selectedService', dest);
        }
        
        function resetBooking() {
            selectedProvider = null;
            document.getElementById('bottom-sheet').classList.remove('expanded');
            displayProviders(providers);
        }
        
        function toggleSheet() {
            document.getElementById('bottom-sheet').classList.toggle('expanded');
        }
        
        function showProfile() {
            var user = JSON.parse(localStorage.getItem('user') || '{}');
            if (user.role === 'admin') window.location.href = 'pages/admin/index.php';
            else if (user.role === 'worker') window.location.href = 'pages/provider-dashboard.php';
            else alert('Profile: ' + user.name);
        }
        
        function logout() {
            localStorage.clear();
            window.location.href = 'landing.php';
        }
    </script>
</body>
</html>
