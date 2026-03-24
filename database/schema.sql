-- HandsOn Database Schema
-- Location-based skilled worker platform

-- Create database
CREATE DATABASE IF NOT EXISTS handson_db;
USE handson_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'worker', 'admin') DEFAULT 'customer',
    is_active BOOLEAN DEFAULT TRUE,
    is_verified BOOLEAN DEFAULT FALSE,
    email_verified BOOLEAN DEFAULT FALSE,
    phone_verified BOOLEAN DEFAULT FALSE,
    profile_photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- API Tokens table
CREATE TABLE IF NOT EXISTS api_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    data JSON,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Worker profiles table
CREATE TABLE IF NOT EXISTS worker_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL,
    experience VARCHAR(50) NOT NULL,
    bio TEXT,
    photo VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    service_radius INT DEFAULT 5,
    hourly_rate DECIMAL(10, 2),
    availability ENUM('available', 'busy', 'offline') DEFAULT 'offline',
    is_verified BOOLEAN DEFAULT FALSE,
    rating_avg DECIMAL(3, 2) DEFAULT 0.00,
    review_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_category (category),
    INDEX idx_availability (availability),
    INDEX idx_location (latitude, longitude)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Job requests table
CREATE TABLE IF NOT EXISTS job_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    worker_id INT,
    category VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    address VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    status ENUM('pending', 'accepted', 'in_progress', 'completed', 'cancelled', 'rejected') DEFAULT 'pending',
    scheduled_date DATE,
    scheduled_time TIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (worker_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_customer (customer_id),
    INDEX idx_worker (worker_id),
    INDEX idx_status (status),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    customer_id INT NOT NULL,
    worker_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    commission_amount DECIMAL(10, 2) DEFAULT 0.00,
    status ENUM('pending', 'initiated', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(100) UNIQUE,
    payment_method VARCHAR(50) DEFAULT 'mpesa',
    phone_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES job_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (worker_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_job (job_id),
    INDEX idx_status (status),
    INDEX idx_transaction (transaction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    customer_id INT NOT NULL,
    worker_id INT NOT NULL,
    rating INT(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    admin_reviewed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES job_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (worker_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_worker (worker_id),
    INDEX idx_customer (customer_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin activity log
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    target_type VARCHAR(50),
    target_id INT,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_admin (admin_id),
    INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: cilstar2022)
INSERT INTO users (name, email, phone, password, role) 
VALUES ('System Admin', 'omondisylvester999@gmail.com', '254700000000', '$2y$10$eQQII3KzbTvFhOHFm8IKkuiSsL6FfLkm5c9NlCG85ym4/fox5lyrm', 'admin');

-- Insert sample customer
INSERT INTO users (name, email, phone, password, role) 
VALUES ('John Doe', 'john@example.com', '254712345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- Insert sample workers
INSERT INTO users (name, email, phone, password, role) 
VALUES 
-- Plumbers (3)
('David Wanjiku', 'david.wanjiku@handson.co.ke', '254723456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'worker'),
('John Kamau', 'john.kamau@handson.co.ke', '254723456790', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'worker'),
('Michael Ochieng', 'michael.ochieng@handson.co.ke', '254723456791', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'worker'),
-- Electricians (3)
('Mary Akinyi', 'mary.akinyi@handson.co.ke', '254734567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'worker'),
('Joseph Nyaga', 'joseph.nyaga@handson.co.ke', '254734567891', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'worker'),
('Paul Kimani', 'paul.kimani@handson.co.ke', '254734567892', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'worker'),
-- Cleaners (3)
('Faith Wambui', 'faith.wambui@handson.co.ke', '254778901234', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'worker'),
('Caroline Njeri', 'caroline.njeri@handson.co.ke', '254778901235', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'worker'),
('Elizabeth Atieno', 'elizabeth.atieno@handson.co.ke', '254778901236', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'worker'),
-- Mechanics (3)
('Samuel Kamau', 'samuel.kamau@handson.co.ke', '254789012345', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'worker'),
('David Kariuki', 'david.kariuki@handson.co.ke', '254789012346', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'worker'),
('Peter Githinji', 'peter.githinji@handson.co.ke', '254789012347', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'worker');

-- Insert worker profiles
INSERT INTO worker_profiles (user_id, category, experience, bio, hourly_rate, latitude, longitude, availability, is_verified) VALUES
-- Plumbers (users 2-4)
(2, 'plumber', '5_plus_years', 'Experienced plumber with over 5 years in residential and commercial plumbing. Expert in pipe installation, repairs, and maintenance.', 1500.00, -1.2234, 36.8656, 'available', TRUE),
(3, 'plumber', '3-5_years', 'Professional plumber offering quality plumbing services. Specializes in leak repairs and pipe fittings.', 1200.00, -1.2240, 36.8660, 'available', TRUE),
(4, 'plumber', '1-2_years', 'Skilled plumber providing reliable plumbing solutions for homes and businesses.', 1000.00, -1.2250, 36.8670, 'available', FALSE),
-- Electricians (users 5-7)
(5, 'electrician', '3-5_years', 'Certified electrician specializing in wiring, installations, and electrical repairs. Safe and reliable service.', 2000.00, -1.2245, 36.8670, 'available', TRUE),
(6, 'electrician', '5_plus_years', 'Expert electrician with extensive experience in commercial and residential electrical work.', 2500.00, -1.2255, 36.8680, 'available', TRUE),
(7, 'electrician', '1-2_years', 'Professional electrician offering affordable electrical services and installations.', 1500.00, -1.2265, 36.8690, 'available', FALSE),
-- Cleaners (users 8-10)
(8, 'cleaner', '1-2_years', 'Professional cleaner offering residential and commercial cleaning services.', 800.00, -1.2290, 36.8710, 'available', TRUE),
(9, 'cleaner', '3-5_years', 'Experienced cleaner providing thorough deep cleaning services.', 1000.00, -1.2300, 36.8720, 'available', TRUE),
(10, 'cleaner', '5_plus_years', 'Expert cleaner offering professional office and home cleaning solutions.', 1200.00, -1.2310, 36.8730, 'available', TRUE),
-- Mechanics (users 11-13)
(11, 'mechanic', '5_plus_years', 'Expert mechanic specializing in vehicle repairs, maintenance, and diagnostic services.', 2500.00, -1.2280, 36.8700, 'available', TRUE),
(12, 'mechanic', '3-5_years', 'Professional mechanic offering reliable car repair and servicing.', 2000.00, -1.2290, 36.8710, 'available', TRUE),
(13, 'mechanic', '1-2_years', 'Skilled mechanic providing affordable vehicle maintenance services.', 1500.00, -1.2300, 36.8720, 'available', FALSE);
