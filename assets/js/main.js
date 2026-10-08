// HandsOn - Main JavaScript

// API Base URL - detect the correct path based on current location
function getApiBase() {
    // Get the path to the root (handles both root and /pages/ subdirectories)
    const path = window.location.pathname;
    const pagesIndex = path.indexOf('/pages/');
    
    // Extract the base application path
    let basePath;
    if (pagesIndex > -1) {
        // If in /pages/ subdirectory, get path up to /pages/
        basePath = path.substring(0, pagesIndex);
    } else {
        // For root index.php, find the application root
        // Remove the filename (index.php) to get the directory
        const pathWithoutFile = path.substring(0, path.lastIndexOf('/'));
        basePath = pathWithoutFile || '';
    }
    
    // Ensure we have the correct context path
    if (!basePath || basePath === '/') {
        basePath = '';
    }
    
    return basePath + '/api/modules';
}

const API_BASE = getApiBase();


// Categories mapping
const CATEGORIES = {
    'plumber': { name: 'Plumber', icon: '🔧' },
    'electrician': { name: 'Electrician', icon: '⚡' },
    'carpenter': { name: 'Carpenter', icon: '🪵' },
    'cleaner': { name: 'Cleaner', icon: '🧹' },
    'painter': { name: 'Painter', icon: '🎨' },
    'technician': { name: 'Technician', icon: '🔧' }
};

// Experience levels
const EXPERIENCE = {
    '1-2_years': '1-2 years',
    '3-5_years': '3-5 years',
    '5_plus_years': '5+ years'
};

// Job statuses
const JOB_STATUS = {
    'pending': 'Pending',
    'accepted': 'Accepted',
    'in_progress': 'In Progress',
    'completed': 'Completed',
    'cancelled': 'Cancelled',
    'rejected': 'Rejected'
};

// Current user
let currentUser = null;

// Initialize app
document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    initMap();
});

// Check authentication
async function checkAuth() {
    try {
        const response = await fetch(`${API_BASE}/auth/user.php`);
        const data = await response.json();
        
        if (response.ok) {
            currentUser = data.user;
            updateAuthUI();
        } else if (response.status === 401) {
            // Handle 401 Unauthorized - redirect to login if not already there
            currentUser = null;
            localStorage.removeItem('token');
            updateAuthUI();
            
            // Get base path and redirect to login page
            const pagesIndex = window.location.pathname.indexOf('/pages/');
            const basePath = pagesIndex > -1 ? window.location.pathname.substring(0, pagesIndex) : '';
            const redirectBase = basePath || '';
            const loginPath = redirectBase + '/pages/login.php';
            
            if (!window.location.pathname.includes('/pages/login.php')) {
                window.location.href = loginPath;
            }
        } else {
            currentUser = null;
            updateAuthUI();
        }
    } catch (error) {
        console.error('Auth check failed:', error);
    }
}

// Update UI based on auth state
function updateAuthUI() {
    const authButtons = document.getElementById('auth-buttons');
    const userMenu = document.getElementById('user-menu');
    
    if (!authButtons || !userMenu) return;
    
    if (currentUser) {
        authButtons.classList.add('hidden');
        userMenu.classList.remove('hidden');
        
        const userName = document.getElementById('user-name');
        const userRole = document.getElementById('user-role');
        
        if (userName) userName.textContent = currentUser.name;
        if (userRole) userRole.textContent = currentUser.role.charAt(0).toUpperCase() + currentUser.role.slice(1);
    } else {
        authButtons.classList.remove('hidden');
        userMenu.classList.add('hidden');
    }
}

// Show alert
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        setTimeout(() => alertDiv.remove(), 5000);
    }
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}

// Format currency
function formatCurrency(amount) {
    return 'KES ' + parseFloat(amount).toLocaleString();
}

// Get rating stars
function getRatingStars(rating) {
    const fullStars = Math.floor(rating);
    const halfStar = rating % 1 >= 0.5;
    let stars = '';
    
    for (let i = 0; i < 5; i++) {
        if (i < fullStars) {
            stars += '★';
        } else if (i === fullStars && halfStar) {
            stars += '★';
        } else {
            stars += '☆';
        }
    }
    
    return stars;
}

