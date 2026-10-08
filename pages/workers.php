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
                    <a href="../index.php" class="nav-link">Home</a>
                    <a href="workers.php" class="nav-link">Workers</a>
                    <a href="#" onclick="showAbout(); return false;" class="nav-link">About</a>
                    
                    <div id="auth-buttons">
                        <a href="../login.php" class="nav-link">Login</a>
                        <a href="../register.php" class="nav-link btn">Sign Up</a>
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
            <p style="margin-top: 10px; font-size: 0.9rem; opacity: 0.9;">HandsOn - Connecting customers with verified plumbers, electricians, and skilled tradespeople in Roysambu, Nairobi</p>
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
    
    <!-- About Modal -->
    <div id="about-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 2000; overflow-y: auto;">
        <div style="background: white; max-width: 700px; margin: 40px auto; border-radius: 20px; padding: 30px; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: #7c3aed;">About HandsOn</h2>
                <button onclick="closeAbout()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">✕</button>
            </div>
            
            <div style="color: #374151; line-height: 1.7;">
                <p style="margin-bottom: 15px;"><strong>HandsOn</strong> is a digital marketplace connecting customers with verified skilled workers in Africa.</p>
                
                <h4 style="color: #7c3aed; margin-top: 20px;">The Problem</h4>
                <p>Finding skilled labor is a manual process relying on referrals. Workers lack digital visibility and verification.</p>
                
                <h4 style="color: #7c3aed; margin-top: 20px;">Our Solution</h4>
                <p>Location-based platform connecting customers with verified plumbers, electricians, and skilled tradespeople via interactive map.</p>
                
                <h4 style="color: #7c3aed; margin-top: 20px;">Features</h4>
                <ul style="margin-left: 20px;">
                    <li>Interactive map with real-time location</li>
                    <li>Verified worker profiles and reviews</li>
                    <li>Secure booking and payment</li>
                    <li>Rating and review system</li>
                </ul>
                
                <h4 style="color: #7c3aed; margin-top: 20px;">Database Design</h4>
                <ul style="margin-left: 20px;">
                    <li><strong>Users:</strong> user_id, name, email, phone, password, role</li>
                    <li><strong>Service_Providers:</strong> provider_id, user_id, skill_category, location, rating</li>
                    <li><strong>Categories:</strong> category_id, category_name</li>
                    <li><strong>Service_Requests:</strong> request_id, customer_id, provider_id, status, date</li>
                    <li><strong>Payments:</strong> payment_id, request_id, amount, method, status</li>
                </ul>
                
                <p style="margin-top: 25px; padding: 15px; background: linear-gradient(135deg, #7c3aed, #f59e0b); color: white; border-radius: 10px; text-align: center; font-weight: 600;">
                    Pilot Area: Roysambu, Nairobi
                </p>
            </div>
        </div>
    </div>
    
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
                .then(data => {
                    const workers = data.workers || data;
                    if (!workers || workers.length === 0) {
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
                                <div style="flex: 1;">
                                    <h4 style="margin: 0; font-size: 14px;">${worker.name}</h4>
                                    <p style="margin: 0; font-size: 12px; color: #666;">${worker.category}</p>
                                    <p style="margin: 0; font-size: 12px; color: #666;">⭐ ${worker.rating_avg || '0.0'}</p>
                                </div>
                                <a href="create-job.php?worker=${worker.user_id}&name=${encodeURIComponent(worker.name)}&category=${worker.category}&rating=${worker.rating_avg || 0}&hourlyRate=${worker.hourly_rate || 0}" style="background: #f59e0b; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Book</a>
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
        
        // About modal functions
        window.showAbout = function() {
            document.getElementById('about-modal').style.display = 'block';
        }
        
        window.closeAbout = function() {
            document.getElementById('about-modal').style.display = 'none';
        }
    </script>
</body>
</html>
