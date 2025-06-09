-- Database creation (run this in your PHPMyAdmin or MySQL console)
CREATE DATABASE IF NOT EXISTS url_shortener;
USE url_shortener;

-- Table structure for storing URLs
CREATE TABLE IF NOT EXISTS urls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    long_url TEXT NOT NULL,
    short_code VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    clicks INT DEFAULT 0,
    UNIQUE KEY (short_code)
);

-- Index for better performance
CREATE INDEX idx_short_code ON urls (short_code);