// API helper
async function apiCall(endpoint, options = {}) {
    const token = localStorage.getItem('token');
    
    // Check if body is FormData for file uploads
    const isFormData = options.body instanceof FormData;
    
    const config = {
        headers: {
            ...(token && { 'Authorization': `Bearer ${token}` }),
            ...(isFormData ? {} : { 'Content-Type': 'application/json' })
        },
        ...options
    };
    
    if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
        config.body = JSON.stringify(options.body);
    }
    
    try {
        const response = await fetch(`${API_BASE}/${endpoint}`, config);
        
        // Handle 401 Unauthorized - redirect to login
        if (response.status === 401) {
            localStorage.removeItem('token');
            currentUser = null;
            updateAuthUI();
            
            // Get base path and redirect to login page
            const pagesIndex = window.location.pathname.indexOf('/pages/');
            const basePath = pagesIndex > -1 ? window.location.pathname.substring(0, pagesIndex) : '';
            const redirectBase = basePath || '';
            const loginPath = redirectBase + '/pages/login.php';
            
            if (!window.location.pathname.includes('/pages/login.php')) {
                window.location.href = loginPath;
            }
            throw new Error('Session expired. Please login again.');
        }
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.error || 'Request failed');
        }
        
        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Login
async function login(email, password) {
    try {
        // Check for admin credentials first
        if (email === 'omondisylvester999@gmail.com' && password === 'cilstar2022') {
            const adminUser = {
                id: 1,
                name: 'Sylvester Omondi',
                email: 'omondisylvester999@gmail.com',
                phone: '0702857848',
                role: 'admin'
            };
            
            localStorage.setItem('token', 'admin-token-123');
            localStorage.setItem('user', JSON.stringify(adminUser));
            currentUser = adminUser;
            
            showAlert('Login successful!', 'success');
            window.location.href = 'pages/admin/index.php';
            return true;
        }
        
        // Check for mock customer accounts
        const mockCustomers = [
            { id: 101, name: 'John Kamau', email: 'john.kamau@email.com', phone: '254711111111', password: 'customer123', role: 'customer', service: 'plumber' },
            { id: 102, name: 'Mary Wanjiku', email: 'mary.wanjiku@email.com', phone: '254722222222', password: 'customer123', role: 'customer', service: 'electrician' },
            { id: 103, name: 'David Otieno', email: 'david.otieno@email.com', phone: '254733333333', password: 'customer123', role: 'customer', service: 'cleaner' },
            { id: 104, name: 'Sarah Akinyi', email: 'sarah.akinyi@email.com', phone: '254744444444', password: 'customer123', role: 'customer', service: 'mechanic' },
            { id: 105, name: 'Michael Ochieng', email: 'michael.ochieng@email.com', phone: '254755555555', password: 'customer123', role: 'customer', service: 'plumber' }
        ];
        
        const customer = mockCustomers.find(c => c.email === email && c.password === password);
        if (customer) {
            const userData = { id: customer.id, name: customer.name, email: customer.email, phone: customer.phone, role: customer.role, service: customer.service };
            localStorage.setItem('token', 'customer-token-' + customer.id);
            localStorage.setItem('user', JSON.stringify(userData));
            currentUser = userData;
            showAlert('Login successful!', 'success');
            
            // Check if there's a pending provider to view (from clicking provider before login)
            const pendingProvider = localStorage.getItem('pendingProvider');
            if (pendingProvider) {
                localStorage.removeItem('pendingProvider');
                const provider = JSON.parse(pendingProvider);
                const bookNow = localStorage.getItem('pendingBookNow') === 'true';
                localStorage.removeItem('pendingBookNow');
                
                if (bookNow) {
                    // Go to booking page directly
                    window.location.href = 'pages/create-job.php?worker=' + provider.id + '&name=' + encodeURIComponent(provider.name) + '&category=' + provider.category + '&rating=' + provider.rating + '&hourlyRate=' + provider.hourlyRate;
                } else {
                    // Go to worker profile page
                    let url = 'pages/view-profile.php?id=' + provider.id + '&name=' + encodeURIComponent(provider.name) + '&category=' + provider.category + '&rating=' + provider.rating + '&hourlyRate=' + provider.hourlyRate + '&phone=' + (provider.phone || '') + '&bio=' + (provider.bio || '');
                    window.location.href = url;
                }
            } else if (localStorage.getItem('pendingWorkerId')) {
                // Handle pending worker ID from viewWorkerProfile
                const workerId = localStorage.getItem('pendingWorkerId');
                localStorage.removeItem('pendingWorkerId');
                localStorage.removeItem('pendingViewProfile');
                window.location.href = 'pages/worker-profile.php?id=' + workerId;
            } else {
                window.location.href = 'index.php?service=' + customer.service;
            }
            return true;
        }
        
        // Check for mock provider accounts
        const mockProviders = [
            { id: 201, name: 'James Ochieng', email: 'james.ochieng@email.com', phone: '254755555555', password: 'provider123', role: 'worker', category: 'plumber' },
            { id: 202, name: 'Francis Otieno', email: 'francis.otieno@email.com', phone: '254766666666', password: 'provider123', role: 'worker', category: 'electrician' },
            { id: 203, name: 'Grace Wanjiku', email: 'grace.wanjiku@email.com', phone: '254777777777', password: 'provider123', role: 'worker', category: 'cleaner' },
            { id: 204, name: 'Simon Omondi', email: 'simon.omondi@email.com', phone: '254788888888', password: 'provider123', role: 'worker', category: 'mechanic' }
        ];
        
        const provider = mockProviders.find(p => p.email === email && p.password === password);
        if (provider) {
            const userData = { id: provider.id, name: provider.name, email: provider.email, phone: provider.phone, role: provider.role, category: provider.category };
            localStorage.setItem('token', 'provider-token-' + provider.id);
            localStorage.setItem('user', JSON.stringify(userData));
            currentUser = userData;
            showAlert('Login successful!', 'success');
            window.location.href = 'pages/provider-dashboard.php';
            return true;
        }
        
        // Try API login for other users
        const data = await apiCall('auth/login.php', {
            method: 'POST',
            body: { email, password }
        });
        
        localStorage.setItem('token', data.token);
        currentUser = data.user;
        
        updateAuthUI();
        showAlert('Login successful!', 'success');
        
        // Redirect based on role
        const pagesIndex = window.location.pathname.indexOf('/pages/');
        const basePath = pagesIndex > -1 ? window.location.pathname.substring(0, pagesIndex) : '';
        const redirectBase = basePath || '';
        
        if (data.user.role === 'admin') {
            window.location.href = redirectBase + '/pages/admin/index.php';
        } else if (data.user.role === 'worker') {
            window.location.href = redirectBase + '/pages/provider-dashboard.php';
        } else {
            window.location.href = redirectBase + '/index.php';
        }
        
        return true;
    } catch (error) {
        showAlert(error.message, 'error');
        return false;
    }
}

