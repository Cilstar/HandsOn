<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Profile - HandsOn</title>
    
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
                    <a href="workers.php" class="nav-link">Workers</a>
                    
                    <div id="auth-buttons" class="hidden">
                        <a href="login.php" class="nav-link">Login</a>
                        <a href="register.php" class="nav-link btn">Sign Up</a>
                    </div>
                    
                    <div id="user-menu">
                        <a href="jobs.php" class="nav-link">My Jobs</a>
                        <a href="#" onclick="logout(); return false;" class="nav-link">Logout</a>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    
    <!-- Profile Section -->
    <section class="section" style="padding-top: 120px;">
        <div class="container">
            <div id="profile-content">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading profile...</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Request Modal -->
    <div id="request-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Request Service</h3>
                <button class="modal-close" onclick="closeModal('request-modal')">&times;</button>
            </div>
            
            <form id="request-form" onsubmit="submitRequest(event)">
                <input type="hidden" id="modal-worker-id">
                <input type="hidden" id="modal-category">
                
                <div class="form-group">
                    <label class="form-label">Job Title</label>
                    <input type="text" id="job-title" class="form-control" placeholder="e.g., Fix leaking pipe" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea id="job-description" class="form-control" placeholder="Describe the service you need..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" id="job-address" class="form-control" placeholder="Your address" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Scheduled Date</label>
                    <input type="date" id="job-date" class="form-control">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Scheduled Time</label>
                    <input type="time" id="job-time" class="form-control">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Request</button>
            </form>
        </div>
    </div>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="../assets/js/main.js"></script>
    
    <script>
        let workerData = null;
        
        document.addEventListener('DOMContentLoaded', async function() {
            await checkAuth();
            
            const urlParams = new URLSearchParams(window.location.search);
            const workerId = urlParams.get('id');
            
            if (!workerId) {
                showAlert('Worker not found', 'error');
                window.location.href = 'workers.php';
                           
                            return;
            }
            await loadWorkerProfile(workerId);
        });
        
        async function loadWorkerProfile(workerId) {
            try {
                const data = await getWorkerProfile(workerId);
                
                if (!data || !data.worker) {
                    showAlert('Worker not found', 'error');
                    window.location.href = 'workers.php';
                    return;
                }
                
                workerData = data;
                renderProfile(data);
            } catch (error) {
                showAlert('Failed to load profile', 'error');
            }
        }
        
        function renderProfile(data) {
            const worker = data.worker;
            const reviews = data.reviews || [];
            
            const content = document.getElementById('profile-content');
            
            content.innerHTML = `
                <div class="profile-header">
                    <div class="profile-info">
                        <div class="profile-avatar">
                            ${worker.photo ? `<img src="${worker.photo}" alt="${worker.name}">` : '👤'}
                        </div>
                        <div class="profile-details">
                            <h2>${worker.name}</h2>
                            <p>${CATEGORIES[worker.category]?.name || worker.category}</p>
                            <p>${worker.is_verified ? '✓ Verified Professional' : ''}</p>
                        </div>
                    </div>
                </div>
                
                <div class="workers-grid" style="grid-template-columns: 1fr 1fr;">
                    <div>
                        <div class="card" style="background: white; padding: 25px; border-radius: var(--radius); margin-bottom: 20px; box-shadow: var(--shadow);">
                            <h3 class="mb-2">About</h3>
                            <p>${worker.bio || 'No bio available'}</p>
                            
                            <div class="worker-stats mt-2">
                                <div class="worker-stat">
                                    <div class="worker-stat-value">${worker.rating_avg || 0}</div>
                                    <div class="worker-stat-label">Rating</div>
                                </div>
                                <div class="worker-stat">
                                    <div class="worker-stat-value">${worker.review_count || 0}</div>
                                    <div class="worker-stat-label">Reviews</div>
                                </div>
                                <div class="worker-stat">
                                    <div class="worker-stat-value">${EXPERIENCE[worker.experience] || worker.experience}</div>
                                    <div class="worker-stat-label">Experience</div>
                                </div>
                            </div>
                            
                            <div class="worker-rating mt-2">
                                ${getRatingStars(worker.rating_avg || 0)}
                            </div>
                            
                            ${worker.hourly_rate ? `<div class="worker-price mt-2">${formatCurrency(worker.hourly_rate)} <span>/ hour</span></div>` : ''}
                            
                            <button class="btn btn-success mt-2" style="width: 100%;" onclick="openRequestModal()">Request Service</button>
                        </div>
                        
                        <div class="card" style="background: white; padding: 25px; border-radius: var(--radius); box-shadow: var(--shadow);">
                            <h3 class="mb-2">Contact</h3>
                            <p><strong>Phone:</strong> ${worker.phone}</p>
                            <p><strong>Email:</strong> ${worker.email}</p>
                        </div>
                    </div>
                    
                    <div>
                        <div class="map-container" style="height: 250px; margin-bottom: 20px;">
                            <div id="mini-map"></div>
                        </div>
                        
                        <div class="card" style="background: white; padding: 25px; border-radius: var(--radius); box-shadow: var(--shadow);">
                            <h3 class="mb-2">Reviews (${reviews.length})</h3>
                            
                            ${reviews.length > 0 ? reviews.map(review => `
                                <div class="review-card">
                                    <div class="review-header">
                                        <strong>${review.customer_name}</strong>
                                        <span class="review-rating">${getRatingStars(review.rating)}</span>
                                    </div>
                                    <p class="review-text">${review.review_text || ''}</p>
                                    <span class="review-date">${formatDate(review.created_at)}</span>
                                </div>
                            `).join('') : '<p>No reviews yet</p>'}
                        </div>
                    </div>
                </div>
            `;
            
            // Initialize mini map
            if (worker.latitude && worker.longitude) {
                const miniMap = L.map('mini-map').setView([worker.latitude, worker.longitude], 14);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(miniMap);
                
                L.marker([worker.latitude, worker.longitude]).addTo(miniMap)
                    .bindPopup(worker.name).openPopup();
            }
        }
        
        function openRequestModal() {
            if (!currentUser) {
                showAlert('Please login to request a service', 'warning');
                window.location.href = 'login.php';
                return;
            }
            
            document.getElementById('modal-worker-id').value = workerData.worker.user_id;
            document.getElementById('modal-category').value = workerData.worker.category;
            openModal('request-modal');
        }
        
        async function submitRequest(e) {
            e.preventDefault();
            
            const jobData = {
                worker_id: document.getElementById('modal-worker-id').value,
                category: document.getElementById('modal-category').value,
                title: document.getElementById('job-title').value,
                description: document.getElementById('job-description').value,
                address: document.getElementById('job-address').value,
                scheduled_date: document.getElementById('job-date').value,
                scheduled_time: document.getElementById('job-time').value
            };
            
            const jobId = await createJob(jobData);
            
            if (jobId) {
                closeModal('request-modal');
                window.location.href = `jobs.php`;
            }
        }
    </script>
</body>
</html>
