-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2025 at 11:34 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `alumni_connect`
--

-- --------------------------------------------------------

--
-- Table structure for table `alumni_profiles`
--

CREATE TABLE `alumni_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `university` varchar(200) DEFAULT 'Ashesi University',
  `major` varchar(150) NOT NULL,
  `graduation_year` int(4) NOT NULL,
  `current_company` varchar(200) DEFAULT NULL,
  `job_title` varchar(150) DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `location_city` varchar(100) DEFAULT NULL,
  `location_country` varchar(100) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `available_for_mentorship` tinyint(1) DEFAULT 0,
  `expertise_areas` text DEFAULT NULL,
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alumni_profiles`
--

INSERT INTO `alumni_profiles` (`profile_id`, `user_id`, `university`, `major`, `graduation_year`, `current_company`, `job_title`, `industry`, `location_city`, `location_country`, `linkedin_url`, `website_url`, `available_for_mentorship`, `expertise_areas`, `date_updated`) VALUES
(1, 5, 'Ashesi University', 'Tempora voluptatibus sed dolore consequuntur fugit.', 1963, 'Stanton - Hauck', 'Error occaecati reprehenderit.', 'Technology', 'Namibia', '', '', NULL, 0, NULL, '2025-11-25 08:27:47'),
(2, 7, 'Ashesi University', 'Computer Science', 2018, 'Google', 'Software Engineer', 'Technology', 'San Francisco', 'USA', NULL, NULL, 1, NULL, '2025-11-30 16:32:48'),
(3, 8, 'Ashesi University', 'Business Administration', 2016, 'Microsoft', 'Product Manager', 'Technology', 'Seattle', 'USA', NULL, NULL, 1, NULL, '2025-11-30 16:32:48'),
(4, 9, 'Ashesi University', 'Marketing', 2019, 'Apple', 'Marketing Director', 'Technology', 'Cupertino', 'USA', NULL, NULL, 0, NULL, '2025-11-30 16:32:48'),
(5, 10, 'Ashesi University', 'Computer Science', 2020, 'MTN Ghana', 'Data Analyst', 'Telecommunications', 'Accra', 'Ghana', NULL, NULL, 1, NULL, '2025-11-30 16:32:48'),
(6, 11, 'Ashesi University', 'Business Administration', 2021, 'Ecobank', 'Financial Analyst', 'Finance', 'Accra', 'Ghana', NULL, NULL, 1, NULL, '2025-11-30 16:32:48'),
(7, 12, 'Ashesi University', 'Computer Science', 2017, 'Google', 'Software Engineer', 'Technology', 'Accra', 'Ghana', NULL, NULL, 1, NULL, '2025-11-30 17:19:33'),
(8, 13, 'Ashesi University', 'Business Administration', 2019, 'MTN Ghana', 'Marketing Manager', 'Telecommunications', 'Accra', 'Ghana', NULL, NULL, 1, NULL, '2025-11-30 17:19:33'),
(9, 14, 'Ashesi University', 'Computer Engineering', 2015, 'Microsoft', 'Senior Developer', 'Technology', 'Lagos', 'Nigeria', NULL, NULL, 0, NULL, '2025-11-30 17:19:33'),
(10, 15, 'Ashesi University', 'Management Information Systems', 2020, 'Stanbic Bank', 'Data Analyst', 'Finance', 'Accra', 'Ghana', NULL, NULL, 1, NULL, '2025-11-30 17:19:33'),
(11, 16, 'Ashesi University', 'Electrical Engineering', 2018, 'Vodafone', 'Network Engineer', 'Telecommunications', 'Kumasi', 'Ghana', NULL, NULL, 0, NULL, '2025-11-30 17:19:33'),
(12, 17, 'Ashesi University', 'Computer Science', 2021, 'Andela', 'Full Stack Developer', 'Technology', 'Nairobi', 'Kenya', NULL, NULL, 1, NULL, '2025-11-30 17:19:33'),
(13, 18, 'Ashesi University', 'Business Administration', 2016, 'Deloitte', 'Consultant', 'Consulting', 'Johannesburg', 'South Africa', NULL, NULL, 1, NULL, '2025-11-30 17:19:33'),
(14, 19, 'Ashesi University', 'Economics', 2022, 'World Bank', 'Research Analyst', 'Non-profit', 'Washington DC', 'USA', NULL, NULL, 0, NULL, '2025-11-30 17:19:33');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `selected_date` date DEFAULT NULL COMMENT 'For scheduled services like mentorship',
  `selected_time` time DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `connections`
--