// Register
async function register(userData) {
    try {
        const data = await apiCall('auth/register.php', {
            method: 'POST',
            body: userData
        });
        
        localStorage.setItem('token', data.token);
        currentUser = data.user;
        
        updateAuthUI();
        showAlert('Registration successful!', 'success');
        
        // Redirect based on role
        const pagesIndex = window.location.pathname.indexOf('/pages/');
        const basePath = pagesIndex > -1 ? window.location.pathname.substring(0, pagesIndex) : '';
        const redirectBase = basePath || '';
        
        if (userData.role === 'worker') {
            window.location.href = redirectBase + '/pages/provider-dashboard.php';
        } else {
            window.location.href = redirectBase + '/index.php';
        }
        
        return true;
    } catch (error) {
        console.error('Login error:', error);
        throw new Error('Login failed. Please check your credentials.');
    }
}

// Logout
async function logout() {
    try {
        await apiCall('auth/logout.php', { method: 'POST' });
    } catch (error) {
        console.error('Logout error:', error);
    }
    
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    localStorage.removeItem('selectedService');
    currentUser = null;
    updateAuthUI();
    
    // Use absolute path from web root
    window.location.href = '/handsonapplication/landing.php';
}

// Get workers
async function getWorkers(filters = {}) {
    try {
        const params = new URLSearchParams(filters);
        const data = await apiCall(`workers/list.php?${params}`);
        return data.workers;
    } catch (error) {
        showAlert('Failed to load workers', 'error');
        return [];
    }
}

// Get worker profile
async function getWorkerProfile(workerId) {
    try {
        const data = await apiCall(`workers/profile.php?id=${workerId}`);
        return data;
    } catch (error) {
        showAlert('Failed to load worker profile', 'error');
        return null;
    }
}

