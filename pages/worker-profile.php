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
            // Check if user is logged in
            await checkAuth();
            
            const urlParams = new URLSearchParams(window.location.search);
            const workerId = urlParams.get('id');
            const workerName = urlParams.get('name');
            const workerCategory = urlParams.get('category');
            const workerRating = urlParams.get('rating');
            const workerHourlyRate = urlParams.get('hourlyRate');
            const workerPhone = urlParams.get('phone');
            const workerBio = urlParams.get('bio');
            const shouldBook = urlParams.get('book') === '1';
            
            // If we have workerId and it's a valid number (from database), try API
            if (workerId && !isNaN(parseInt(workerId)) && parseInt(workerId) < 1000) {
                await loadWorkerProfile(workerId);
                // If book=1, open booking modal after profile loads
                if (shouldBook) {
                    setTimeout(function() {
                        if (workerData) {
                            openRequestModal();
                        } else {
                            openBookingModal();
                        }
                    }, 500);
                }
            } else if (workerName && workerCategory) {
                // If workerId is >= 1000 (mock data), render from URL params
                renderFromUrl(workerName, workerCategory, workerRating, workerHourlyRate, workerPhone, workerBio);
                // If book=1, open booking modal
                if (shouldBook) {
                    setTimeout(openBookingModal, 500);
                }
            } else {
                showAlert('Worker not found', 'error');
                window.location.href = 'workers.php';
            }
        });
        
        function renderFromUrl(name, category, rating, hourlyRate, phone, bio) {
            const content = document.getElementById('profile-content');
            
            content.innerHTML = `
                <div class="profile-header">
                    <div class="profile-info">
                        <div class="profile-avatar">
                            ${CATEGORIES[category] ? CATEGORIES[category].icon : '👤'}
                        </div>
                        <div class="profile-details">
                            <h2>${name}</h2>
                            <p>${CATEGORIES[category]?.name || category}</p>
                            <p>Available for service</p>
                        </div>
                    </div>
                </div>
                
                <div class="workers-grid" style="grid-template-columns: 1fr 1fr;">
                    <div>
                        <div class="card" style="background: white; padding: 25px; border-radius: var(--radius); margin-bottom: 20px; box-shadow: var(--shadow);">
                            <h3 class="mb-2">About</h3>
                            <p>${bio || 'Professional ' + (CATEGORIES[category]?.name || category) + ' ready to serve you.'}</p>
                            
                            <div class="worker-stats mt-2">
                                <div class="worker-stat">
                                    <div class="worker-stat-value">${rating || '0.0'}</div>
                                    <div class="worker-stat-label">Rating</div>
                                </div>
                                <div class="worker-stat">
                                    <div class="worker-stat-value">0</div>
                                    <div class="worker-stat-label">Reviews</div>
                                </div>
                                <div class="worker-stat">
                                    <div class="worker-stat-value">Expert</div>
                                    <div class="worker-stat-label">Experience</div>
                                </div>
                            </div>
                            
                            <div class="worker-rating mt-2">
                                ${getRatingStars(parseFloat(rating) || 0)}
                            </div>
                            
                            ${hourlyRate ? `<div class="worker-price mt-2">KSh ${hourlyRate} <span>/ hour</span></div>` : ''}
                            
                            <button class="btn btn-primary mt-2" style="width: 100%;" onclick="openBookingModal()">Book Now</button>
                        </div>
                        
                        <div class="card" style="background: white; padding: 25px; border-radius: var(--radius); box-shadow: var(--shadow);">
                            <h3 class="mb-2">Contact</h3>
                            <p><strong>Phone:</strong> ${phone || 'Available on booking'}</p>
                            <p><strong>Email:</strong> Available on booking</p>
                        </div>
                    </div>
                    
                    <div>
                        <div class="card" style="background: white; padding: 25px; border-radius: var(--radius); box-shadow: var(--shadow);">
                            <h3 class="mb-2">Services Offered</h3>
                            <p>Professional ${CATEGORIES[category]?.name || category} services including:</p>
                            <ul style="margin-top: 10px; padding-left: 20px;">
                                <li>Home repairs and maintenance</li>
                                <li>Emergency services</li>
                                <li>Quality guaranteed work</li>
                            </ul>
                        </div>
                    </div>
                </div>
            `;
            
            // Store data for booking
            window.bookingData = {
                name: name,
                category: category,
                rating: rating,
                hourlyRate: hourlyRate,
                phone: phone,
                bio: bio
            };
        }
        
        function openBookingModal() {
            if (!currentUser) {
                showAlert('Please login to book a service', 'warning');
                // Store the current provider info for redirect after login
                localStorage.setItem('pendingProvider', JSON.stringify({
                    id: workerId,
                    name: workerData.name,
                    category: workerData.category,
                    rating: workerData.rating,
                    hourlyRate: workerData.hourly_rate,
                    phone: workerData.phone,
                    bio: workerData.bio
                }));
                localStorage.setItem('pendingBookNow', 'true');
                window.location.href = 'login.php';
                return;
            }
            
            openModal('request-modal');
        }
        
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
                            
                            <button class="btn btn-primary mt-2" style="width: 100%;" onclick="openRequestModal()">Request Service</button>
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
                // Store the current provider info for redirect after login
                localStorage.setItem('pendingProvider', JSON.stringify({
                    id: workerId,
                    name: workerData.name,
                    category: workerData.category,
                    rating: workerData.rating,
                    hourlyRate: workerData.hourly_rate,
                    phone: workerData.phone,
                    bio: workerData.bio
                }));
                localStorage.setItem('pendingBookNow', 'false');
                window.location.href = 'login.php';
                return;
            }
            
            document.getElementById('modal-worker-id').value = workerData.worker.user_id;
            document.getElementById('modal-category').value = workerData.worker.category;
            openModal('request-modal');
        }
        
        async function submitRequest(e) {
            e.preventDefault();
            
            // Check if we have booking data from URL (direct navigation)
            if (window.bookingData) {
                const jobData = {
                    worker_id: 0, // Will be assigned by system
                    category: window.bookingData.category,
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
                return;
            }
            
            // Original flow for API-loaded worker
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
