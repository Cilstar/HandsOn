<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Jobs - HandsOn</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    
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
                    <a href="jobs.php" class="nav-link">My Jobs</a>
                    
                    <div id="user-menu">
                        <a href="#" onclick="logout(); return false;" class="nav-link">Logout</a>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    
    <!-- Dashboard -->
    <div class="dashboard">
        <div class="container">
            <div class="dashboard-grid">
                <!-- Sidebar -->
                <aside class="sidebar">
                    <ul class="sidebar-menu">
                        <li><a href="jobs.php" class="sidebar-link active">My Jobs</a></li>
                        <li><a href="create-job.php" class="sidebar-link">New Request</a></li>
                        ${currentUser?.role === 'worker' ? '<li><a href="profile.php" class="sidebar-link">My Profile</a></li>' : ''}
                        ${currentUser?.role === 'admin' ? '<li><a href="admin/index.php" class="sidebar-link">Admin Panel</a></li>' : ''}
                    </ul>
                </aside>
                
                <!-- Content -->
                <div class="dashboard-content">
                    <div class="dashboard-header">
                        <h2>My Jobs</h2>
                        <p>View and manage your service requests</p>
                    </div>
                    
                    <!-- Filter -->
                    <div class="mb-2">
                        <select id="job-status-filter" class="form-control" onchange="loadJobs()" style="max-width: 200px;">
                            <option value="">All Jobs</option>
                            <option value="pending">Pending</option>
                            <option value="accepted">Accepted</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <!-- Jobs List -->
                    <div id="jobs-list" class="jobs-list">
                        <div class="loading">
                            <div class="spinner"></div>
                            <p>Loading jobs...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Modal -->
    <div id="payment-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Make Payment</h3>
                <button class="modal-close" onclick="closeModal('payment-modal')">&times;</button>
            </div>
            
            <form id="payment-form" onsubmit="submitPayment(event)">
                <input type="hidden" id="payment-job-id">
                
                <div class="form-group">
                    <label class="form-label">Amount (KES)</label>
                    <input type="number" id="payment-amount" class="form-control" placeholder="Enter amount" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" id="payment-phone" class="form-control" placeholder="2547xxxxxxx" required>
                </div>
                
                <div class="alert alert-info">
                    This is a mock payment system. In production, an M-Pesa STK push will be sent to your phone.
                </div>
                
                <button type="submit" class="btn btn-success" style="width: 100%;">Pay with M-Pesa</button>
            </form>
        </div>
    </div>
    
    <!-- Review Modal -->
    <div id="review-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Leave a Review</h3>
                <button class="modal-close" onclick="closeModal('review-modal')">&times;</button>
            </div>
            
            <form id="review-form" onsubmit="submitReview(event)">
                <input type="hidden" id="review-job-id">
                
                <div class="form-group">
                    <label class="form-label">Rating</label>
                    <select id="review-rating" class="form-control" required>
                        <option value="">Select rating</option>
                        <option value="5">★★★★★ (Excellent)</option>
                        <option value="4">★★★★☆ (Good)</option>
                        <option value="3">★★★☆☆ (Average)</option>
                        <option value="2">★★☆☆☆ (Poor)</option>
                        <option value="1">★☆☆☆☆ (Terrible)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Review (optional)</label>
                    <textarea id="review-text" class="form-control" placeholder="Share your experience..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Review</button>
            </form>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            await checkAuth();
            
            if (!currentUser) {
                window.location.href = 'login.php';
                return;
            }
            
            await loadJobs();
        });
        
        async function loadJobs() {
            const status = document.getElementById('job-status-filter').value;
            
            try {
                const jobs = await getJobs(status);
                renderJobs(jobs);
            } catch (error) {
                showAlert('Failed to load jobs', 'error');
            }
        }
        
        function renderJobs(jobs) {
            const list = document.getElementById('jobs-list');
            
            if (jobs.length === 0) {
                list.innerHTML = '<div class="text-center" style="padding: 40px;"><p>No jobs found</p><a href="workers.php" class="btn btn-primary mt-2">Find Workers</a></div>';
                return;
            }
            
            const isWorker = currentUser.role === 'worker';
            
            list.innerHTML = jobs.map(job => `
                <div class="job-card">
                    <div class="job-info">
                        <h4>${job.title}</h4>
                        <div class="job-meta">
                            <span>📋 ${CATEGORIES[job.category]?.name || job.category}</span>
                            <span>📍 ${job.address || 'No address'}</span>
                            <span>📅 ${formatDate(job.created_at)}</span>
                            ${job.scheduled_date ? `<span>🕐 ${job.scheduled_date}</span>` : ''}
                        </div>
                        <p style="margin-top: 10px; color: var(--text-secondary);">${job.description || ''}</p>
                    </div>
                    <div style="text-align: right;">
                        <span class="job-status ${job.status}">${JOB_STATUS[job.status] || job.status}</span>
                        
                        <div class="mt-2" style="display: flex; gap: 10px; flex-direction: column;">
                            ${renderJobActions(job, isWorker)}
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        function renderJobActions(job, isWorker) {
            let actions = '';
            
            // Customer actions
            if (!isWorker && currentUser.role === 'customer') {
                if (job.status === 'pending') {
                    actions += `<button class="btn btn-danger" onclick="cancelJob(${job.id})">Cancel</button>`;
                } else if (job.status === 'completed') {
                    actions += `<button class="btn btn-primary" onclick="openPaymentModal(${job.id})">Pay</button>`;
                }
            }
            
            // Worker actions
            if (isWorker) {
                if (job.status === 'pending') {
                    actions += `<button class="btn btn-success" onclick="acceptJob(${job.id})">Accept</button>`;
                    actions += `<button class="btn btn-danger" onclick="rejectJob(${job.id})">Reject</button>`;
                } else if (job.status === 'accepted') {
                    actions += `<button class="btn btn-primary" onclick="startJob(${job.id})">Start Work</button>`;
                } else if (job.status === 'in_progress') {
                    actions += `<button class="btn btn-success" onclick="completeJob(${job.id})">Complete</button>`;
                }
            }
            
            // Review for completed jobs (customer only)
            if (!isWorker && job.status === 'completed') {
                actions += `<button class="btn btn-outline" onclick="openReviewModal(${job.id})">Leave Review</button>`;
            }
            
            return actions;
        }
        
        // Job actions
        async function acceptJob(jobId) {
            if (await updateJobStatus(jobId, 'accepted')) {
                loadJobs();
            }
        }
        
        async function rejectJob(jobId) {
            if (await updateJobStatus(jobId, 'rejected')) {
                loadJobs();
            }
        }
        
        async function startJob(jobId) {
            if (await updateJobStatus(jobId, 'in_progress')) {
                loadJobs();
            }
        }
        
        async function completeJob(jobId) {
            if (await updateJobStatus(jobId, 'completed')) {
                loadJobs();
            }
        }
        
        async function cancelJob(jobId) {
            if (await updateJobStatus(jobId, 'cancelled')) {
                loadJobs();
            }
        }
        
        // Payment
        function openPaymentModal(jobId) {
            document.getElementById('payment-job-id').value = jobId;
            openModal('payment-modal');
        }
        
        async function submitPayment(e) {
            e.preventDefault();
            
            const jobId = document.getElementById('payment-job-id').value;
            const amount = document.getElementById('payment-amount').value;
            const phone = document.getElementById('payment-phone').value;
            
            const result = await createPayment(jobId, amount, phone);
            
            if (result) {
                closeModal('payment-modal');
                showAlert('Payment initiated! Check your phone.', 'success');
            }
        }
        
        // Review
        function openReviewModal(jobId) {
            document.getElementById('review-job-id').value = jobId;
            openModal('review-modal');
        }
        
        async function submitReview(e) {
            e.preventDefault();
            
            const jobId = document.getElementById('review-job-id').value;
            const rating = document.getElementById('review-rating').value;
            const reviewText = document.getElementById('review-text').value;
            
            if (await createReview(jobId, rating, reviewText)) {
                closeModal('review-modal');
                showAlert('Thank you for your review!', 'success');
            }
        }
    </script>
</body>
</html>