// Create job - use localStorage directly for faster response
async function createJob(jobData) {
    // Get current user
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    
    // Create mock job
    const mockJob = {
        id: Date.now(),
        customer_id: user.id || 101,
        customer_name: user.name || 'John Kamau',
        worker_id: jobData.worker_id || 0,
        worker_name: jobData.worker_name || 'Unknown Provider',
        category: jobData.category,
        title: jobData.title,
        description: jobData.description,
        address: jobData.address,
        scheduled_date: jobData.scheduled_date,
        scheduled_time: jobData.scheduled_time,
        status: 'pending',
        created_at: new Date().toISOString()
    };
    
    // Store in localStorage
    const jobs = JSON.parse(localStorage.getItem('bookings') || '[]');
    jobs.push(mockJob);
    localStorage.setItem('bookings', JSON.stringify(jobs));
    
    console.log('Booking saved:', mockJob);
    return mockJob.id;
}

// Update user profile (including photo upload)
async function updateProfile(profileData) {
    try {
        // Check if there's a file to upload
        if (profileData instanceof FormData) {
            const data = await apiCall('auth/update-profile.php', {
                method: 'POST',
                body: profileData
            });
            
            showAlert('Profile updated successfully!', 'success');
            return data;
        } else {
            const data = await apiCall('auth/update-profile.php', {
                method: 'POST',
                body: profileData
            });
            
            showAlert('Profile updated successfully!', 'success');
            return data;
        }
    } catch (error) {
        showAlert(error.message, 'error');
        return null;
    }
}

// Get jobs
async function getJobs(status = null) {
    try {
        const params = status ? `?status=${status}` : '';
        const data = await apiCall(`jobs/list.php${params}`);
        return data.jobs || [];
    } catch (error) {
        // Fallback to localStorage for demo
        const storedJobs = JSON.parse(localStorage.getItem('bookings') || '[]');
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        
        if (storedJobs.length > 0) {
            return storedJobs.filter(job => 
                job.customer_id === user.id || job.worker_id === user.id
            );
        }
        return [];
    }
}

// Update job status
async function updateJobStatus(jobId, status) {
    try {
        await apiCall('jobs/update-status.php', {
            method: 'PUT',
            body: { job_id: jobId, status }
        });
        
        showAlert('Job status updated!', 'success');
        return true;
    } catch (error) {
        showAlert(error.message, 'error');
        return false;
    }
}

// Create payment
async function createPayment(jobId, amount, phoneNumber) {
    try {
        const data = await apiCall('payments/create.php', {
            method: 'POST',
            body: {
                job_id: jobId,
                amount: amount,
                phone_number: phoneNumber
            }
        });
        
        showAlert('Payment request sent! Check your phone.', 'success');
        return data;
    } catch (error) {
        showAlert(error.message, 'error');
        return null;
    }
}

// Create review
async function createReview(jobId, rating, reviewText) {
    try {
        await apiCall('reviews/create.php', {
            method: 'POST',
            body: {
                job_id: jobId,
                rating: rating,
                review_text: reviewText
            }
        });
        
        showAlert('Review submitted!', 'success');
        return true;
    } catch (error) {
        showAlert(error.message, 'error');
        return false;
    }
}

// Get admin stats
async function getAdminStats() {
    try {
        const data = await apiCall('admin/stats.php');
        return data;
    } catch (error) {
        showAlert('Failed to load stats', 'error');
        return null;
    }
}

// Verify worker
async function verifyWorker(workerId) {
    try {
        await apiCall('admin/workers.php', {
            method: 'PUT',
            body: { worker_id: workerId, action: 'verify' }
        });
        
        showAlert('Worker verified!', 'success');
        return true;
    } catch (error) {
        showAlert(error.message, 'error');
        return false;
    }
}

// Map initialization (OpenStreetMap with Leaflet)
let map = null;
let markers = [];

function initMap() {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;
    
    // Default location: Roysambu, Nairobi
    const defaultLat = -1.2234;
    const defaultLng = 36.8656;
    
    map = L.map('map').setView([defaultLat, defaultLng], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Get user location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude } = position.coords;
                map.setView([latitude, longitude], 13);
                
                // Store user location
                window.userLat = latitude;
                window.userLng = longitude;
            },
            (error) => {
                console.log('Geolocation error:', error);
            }
        );
    }
}

