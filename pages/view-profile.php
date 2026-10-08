<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Provider Profile - HandsOn</title>
    
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
            background: #f8fafc;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            padding: 40px 20px;
            color: white;
        }
        
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .profile-main {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            border: 4px solid white;
        }
        
        .profile-info h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .profile-category {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .profile-rating {
            font-size: 1.2rem;
            color: #fbbf24;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .profile-body {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        .info-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .info-card h3 {
            color: #1f2937;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #6b7280;
            font-weight: 500;
        }
        
        .info-value {
            color: #1f2937;
            font-weight: 600;
        }
        
        .price-tag {
            font-size: 2rem;
            color: #7c3aed;
            font-weight: 700;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: white;
            flex: 1;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);
        }
        
        .btn-secondary {
            background: #e5e7eb;
            color: #1f2937;
            flex: 1;
        }
        
        .btn-secondary:hover {
            background: #d1d5db;
        }
        
        .services-list {
            list-style: none;
        }
        
        .services-list li {
            padding: 10px 0;
            color: #4b5563;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .services-list li::before {
            content: "✓";
            color: #10b981;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="profile-header">
        <div class="profile-container">
            <a href="javascript:history.back()" class="back-btn">← Back</a>
            
            <div class="profile-main">
                <div class="profile-avatar" id="provider-icon">👤</div>
                <div class="profile-info">
                    <h1 id="provider-name">Loading...</h1>
                    <span class="profile-category" id="provider-category">...</span>
                    <div class="profile-rating" id="provider-rating">⭐ 0.0</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="profile-body">
        <div class="info-card">
            <h3>💰 Pricing</h3>
            <div class="price-tag" id="provider-price">KSh 0/hr</div>
        </div>
        
        <div class="info-card">
            <h3>ℹ️ About</h3>
            <p id="provider-bio" style="color: #4b5563; line-height: 1.6;">Loading...</p>
        </div>
        
        <div class="info-card">
            <h3>🛠️ Services Offered</h3>
            <ul class="services-list">
                <li>Home repairs and maintenance</li>
                <li>Emergency services available</li>
                <li>Quality guaranteed work</li>
                <li>Professional equipment and tools</li>
            </ul>
        </div>
        
        <div class="info-card">
            <h3>📋 Details</h3>
            <div class="info-row">
                <span class="info-label">Experience</span>
                <span class="info-value" id="provider-experience">Expert</span>
            </div>
            <div class="info-row">
                <span class="info-label">Location</span>
                <span class="info-value">Roysambu, Nairobi</span>
            </div>
            <div class="info-row">
                <span class="info-label">Availability</span>
                <span class="info-value" style="color: #10b981;">Available</span>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="#" id="book-btn" class="btn btn-primary">📅 Book Now</a>
            <a href="tel:+254700000000" class="btn btn-secondary">📞 Contact</a>
        </div>
    </div>
    
    <script>
        const CATEGORIES = {
            plumber: { name: 'Plumber', icon: '🔧' },
            electrician: { name: 'Electrician', icon: '⚡' },
            cleaner: { name: 'Cleaner', icon: '🧹' },
            mechanic: { name: 'Mechanic', icon: '🚗' },
            carpenter: { name: 'Carpenter', icon: '🪵' }
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const name = urlParams.get('name');
            const category = urlParams.get('category');
            const rating = urlParams.get('rating');
            const hourlyRate = urlParams.get('hourlyRate');
            const bio = urlParams.get('bio');
            
            if (name && category) {
                document.getElementById('provider-icon').textContent = CATEGORIES[category]?.icon || '👤';
                document.getElementById('provider-name').textContent = name;
                document.getElementById('provider-category').textContent = CATEGORIES[category]?.name || category;
                document.getElementById('provider-rating').textContent = '⭐ ' + (rating || '0.0');
                document.getElementById('provider-price').textContent = 'KSh ' + (hourlyRate || '0') + '/hr';
                
                const bioText = bio || 'Professional ' + (CATEGORIES[category]?.name || 'service provider') + ' with years of experience serving customers in Nairobi. Committed to quality work and customer satisfaction.';
                document.getElementById('provider-bio').textContent = bioText;
                
                // Set up book button with provider info
                const bookBtn = document.getElementById('book-btn');
                const workerId = urlParams.get('id') || '0';
                bookBtn.href = 'create-job.php?worker=' + workerId + '&name=' + encodeURIComponent(name) + '&category=' + category + '&rating=' + rating + '&hourlyRate=' + hourlyRate;
            } else {
                alert('Provider information not found');
                window.location.href = 'index.php';
            }
        });
    </script>
</body>
</html>