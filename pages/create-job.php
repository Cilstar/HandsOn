<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Service - HandsOn</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        :root {
            --primary: #7c3aed;
            --secondary: #f59e0b;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f3f4f6;
        }
        
        .booking-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .booking-header {
            background: white;
            padding: 25px;
            border-radius: 16px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .provider-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .provider-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }
        
        .provider-details h2 {
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .provider-details p {
            color: #6b7280;
            margin-bottom: 5px;
        }
        
        .provider-rating {
            color: #f59e0b;
            font-weight: 600;
        }
        
        .provider-price {
            font-size: 1.5rem;
            color: #7c3aed;
            font-weight: 700;
        }
        
        .booking-form {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #7c3aed;
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: white;
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);
        }
        
        .btn-secondary {
            background: #e5e7eb;
            color: #1f2937;
            margin-right: 10px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #7c3aed;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <a href="javascript:history.back()" class="back-link">← Back</a>
        
        <div class="booking-header">
            <div class="provider-info">
                <div class="provider-avatar" id="provider-icon">👤</div>
                <div class="provider-details">
                    <h2 id="provider-name">Loading...</h2>
                    <p id="provider-category">Loading...</p>
                    <div class="provider-rating" id="provider-rating">⭐ 0.0</div>
                </div>
                <div class="provider-price" id="provider-price">KSh 0/hr</div>
            </div>
        </div>
        
        <div class="booking-form">
            <h2 style="margin-bottom: 25px;">Book a Service</h2>
            
            <form id="booking-form" onsubmit="submitBooking(event)">
                <input type="hidden" id="worker-id" name="worker_id">
                <input type="hidden" id="category" name="category">
                
                <div class="form-group">
                    <label class="form-label">Service Title *</label>
                    <input type="text" id="job-title" name="title" class="form-control" placeholder="e.g., Fix leaking pipe in kitchen" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description *</label>
                    <textarea id="job-description" name="description" class="form-control" placeholder="Describe what you need done in detail..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Your Address *</label>
                    <input type="text" id="job-address" name="address" class="form-control" placeholder="Enter your full address" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Preferred Date</label>
                        <input type="date" id="job-date" name="scheduled_date" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Preferred Time</label>
                        <input type="time" id="job-time" name="scheduled_time" class="form-control">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="confirm-btn">Confirm Booking</button>
            </form>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
    <script>
        var urlParams = new URLSearchParams(window.location.search);
        var workerId = urlParams.get('worker') || urlParams.get('id') || '0';
        var workerName = urlParams.get('name') || 'Service Provider';
        var category = urlParams.get('category') || 'plumber';
        var rating = urlParams.get('rating') || '4.5';
        var hourlyRate = urlParams.get('hourlyRate') || '500';
        var icons = { plumber: '🔧', electrician: '⚡', cleaner: '🧹', mechanic: '🚗' };
        
        document.getElementById('provider-icon').textContent = icons[category] || '🔧';
        document.getElementById('provider-name').textContent = decodeURIComponent(workerName);
        document.getElementById('provider-category').textContent = category.charAt(0).toUpperCase() + category.slice(1);
        document.getElementById('provider-rating').textContent = '⭐ ' + rating;
        document.getElementById('provider-price').textContent = 'KSh ' + hourlyRate + '/hr';
        document.getElementById('worker-id').value = workerId;
        document.getElementById('category').value = category;
        
        var storedUser = localStorage.getItem('user');
        if (!storedUser) { window.location.href = 'login.php'; }
        
        document.getElementById('confirm-btn').onclick = function() {
            var title = document.getElementById('job-title').value;
            var description = document.getElementById('job-description').value;
            var address = document.getElementById('job-address').value;
            
            if (!title || !description || !address) {
                alert('Please fill in all required fields');
                return;
            }
            
            var btn = this;
            btn.textContent = 'Ordering...';
            btn.disabled = true;
            
            var user = JSON.parse(localStorage.getItem('user') || '{}');
            var providerName = decodeURIComponent(urlParams.get('name')) || 'Unknown Provider';
            var scheduledDate = document.getElementById('job-date').value || new Date().toLocaleDateString();
            var scheduledTime = document.getElementById('job-time').value || '09:00';
            
            // Create job object
            var mockJob = {
                id: Date.now(),
                customer_id: user.id || 101,
                customer_name: user.name || 'John Kamau',
                customer_phone: user.phone || '',
                worker_id: parseInt(workerId),
                worker_name: providerName,
                category: category,
                title: title,
                description: description,
                address: address,
                scheduled_date: scheduledDate,
                scheduled_time: scheduledTime,
                status: 'pending',
                created_at: new Date().toISOString()
            };
            
            // Save to localStorage
            var jobs = JSON.parse(localStorage.getItem('bookings') || '[]');
            jobs.push(mockJob);
            localStorage.setItem('bookings', JSON.stringify(jobs));
            
            // Store worker notification
            var workerNotify = JSON.parse(localStorage.getItem('worker_notifications') || '[]');
            workerNotify.push({
                id: Date.now(),
                worker_id: parseInt(workerId),
                worker_name: providerName,
                customer_name: user.name || 'Customer',
                service: title,
                status: 'new',
                created_at: new Date().toISOString()
            });
            localStorage.setItem('worker_notifications', JSON.stringify(workerNotify));
            
            console.log('Order placed:', mockJob);
            
            var catName = category.charAt(0).toUpperCase() + category.slice(1);
            
            // Show success message
            var formEl = document.querySelector('.booking-form');
            formEl.innerHTML = '<div style="text-align:center;padding:40px;">' +
                '<div style="font-size:4rem;">✅</div>' +
                '<h2 style="color:#10b981;margin:20px 0;">Booking Confirmed!</h2>' +
                '<div style="background:#f0fdf4;padding:20px;border-radius:12px;text-align:left;">' +
                '<p><strong>Service:</strong> ' + catName + '</p>' +
                '<p><strong>Provider:</strong> ' + providerName + '</p>' +
                '<p><strong>Date:</strong> ' + scheduledDate + '</p>' +
                '<p><strong>Job ID:</strong> #' + mockJob.id + '</p>' +
                '</div>' +
                '<a href="jobs.php" class="btn btn-primary" style="display:inline-block;margin-top:20px;padding:14px 30px;text-decoration:none;">View My Bookings</a>' +
                '</div>';
        };
    </script>
</body>
</html>