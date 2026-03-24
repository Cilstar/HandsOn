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
            <button class="header-btn" onclick="window.location.href='landing.php'">Login</button>
        </div>
        
        <div class="header-actions" id="user-menu" style="display: none;">
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
                <input type="text" id="search-destination" placeholder="What service do you need?">
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
        
        <!-- Bottom Sheet -->
        <div class="bottom-sheet" id="bottom-sheet">
            <div class="bottom-sheet-handle" onclick="toggleSheet()"></div>
            
            <div id="sheet-content">
                <h3 style="margin-bottom: 16px;">Nearby Service Providers</h3>
                <p id="provider-count" style="color: #6b7280; margin-bottom: 16px;">Loading...</p>
                <div id="providers-list"></div>
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
            
            // Init map
            map = L.map('map').setView([-1.2921, 36.8219], 13);
            
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
            var base = userLocation || [-1.2921, 36.8219];
            providers = [];
            var cats = ['plumber', 'electrician', 'cleaner', 'mechanic'];
            
            for (var i = 0; i < 25; i++) {
                var cat = cats[Math.floor(Math.random() * cats.length)];
                providers.push({
                    id: i + 1,
                    name: kenyanNames[cat][Math.floor(Math.random() * kenyanNames[cat].length)],
                    category: cat,
                    rating: (3.5 + Math.random() * 1.5).toFixed(1),
                    reviews: Math.floor(Math.random() * 150),
                    hourlyRate: Math.floor(500 + Math.random() * 2500),
                    distance: (Math.random() * 5).toFixed(1),
                    location: [base[0] + (Math.random() - 0.5) * 0.03, base[1] + (Math.random() - 0.5) * 0.03]
                });
            }
            
            displayProviders(providers);
            addMarkers(providers);
        }
        
        function displayProviders(list) {
            var filtered = currentCategory === 'all' ? list : list.filter(p => p.category === currentCategory);
            document.getElementById('provider-count').textContent = filtered.length + ' providers available';
            
            if (filtered.length === 0) {
                document.getElementById('providers-list').innerHTML = '<p style="text-align:center;color:#6b7280;padding:30px;">No providers found</p>';
                return;
            }
            
            document.getElementById('providers-list').innerHTML = filtered.map(function(p) {
                return '<div class="provider-card ' + (selectedProvider && selectedProvider.id === p.id ? 'selected' : '') + '" onclick="selectProvider(' + p.id + ')">' +
                    '<div class="provider-avatar">' + (CATEGORIES[p.category] ? CATEGORIES[p.category].icon : '👤') + '</div>' +
                    '<div class="provider-info"><div class="provider-name">' + p.name + '</div>' +
                    '<div class="provider-category">' + (CATEGORIES[p.category] ? CATEGORIES[p.category].name : p.category) + '</div>' +
                    '<div class="provider-details"><span>⭐ ' + p.rating + '</span><span>📍 ' + p.distance + 'km</span></div></div>' +
                    '<div class="provider-price">KSh ' + p.hourlyRate + '/hr</div></div>';
            }).join('');
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
                    '<button onclick="selectProvider(' + p.id + ')" style="margin-top:8px;padding:6px 16px;background:#7c3aed;color:white;border:none;border-radius:6px;cursor:pointer;">Select</button></div>'
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
            document.getElementById('bottom-sheet').classList.add('expanded');
        }
        
        function searchServices() {
            var dest = document.getElementById('search-destination').value;
            if (!dest) { alert('Please enter a service'); return; }
            if (!selectedProvider) {
                document.getElementById('bottom-sheet').classList.add('expanded');
                return;
            }
            document.getElementById('sheet-content').innerHTML = 
                '<div style="text-align:center;padding:20px;"><div style="font-size:3rem;">✓</div>' +
                '<h3>' + selectedProvider.name + ' Accepted!</h3>' +
                '<p style="color:#6b7280;">On the way to you</p>' +
                '<button onclick="resetBooking()" style="margin-top:20px;padding:12px 24px;background:#fee2e2;color:#dc2626;border:none;border-radius:8px;cursor:pointer;">Cancel</button></div>';
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