CREATE TABLE `connections` (
  `connection_id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `request_message` text DEFAULT NULL,
  `date_requested` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_responded` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `connections`
--

INSERT INTO `connections` (`connection_id`, `requester_id`, `receiver_id`, `status`, `request_message`, `date_requested`, `date_responded`) VALUES
(1, 5, 11, 'pending', '', '2025-11-30 16:44:00', NULL),
(2, 5, 3, 'pending', '', '2025-11-30 16:51:29', NULL),
(3, 5, 8, 'pending', '', '2025-11-30 17:10:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `coupon_id` int(11) NOT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `max_discount_amount` decimal(10,2) DEFAULT NULL COMMENT 'Maximum discount for percentage coupons',
  `usage_limit` int(11) DEFAULT NULL COMMENT 'Maximum number of times coupon can be used',
  `usage_count` int(11) DEFAULT 0,
  `per_user_limit` int(11) DEFAULT 1 COMMENT 'Maximum uses per user',
  `valid_from` datetime DEFAULT current_timestamp(),
  `valid_until` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `applicable_services` text DEFAULT NULL COMMENT 'JSON array of service IDs, null means all',
  `applicable_types` varchar(255) DEFAULT NULL COMMENT 'Comma-separated service types',
  `created_by` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`coupon_id`, `coupon_code`, `description`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount_amount`, `usage_limit`, `usage_count`, `per_user_limit`, `valid_from`, `valid_until`, `is_active`, `applicable_services`, `applicable_types`, `created_by`, `date_created`, `date_updated`) VALUES
(1, 'WELCOME10', 'Welcome discount - 10% off first order', 'percentage', 10.00, 50.00, 100.00, NULL, 0, 1, '2025-11-29 02:35:00', '2026-05-29 02:35:00', 1, NULL, NULL, NULL, '2025-11-29 02:35:00', NULL),
(2, 'ALUMNI20', 'Alumni special - 20% off', 'percentage', 20.00, 100.00, 200.00, 100, 0, 1, '2025-11-29 02:35:00', '2026-02-28 02:35:00', 1, NULL, NULL, NULL, '2025-11-29 02:35:00', NULL),
(3, 'FLAT50', 'Flat GHS 50 off orders above GHS 200', 'fixed', 50.00, 200.00, NULL, 50, 0, 1, '2025-11-29 02:35:00', '2025-12-29 02:35:00', 1, NULL, NULL, NULL, '2025-11-29 02:35:00', NULL),
(4, 'MENTOR25', 'Mentorship discount - 25% off', 'percentage', 25.00, 0.00, 150.00, NULL, 0, 1, '2025-11-29 02:35:00', '2026-05-29 02:35:00', 1, NULL, NULL, NULL, '2025-11-29 02:35:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usage`
--

CREATE TABLE `coupon_usage` (
  `usage_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `discount_applied` decimal(10,2) NOT NULL,
  `date_used` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_description` text NOT NULL,
  `event_location` varchar(255) DEFAULT NULL,
  `event_type` enum('networking','workshop','webinar','social','career_fair') DEFAULT 'networking',
  `event_date` datetime NOT NULL,
  `registration_link` varchar(255) DEFAULT NULL,
  `max_attendees` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_registrations`
--

CREATE TABLE `event_registrations` (
  `registration_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `attendance_status` enum('registered','attended','cancelled') DEFAULT 'registered'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
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
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_sent` timestamp NULL DEFAULT NULL,
  `date_paid` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`invoice_id`, `invoice_number`, `order_id`, `user_id`, `invoice_date`, `due_date`, `subtotal`, `tax_amount`, `discount_amount`, `total_amount`, `invoice_status`, `notes`, `date_created`, `date_sent`, `date_paid`) VALUES
(1, 'INV-2025-0001', 1, 1, '2025-11-01', NULL, 225.00, 0.00, 0.00, 225.00, 'paid', NULL, '2025-11-01 10:31:20', '2025-11-01 10:31:25', '2025-11-01 10:31:15'),
(2, 'INV-2025-0002', 2, 1, '2025-11-15', NULL, 150.00, 0.00, 0.00, 150.00, 'paid', NULL, '2025-11-15 14:22:35', '2025-11-15 14:22:40', '2025-11-15 14:22:30'),
(3, 'INV-2025-0003', 3, 1, '2025-11-20', NULL, 500.00, 0.00, 50.00, 450.00, 'paid', NULL, '2025-11-20 09:16:50', '2025-11-20 09:16:55', '2025-11-20 09:16:45');

-- --------------------------------------------------------

--
-- Table structure for table `job_opportunities`
--

CREATE TABLE `job_opportunities` (
  `job_id` int(11) NOT NULL,
  `posted_by` int(11) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `job_title` varchar(200) NOT NULL,
  `job_description` text NOT NULL,
  `job_location` varchar(200) DEFAULT NULL,
  `job_type` enum('Full-time','Part-time','Internship','Contract') DEFAULT 'Full-time',
  `salary_range` varchar(100) DEFAULT NULL,
  `application_url` varchar(255) DEFAULT NULL,
  `application_deadline` date DEFAULT NULL,
  `date_posted` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mentorships`
--

CREATE TABLE `mentorships` (
  `mentorship_id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `mentee_id` int(11) NOT NULL,
  `status` enum('requested','active','completed','cancelled') DEFAULT 'requested',
  `focus_area` varchar(200) DEFAULT NULL,
  `request_message` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `date_requested` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `date_sent` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_read` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_type` enum('connection','message','post','event','job','mentorship') NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `coupon_id` int(11) DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
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
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_number`, `user_id`, `total_amount`, `discount_amount`, `coupon_id`, `coupon_code`, `tax_amount`, `final_amount`, `order_status`, `payment_status`, `payment_method`, `payment_reference`, `billing_name`, `billing_email`, `billing_phone`, `notes`, `date_created`, `date_updated`) VALUES
(1, 'ORD-20251101-0001', 1, 225.00, 0.00, NULL, NULL, 0.00, 225.00, 'completed', 'paid', 'paystack', 'PST-20251101-001', 'Admin User', 'admin@alumniconnect.com', '+233241234567', NULL, '2025-11-01 10:30:00', NULL),
(2, 'ORD-20251115-0002', 1, 150.00, 0.00, NULL, NULL, 0.00, 150.00, 'completed', 'paid', 'momo', 'MMO-20251115-002', 'Admin User', 'admin@alumniconnect.com', '+233241234567', NULL, '2025-11-15 14:20:00', NULL),
(3, 'ORD-20251120-0003', 1, 500.00, 50.00, NULL, NULL, 0.00, 450.00, 'processing', 'paid', 'paystack', 'PST-20251120-003', 'Admin User', 'admin@alumniconnect.com', '+233241234567', NULL, '2025-11-20 09:15:00', NULL),
(4, 'ORD-20251125-6078', 5, 450.00, 0.00, NULL, NULL, 0.00, 450.00, 'pending', 'pending', NULL, NULL, 'Otis Batz', 'your.email+fakedata52367@gmail.com', '166-675-5654', '565', '2025-11-25 13:44:30', NULL),
(5, 'ORD-20251125-0753', 5, 450.00, 0.00, NULL, NULL, 0.00, 450.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '+233 558444384', '', '2025-11-25 15:25:45', NULL),
(6, 'ORD-20251125-6005', 5, 450.00, 0.00, NULL, NULL, 0.00, 450.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '+233 558444384', '', '2025-11-25 15:29:43', NULL),
(7, 'ORD-20251125-2138', 5, 450.00, 0.00, NULL, NULL, 0.00, 450.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '+233 558444384', '', '2025-11-25 15:29:47', NULL),
(8, 'ORD-20251125-6585', 5, 450.00, 0.00, NULL, NULL, 0.00, 450.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '348-504-2641', '', '2025-11-25 15:30:03', NULL),
(9, 'ORD-20251125-6911', 5, 450.00, 0.00, NULL, NULL, 0.00, 450.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '348-504-2641', '', '2025-11-25 15:30:07', NULL),
(10, 'ORD-20251125-6763', 5, 450.00, 0.00, NULL, NULL, 0.00, 450.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '+1 (976) 364-5441', '', '2025-11-25 15:30:20', NULL),
(11, 'ORD-20251125-2273', 5, 300.00, 0.00, NULL, NULL, 0.00, 300.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '+1 (119) 495-3348', '', '2025-11-25 15:33:12', NULL),
(12, 'ORD-20251125-2247', 5, 300.00, 0.00, NULL, NULL, 0.00, 300.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '+1 (119) 495-3348', '', '2025-11-25 15:36:24', NULL),
(13, 'ORD-20251125-8391', 5, 300.00, 0.00, NULL, NULL, 0.00, 300.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '+1 (119) 495-3348', '', '2025-11-25 15:36:34', NULL),
(14, 'ORD-20251125-6140', 5, 300.00, 0.00, NULL, NULL, 0.00, 300.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '+1 (119) 495-3348', '', '2025-11-25 15:36:47', NULL),
(15, 'ORD-20251125-6080', 5, 300.00, 0.00, NULL, NULL, 0.00, 300.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '+1 (119) 495-3348', '', '2025-11-25 15:38:45', NULL),
(16, 'ORD-20251125-5560', 5, 300.00, 0.00, NULL, NULL, 0.00, 300.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '+1 (119) 495-3348', '', '2025-11-25 15:38:48', NULL),
(18, 'ORD-20251126-8296', 5, 150.00, 0.00, NULL, NULL, 0.00, 150.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-26 10:06:17', NULL),
(19, 'ORD-20251126-5895', 5, 200.00, 0.00, NULL, NULL, 0.00, 200.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-26 10:08:06', NULL),
(25, 'ORD-20251126-5246', 5, 60.00, 0.00, NULL, NULL, 0.00, 60.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-26 12:22:38', NULL),
(26, 'ORD-20251126-8163', 5, 25.00, 0.00, NULL, NULL, 0.00, 25.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-26 12:25:01', NULL),
(27, 'ORD-20251126-1147', 5, 50.00, 0.00, NULL, NULL, 0.00, 50.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-26 12:26:36', NULL),
(28, 'ORD-20251126-0488', 5, 105.00, 0.00, NULL, NULL, 0.00, 105.00, 'pending', 'pending', NULL, NULL, 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-26 12:28:47', NULL),
(29, 'ORD-20251126-6550', 5, 150.00, 0.00, NULL, NULL, 0.00, 150.00, 'pending', 'paid', 'paystack', 'ALUMNI-5-1764160317', 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-26 12:32:05', '2025-11-26 12:32:05'),
(30, 'ORD-20251126-0678', 5, 150.00, 0.00, NULL, NULL, 0.00, 150.00, 'pending', 'paid', 'paystack', 'ALUMNI-5-1764160317', 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-26 12:32:14', '2025-11-26 12:32:14'),
(31, 'ORD-20251126-4906', 5, 150.00, 0.00, NULL, NULL, 0.00, 150.00, 'pending', 'paid', 'paystack', 'ALUMNI-5-1764160598', 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-26 12:36:48', '2025-11-26 12:36:48'),
(32, 'ORD-20251126-5139', 5, 150.00, 0.00, NULL, NULL, 0.00, 150.00, 'pending', 'paid', 'paystack', 'ALUMNI-5-1764160598', 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-26 12:37:42', '2025-11-26 12:37:42'),
(33, 'ORD-20251126-9630', 5, 150.00, 0.00, NULL, NULL, 0.00, 150.00, 'pending', 'paid', 'paystack', 'ALUMNI-5-1764160671', 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-26 12:38:04', '2025-11-26 12:38:04'),
(34, 'ORD-20251126-1994', 5, 300.00, 0.00, NULL, NULL, 0.00, 300.00, 'pending', 'paid', 'paystack', 'ALUMNI-5-1764160756', 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-26 12:39:25', '2025-11-26 12:39:25'),
(35, 'ORD-20251128-2009', 5, 100.00, 0.00, NULL, NULL, 0.00, 100.00, 'completed', 'paid', 'paystack', 'ALUMNI-5-1764288916', 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-28 00:15:32', '2025-11-28 15:23:56'),
(36, 'ORD-20251128-4746', 4, 50.00, 0.00, NULL, NULL, 0.00, 50.00, 'pending', 'paid', 'paystack', 'ALUMNI-4-1764345968', 'Wava DuBuque', 'your.email+fakedata94775@gmail.com', '', 'Payment via Paystack', '2025-11-28 16:06:18', '2025-11-28 16:06:18'),
(37, 'ORD-20251128-6902', 5, 120.00, 0.00, NULL, NULL, 0.00, 120.00, 'pending', 'paid', 'paystack', 'ALUMNI-5-1764350185', 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-28 17:16:34', '2025-11-28 17:16:34'),
(38, 'ORD-20251128-9106', 4, 50.00, 0.00, NULL, NULL, 0.00, 50.00, 'pending', 'paid', 'paystack', 'ALUMNI-4-1764353012', 'Wava DuBuque', 'your.email+fakedata94775@gmail.com', '', 'Payment via Paystack', '2025-11-28 18:03:42', '2025-11-28 18:03:42'),
(39, 'ORD-20251128-5677', 4, 30.00, 0.00, NULL, NULL, 0.00, 30.00, 'completed', 'paid', 'paystack', 'ALUMNI-4-1764353856', 'Wava DuBuque', 'your.email+fakedata94775@gmail.com', '', 'Payment via Paystack', '2025-11-28 18:17:46', '2025-11-28 22:12:19'),
(40, 'ORD-20251130-0352', 5, 300.00, 0.00, NULL, NULL, 0.00, 300.00, 'pending', 'paid', 'paystack', 'ALUMNI-5-1764521564', 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-30 16:52:55', '2025-11-30 16:52:55'),
(41, 'ORD-20251130-0540', 5, 50.00, 0.00, NULL, NULL, 0.00, 50.00, 'pending', 'paid', 'paystack', 'ALUMNI-5-1764529856', 'Juanita Braun', 'your.email+fakedata49627@gmail.com', '', 'Payment via Paystack', '2025-11-30 19:11:11', '2025-11-30 19:11:11');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
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
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `service_id`, `service_name`, `quantity`, `unit_price`, `total_price`, `selected_date`, `selected_time`, `special_requests`, `fulfillment_status`, `date_created`) VALUES
(1, 1, 4, 'Career Guidance Session - 1 Hour', 1, 75.00, 75.00, '2025-11-05', '14:00:00', NULL, 'completed', '2025-11-25 08:59:18'),
(2, 1, 8, 'Annual Alumni Networking Gala 2025', 1, 120.00, 120.00, NULL, NULL, NULL, 'scheduled', '2025-11-25 08:59:18'),
(3, 1, 12, 'Premium Profile - 1 Month', 1, 25.00, 25.00, NULL, NULL, NULL, 'completed', '2025-11-25 08:59:18'),
(4, 2, 5, 'Technical Skills Mentorship - 2 Hours', 1, 150.00, 150.00, '2025-11-18', '10:00:00', NULL, 'scheduled', '2025-11-25 08:59:18'),
(5, 3, 2, '60-Day Job Posting - Mid Level', 1, 300.00, 300.00, NULL, NULL, NULL, 'pending', '2025-11-25 08:59:18'),
(6, 3, 9, 'Tech Career Fair - December 2025', 4, 50.00, 200.00, NULL, NULL, NULL, 'scheduled', '2025-11-25 08:59:18'),
(15, 18, 1, '30-Day Job Posting - Entry Level', 1, 150.00, 150.00, NULL, NULL, NULL, 'pending', '2025-11-26 10:06:17'),
(16, 19, 14, 'Premium Profile - 1 Year', 1, 200.00, 200.00, NULL, NULL, NULL, 'pending', '2025-11-26 10:08:06'),
(22, 25, 10, 'Alumni Football Tournament', 2, 30.00, 60.00, NULL, NULL, NULL, 'pending', '2025-11-26 12:22:38'),
(23, 26, 12, 'Premium Profile - 1 Month', 1, 25.00, 25.00, NULL, NULL, NULL, 'pending', '2025-11-26 12:25:01'),
(24, 27, 9, 'Tech Career Fair - December 2025', 1, 50.00, 50.00, NULL, NULL, NULL, 'pending', '2025-11-26 12:26:36'),
(25, 28, 12, 'Premium Profile - 1 Month', 1, 25.00, 25.00, NULL, NULL, NULL, 'pending', '2025-11-26 12:28:47'),
(26, 28, 11, 'Women in Tech Leadership Summit', 1, 80.00, 80.00, NULL, NULL, NULL, 'pending', '2025-11-26 12:28:47'),
(27, 29, 1, '30-Day Job Posting - Entry Level', 1, 150.00, 150.00, NULL, NULL, NULL, 'pending', '2025-11-26 12:32:05'),
(28, 30, 1, '30-Day Job Posting - Entry Level', 1, 150.00, 150.00, NULL, NULL, NULL, 'pending', '2025-11-26 12:32:14'),
(29, 31, 1, '30-Day Job Posting - Entry Level', 1, 150.00, 150.00, NULL, NULL, NULL, 'pending', '2025-11-26 12:36:48'),
(30, 32, 1, '30-Day Job Posting - Entry Level', 1, 150.00, 150.00, NULL, NULL, NULL, 'pending', '2025-11-26 12:37:42'),
(31, 33, 1, '30-Day Job Posting - Entry Level', 1, 150.00, 150.00, NULL, NULL, NULL, 'pending', '2025-11-26 12:38:04'),
(32, 34, 2, '60-Day Job Posting - Mid Level', 1, 300.00, 300.00, NULL, NULL, NULL, 'pending', '2025-11-26 12:39:25'),
(33, 35, 9, 'Tech Career Fair - December 2025', 2, 50.00, 100.00, NULL, NULL, NULL, 'pending', '2025-11-28 00:15:32'),
(34, 36, 9, 'Tech Career Fair - December 2025', 1, 50.00, 50.00, NULL, NULL, NULL, 'pending', '2025-11-28 16:06:18'),
(35, 37, 8, 'Annual Alumni Networking Gala 2025', 1, 120.00, 120.00, NULL, NULL, NULL, 'pending', '2025-11-28 17:16:34'),
(36, 38, 9, 'Tech Career Fair - December 2025', 1, 50.00, 50.00, NULL, NULL, NULL, 'pending', '2025-11-28 18:03:42'),
(37, 39, 10, 'Alumni Football Tournament', 1, 30.00, 30.00, NULL, NULL, NULL, 'pending', '2025-11-28 18:17:46'),
(38, 40, 2, '60-Day Job Posting - Mid Level', 1, 300.00, 300.00, NULL, NULL, 'Job: Dolor anim cillum re at Gordon Terry Inc, Architecto officiis ', 'pending', '2025-11-30 16:52:55'),
(39, 41, 9, 'Tech Career Fair - December 2025', 1, 50.00, 50.00, NULL, NULL, NULL, 'pending', '2025-11-30 19:11:11');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_gateway` varchar(50) NOT NULL COMMENT 'paystack, flutterwave, momo',
  `transaction_reference` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'GHS',
  `payment_status` enum('pending','success','failed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_channel` varchar(50) DEFAULT NULL COMMENT 'card, mobile_money, bank_transfer',
  `customer_email` varchar(150) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `gateway_response` text DEFAULT NULL COMMENT 'JSON response from payment gateway',
  `date_initiated` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_completed` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_gateway`, `transaction_reference`, `amount`, `currency`, `payment_status`, `payment_channel`, `customer_email`, `customer_phone`, `gateway_response`, `date_initiated`, `date_completed`) VALUES
(1, 1, 'paystack', 'PST-20251101-001', 225.00, 'GHS', 'success', 'card', 'admin@alumniconnect.com', '+233241234567', NULL, '2025-11-01 10:30:00', '2025-11-01 10:31:15'),
(2, 2, 'momo', 'MMO-20251115-002', 150.00, 'GHS', 'success', 'mobile_money', 'admin@alumniconnect.com', '+233241234567', NULL, '2025-11-15 14:20:00', '2025-11-15 14:22:30'),
(3, 3, 'paystack', 'PST-20251120-003', 450.00, 'GHS', 'success', 'card', 'admin@alumniconnect.com', '+233241234567', NULL, '2025-11-20 09:15:00', '2025-11-20 09:16:45'),
(10, 29, 'paystack', 'ALUMNI-5-1764160317', 150.00, 'GHS', '', 'card', 'your.email+fakedata49627@gmail.com', NULL, '{\"id\":5573735178,\"domain\":\"test\",\"status\":\"success\",\"reference\":\"ALUMNI-5-1764160317\",\"receipt_number\":null,\"amount\":15000,\"message\":null,\"gateway_response\":\"Successful\",\"paid_at\":\"2025-11-26T12:32:03.000Z\",\"created_at\":\"2025-11-26T12:31:59.000Z\",\"channel\":\"card\",\"currency\":\"GHS\",\"ip_address\":\"41.79.97.5\",\"metadata\":{\"currency\":\"GHS\",\"app\":\"Alumni Connect\",\"environment\":\"test\",\"referrer\":\"http:\\/\\/localhost\\/\"},\"log\":{\"start_time\":1764160320,\"time_spent\":3,\"attempts\":1,\"errors\":0,\"success\":true,\"mobile\":false,\"input\":[],\"history\":[{\"type\":\"action\",\"message\":\"Attempted to pay with card\",\"time\":3},{\"type\":\"success\",\"message\":\"Successfully paid with card\",\"time\":3}]},\"fees\":293,\"fees_split\":null,\"authorization\":{\"authorization_code\":\"AUTH_mpjp58x7ir\",\"bin\":\"408408\",\"last4\":\"4081\",\"exp_month\":\"12\",\"exp_year\":\"2030\",\"channel\":\"card\",\"card_type\":\"visa \",\"bank\":\"TEST BANK\",\"country_code\":\"GH\",\"brand\":\"visa\",\"reusable\":true,\"signature\":\"SIG_Ggrc4HZwbcmYsGnbK4kh\",\"account_name\":null,\"receiver_bank_account_number\":null,\"receiver_bank\":null},\"customer\":{\"id\":321675136,\"first_name\":null,\"last_name\":null,\"email\":\"your.email+fakedata49627@gmail.com\",\"customer_code\":\"CUS_8po9040yx2onasw\",\"phone\":null,\"metadata\":null,\"risk_action\":\"default\",\"international_format_phone\":null},\"plan\":null,\"split\":[],\"order_id\":null,\"paidAt\":\"2025-11-26T12:32:03.000Z\",\"createdAt\":\"2025-11-26T12:31:59.000Z\",\"requested_amount\":15000,\"pos_transaction_data\":null,\"source\":null,\"fees_breakdown\":null,\"connect\":null,\"transaction_date\":\"2025-11-26T12:31:59.000Z\",\"plan_object\":[],\"subaccount\":[]}', '2025-11-26 12:32:05', '2025-11-26 12:32:05'),
(12, 31, 'paystack', 'ALUMNI-5-1764160598', 150.00, 'GHS', 'success', 'card', 'your.email+fakedata49627@gmail.com', NULL, '{\"id\":5573752432,\"domain\":\"test\",\"status\":\"success\",\"reference\":\"ALUMNI-5-1764160598\",\"receipt_number\":null,\"amount\":15000,\"message\":null,\"gateway_response\":\"Successful\",\"paid_at\":\"2025-11-26T12:36:46.000Z\",\"created_at\":\"2025-11-26T12:36:39.000Z\",\"channel\":\"card\",\"currency\":\"GHS\",\"ip_address\":\"41.79.97.5\",\"metadata\":{\"currency\":\"GHS\",\"app\":\"Alumni Connect\",\"environment\":\"test\",\"referrer\":\"http:\\/\\/localhost\\/\"},\"log\":{\"start_time\":1764160603,\"time_spent\":3,\"attempts\":1,\"errors\":0,\"success\":true,\"mobile\":false,\"input\":[],\"history\":[{\"type\":\"action\",\"message\":\"Attempted to pay with card\",\"time\":3},{\"type\":\"success\",\"message\":\"Successfully paid with card\",\"time\":3}]},\"fees\":293,\"fees_split\":null,\"authorization\":{\"authorization_code\":\"AUTH_t7ba3m7bmd\",\"bin\":\"408408\",\"last4\":\"4081\",\"exp_month\":\"12\",\"exp_year\":\"2030\",\"channel\":\"card\",\"card_type\":\"visa \",\"bank\":\"TEST BANK\",\"country_code\":\"GH\",\"brand\":\"visa\",\"reusable\":true,\"signature\":\"SIG_Ggrc4HZwbcmYsGnbK4kh\",\"account_name\":null,\"receiver_bank_account_number\":null,\"receiver_bank\":null},\"customer\":{\"id\":321675136,\"first_name\":null,\"last_name\":null,\"email\":\"your.email+fakedata49627@gmail.com\",\"customer_code\":\"CUS_8po9040yx2onasw\",\"phone\":null,\"metadata\":null,\"risk_action\":\"default\",\"international_format_phone\":null},\"plan\":null,\"split\":[],\"order_id\":null,\"paidAt\":\"2025-11-26T12:36:46.000Z\",\"createdAt\":\"2025-11-26T12:36:39.000Z\",\"requested_amount\":15000,\"pos_transaction_data\":null,\"source\":null,\"fees_breakdown\":null,\"connect\":null,\"transaction_date\":\"2025-11-26T12:36:39.000Z\",\"plan_object\":[],\"subaccount\":[]}', '2025-11-26 12:36:48', '2025-11-26 12:36:48'),
(14, 33, 'paystack', 'ALUMNI-5-1764160671', 150.00, 'GHS', 'success', 'card', 'your.email+fakedata49627@gmail.com', NULL, '{\"id\":5573756756,\"domain\":\"test\",\"status\":\"success\",\"reference\":\"ALUMNI-5-1764160671\",\"receipt_number\":null,\"amount\":15000,\"message\":null,\"gateway_response\":\"Successful\",\"paid_at\":\"2025-11-26T12:38:02.000Z\",\"created_at\":\"2025-11-26T12:37:52.000Z\",\"channel\":\"card\",\"currency\":\"GHS\",\"ip_address\":\"41.79.97.5\",\"metadata\":{\"currency\":\"GHS\",\"app\":\"Alumni Connect\",\"environment\":\"test\",\"referrer\":\"http:\\/\\/localhost\\/\"},\"log\":{\"start_time\":1764160679,\"time_spent\":3,\"attempts\":1,\"errors\":0,\"success\":true,\"mobile\":false,\"input\":[],\"history\":[{\"type\":\"action\",\"message\":\"Attempted to pay with card\",\"time\":2},{\"type\":\"success\",\"message\":\"Successfully paid with card\",\"time\":3}]},\"fees\":293,\"fees_split\":null,\"authorization\":{\"authorization_code\":\"AUTH_ct6qrkwcz9\",\"bin\":\"408408\",\"last4\":\"4081\",\"exp_month\":\"12\",\"exp_year\":\"2030\",\"channel\":\"card\",\"card_type\":\"visa \",\"bank\":\"TEST BANK\",\"country_code\":\"GH\",\"brand\":\"visa\",\"reusable\":true,\"signature\":\"SIG_Ggrc4HZwbcmYsGnbK4kh\",\"account_name\":null,\"receiver_bank_account_number\":null,\"receiver_bank\":null},\"customer\":{\"id\":321675136,\"first_name\":null,\"last_name\":null,\"email\":\"your.email+fakedata49627@gmail.com\",\"customer_code\":\"CUS_8po9040yx2onasw\",\"phone\":null,\"metadata\":null,\"risk_action\":\"default\",\"international_format_phone\":null},\"plan\":null,\"split\":[],\"order_id\":null,\"paidAt\":\"2025-11-26T12:38:02.000Z\",\"createdAt\":\"2025-11-26T12:37:52.000Z\",\"requested_amount\":15000,\"pos_transaction_data\":null,\"source\":null,\"fees_breakdown\":null,\"connect\":null,\"transaction_date\":\"2025-11-26T12:37:52.000Z\",\"plan_object\":[],\"subaccount\":[]}', '2025-11-26 12:38:04', '2025-11-26 12:38:04'),
(15, 34, 'paystack', 'ALUMNI-5-1764160756', 300.00, 'GHS', 'success', 'card', 'your.email+fakedata49627@gmail.com', NULL, '{\"id\":5573762664,\"domain\":\"test\",\"status\":\"success\",\"reference\":\"ALUMNI-5-1764160756\",\"receipt_number\":null,\"amount\":30000,\"message\":null,\"gateway_response\":\"Successful\",\"paid_at\":\"2025-11-26T12:39:23.000Z\",\"created_at\":\"2025-11-26T12:39:18.000Z\",\"channel\":\"card\",\"currency\":\"GHS\",\"ip_address\":\"41.79.97.5\",\"metadata\":{\"currency\":\"GHS\",\"app\":\"Alumni Connect\",\"environment\":\"test\",\"referrer\":\"http:\\/\\/localhost\\/\"},\"log\":{\"start_time\":1764160760,\"time_spent\":3,\"attempts\":1,\"errors\":0,\"success\":true,\"mobile\":false,\"input\":[],\"history\":[{\"type\":\"action\",\"message\":\"Attempted to pay with card\",\"time\":3},{\"type\":\"success\",\"message\":\"Successfully paid with card\",\"time\":3}]},\"fees\":585,\"fees_split\":null,\"authorization\":{\"authorization_code\":\"AUTH_xkvrlb8lnd\",\"bin\":\"408408\",\"last4\":\"4081\",\"exp_month\":\"12\",\"exp_year\":\"2030\",\"channel\":\"card\",\"card_type\":\"visa \",\"bank\":\"TEST BANK\",\"country_code\":\"GH\",\"brand\":\"visa\",\"reusable\":true,\"signature\":\"SIG_Ggrc4HZwbcmYsGnbK4kh\",\"account_name\":null,\"receiver_bank_account_number\":null,\"receiver_bank\":null},\"customer\":{\"id\":321675136,\"first_name\":null,\"last_name\":null,\"email\":\"your.email+fakedata49627@gmail.com\",\"customer_code\":\"CUS_8po9040yx2onasw\",\"phone\":null,\"metadata\":null,\"risk_action\":\"default\",\"international_format_phone\":null},\"plan\":null,\"split\":[],\"order_id\":null,\"paidAt\":\"2025-11-26T12:39:23.000Z\",\"createdAt\":\"2025-11-26T12:39:18.000Z\",\"requested_amount\":30000,\"pos_transaction_data\":null,\"source\":null,\"fees_breakdown\":null,\"connect\":null,\"transaction_date\":\"2025-11-26T12:39:18.000Z\",\"plan_object\":[],\"subaccount\":[]}', '2025-11-26 12:39:25', '2025-11-26 12:39:25'),
(16, 35, 'paystack', 'ALUMNI-5-1764288916', 100.00, 'GHS', 'success', 'card', 'your.email+fakedata49627@gmail.com', NULL, '{\"id\":5578939864,\"domain\":\"test\",\"status\":\"success\",\"reference\":\"ALUMNI-5-1764288916\",\"receipt_number\":null,\"amount\":10000,\"message\":null,\"gateway_response\":\"Successful\",\"paid_at\":\"2025-11-28T00:15:32.000Z\",\"created_at\":\"2025-11-28T00:15:20.000Z\",\"channel\":\"card\",\"currency\":\"GHS\",\"ip_address\":\"154.65.20.56\",\"metadata\":{\"currency\":\"GHS\",\"app\":\"Alumni Connect\",\"environment\":\"test\",\"referrer\":\"http:\\/\\/localhost\\/\"},\"log\":{\"start_time\":1764288926,\"time_spent\":4,\"attempts\":1,\"errors\":0,\"success\":true,\"mobile\":false,\"input\":[],\"history\":[{\"type\":\"action\",\"message\":\"Attempted to pay with card\",\"time\":3},{\"type\":\"success\",\"message\":\"Successfully paid with card\",\"time\":4}]},\"fees\":195,\"fees_split\":null,\"authorization\":{\"authorization_code\":\"AUTH_yww7pljc4h\",\"bin\":\"408408\",\"last4\":\"4081\",\"exp_month\":\"12\",\"exp_year\":\"2030\",\"channel\":\"card\",\"card_type\":\"visa \",\"bank\":\"TEST BANK\",\"country_code\":\"GH\",\"brand\":\"visa\",\"reusable\":true,\"signature\":\"SIG_Ggrc4HZwbcmYsGnbK4kh\",\"account_name\":null,\"receiver_bank_account_number\":null,\"receiver_bank\":null},\"customer\":{\"id\":321675136,\"first_name\":null,\"last_name\":null,\"email\":\"your.email+fakedata49627@gmail.com\",\"customer_code\":\"CUS_8po9040yx2onasw\",\"phone\":null,\"metadata\":null,\"risk_action\":\"default\",\"international_format_phone\":null},\"plan\":null,\"split\":[],\"order_id\":null,\"paidAt\":\"2025-11-28T00:15:32.000Z\",\"createdAt\":\"2025-11-28T00:15:20.000Z\",\"requested_amount\":10000,\"pos_transaction_data\":null,\"source\":null,\"fees_breakdown\":null,\"connect\":null,\"transaction_date\":\"2025-11-28T00:15:20.000Z\",\"plan_object\":[],\"subaccount\":[]}', '2025-11-28 00:15:32', '2025-11-28 00:15:32'),
(17, 36, 'paystack', 'ALUMNI-4-1764345968', 50.00, 'GHS', 'success', 'card', 'your.email+fakedata94775@gmail.com', NULL, '{\"id\":5581402362,\"domain\":\"test\",\"status\":\"success\",\"reference\":\"ALUMNI-4-1764345968\",\"receipt_number\":null,\"amount\":5000,\"message\":null,\"gateway_response\":\"Successful\",\"paid_at\":\"2025-11-28T16:06:16.000Z\",\"created_at\":\"2025-11-28T16:06:10.000Z\",\"channel\":\"card\",\"currency\":\"GHS\",\"ip_address\":\"41.79.97.5\",\"metadata\":{\"currency\":\"GHS\",\"app\":\"Alumni Connect\",\"environment\":\"test\",\"referrer\":\"http:\\/\\/localhost\\/\"},\"log\":{\"start_time\":1764345972,\"time_spent\":4,\"attempts\":1,\"errors\":0,\"success\":true,\"mobile\":false,\"input\":[],\"history\":[{\"type\":\"action\",\"message\":\"Attempted to pay with card\",\"time\":4},{\"type\":\"success\",\"message\":\"Successfully paid with card\",\"time\":4}]},\"fees\":98,\"fees_split\":null,\"authorization\":{\"authorization_code\":\"AUTH_unpbaj1pwt\",\"bin\":\"408408\",\"last4\":\"4081\",\"exp_month\":\"12\",\"exp_year\":\"2030\",\"channel\":\"card\",\"card_type\":\"visa \",\"bank\":\"TEST BANK\",\"country_code\":\"GH\",\"brand\":\"visa\",\"reusable\":true,\"signature\":\"SIG_Ggrc4HZwbcmYsGnbK4kh\",\"account_name\":null,\"receiver_bank_account_number\":null,\"receiver_bank\":null},\"customer\":{\"id\":322260440,\"first_name\":null,\"last_name\":null,\"email\":\"your.email+fakedata94775@gmail.com\",\"customer_code\":\"CUS_h88bzo1ysrutkx7\",\"phone\":null,\"metadata\":null,\"risk_action\":\"default\",\"international_format_phone\":null},\"plan\":null,\"split\":[],\"order_id\":null,\"paidAt\":\"2025-11-28T16:06:16.000Z\",\"createdAt\":\"2025-11-28T16:06:10.000Z\",\"requested_amount\":5000,\"pos_transaction_data\":null,\"source\":null,\"fees_breakdown\":null,\"connect\":null,\"transaction_date\":\"2025-11-28T16:06:10.000Z\",\"plan_object\":[],\"subaccount\":[]}', '2025-11-28 16:06:18', '2025-11-28 16:06:18'),
(18, 37, 'paystack', 'ALUMNI-5-1764350185', 120.00, 'GHS', 'success', 'card', 'your.email+fakedata49627@gmail.com', NULL, '{\"id\":5581591349,\"domain\":\"test\",\"status\":\"success\",\"reference\":\"ALUMNI-5-1764350185\",\"receipt_number\":null,\"amount\":12000,\"message\":null,\"gateway_response\":\"Successful\",\"paid_at\":\"2025-11-28T17:16:34.000Z\",\"created_at\":\"2025-11-28T17:16:28.000Z\",\"channel\":\"card\",\"currency\":\"GHS\",\"ip_address\":\"41.79.97.5\",\"metadata\":{\"currency\":\"GHS\",\"app\":\"Alumni Connect\",\"environment\":\"test\",\"referrer\":\"http:\\/\\/localhost\\/\"},\"log\":{\"start_time\":1764350189,\"time_spent\":4,\"attempts\":1,\"errors\":0,\"success\":true,\"mobile\":false,\"input\":[],\"history\":[{\"type\":\"action\",\"message\":\"Attempted to pay with card\",\"time\":3},{\"type\":\"success\",\"message\":\"Successfully paid with card\",\"time\":4}]},\"fees\":234,\"fees_split\":null,\"authorization\":{\"authorization_code\":\"AUTH_trkhblwlv0\",\"bin\":\"408408\",\"last4\":\"4081\",\"exp_month\":\"12\",\"exp_year\":\"2030\",\"channel\":\"card\",\"card_type\":\"visa \",\"bank\":\"TEST BANK\",\"country_code\":\"GH\",\"brand\":\"visa\",\"reusable\":true,\"signature\":\"SIG_Ggrc4HZwbcmYsGnbK4kh\",\"account_name\":null,\"receiver_bank_account_number\":null,\"receiver_bank\":null},\"customer\":{\"id\":321675136,\"first_name\":null,\"last_name\":null,\"email\":\"your.email+fakedata49627@gmail.com\",\"customer_code\":\"CUS_8po9040yx2onasw\",\"phone\":null,\"metadata\":null,\"risk_action\":\"default\",\"international_format_phone\":null},\"plan\":null,\"split\":[],\"order_id\":null,\"paidAt\":\"2025-11-28T17:16:34.000Z\",\"createdAt\":\"2025-11-28T17:16:28.000Z\",\"requested_amount\":12000,\"pos_transaction_data\":null,\"source\":null,\"fees_breakdown\":null,\"connect\":null,\"transaction_date\":\"2025-11-28T17:16:28.000Z\",\"plan_object\":[],\"subaccount\":[]}', '2025-11-28 17:16:34', '2025-11-28 17:16:34'),
(19, 38, 'paystack', 'ALUMNI-4-1764353012', 50.00, 'GHS', 'success', 'card', 'your.email+fakedata94775@gmail.com', NULL, '{\"id\":5581718801,\"domain\":\"test\",\"status\":\"success\",\"reference\":\"ALUMNI-4-1764353012\",\"receipt_number\":null,\"amount\":5000,\"message\":null,\"gateway_response\":\"Successful\",\"paid_at\":\"2025-11-28T18:03:41.000Z\",\"created_at\":\"2025-11-28T18:03:35.000Z\",\"channel\":\"card\",\"currency\":\"GHS\",\"ip_address\":\"154.65.20.56\",\"metadata\":{\"currency\":\"GHS\",\"app\":\"Alumni Connect\",\"environment\":\"test\",\"referrer\":\"http:\\/\\/localhost\\/\"},\"log\":{\"start_time\":1764353017,\"time_spent\":3,\"attempts\":1,\"errors\":0,\"success\":true,\"mobile\":false,\"input\":[],\"history\":[{\"type\":\"action\",\"message\":\"Attempted to pay with card\",\"time\":2},{\"type\":\"success\",\"message\":\"Successfully paid with card\",\"time\":3}]},\"fees\":98,\"fees_split\":null,\"authorization\":{\"authorization_code\":\"AUTH_echfcy97mn\",\"bin\":\"408408\",\"last4\":\"4081\",\"exp_month\":\"12\",\"exp_year\":\"2030\",\"channel\":\"card\",\"card_type\":\"visa \",\"bank\":\"TEST BANK\",\"country_code\":\"GH\",\"brand\":\"visa\",\"reusable\":true,\"signature\":\"SIG_Ggrc4HZwbcmYsGnbK4kh\",\"account_name\":null,\"receiver_bank_account_number\":null,\"receiver_bank\":null},\"customer\":{\"id\":322260440,\"first_name\":null,\"last_name\":null,\"email\":\"your.email+fakedata94775@gmail.com\",\"customer_code\":\"CUS_h88bzo1ysrutkx7\",\"phone\":null,\"metadata\":null,\"risk_action\":\"default\",\"international_format_phone\":null},\"plan\":null,\"split\":[],\"order_id\":null,\"paidAt\":\"2025-11-28T18:03:41.000Z\",\"createdAt\":\"2025-11-28T18:03:35.000Z\",\"requested_amount\":5000,\"pos_transaction_data\":null,\"source\":null,\"fees_breakdown\":null,\"connect\":null,\"transaction_date\":\"2025-11-28T18:03:35.000Z\",\"plan_object\":[],\"subaccount\":[]}', '2025-11-28 18:03:42', '2025-11-28 18:03:42'),
(20, 39, 'paystack', 'ALUMNI-4-1764353856', 30.00, 'GHS', 'success', 'card', 'your.email+fakedata94775@gmail.com', NULL, '{\"id\":5581761224,\"domain\":\"test\",\"status\":\"success\",\"reference\":\"ALUMNI-4-1764353856\",\"receipt_number\":null,\"amount\":3000,\"message\":null,\"gateway_response\":\"Successful\",\"paid_at\":\"2025-11-28T18:17:44.000Z\",\"created_at\":\"2025-11-28T18:17:38.000Z\",\"channel\":\"card\",\"currency\":\"GHS\",\"ip_address\":\"154.65.20.56\",\"metadata\":{\"currency\":\"GHS\",\"app\":\"Alumni Connect\",\"environment\":\"test\",\"referrer\":\"http:\\/\\/localhost\\/\"},\"log\":{\"start_time\":1764353861,\"time_spent\":3,\"attempts\":1,\"errors\":0,\"success\":true,\"mobile\":false,\"input\":[],\"history\":[{\"type\":\"action\",\"message\":\"Attempted to pay with card\",\"time\":2},{\"type\":\"success\",\"message\":\"Successfully paid with card\",\"time\":3}]},\"fees\":59,\"fees_split\":null,\"authorization\":{\"authorization_code\":\"AUTH_6rl3poz9yw\",\"bin\":\"408408\",\"last4\":\"4081\",\"exp_month\":\"12\",\"exp_year\":\"2030\",\"channel\":\"card\",\"card_type\":\"visa \",\"bank\":\"TEST BANK\",\"country_code\":\"GH\",\"brand\":\"visa\",\"reusable\":true,\"signature\":\"SIG_Ggrc4HZwbcmYsGnbK4kh\",\"account_name\":null,\"receiver_bank_account_number\":null,\"receiver_bank\":null},\"customer\":{\"id\":322260440,\"first_name\":null,\"last_name\":null,\"email\":\"your.email+fakedata94775@gmail.com\",\"customer_code\":\"CUS_h88bzo1ysrutkx7\",\"phone\":null,\"metadata\":null,\"risk_action\":\"default\",\"international_format_phone\":null},\"plan\":null,\"split\":[],\"order_id\":null,\"paidAt\":\"2025-11-28T18:17:44.000Z\",\"createdAt\":\"2025-11-28T18:17:38.000Z\",\"requested_amount\":3000,\"pos_transaction_data\":null,\"source\":null,\"fees_breakdown\":null,\"connect\":null,\"transaction_date\":\"2025-11-28T18:17:38.000Z\",\"plan_object\":[],\"subaccount\":[]}', '2025-11-28 18:17:47', '2025-11-28 18:17:47'),
(21, 40, 'paystack', 'ALUMNI-5-1764521564', 300.00, 'GHS', 'success', 'card', 'your.email+fakedata49627@gmail.com', NULL, '{\"id\":5588542380,\"domain\":\"test\",\"status\":\"success\",\"reference\":\"ALUMNI-5-1764521564\",\"receipt_number\":null,\"amount\":30000,\"message\":null,\"gateway_response\":\"Successful\",\"paid_at\":\"2025-11-30T16:52:53.000Z\",\"created_at\":\"2025-11-30T16:52:47.000Z\",\"channel\":\"card\",\"currency\":\"GHS\",\"ip_address\":\"154.65.20.56\",\"metadata\":{\"currency\":\"GHS\",\"app\":\"Alumni Connect\",\"environment\":\"test\",\"referrer\":\"http:\\/\\/localhost\\/\"},\"log\":{\"start_time\":1764521570,\"time_spent\":3,\"attempts\":1,\"errors\":0,\"success\":true,\"mobile\":false,\"input\":[],\"history\":[{\"type\":\"action\",\"message\":\"Attempted to pay with card\",\"time\":3},{\"type\":\"success\",\"message\":\"Successfully paid with card\",\"time\":3}]},\"fees\":585,\"fees_split\":null,\"authorization\":{\"authorization_code\":\"AUTH_ylt1pvephs\",\"bin\":\"408408\",\"last4\":\"4081\",\"exp_month\":\"12\",\"exp_year\":\"2030\",\"channel\":\"card\",\"card_type\":\"visa \",\"bank\":\"TEST BANK\",\"country_code\":\"GH\",\"brand\":\"visa\",\"reusable\":true,\"signature\":\"SIG_Ggrc4HZwbcmYsGnbK4kh\",\"account_name\":null,\"receiver_bank_account_number\":null,\"receiver_bank\":null},\"customer\":{\"id\":321675136,\"first_name\":null,\"last_name\":null,\"email\":\"your.email+fakedata49627@gmail.com\",\"customer_code\":\"CUS_8po9040yx2onasw\",\"phone\":null,\"metadata\":null,\"risk_action\":\"default\",\"international_format_phone\":null},\"plan\":null,\"split\":[],\"order_id\":null,\"paidAt\":\"2025-11-30T16:52:53.000Z\",\"createdAt\":\"2025-11-30T16:52:47.000Z\",\"requested_amount\":30000,\"pos_transaction_data\":null,\"source\":null,\"fees_breakdown\":null,\"connect\":null,\"transaction_date\":\"2025-11-30T16:52:47.000Z\",\"plan_object\":[],\"subaccount\":[]}', '2025-11-30 16:52:55', '2025-11-30 16:52:55'),
(22, 41, 'paystack', 'ALUMNI-5-1764529856', 50.00, 'GHS', 'success', 'card', 'your.email+fakedata49627@gmail.com', NULL, '{\"id\":5588867396,\"domain\":\"test\",\"status\":\"success\",\"reference\":\"ALUMNI-5-1764529856\",\"receipt_number\":null,\"amount\":5000,\"message\":null,\"gateway_response\":\"Successful\",\"paid_at\":\"2025-11-30T19:11:10.000Z\",\"created_at\":\"2025-11-30T19:10:58.000Z\",\"channel\":\"card\",\"currency\":\"GHS\",\"ip_address\":\"41.79.97.5\",\"metadata\":{\"currency\":\"GHS\",\"app\":\"Alumni Connect\",\"environment\":\"test\",\"referrer\":\"http:\\/\\/localhost\\/\"},\"log\":{\"start_time\":1764529860,\"time_spent\":10,\"attempts\":1,\"errors\":0,\"success\":true,\"mobile\":false,\"input\":[],\"history\":[{\"type\":\"action\",\"message\":\"Attempted to pay with card\",\"time\":9},{\"type\":\"success\",\"message\":\"Successfully paid with card\",\"time\":10}]},\"fees\":98,\"fees_split\":null,\"authorization\":{\"authorization_code\":\"AUTH_3ashspyy92\",\"bin\":\"408408\",\"last4\":\"4081\",\"exp_month\":\"12\",\"exp_year\":\"2030\",\"channel\":\"card\",\"card_type\":\"visa \",\"bank\":\"TEST BANK\",\"country_code\":\"GH\",\"brand\":\"visa\",\"reusable\":true,\"signature\":\"SIG_Ggrc4HZwbcmYsGnbK4kh\",\"account_name\":null,\"receiver_bank_account_number\":null,\"receiver_bank\":null},\"customer\":{\"id\":321675136,\"first_name\":null,\"last_name\":null,\"email\":\"your.email+fakedata49627@gmail.com\",\"customer_code\":\"CUS_8po9040yx2onasw\",\"phone\":null,\"metadata\":null,\"risk_action\":\"default\",\"international_format_phone\":null},\"plan\":null,\"split\":[],\"order_id\":null,\"paidAt\":\"2025-11-30T19:11:10.000Z\",\"createdAt\":\"2025-11-30T19:10:58.000Z\",\"requested_amount\":5000,\"pos_transaction_data\":null,\"source\":null,\"fees_breakdown\":null,\"connect\":null,\"transaction_date\":\"2025-11-30T19:10:58.000Z\",\"plan_object\":[],\"subaccount\":[]}', '2025-11-30 19:11:12', '2025-11-30 19:11:12');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_title` varchar(255) DEFAULT NULL,
  `post_content` text NOT NULL,
  `post_type` enum('general','job','event','article','opportunity') DEFAULT 'general',
  `image_url` varchar(255) DEFAULT NULL,
  `likes_count` int(11) DEFAULT 0,
  `comments_count` int(11) DEFAULT 0,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_published` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `user_id`, `post_title`, `post_content`, `post_type`, `image_url`, `likes_count`, `comments_count`, `date_created`, `date_updated`, `is_published`) VALUES
(1, 5, 'New Here', 'Excited to Join this Platform!!', 'general', NULL, 0, 0, '2025-11-30 17:10:25', '2025-11-30 17:10:33', 1);

-- --------------------------------------------------------

--
-- Table structure for table `post_comments`
--

CREATE TABLE `post_comments` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_content` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `like_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
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
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `service_type`, `description`, `price`, `duration`, `provider_id`, `category`, `location`, `is_active`, `stock_quantity`, `image_url`, `date_created`, `date_updated`) VALUES
(1, '30-Day Job Posting - Entry Level', 'job_posting', 'Post an entry-level job opportunity visible to all students for 30 days. Perfect for internships and graduate positions.', 150.00, 30, 1, 'Entry Level', 'Ghana', 1, NULL, 'https://ui-avatars.com/api/?name=Job+Post&background=10b981&color=fff&size=400', '2025-11-25 08:59:18', NULL),
(2, '60-Day Job Posting - Mid Level', 'job_posting', 'Post a mid-level position for 60 days with featured placement on the jobs board.', 300.00, 60, 1, 'Mid Level', 'Ghana', 1, NULL, 'https://ui-avatars.com/api/?name=Featured+Job&background=3b82f6&color=fff&size=400', '2025-11-25 08:59:18', NULL),
(3, '90-Day Premium Job Posting', 'job_posting', 'Premium job posting with top placement, company logo, and extended visibility for 90 days.', 500.00, 90, 1, 'Senior Level', 'Remote', 1, NULL, 'https://ui-avatars.com/api/?name=Premium&background=8b5cf6&color=fff&size=400', '2025-11-25 08:59:18', NULL),
(4, 'Career Guidance Session - 1 Hour', 'mentorship', 'One-on-one career guidance session with experienced alumni. Get personalized advice on career paths, job search strategies, and industry insights.', 75.00, 60, 1, 'Career Development', 'Virtual', 1, NULL, 'https://ui-avatars.com/api/?name=Career+Mentor&background=f59e0b&color=fff&size=400', '2025-11-25 08:59:18', NULL),
(5, 'Technical Skills Mentorship - 2 Hours', 'mentorship', 'Deep-dive technical mentorship covering programming, software development, or data science. Includes code review and project guidance.', 150.00, 120, 1, 'Technical Skills', 'Virtual', 1, NULL, 'https://ui-avatars.com/api/?name=Tech+Mentor&background=06b6d4&color=fff&size=400', '2025-11-25 08:59:18', NULL),
(6, 'Resume & Interview Prep Session', 'mentorship', 'Comprehensive resume review and mock interview session to help you land your dream job.', 100.00, 90, 1, 'Interview Prep', 'Virtual', 1, NULL, 'https://ui-avatars.com/api/?name=Interview+Prep&background=ec4899&color=fff&size=400', '2025-11-25 08:59:18', NULL),
(7, 'Entrepreneurship Coaching - 3 Hours', 'mentorship', 'Learn how to start and grow your business from successful alumni entrepreneurs. Includes business plan review and startup strategy.', 250.00, 180, 1, 'Entrepreneurship', 'Hybrid', 1, NULL, 'https://ui-avatars.com/api/?name=Startup+Coach&background=7c3aed&color=fff&size=400', '2025-11-25 08:59:18', NULL),
(8, 'Annual Alumni Networking Gala 2025', 'event', 'Join 500+ alumni and students for the biggest networking event of the year. Includes dinner, keynote speeches, and career fair.', 120.00, NULL, 1, 'Networking', 'Accra, Ghana', 1, 449, 'https://ui-avatars.com/api/?name=Gala+2025&background=dc2626&color=fff&size=400', '2025-11-25 08:59:18', '2025-11-28 17:16:34'),
(9, 'Tech Career Fair - December 2025', 'event', 'Meet with 50+ tech companies actively hiring. Free for students, alumni pay to sponsor booths and network with recruiters.', 50.00, NULL, 1, 'Career Fair', 'Kumasi, Ghana', 1, 194, 'https://ui-avatars.com/api/?name=Career+Fair&background=059669&color=fff&size=400', '2025-11-25 08:59:18', '2025-11-30 19:11:11'),
(10, 'Alumni Football Tournament', 'event', 'Annual alumni vs students football match followed by BBQ and networking. All proceeds support student scholarships.', 30.00, NULL, 1, 'Sports & Social', 'Ashesi Campus', 1, 97, 'https://ui-avatars.com/api/?name=Football&background=ea580c&color=fff&size=400', '2025-11-25 08:59:18', '2025-11-28 18:17:46'),
(11, 'Women in Tech Leadership Summit', 'event', 'Full-day conference featuring female tech leaders, panel discussions, workshops, and mentorship speed-dating.', 80.00, NULL, 1, 'Professional Development', 'Accra, Ghana', 1, 149, 'https://ui-avatars.com/api/?name=Women+Tech&background=db2777&color=fff&size=400', '2025-11-25 08:59:18', '2025-11-26 12:28:47'),
(12, 'Premium Profile - 1 Month', 'premium_feature', 'Boost your profile visibility with featured placement, unlimited messages, and priority support for 30 days.', 25.00, 30, NULL, 'Profile Enhancement', NULL, 0, NULL, 'https://ui-avatars.com/api/?name=Premium&background=fbbf24&color=000&size=400', '2025-11-25 08:59:18', '2025-11-29 21:55:07'),
(13, 'Premium Profile - 6 Months', 'premium_feature', 'Get 6 months of premium features at a discounted rate. Includes all premium benefits plus exclusive job alerts.', 120.00, 180, NULL, 'Profile Enhancement', NULL, 0, NULL, 'https://ui-avatars.com/api/?name=6+Months&background=f97316&color=fff&size=400', '2025-11-25 08:59:18', '2025-11-29 21:55:07'),
(14, 'Premium Profile - 1 Year', 'premium_feature', 'Annual premium membership with maximum savings. Perfect for active alumni looking to give back and network extensively.', 200.00, 365, NULL, 'Profile Enhancement', NULL, 0, NULL, 'https://ui-avatars.com/api/?name=Annual&background=7a1e1e&color=fff&size=400', '2025-11-25 08:59:18', '2025-11-29 21:55:07'),
(15, 'Football Coaching', 'mentorship', 'Learn Football Basics from Alumni Papa Yaw Badu (C\'2026)', 50.00, 60, 6, 'other', 'Ashpitch', 1, NULL, 'service_1764367254_692a1b96d557a.jpg', '2025-11-28 19:39:03', '2025-11-28 22:00:54');

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `university` varchar(200) DEFAULT 'Ashesi University',
  `major` varchar(150) NOT NULL,
  `expected_graduation` int(4) NOT NULL,
  `year_level` enum('Freshman','Sophomore','Junior','Senior') NOT NULL,
  `interests` text DEFAULT NULL,
  `career_goals` text DEFAULT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `portfolio_url` varchar(255) DEFAULT NULL,
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`profile_id`, `user_id`, `university`, `major`, `expected_graduation`, `year_level`, `interests`, `career_goals`, `gpa`, `linkedin_url`, `portfolio_url`, `date_updated`) VALUES
(1, 4, 'Ashesi University', 'Accusantium libero nisi at tempore rem placeat asperiores facere.', 1989, '', NULL, '', NULL, NULL, NULL, '2025-11-25 08:25:47'),
(2, 6, 'Ashesi University', 'Computer Science', 2026, '', NULL, 'To be a Software Developer\r\n', NULL, NULL, NULL, '2025-11-28 22:03:20'),
(3, 20, 'Ashesi University', 'Computer Science', 2026, 'Junior', 'AI/ML, Web Development', 'Software Engineer at a tech company', NULL, NULL, NULL, '2025-11-30 17:20:02'),
(4, 21, 'Ashesi University', 'Business Administration', 2027, 'Sophomore', 'Finance, Entrepreneurship', 'Start my own business', NULL, NULL, NULL, '2025-11-30 17:20:02'),
(5, 22, 'Ashesi University', 'Computer Engineering', 2025, 'Senior', 'Embedded Systems, IoT', 'Hardware Engineer at Apple', NULL, NULL, NULL, '2025-11-30 17:20:02'),
(6, 23, 'Ashesi University', 'Economics', 2026, 'Junior', 'Development Economics', 'Policy Analyst', NULL, NULL, NULL, '2025-11-30 17:20:02'),
(7, 24, 'Ashesi University', 'Management Information Systems', 2027, 'Sophomore', 'Data Analytics', 'Data Scientist', NULL, NULL, NULL, '2025-11-30 17:20:02'),
(8, 25, 'Ashesi University', 'Computer Science', 2025, 'Senior', 'Cybersecurity', 'Security Engineer', NULL, NULL, NULL, '2025-11-30 17:20:02'),
(9, 26, 'Ashesi University', 'Electrical Engineering', 2026, 'Junior', 'Renewable Energy', 'Energy Consultant', NULL, NULL, NULL, '2025-11-30 17:20:02'),
(10, 27, 'Ashesi University', 'Business Administration', 2028, 'Freshman', 'Marketing, Social Media', 'Marketing Director', NULL, NULL, NULL, '2025-11-30 17:20:02'),
(11, 2, 'Ashesi University', 'Computer Science', 2026, 'Junior', 'Software Development', 'Full Stack Developer', NULL, NULL, NULL, '2025-11-30 17:20:28'),
(12, 3, 'Ashesi University', 'Business Administration', 2025, 'Senior', 'Entrepreneurship', 'Startup Founder', NULL, NULL, NULL, '2025-11-30 17:20:28');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `subscription_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_type` enum('free','professional','premium') NOT NULL DEFAULT 'free',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','cancelled','expired','pending') NOT NULL DEFAULT 'pending',
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `starts_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `auto_renew` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`subscription_id`, `user_id`, `plan_type`, `amount`, `status`, `payment_reference`, `payment_method`, `starts_at`, `expires_at`, `cancelled_at`, `auto_renew`, `created_at`, `updated_at`) VALUES
(1, 4, 'professional', 49.00, 'cancelled', 'SUB-professional-4-1764452151', NULL, '2025-11-29 21:36:02', '2025-12-29 22:36:02', NULL, 1, '2025-11-29 21:36:02', '2025-11-29 21:40:55'),
(2, 4, 'professional', 49.00, 'cancelled', 'SUB-professional-4-1764452443', NULL, '2025-11-29 21:40:55', '2025-12-29 22:40:55', NULL, 1, '2025-11-29 21:40:55', '2025-11-29 21:53:02'),
(3, 4, 'professional', 49.00, 'cancelled', 'SUB-professional-4-1764453172', NULL, '2025-11-29 21:53:02', '2025-12-29 22:53:02', NULL, 1, '2025-11-29 21:53:02', '2025-11-29 21:56:14'),
(4, 4, 'professional', 49.00, 'cancelled', 'SUB-professional-4-1764453364', NULL, '2025-11-29 21:56:14', '2025-12-29 22:56:14', NULL, 1, '2025-11-29 21:56:14', '2025-11-29 21:56:19'),
(5, 4, 'professional', 49.00, 'cancelled', 'SUB-professional-4-1764453364', NULL, '2025-11-29 21:56:19', '2025-12-29 22:56:19', NULL, 1, '2025-11-29 21:56:19', '2025-11-29 21:58:28'),
(6, 4, 'professional', 49.00, 'active', 'SUB-professional-4-1764453499', NULL, '2025-11-29 21:58:28', '2025-12-29 22:58:28', NULL, 1, '2025-11-29 21:58:28', NULL),
(7, 5, 'professional', 49.00, 'active', 'SUB-professional-5-1764529822', NULL, '2025-11-30 19:10:32', '2025-12-30 20:10:32', NULL, 1, '2025-11-30 19:10:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `plan_id` int(11) NOT NULL,
  `plan_type` varchar(50) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `monthly_price` decimal(10,2) NOT NULL,
  `yearly_price` decimal(10,2) DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_plans`
--

INSERT INTO `subscription_plans` (`plan_id`, `plan_type`, `plan_name`, `description`, `monthly_price`, `yearly_price`, `features`, `is_active`, `display_order`, `created_at`) VALUES
(1, 'free', 'Free', 'Perfect for getting started', 0.00, 0.00, '{\"messages_per_month\": 5, \"mentorship_sessions\": 0, \"job_postings\": 0, \"event_discount\": 0, \"profile_badge\": null}', 1, 1, '2025-11-29 08:13:35'),
(2, 'professional', 'Professional', 'Best for active networking', 49.00, 470.00, '{\"messages_per_month\": -1, \"mentorship_sessions\": 5, \"job_postings\": 3, \"event_discount\": 10, \"profile_badge\": \"star\"}', 1, 2, '2025-11-29 08:13:35'),
(3, 'premium', 'Premium', 'For power networkers', 99.00, 950.00, '{\"messages_per_month\": -1, \"mentorship_sessions\": -1, \"job_postings\": -1, \"event_discount\": 25, \"service_discount\": 25, \"profile_badge\": \"crown\", \"vip_events\": true, \"priority_support\": true}', 1, 3, '2025-11-29 08:13:35');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_usage`
--

CREATE TABLE `subscription_usage` (
  `usage_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `usage_type` varchar(50) NOT NULL COMMENT 'messages_per_month, mentorship_sessions, job_postings',
  `reference_id` int(11) DEFAULT NULL COMMENT 'ID of related record (message_id, session_id, etc)',
  `used_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_role` enum('student','alumni','admin') NOT NULL DEFAULT 'student',
  `phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `user_role`, `phone`, `profile_image`, `bio`, `date_created`, `last_login`, `is_active`) VALUES
(1, 'Admin', 'User', 'admin@alumniconnect.com', '\\.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, NULL, '2025-11-24 12:38:47', NULL, 0),
(2, 'John', 'Student', 'john.student@ashesi.edu.gh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '+233241234567', NULL, NULL, '2025-11-25 08:58:14', NULL, 1),
(3, 'Papa Yaw', 'Badu', 'raybadu10@gmail.com', '$2y$10$lnmsTjiVkkE9XW0pealOveLHsxfWzpwv8frgYIxfPnEvl/oWH96n2', 'student', '0557333735', NULL, NULL, '2025-11-24 23:55:25', NULL, 1),
(4, 'Wava', 'DuBuque', 'your.email+fakedata94775@gmail.com', '$2y$10$lFBIq52inLsml1uawMEmee.xOwRbnVBM1QiHqqoacH1ltQV3J/Pvy', 'student', '101-338-8939', NULL, NULL, '2025-11-25 08:25:47', '2025-11-29 03:01:13', 1),
(5, 'Juanita', 'Braun', 'your.email+fakedata49627@gmail.com', '$2y$10$cKjT.NcgWA/lS.XaYM0on.SaFoihTwtXrvSOxhhXnMh63vr5k07Li', 'alumni', '102-868-3633', NULL, NULL, '2025-11-25 08:27:47', '2025-11-30 16:43:51', 1),
(6, 'Admin', 'Test', 'Admin@gmail.com', '$2y$10$mIGTe0WGbqXzUuwlLJ31NuqSg0Ni2dSvv.KgfFnQAFDUTh2sgojcq', 'admin', '+1 (604) 509-5396', 'user_6_1764367497.jpg', 'HIII\r\n', '2025-11-28 00:57:31', '2025-11-29 21:59:27', 1),
(7, 'Sarah', 'Johnson', 'sarah.johnson@alumni.edu', 'password123', 'alumni', '+1 (193) 215-4597', NULL, '', '2025-11-30 16:32:34', NULL, 1),
(8, 'Michael', 'Chen', 'michael.chen@alumni.edu', 'password123', 'alumni', NULL, NULL, NULL, '2025-11-30 16:32:34', NULL, 1),
(9, 'Emma', 'Davis', 'emma.davis@alumni.edu', 'password123', 'alumni', NULL, NULL, NULL, '2025-11-30 16:32:34', NULL, 1),
(10, 'David', 'Mensah', 'david.mensah@alumni.edu', 'password123', 'alumni', NULL, NULL, NULL, '2025-11-30 16:32:34', NULL, 1),
(11, 'Ama', 'Owusu', 'ama.owusu@alumni.edu', 'password123', 'alumni', NULL, NULL, NULL, '2025-11-30 16:32:34', NULL, 1),
(12, 'Kwame', 'Asante', 'kwame.asante@alumni.edu', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni', NULL, NULL, NULL, '2025-11-30 17:19:18', NULL, 1),
(13, 'Abena', 'Mensah', 'abena.mensah@alumni.edu', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni', NULL, NULL, NULL, '2025-11-30 17:19:18', NULL, 1),
(14, 'Kofi', 'Osei', 'kofi.osei@alumni.edu', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni', NULL, NULL, NULL, '2025-11-30 17:19:18', NULL, 1),
(15, 'Akosua', 'Darko', 'akosua.darko@alumni.edu', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni', NULL, NULL, NULL, '2025-11-30 17:19:18', NULL, 1),
(16, 'Yaw', 'Boateng', 'yaw.boateng@alumni.edu', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni', NULL, NULL, NULL, '2025-11-30 17:19:18', NULL, 1),
(17, 'Esi', 'Adjei', 'esi.adjei@alumni.edu', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni', NULL, NULL, NULL, '2025-11-30 17:19:18', NULL, 1),
(18, 'Nana', 'Agyeman', 'nana.agyeman@alumni.edu', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni', NULL, NULL, NULL, '2025-11-30 17:19:18', NULL, 1),
(19, 'Adwoa', 'Sarpong', 'adwoa.sarpong@alumni.edu', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni', NULL, NULL, NULL, '2025-11-30 17:19:18', NULL, 1),
(20, 'Kelvin', 'Amartey', 'kelvin.amartey@ashesi.edu.gh', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NULL, NULL, NULL, '2025-11-30 17:19:48', NULL, 1),
(21, 'Efua', 'Annan', 'efua.annan@ashesi.edu.gh', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NULL, NULL, NULL, '2025-11-30 17:19:48', NULL, 1),
(22, 'Kweku', 'Frimpong', 'kweku.frimpong@ashesi.edu.gh', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NULL, NULL, NULL, '2025-11-30 17:19:48', NULL, 1),
(23, 'Ama', 'Asamoah', 'ama.asamoah@ashesi.edu.gh', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NULL, NULL, NULL, '2025-11-30 17:19:48', NULL, 1),
(24, 'Kofi', 'Mensah', 'kofi.mensah@ashesi.edu.gh', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NULL, NULL, NULL, '2025-11-30 17:19:48', NULL, 1),
(25, 'Abigail', 'Oppong', 'abigail.oppong@ashesi.edu.gh', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NULL, NULL, NULL, '2025-11-30 17:19:48', NULL, 1),
(26, 'Daniel', 'Adjei', 'daniel.adjei@ashesi.edu.gh', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NULL, NULL, NULL, '2025-11-30 17:19:48', NULL, 1),
(27, 'Naomi', 'Boateng', 'naomi.boateng@ashesi.edu.gh', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NULL, NULL, NULL, '2025-11-30 17:19:48', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alumni_profiles`
--
ALTER TABLE `alumni_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_graduation_year` (`graduation_year`),
  ADD KEY `idx_major` (`major`),
  ADD KEY `idx_company` (`current_company`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_service` (`service_id`);

--
-- Indexes for table `connections`
--
ALTER TABLE `connections`
  ADD PRIMARY KEY (`connection_id`),
  ADD UNIQUE KEY `unique_connection` (`requester_id`,`receiver_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_requester` (`requester_id`),
  ADD KEY `idx_receiver` (`receiver_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`coupon_id`),
  ADD UNIQUE KEY `coupon_code` (`coupon_code`),
  ADD UNIQUE KEY `idx_coupon_code` (`coupon_code`),
  ADD KEY `idx_valid_dates` (`valid_from`,`valid_until`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD PRIMARY KEY (`usage_id`),
  ADD KEY `idx_coupon` (`coupon_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_order` (`order_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_date` (`event_date`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD UNIQUE KEY `unique_registration` (`event_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD UNIQUE KEY `idx_invoice_number` (`invoice_number`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `job_opportunities`
--
ALTER TABLE `job_opportunities`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `idx_posted_by` (`posted_by`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `mentorships`
--
ALTER TABLE `mentorships`
  ADD PRIMARY KEY (`mentorship_id`),
  ADD KEY `idx_mentor` (`mentor_id`),
  ADD KEY `idx_mentee` (`mentee_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_receiver` (`receiver_id`),
  ADD KEY `idx_read` (`is_read`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_read` (`is_read`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD UNIQUE KEY `idx_order_number` (`order_number`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`order_status`),
  ADD KEY `idx_payment` (`payment_status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_service` (`service_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `token` (`token`),
  ADD KEY `expires_at` (`expires_at`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `transaction_reference` (`transaction_reference`),
  ADD UNIQUE KEY `idx_transaction_ref` (`transaction_reference`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_status` (`payment_status`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_type` (`post_type`),
  ADD KEY `idx_created` (`date_created`);

--
-- Indexes for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_post` (`post_id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`like_id`),
  ADD UNIQUE KEY `unique_like` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `idx_service_type` (`service_type`),
  ADD KEY `idx_provider` (`provider_id`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_major` (`major`),
  ADD KEY `idx_grad_year` (`expected_graduation`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`plan_id`),
  ADD UNIQUE KEY `plan_type` (`plan_type`);

--
-- Indexes for table `subscription_usage`
--
ALTER TABLE `subscription_usage`
  ADD PRIMARY KEY (`usage_id`),
  ADD KEY `idx_user_type` (`user_id`,`usage_type`),
  ADD KEY `idx_month` (`used_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`user_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alumni_profiles`
--
ALTER TABLE `alumni_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `connections`
--
ALTER TABLE `connections`
  MODIFY `connection_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `coupon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_registrations`
--
ALTER TABLE `event_registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `job_opportunities`
--
ALTER TABLE `job_opportunities`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mentorships`
--
ALTER TABLE `mentorships`
  MODIFY `mentorship_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `post_comments`
--
ALTER TABLE `post_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subscription_usage`
--
ALTER TABLE `subscription_usage`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alumni_profiles`
--
ALTER TABLE `alumni_profiles`
  ADD CONSTRAINT `alumni_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE;

--
-- Constraints for table `connections`
--
ALTER TABLE `connections`
  ADD CONSTRAINT `connections_ibfk_1` FOREIGN KEY (`requester_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `connections_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD CONSTRAINT `coupon_usage_ibfk_1` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`coupon_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usage_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usage_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE SET NULL;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD CONSTRAINT `event_registrations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_registrations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `job_opportunities`
--
ALTER TABLE `job_opportunities`
  ADD CONSTRAINT `job_opportunities_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `mentorships`
--
ALTER TABLE `mentorships`
  ADD CONSTRAINT `mentorships_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentorships_ibfk_2` FOREIGN KEY (`mentee_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD CONSTRAINT `post_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `student_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `subscription_usage`
--
ALTER TABLE `subscription_usage`
  ADD CONSTRAINT `subscription_usage_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
