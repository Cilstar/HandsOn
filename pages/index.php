<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HandsOn - Connect with Verified Skilled Workers</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-inner">
                <a href="index.php" class="logo">
                    <div class="logo-icon">🛠️</div>
                    <span>HandsOn</span>
                </a>
                
                <nav class="nav-menu">
                    <a href="#categories" class="nav-link">Services</a>
                    <a href="#workers" class="nav-link">Workers</a>
                    <a href="#how-it-works" class="nav-link">How It Works</a>
                    
                    <div id="auth-buttons">
                        <a href="login.php" class="nav-link">Login</a>
                        <a href="register.php" class="nav-link btn">Sign Up</a>
                    </div>
                    
                    <div id="user-menu" class="hidden">
                        <a href="jobs.php" class="nav-link">My Jobs</a>
                        <a href="#" onclick="logout(); return false;" class="nav-link">Logout</a>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Find Trusted Skilled Workers Near You</h1>
            <p>Connect with verified plumbers, electricians, carpenters, and more in Roysambu, Nairobi</p>
            
            <!-- Search Box -->
            <div class="search-box">
                <select id="search-category" class="form-control">
                    <option value="">Select Service</option>
                    <option value="plumber">Plumber</option>
                    <option value="electrician">Electrician</option>
                    <option value="cleaner">Cleaner</option>
                    <option value="mechanic">Mechanic</option>
                </select>
                
                <input type="text" id="search-location" placeholder="Enter your location">
                
                <button onclick="searchWorkers()">Search Workers</button>
            </div>
        </div>
    </section>
    
    <!-- Services Section with Images -->
    <section id="categories" class="section">
        <div class="container">
            <div class="section-title">
                <h2>Our Services</h2>
                <p>Find the right professional for your needs</p>
            </div>
            
            <div class="categories-grid">
                <div class="category-card" onclick="filterByCategory('plumber')" style="padding: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); cursor: pointer;">
                    <div class="category-image" style="height: 180px; background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('https://images.unsplash.com/photo-1506806732259-39c2d0268443?w=400&h=300&fit=crop') center/cover no-repeat;"></div>
                    <div style="padding: 20px; text-align: center;">
                        <div class="category-icon">🔧</div>
                        <h3>Plumber</h3>
                    </div>
                </div>
                <div class="category-card" onclick="filterByCategory('electrician')" style="padding: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); cursor: pointer;">
                    <div class="category-image" style="height: 180px; background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('https://images.unsplash.com/photo-1621905252507-b35492cc74b4?w=400&h=300&fit=crop') center/cover no-repeat;"></div>
                    <div style="padding: 20px; text-align: center;">
                        <div class="category-icon">⚡</div>
                        <h3>Electrician</h3>
                    </div>
                </div>
                <div class="category-card" onclick="filterByCategory('cleaner')" style="padding: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); cursor: pointer;">
                    <div class="category-image" style="height: 180px; background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('https://images.unsplash.com/photo-1581578731117-104f2a41272c?w=400&h=300&fit=crop') center/cover no-repeat;"></div>
                    <div style="padding: 20px; text-align: center;">
                        <div class="category-icon">🧹</div>
                        <h3>Cleaner</h3>
                    </div>
                </div>
                <div class="category-card" onclick="filterByCategory('mechanic')" style="padding: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); cursor: pointer;">
                    <div class="category-image" style="height: 180px; background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?w=400&h=300&fit=crop') center/cover no-repeat;"></div>
                    <div style="padding: 20px; text-align: center;
