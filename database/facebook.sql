CREATE DATABASE IF NOT EXISTS facebook_news_feed CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE facebook_news_feed;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(190) DEFAULT '',
 phone VARCHAR(30) DEFAULT '',
 password VARCHAR(255) NOT NULL,
 profile_image VARCHAR(255) DEFAULT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_email (email),
 UNIQUE KEY uq_phone (phone)
);

CREATE TABLE posts (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 content TEXT,
 image VARCHAR(255) DEFAULT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_posts_created (created_at)
);

CREATE TABLE likes (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 post_id INT NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_like (user_id, post_id),
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE comments (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 post_id INT NOT NULL,
 comment TEXT NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE friends (
 id INT AUTO_INCREMENT PRIMARY KEY,
 sender_id INT NOT NULL,
 receiver_id INT NOT NULL,
 status ENUM('pending','accepted') DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_friend_request (sender_id, receiver_id),
 FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Optional demo data: create accounts through register.php so passwords are hashed safely.
