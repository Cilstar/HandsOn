<?php
/**
 * List Workers API
 * HandsOn - Location-based skilled worker platform
 */

// Turn off error display for API
error_reporting(0);
ini_set('display_errors', 0);

require_once '../../config/database.php';
require_once '../../config/constants.php';

// Enable CORS and handle preflight
add_cors_headers();
handle_cors_preflight();

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(API_BAD_REQUEST);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

try {
    $db = getDB();
    
    // Get query parameters
    $category = $_GET['category'] ?? null;
    $latitude = $_GET['lat'] ?? null;
    $longitude = $_GET['lng'] ?? null;
    $radius = $_GET['radius'] ?? 10; // Default 10km
    $availability = $_GET['availability'] ?? 'available';
    
    // Base query
    $sql = "SELECT 
                u.id as user_id,
                u.name,
                u.email,
                u.phone,
                u.profile_photo,
                wp.id as profile_id,
                wp.category,
                wp.experience,
                wp.bio,
                wp.photo,
                wp.latitude,
                wp.longitude,
                wp.service_radius,
                wp.hourly_rate,
                wp.availability,
                wp.is_verified,
                wp.rating_avg,
                wp.review_count
            FROM users u
            INNER JOIN worker_profiles wp ON u.id = wp.user_id
            WHERE u.role = 'worker' AND u.is_active = 1";
    
    $params = [];
    
    // Add category filter
    if ($category) {
        $sql .= " AND wp.category = ?";
        $params[] = $category;
    }
    
    // Add availability filter
    if ($availability) {
        $sql .= " AND wp.availability = ?";
        $params[] = $availability;
    }
    
    $sql .= " ORDER BY wp.is_verified DESC, wp.rating_avg DESC, wp.review_count DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $workers = $stmt->fetchAll();
    
    // Calculate distance if coordinates provided
    if ($latitude && $longitude) {
        foreach ($workers as &$worker) {
            if ($worker['latitude'] && $worker['longitude']) {
                $worker['distance'] = calculateDistance(
                    $latitude, 
                    $longitude, 
                    $worker['latitude'], 
                    $worker['longitude']
                );
            } else {
                $worker['distance'] = null;
            }
        }
        
        // Filter by radius
        if ($radius) {
            $workers = array_filter($workers, function($w) use ($radius) {
                return $w['distance'] === null || $w['distance'] <= $radius;
            });
            $workers = array_values($workers);
        }
        
        // Sort by distance
        usort($workers, function($a, $b) {
            if ($a['distance'] === null) return 1;
            if ($b['distance'] === null) return -1;
            return $a['distance'] - $b['distance'];
        });
    }
    
    // Return sample data if no workers found
    if (count($workers) === 0) {
        // Use provider accounts from landing.php for consistency
        $workers = [
            [
                'user_id' => 201,
                'name' => 'James Ochieng',
                'phone' => '254755555555',
                'category' => 'plumber',
                'experience' => '5_plus_years',
                'bio' => 'Experienced plumber with over 5 years in residential and commercial plumbing.',
                'hourly_rate' => 500,
                'availability' => 'available',
                'is_verified' => true,
                'rating_avg' => 4.8,
                'review_count' => 45,
                'distance' => 1.2,
                'latitude' => -1.2225 + 0.005,
                'longitude' => 36.8947 + 0.003
            ],
            [
                'user_id' => 202,
                'name' => 'Francis Otieno',
                'phone' => '254766666666',
                'category' => 'electrician',
                'experience' => '3-5_years',
                'bio' => 'Certified electrician specializing in wiring, installations, and electrical repairs.',
                'hourly_rate' => 600,
                'availability' => 'available',
                'is_verified' => true,
                'rating_avg' => 4.6,
                'review_count' => 38,
                'distance' => 2.5,
                'latitude' => -1.2180,
                'longitude' => 36.8900
            ],
            [
                'user_id' => 203,
                'name' => 'Grace Wanjiku',
                'phone' => '254777777777',
                'category' => 'cleaner',
                'experience' => '5_plus_years',
                'bio' => 'Professional cleaner with expertise in residential and office cleaning.',
                'hourly_rate' => 300,
                'availability' => 'available',
                'is_verified' => true,
                'rating_avg' => 4.9,
                'review_count' => 62,
                'distance' => 3.1,
                'latitude' => -1.2300,
                'longitude' => 36.8980
            ],
            [
                'user_id' => 204,
                'name' => 'Simon Omondi',
                'phone' => '254788888888',
                'category' => 'mechanic',
                'experience' => '5_plus_years',
                'bio' => 'Experienced mechanic specializing in car repairs and maintenance.',
                'hourly_rate' => 700,
                'availability' => 'available',
                'is_verified' => true,
                'rating_avg' => 4.7,
                'review_count' => 51,
                'distance' => 4.0,
                'latitude' => -1.2350,
                'longitude' => 36.8850
            ]
        ];
        
        // Filter by category if specified
        if ($category) {
            $workers = array_filter($workers, function($w) use ($category) {
                return $w['category'] === $category;
            });
            $workers = array_values($workers);
        }
    }
    
    http_response_code(API_SUCCESS);
    echo json_encode([
        'success' => true,
        'workers' => $workers,
        'count' => count($workers)
    ]);
    
} catch (PDOException $e) {
    error_log("Workers List Error: " . $e->getMessage());
    http_response_code(API_SERVER_ERROR);
    echo json_encode(['error' => 'Failed to fetch workers']);
}

/**
 * Calculate distance between two coordinates using Haversine formula
 */
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // km
    
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);
    
    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;
    
    $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    return round($earthRadius * $c, 2);
}