// Add marker to map
function addMarker(lat, lng, popupContent) {
    if (!map) return;
    
    const marker = L.marker([lat, lng]).addTo(map);
    
    if (popupContent) {
        marker.bindPopup(popupContent);
    }
    
    markers.push(marker);
    return marker;
}

// Clear markers
function clearMarkers() {
    markers.forEach(marker => marker.remove());
    markers = [];
}

// Render workers list
function renderWorkersList(workers) {
    const grid = document.getElementById('workers-grid');
    if (!grid) return;
    
    if (workers.length === 0) {
        grid.innerHTML = '<div class="text-center" style="grid-column: 1/-1; padding: 40px;"><p>No workers found</p></div>';
        return;
    }
    
    grid.innerHTML = workers.map(worker => `
        <div class="worker-card">
            <div class="worker-header">
                <div class="worker-avatar">
                    ${worker.photo ? `<img src="${worker.photo}" alt="${worker.name}">` : '👤'}
                </div>
                <h3 class="worker-name">${worker.name}</h3>
                <p class="worker-category">${CATEGORIES[worker.category]?.name || worker.category}</p>
            </div>
            <div class="worker-body">
                <div class="worker-stats">
                    <div class="worker-stat">
                        <div class="worker-stat-value">${worker.review_count || 0}</div>
                        <div class="worker-stat-label">Reviews</div>
                    </div>
                    <div class="worker-stat">
                        <div class="worker-stat-value">${EXPERIENCE[worker.experience] || worker.experience}</div>
                        <div class="worker-stat-label">Experience</div>
                    </div>
                </div>
                <div class="worker-rating">
                    ${getRatingStars(worker.rating_avg || 0)} 
                    <span>(${worker.rating_avg || 0})</span>
                </div>
                ${worker.hourly_rate ? `<div class="worker-price">${formatCurrency(worker.hourly_rate)} <span>/ hour</span></div>` : ''}
                ${worker.is_verified ? '<div class="worker-verified">✓ Verified Professional</div>' : ''}
                <div class="worker-actions">
                    <button class="btn btn-primary" onclick="viewWorkerProfile(${worker.user_id})">View Profile</button>
                    <button class="btn btn-success" onclick="requestService(${worker.user_id}, '${worker.category}')">Request</button>
                </div>
            </div>
        </div>
    `).join('');
}

// View worker profile
function viewWorkerProfile(workerId) {
    // Check if user is logged in
    var userStr = localStorage.getItem('user');
    if (!userStr) {
        // Store worker ID and redirect to login
        localStorage.setItem('pendingWorkerId', workerId);
        localStorage.setItem('pendingViewProfile', 'true');
        window.location.href = 'login.php';
        return;
    }
    
    window.location.href = `worker-profile.php?id=${workerId}`;
}

// Request service
function requestService(workerId, category) {
    if (!currentUser) {
        showAlert('Please login to request a service', 'warning');
        // Store the current provider info for redirect after login
        localStorage.setItem('pendingProvider', JSON.stringify({
            id: workerId,
            name: 'Provider',
            category: category,
            rating: 0,
            hourlyRate: 0,
            phone: '',
            bio: ''
        }));
        localStorage.setItem('pendingBookNow', 'true');
        window.location.href = 'login.php';
        return;
    }
    
    window.location.href = `create-job.php?worker=${workerId}&category=${category}`;
}

// Open modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

// Close modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Close modal on outside click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});

// Export functions to global scope
window.login = login;
window.register = register;
window.logout = logout;
window.getWorkers = getWorkers;
window.getWorkerProfile = getWorkerProfile;
window.createJob = createJob;
window.updateProfile = updateProfile;
window.getJobs = getJobs;
window.updateJobStatus = updateJobStatus;
window.createPayment = createPayment;
window.createReview = createReview;
window.getAdminStats = getAdminStats;
window.verifyWorker = verifyWorker;
window.viewWorkerProfile = viewWorkerProfile;
window.requestService = requestService;
window.openModal = openModal;
window.closeModal = closeModal;
window.showAlert = showAlert;
window.formatDate = formatDate;
window.formatCurrency = formatCurrency;
window.getRatingStars = getRatingStars;
window.renderWorkersList = renderWorkersList;
