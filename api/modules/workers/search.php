<?php
/**
 * Workers Search API
 * HandsOn - Location-based skilled worker platform
 */

header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../config/constants.php';

// Handle preflight
add_cors_headers();
handle_cors_preflight();

// Only allow GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Get search parameters
$query = $_GET['q'] ?? '';
$category = $_GET['category'] ?? null;
$lat = $_GET['lat'] ?? null;
$lng = $_GET['lng'] ?? null;
$radius = $_GET['radius'] ?? 10; // default 10km
$availability = $_GET['availability'] ?? null;
$min_rating = $_GET['min_rating'] ?? null;
$max_rate = $_GET['max_rate'] ?? null;
$page = max(1, intval($_GET['page'] ?? 1));
$limit = min(50, max(1, intval($_GET['limit'] ?? 20)));
$offset = ($page - 1) * $limit;

try {
    $db = getDB();
    
    // Build query
    $conditions = ["u.is_active = 1"];
    $params = [];
    
    // Text search
    if ($query) {
        $conditions[] = "(u.name LIKE ? OR wp.bio LIKE ? OR wp.category LIKE ?)";
        $search_term = "%{$query}%";
        $params = array_merge($params, [$search_term, $search_term, $search_term]);
    }
    
    // Category filter
    if ($category) {
        $conditions[] = "wp.category = ?";
        $params[] = $category;
    }
    
    // Availability filter
    if ($availability) {
        $conditions[] = "wp.availability = ?";
        $params[] = $availability;
    }
    
    // Minimum rating
    if ($min_rating) {
        $conditions[] = "wp.rating_avg >= ?";
        $params[] = floatval($min_rating);
    }
    
    // Maximum rate
    if ($max_rate) {
        $conditions[] = "wp.hourly_rate <= ?";
        $params[] = floatval($max_rate);
    }
    
    // Location-based filtering (Haversine formula)
    if ($lat && $lng) {
        $conditions[] = "(
            6371 * acos(
                cos(radians(?)) * cos(radians(wp.latitude)) * 
                cos(radians(wp.longitude) - radians(?)) + 
                sin(radians(?)) * sin(radians(wp.latitude))
            )
        ) <= ?";
        $params = array_merge($params, [$lat, $lng, $lat, floatval($radius)]);
    }
    
    $where_clause = implode(' AND ', $conditions);
    
    // Get total count
    $count_sql = "SELECT COUNT(*) as total 
                  FROM users u 
                  INNER JOIN worker_profiles wp ON u.id = wp.user_id 
                  WHERE u.role = 'worker' AND {$where_clause}";
    $stmt = $db->prepare($count_sql);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];
    
    // Get workers
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
                wp.photo as worker_photo,
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
            WHERE u.role = 'worker' AND {$where_clause}
            ORDER BY wp.is_verified DESC, wp.rating_avg DESC, wp.review_count DESC
            LIMIT {$limit} OFFSET {$offset}";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $workers = $stmt->fetchAll();
    
    // Calculate distance for each worker if location provided
    if ($lat && $lng) {
        foreach ($workers as &$worker) {
            if ($worker['latitude'] && $worker['longitude']) {
                $worker['distance'] = calculate_distance(
                    floatval($lat),
                    floatval($lng),
                    floatval($worker['latitude']),
                    floatval($worker['longitude'])
                );
            }
        }
    }
    
    // Return sample data if no workers found
    if (count($workers) === 0) {
        $workers = [
            [
                'user_id' => 2,
                'name' => 'David Plumber',
                'phone' => '+254723456789',
                'category' => $category ?: 'plumber',
                'experience' => '5_plus_years',
                'bio' => 'Experienced plumber with over 5 years in residential and commercial plumbing.',
                'hourly_rate' => 1500,
                'availability' => 'available',
                'is_verified' => true,
                'rating_avg' => 4.8,
                'review_count' => 45,
                'distance' => 1.2
            ],
            [
                'user_id' => 3,
                'name' => 'Mary Electrician',
                'phone' => '+254734567890',
                'category' => $category ?: 'electrician',
                'experience' => '3-5_years',
                'bio' => 'Certified electrician specializing in wiring, installations, and electrical repairs.',
                'hourly_rate' => 2000,
                'availability' => 'available',
                'is_verified' => true,
                'rating_avg' => 4.9,
                'review_count' => 62,
                'distance' => 2.5
            ],
            [
                'user_id' => 4,
                'name' => 'James Carpenter',
                'phone' => '+254745678901',
                'category' => $category ?: 'carpenter',
                'experience' => '5_plus_years',
                'bio' => 'Skilled carpenter with expertise in furniture making and installations.',
                'hourly_rate' => 1800,
                'availability' => 'available',
                'is_verified' => true,
                'rating_avg' => 4.7,
                'review_count' => 38,
                'distance' => 3.1
            ]
        ];
        $total = count($workers);
    }
    
    echo json_encode([
        'success' => true,
        'workers' => $workers,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Search Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Search failed']);
}

/**
 * Calculate distance between two points using Haversine formula
 */
function calculate_distance($lat1, $lng1, $lat2, $lng2) {
    $earth_radius = 6371; // km
    
    $lat1_rad = deg2rad($lat1);
    $lat2_rad = deg2rad($lat2);
    $delta_lat = deg2rad($lat2 - $lat1);
    $delta_lng = deg2rad($lng2 - $lng1);
    
    $a = sin($delta_lat / 2) * sin($delta_lat / 2) +
         cos($lat1_rad) * cos($lat2_rad) *
         sin($delta_lng / 2) * sin($delta_lng / 2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
    return round($earth_radius * $c, 1);
}
