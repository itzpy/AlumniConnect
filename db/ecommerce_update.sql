-- E-Commerce Features Update for Alumni Connect Platform
-- Date: November 25, 2025
-- This script adds all required e-commerce functionality

USE alumni_connect;

-- Drop existing e-commerce tables if they exist (for clean reinstall)
DROP TABLE IF EXISTS `invoices`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `cart`;
DROP TABLE IF EXISTS `services`;

-- --------------------------------------------------------
-- Table structure for table `services`
-- Stores all purchasable items: job postings, mentorship sessions, event tickets, premium features
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `services` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(200) NOT NULL,
  `service_type` enum('job_posting','mentorship','event','premium_feature') NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duration` int(11) DEFAULT NULL COMMENT 'Duration in minutes for mentorship or days for job postings',
  `provider_id` int(11) DEFAULT NULL COMMENT 'User ID of the service provider (alumni)',
  `category` varchar(100) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `stock_quantity` int(11) DEFAULT NULL COMMENT 'For event tickets',
  `image_url` varchar(255) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`service_id`),
  KEY `idx_service_type` (`service_type`),
  KEY `idx_provider` (`provider_id`),
  KEY `idx_category` (`category`),
  FOREIGN KEY (`provider_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `cart`
-- Stores items added to user's cart before checkout
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cart` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `selected_date` date DEFAULT NULL COMMENT 'For scheduled services like mentorship',
  `selected_time` time DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_service` (`service_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_id`) REFERENCES `services`(`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `orders`
-- Stores completed orders after checkout
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL UNIQUE,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `final_amount` decimal(10,2) NOT NULL,
  `order_status` enum('pending','processing','completed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `billing_name` varchar(200) DEFAULT NULL,
  `billing_email` varchar(150) DEFAULT NULL,
  `billing_phone` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `idx_order_number` (`order_number`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`order_status`),
  KEY `idx_payment` (`payment_status`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `order_items`
-- Stores individual items within each order
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_name` varchar(200) NOT NULL COMMENT 'Stored for historical record',
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `selected_date` date DEFAULT NULL,
  `selected_time` time DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `fulfillment_status` enum('pending','scheduled','completed','cancelled') DEFAULT 'pending',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_item_id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_service` (`service_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`order_id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_id`) REFERENCES `services`(`service_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `payments`
-- Stores payment transaction details
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `payment_gateway` varchar(50) NOT NULL COMMENT 'paystack, flutterwave, momo',
  `transaction_reference` varchar(100) NOT NULL UNIQUE,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'GHS',
  `payment_status` enum('pending','success','failed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_channel` varchar(50) DEFAULT NULL COMMENT 'card, mobile_money, bank_transfer',
  `customer_email` varchar(150) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `gateway_response` text DEFAULT NULL COMMENT 'JSON response from payment gateway',
  `date_initiated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_completed` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `idx_transaction_ref` (`transaction_reference`),
  KEY `idx_order` (`order_id`),
  KEY `idx_status` (`payment_status`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`order_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `invoices`
-- Stores generated invoices for completed orders
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `invoices` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL UNIQUE,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `invoice_status` enum('draft','sent','paid','overdue','cancelled') DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_sent` timestamp NULL DEFAULT NULL,
  `date_paid` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`invoice_id`),
  UNIQUE KEY `idx_invoice_number` (`invoice_number`),
  KEY `idx_order` (`order_id`),
  KEY `idx_user` (`user_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`order_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Insert Sample Services Data for Testing
-- --------------------------------------------------------

-- First, create test users if they don't exist (for sample orders)
INSERT IGNORE INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `user_role`, `phone`, `is_active`) 
VALUES 
(2, 'John', 'Student', 'john.student@ashesi.edu.gh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '+233241234567', 1),
(3, 'Sarah', 'Alumni', 'sarah.alumni@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni', '+233201234567', 1);

-- Job Posting Services
INSERT INTO `services` (`service_name`, `service_type`, `description`, `price`, `duration`, `provider_id`, `category`, `location`, `stock_quantity`, `image_url`) VALUES
('30-Day Job Posting - Entry Level', 'job_posting', 'Post an entry-level job opportunity visible to all students for 30 days. Perfect for internships and graduate positions.', 150.00, 30, 1, 'Entry Level', 'Ghana', NULL, 'https://ui-avatars.com/api/?name=Job+Post&background=10b981&color=fff&size=400'),
('60-Day Job Posting - Mid Level', 'job_posting', 'Post a mid-level position for 60 days with featured placement on the jobs board.', 300.00, 60, 1, 'Mid Level', 'Ghana', NULL, 'https://ui-avatars.com/api/?name=Featured+Job&background=3b82f6&color=fff&size=400'),
('90-Day Premium Job Posting', 'job_posting', 'Premium job posting with top placement, company logo, and extended visibility for 90 days.', 500.00, 90, 1, 'Senior Level', 'Remote', NULL, 'https://ui-avatars.com/api/?name=Premium&background=8b5cf6&color=fff&size=400');

-- Mentorship Sessions
INSERT INTO `services` (`service_name`, `service_type`, `description`, `price`, `duration`, `provider_id`, `category`, `location`, `stock_quantity`, `image_url`) VALUES
('Career Guidance Session - 1 Hour', 'mentorship', 'One-on-one career guidance session with experienced alumni. Get personalized advice on career paths, job search strategies, and industry insights.', 75.00, 60, 1, 'Career Development', 'Virtual', NULL, 'https://ui-avatars.com/api/?name=Career+Mentor&background=f59e0b&color=fff&size=400'),
('Technical Skills Mentorship - 2 Hours', 'mentorship', 'Deep-dive technical mentorship covering programming, software development, or data science. Includes code review and project guidance.', 150.00, 120, 1, 'Technical Skills', 'Virtual', NULL, 'https://ui-avatars.com/api/?name=Tech+Mentor&background=06b6d4&color=fff&size=400'),
('Resume & Interview Prep Session', 'mentorship', 'Comprehensive resume review and mock interview session to help you land your dream job.', 100.00, 90, 1, 'Interview Prep', 'Virtual', NULL, 'https://ui-avatars.com/api/?name=Interview+Prep&background=ec4899&color=fff&size=400'),
('Entrepreneurship Coaching - 3 Hours', 'mentorship', 'Learn how to start and grow your business from successful alumni entrepreneurs. Includes business plan review and startup strategy.', 250.00, 180, 1, 'Entrepreneurship', 'Hybrid', NULL, 'https://ui-avatars.com/api/?name=Startup+Coach&background=7c3aed&color=fff&size=400');

-- Event Tickets
INSERT INTO `services` (`service_name`, `service_type`, `description`, `price`, `duration`, `provider_id`, `category`, `location`, `stock_quantity`, `image_url`) VALUES
('Annual Alumni Networking Gala 2025', 'event', 'Join 500+ alumni and students for the biggest networking event of the year. Includes dinner, keynote speeches, and career fair.', 120.00, NULL, 1, 'Networking', 'Accra, Ghana', 450, 'https://ui-avatars.com/api/?name=Gala+2025&background=dc2626&color=fff&size=400'),
('Tech Career Fair - December 2025', 'event', 'Meet with 50+ tech companies actively hiring. Free for students, alumni pay to sponsor booths and network with recruiters.', 50.00, NULL, 1, 'Career Fair', 'Kumasi, Ghana', 200, 'https://ui-avatars.com/api/?name=Career+Fair&background=059669&color=fff&size=400'),
('Alumni Football Tournament', 'event', 'Annual alumni vs students football match followed by BBQ and networking. All proceeds support student scholarships.', 30.00, NULL, 1, 'Sports & Social', 'Ashesi Campus', 100, 'https://ui-avatars.com/api/?name=Football&background=ea580c&color=fff&size=400'),
('Women in Tech Leadership Summit', 'event', 'Full-day conference featuring female tech leaders, panel discussions, workshops, and mentorship speed-dating.', 80.00, NULL, 1, 'Professional Development', 'Accra, Ghana', 150, 'https://ui-avatars.com/api/?name=Women+Tech&background=db2777&color=fff&size=400');

-- Premium Features
INSERT INTO `services` (`service_name`, `service_type`, `description`, `price`, `duration`, `provider_id`, `category`, `location`, `stock_quantity`, `image_url`) VALUES
('Premium Profile - 1 Month', 'premium_feature', 'Boost your profile visibility with featured placement, unlimited messages, and priority support for 30 days.', 25.00, 30, NULL, 'Profile Enhancement', NULL, NULL, 'https://ui-avatars.com/api/?name=Premium&background=fbbf24&color=000&size=400'),
('Premium Profile - 6 Months', 'premium_feature', 'Get 6 months of premium features at a discounted rate. Includes all premium benefits plus exclusive job alerts.', 120.00, 180, NULL, 'Profile Enhancement', NULL, NULL, 'https://ui-avatars.com/api/?name=6+Months&background=f97316&color=fff&size=400'),
('Premium Profile - 1 Year', 'premium_feature', 'Annual premium membership with maximum savings. Perfect for active alumni looking to give back and network extensively.', 200.00, 365, NULL, 'Profile Enhancement', NULL, NULL, 'https://ui-avatars.com/api/?name=Annual&background=7a1e1e&color=fff&size=400');

-- --------------------------------------------------------
-- Insert Sample Orders and Invoices for Testing
-- --------------------------------------------------------

-- Sample completed orders (these will show in order history)
INSERT INTO `orders` (`order_number`, `user_id`, `total_amount`, `discount_amount`, `tax_amount`, `final_amount`, `order_status`, `payment_status`, `payment_method`, `payment_reference`, `billing_name`, `billing_email`, `billing_phone`, `date_created`) VALUES
('ORD-20251101-0001', 1, 225.00, 0.00, 0.00, 225.00, 'completed', 'paid', 'paystack', 'PST-20251101-001', 'Admin User', 'admin@alumniconnect.com', '+233241234567', '2025-11-01 10:30:00'),
('ORD-20251115-0002', 1, 150.00, 0.00, 0.00, 150.00, 'completed', 'paid', 'momo', 'MMO-20251115-002', 'Admin User', 'admin@alumniconnect.com', '+233241234567', '2025-11-15 14:20:00'),
('ORD-20251120-0003', 1, 500.00, 50.00, 0.00, 450.00, 'processing', 'paid', 'paystack', 'PST-20251120-003', 'Admin User', 'admin@alumniconnect.com', '+233241234567', '2025-11-20 09:15:00');

-- Get the last 3 order IDs for order_items
SET @order1_id = (SELECT order_id FROM orders WHERE order_number = 'ORD-20251101-0001');
SET @order2_id = (SELECT order_id FROM orders WHERE order_number = 'ORD-20251115-0002');
SET @order3_id = (SELECT order_id FROM orders WHERE order_number = 'ORD-20251120-0003');

-- Sample order items
INSERT INTO `order_items` (`order_id`, `service_id`, `service_name`, `quantity`, `unit_price`, `total_price`, `selected_date`, `selected_time`, `fulfillment_status`) VALUES
(@order1_id, 4, 'Career Guidance Session - 1 Hour', 1, 75.00, 75.00, '2025-11-05', '14:00:00', 'completed'),
(@order1_id, 8, 'Annual Alumni Networking Gala 2025', 1, 120.00, 120.00, NULL, NULL, 'scheduled'),
(@order1_id, 12, 'Premium Profile - 1 Month', 1, 25.00, 25.00, NULL, NULL, 'completed'),
(@order2_id, 5, 'Technical Skills Mentorship - 2 Hours', 1, 150.00, 150.00, '2025-11-18', '10:00:00', 'scheduled'),
(@order3_id, 2, '60-Day Job Posting - Mid Level', 1, 300.00, 300.00, NULL, NULL, 'pending'),
(@order3_id, 9, 'Tech Career Fair - December 2025', 4, 50.00, 200.00, NULL, NULL, 'scheduled');

-- Sample payments
INSERT INTO `payments` (`order_id`, `payment_gateway`, `transaction_reference`, `amount`, `currency`, `payment_status`, `payment_channel`, `customer_email`, `customer_phone`, `date_initiated`, `date_completed`) VALUES
(@order1_id, 'paystack', 'PST-20251101-001', 225.00, 'GHS', 'success', 'card', 'admin@alumniconnect.com', '+233241234567', '2025-11-01 10:30:00', '2025-11-01 10:31:15'),
(@order2_id, 'momo', 'MMO-20251115-002', 150.00, 'GHS', 'success', 'mobile_money', 'admin@alumniconnect.com', '+233241234567', '2025-11-15 14:20:00', '2025-11-15 14:22:30'),
(@order3_id, 'paystack', 'PST-20251120-003', 450.00, 'GHS', 'success', 'card', 'admin@alumniconnect.com', '+233241234567', '2025-11-20 09:15:00', '2025-11-20 09:16:45');

-- Sample invoices
INSERT INTO `invoices` (`invoice_number`, `order_id`, `user_id`, `invoice_date`, `subtotal`, `tax_amount`, `discount_amount`, `total_amount`, `invoice_status`, `date_created`, `date_sent`, `date_paid`) VALUES
('INV-2025-0001', @order1_id, 1, '2025-11-01', 225.00, 0.00, 0.00, 225.00, 'paid', '2025-11-01 10:31:20', '2025-11-01 10:31:25', '2025-11-01 10:31:15'),
('INV-2025-0002', @order2_id, 1, '2025-11-15', 150.00, 0.00, 0.00, 150.00, 'paid', '2025-11-15 14:22:35', '2025-11-15 14:22:40', '2025-11-15 14:22:30'),
('INV-2025-0003', @order3_id, 1, '2025-11-20', 500.00, 0.00, 50.00, 450.00, 'paid', '2025-11-20 09:16:50', '2025-11-20 09:16:55', '2025-11-20 09:16:45');

COMMIT;
