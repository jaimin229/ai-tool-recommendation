-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS ai_tool_portal;
USE ai_tool_portal;

-- 1. Users Table (Handles Authentication and Roles)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Categories Table (For Filtering Tools)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL,
    description TEXT
);

-- 3. AI Tools Table (Stores the actual tools)
CREATE TABLE ai_tools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tool_name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    url VARCHAR(255) NOT NULL,
    pricing ENUM('Free', 'Freemium', 'Paid') NOT NULL,
    category_id INT,
    added_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 4. Reviews & Ratings Table (User feedback)
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    tool_id INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tool_id) REFERENCES ai_tools(id) ON DELETE CASCADE
);

-- Insert a default Admin user (Password is 'admin123')
-- NOTE: The password hash below is pre-generated for 'admin123' using password_hash()
INSERT INTO users (username, email, password_hash, role) 
VALUES ('PortalAdmin', 'admin@silveroakuni.ac.in', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');