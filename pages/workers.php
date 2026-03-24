<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Workers - HandsOn</title>
    
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
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="index.php#workers" class="nav-link">Workers</a>
                    
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
    
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1>Find Skilled Workers</h1>
            <p>Browse verified professionals in your area</p>
        </div>
    </div>
    
    <!-- Workers Section -->
    <section class="section">
        <div class="container">
            <!-- Filters -->
            <div class="workers-filters">
                <div class="search-box" style="box-shadow: none; padding: 0;">
                    <select id="filter-category" class="form-control" onchange="applyFilters(); updateMap();">
                        <option value="">All Categories</option>
                        <option value="plumber">Plumber</option>
                        <option value="electrician">Electrician</option>
                        <option value="carpenter">Carpenter</option>
                        <option value="cleaner">Cleaner</option>
                        <option value="painter">Painter</option>
                        <option value="technician">Technician</option>
                    </select>
                    
                    <input type="text" id="filter-location" placeholder="Your location">
                    
                    <button onclick="applyFilters(); updateMap();">Apply Filters</button>
                </div>
            </div>
            
            <!-- Map and Workers Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <!-- Map -->
                <div class="map-container" style="height: 400px; border-radius: 12px; overflow: hidden;">
                    <div id="workers-map" style="height: 100%;"></div>
                </div>
                
                <!-- Workers List Preview -->
                <div id="workers-preview" class="workers-grid" style="max-height: 400px; overflow-y: auto;">
                    <p class="text-gray-500 text-center py-8">Select a category to see workers on the map</p>
                </div>
            </div>
            
            <!-- Full Workers Grid -->
            <div id="workers-grid" class="workers-grid">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading workers...</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2024 HandsOn. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="../assets/js/main.js"></script>
    
    <script>
        let map;
        let markers = [];
        
        // Initialize
        document.addEventListener('DOMContentLoaded', async function() {
            await checkAuth();
            
            // Initialize map
            initWorkersMap();
            
            // Get URL params
            const urlParams = new URLSearchParams(window.location.search);
            const category = urlParams.get('category');
            
            if (category) {
                document.getElementById('filter-category').value = category;
                updateMap();
            }
            
            await loadWorkers();
        });
        
        function initWorkersMap() {
            // Initialize map centered on Nairobi
            map = L.map('workers-map').setView([-1.2921, 36.8219], 12);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
        }
        
        function updateMap() {
            // Clear existing markers
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];
            
            const category = document.getElementById('filter-category').value;
            const workersPreview = document.getElementById('workers-preview');
            
            if (!category) {
                workersPreview.innerHTML = '<p class="text-gray-500 text-center py-8">Select a category to see workers on the map</p>';
                return;
            }
            
            // Fetch workers for the selected category
            fetch(`../api/modules/workers/list.php?category=${category}`)
                .then(res => res.json())
                .then(workers => {
                    if (workers.length === 0) {
                        workersPreview.innerHTML = '<p class="text-gray-500 text-center py-8">No workers found for this category</p>';
                        return;
                    }
                    
                    // Update preview list
                    workersPreview.innerHTML = workers.map(worker => `
                        <div class="worker-card" style="padding: 12px; border-bottom: 1px solid #eee;">
                            <div style="display: flex; gap: 12px; align-items: center;">
                                <div style="width: 50px; height: 50px; border-radius: 50%; background: #667eea; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    ${worker.name.split(' ').map(n => n[0]).join('')}
                                </div>
                                <div>
                                    <h4 style="margin: 0; font-size: 14px;">${worker.name}</h4>
                                    <p style="margin: 0; font-size: 12px; color: #666;">${worker.profession}</p>
                                    <p style="margin: 0; font-size: 12px; color: #666;">⭐ ${worker.rating}</p>
                                </div>
                            </div>
                        </div>
                    `).join('');
                    
                    // Add markers to map
                    const bounds = [];
                    workers.forEach(worker => {
                        if (worker.latitude && worker.longitude) {
                            const marker = L.marker([worker.latitude, worker.longitude])
                                .addTo(map)
                                .bindPopup(`
                                    <div style="min-width: 150px;">
                                        <strong>${worker.name}</strong><br>
                                        ${worker.profession}<br>
                                        ⭐ ${worker.rating} (${worker.reviews} reviews)<br>
                                        <a href="worker-profile.php?id=${worker.id}" style="color: #667eea;">View Profile</a>
                                    </div>
                                `);
                            markers.push(marker);
                            bounds.push([worker.latitude, worker.longitude]);
                        }
                    });
                    
                    // Fit map to markers
                    if (bounds.length > 0) {
                        map.fitBounds(bounds, { padding: [50, 50] });
                    }
                })
                .catch(err => {
                    console.error('Error loading workers:', err);
                    workersPreview.innerHTML = '<p class="text-gray-500 text-center py-8">Error loading workers</p>';
                });
        }
        
        async function loadWorkers() {
            const category = document.getElementById('filter-category').value;
            
            const filters = {};
            if (category) {
                filters.category = category;
            }
            
            // Get user location if available
            if (window.userLat && window.userLng) {
                filters.lat = window.userLat;
                filters.lng = window.userLng;
            }
            
            try {
                const workers = await getWorkers(filters);
                renderWorkersList(workers);
            } catch (error) {
                showAlert('Failed to load workers', 'error');
            }
        }
        
        function applyFilters() {
            loadWorkers();
        }
    </script>
</body>
</html>