">
                        <div class="category-icon">🚗</div>
                        <h3>Mechanic</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- How It Works -->
    <section id="how-it-works" class="section" style="background: white;">
        <div class="container">
            <div class="section-title">
                <h2>How It Works</h2>
                <p>Get professional help in three simple steps</p>
            </div>
            
            <div class="categories-grid">
                <div class="category-card">
                    <div class="category-icon" style="background: #E3F2FD;">🔍</div>
                    <h3>1. Search</h3>
                    <p style="color: var(--text-secondary); margin-top: 10px; font-size: 0.9rem;">Browse verified workers near your location</p>
                </div>
                <div class="category-card">
                    <div class="category-icon" style="background: #FFF3E0;">📋</div>
                    <h3>2. Request</h3>
                    <p style="color: var(--text-secondary); margin-top: 10px; font-size: 0.9rem;">Send a service request with your details</p>
                </div>
                <div class="category-card">
                    <div class="category-icon" style="background: #E8F5E9;">✓</div>
                    <h3>3. Get Service</h3>
                    <p style="color: var(--text-secondary); margin-top: 10px; font-size: 0.9rem;">Receive professional service and pay securely</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Workers Section -->
    <section id="workers" class="section">
        <div class="container">
            <div class="section-title">
                <h2>Featured Workers</h2>
                <p>Top-rated professionals in your area</p>
            </div>
            
            <div id="workers-grid" class="workers-grid">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading workers...</p>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <a href="workers.php" class="btn btn-primary">View All Workers</a>
            </div>
        </div>
    </section>
    
    <!-- Map Section -->
    <section class="section" style="background: white;">
        <div class="container">
            <div class="section-title">
                <h2>Find Workers Near You</h2>
                <p>Select a service to view available workers on the map</p>
            </div>
            
            <!-- Map Service Category Filter -->
            <div class="map-category-filter">
                <button class="map-category-btn active" data-category="all" onclick="filterMapByCategory('all')">
                    <span>👀</span> All
                </button>
                <button class="map-category-btn" data-category="plumber" onclick="filterMapByCategory('plumber')">
                    <span>🔧</span> Plumber
                </button>
                <button class="map-category-btn" data-category="electrician" onclick="filterMapByCategory('electrician')">
                    <span>⚡</span> Electrician
                </button>
                <button class="map-category-btn" data-category="cleaner" onclick="filterMapByCategory('cleaner')">
                    <span>🧹</span> Cleaner
                </button>
                <button class="map-category-btn" data-category="mechanic" onclick="filterMapByCategory('mechanic')">
                    <span>🚗</span> Mechanic
                </button>
            </div>
            
            <div class="map-container">
                <div id="map"></div>
            </div>
            
            <!-- Worker Count -->
            <div id="worker-count" class="text-center mt-2" style="color: var(--text-secondary);">
                Loading workers...
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <h3>HandsOn</h3>
                    <p style="color: rgba(255,255,255,0.7);">Connecting customers with verified skilled workers in Kenya.</p>
                </div>
                <div>
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#categories">Services</a></li>
                        <li><a href="#workers">Workers</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                    </ul>
                </div>
                <div>
                    <h3>Contact</h3>
                    <ul class="footer-links">
                        <li>📍 Roysambu, Nairobi</li>
                        <li>📞 +254 700 000 000</li>
                        <li>✉️ info@handson.co.ke</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 HandsOn. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
    
    <script>
        let allWorkers = [];
        let mapMarkers = [];
        let currentMapFilter = 'all';
        
        // Initialize map with sample worker locations
        async function initFeaturedWorkers() {
            try {
                allWorkers = await getWorkers();
                renderWorkersList(allWorkers.slice(0, 6));
                
                // Add markers to map
                addMarkersToMap(allWorkers);
                
                // Update worker count
                updateWorkerCount(allWorkers.length);
            } catch (error) {
                console.error('Failed to load workers:', error);
            }
        }
        
        // Add markers to map
        function addMarkersToMap(workers) {
            // Clear existing markers
            mapMarkers.forEach(marker => map.removeLayer(marker));
            mapMarkers = [];
            
            workers.forEach(worker => {
                if (worker.latitude && worker.longitude) {
                    const icon = L.divIcon({
                        html: CATEGORIES[worker.category]?.icon || '🔧',
                        className: 'custom-marker',
                        iconSize: [40, 40],
                        iconAnchor: [20, 20]
                    });
                    
                    const marker = L.marker([worker.latitude, worker.longitude], { icon: icon })
                        .bindPopup(`
                            <div style="text-align: center;">
                                <strong>${worker.name}</strong><br>
                                <span style="color: #7c3aed;">${CATEGORIES[worker.category]?.name}</span><br>
                                <span>⭐ ${worker.rating_avg || 0}</span><br>
                                <button onclick="viewWorkerProfile(${worker.user_id})" class="btn btn-primary btn-sm" style="margin-top: 8px;">View Profile</button>
                            </div>
                        `)
                        .addTo(map);
                    
                    mapMarkers.push(marker);
                }
            });
        }
        
        // Filter map by category
        function filterMapByCategory(category) {
            currentMapFilter = category;
            
            // Update button states
            document.querySelectorAll('.map-category-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.category === category) {
                    btn.classList.add('active');
                }
            });
            
            // Filter workers
            let filteredWorkers;
            if (category === 'all') {
                filteredWorkers = allWorkers;
            } else {
                filteredWorkers = allWorkers.filter(w => w.category === category);
            }
            
            // Update markers
            addMarkersToMap(filteredWorkers);
            
            // Update count
            updateWorkerCount(filteredWorkers.length);
            
            // Fit map to markers if there are any
            if (filteredWorkers.length > 0) {
                const validWorkers = filteredWorkers.filter(w => w.latitude && w.longitude);
                if (validWorkers.length > 0) {
                    const group = L.featureGroup(mapMarkers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            }
        }
        
        // Update worker count display
        function updateWorkerCount(count) {
            const countDiv = document.getElementById('worker-count');
            if (countDiv) {
                if (currentMapFilter === 'all') {
                    countDiv.textContent = `${count} workers available in your area`;
                } else {
                    const categoryName = CATEGORIES[currentMapFilter]?.name || currentMapFilter;
                    countDiv.textContent = `${count} ${categoryName}s available in your area`;
                }
            }
        }
        
        // Search workers
        function searchWorkers() {
            const category = document.getElementById('search-category').value;
            const url = category ? `workers.php?category=${category}` : 'workers.php';
            window.location.href = url;
        }
        
        // Filter by category
        function filterByCategory(category) {
            window.location.href = `workers.php?category=${category}`;
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initFeaturedWorkers();
        });
    </script>
</body>
</html>
