CREATE DATABASE IF NOT EXISTS mailflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mailflow;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') NOT NULL DEFAULT 'user',
    phone VARCHAR(50) DEFAULT NULL,
    company VARCHAR(150) DEFAULT NULL,
    website VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    plan VARCHAR(50) NOT NULL DEFAULT 'Free',
    avatar_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(190) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_contact_messages_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS personal_information (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    company VARCHAR(150) DEFAULT NULL,
    website VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_personal_information_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT uq_personal_information_user UNIQUE (user_id),
    CONSTRAINT uq_personal_information_email UNIQUE (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO personal_information (user_id, first_name, last_name, email, phone, company, website, bio)
SELECT u.id, u.first_name, u.last_name, u.email, u.phone, u.company, u.website, u.bio
FROM users u
LEFT JOIN personal_information pi ON pi.user_id = u.id
WHERE pi.user_id IS NULL;

CREATE TABLE IF NOT EXISTS contacts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    email VARCHAR(190) NOT NULL,
    company VARCHAR(150) DEFAULT NULL,
    tags TEXT DEFAULT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_contacts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT uq_contacts_user_email UNIQUE (user_id, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS campaigns (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(190) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    sender_name VARCHAR(150) DEFAULT NULL,
    sender_email VARCHAR(190) DEFAULT NULL,
    preview_text VARCHAR(255) DEFAULT NULL,
    campaign_template VARCHAR(100) DEFAULT NULL,
    selected_image VARCHAR(255) DEFAULT NULL,
    image_alt VARCHAR(255) DEFAULT NULL,
    image_width INT UNSIGNED DEFAULT NULL,
    image_align VARCHAR(20) DEFAULT NULL,
    email_title VARCHAR(255) DEFAULT NULL,
    email_body MEDIUMTEXT DEFAULT NULL,
    button_text VARCHAR(150) DEFAULT NULL,
    button_link VARCHAR(255) DEFAULT NULL,
    content MEDIUMTEXT DEFAULT NULL,
    recipients_raw MEDIUMTEXT DEFAULT NULL,
    audience_tags TEXT DEFAULT NULL,
    audience_notes TEXT DEFAULT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'draft',
    recipients_count INT UNSIGNED NOT NULL DEFAULT 0,
    opens_count INT UNSIGNED NOT NULL DEFAULT 0,
    clicks_count INT UNSIGNED NOT NULL DEFAULT 0,
    schedule_at DATETIME DEFAULT NULL,
    sent_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_campaigns_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS billing_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(190) NOT NULL,
    amount_label VARCHAR(50) NOT NULL DEFAULT '$0.00',
    icon VARCHAR(20) NOT NULL DEFAULT '💳',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_billing_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    campaign_id INT UNSIGNED DEFAULT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('pending','paid','cancelled','completed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_orders_campaign FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin account (password: Admin1234!)
INSERT INTO users (first_name, last_name, email, password, role, plan)
VALUES (
    'Admin',
    'MailFlow',
    'admin@mailflow.com',
    '$2y$10$TKh8H1.PfbuNIyMOvjBsTOVp5p0J1AKjByEioGnm9z6lDFwFRElYe',
    'admin',
    'Free'
) ON DUPLICATE KEY UPDATE
    password = '$2y$10$TKh8H1.PfbuNIyMOvjBsTOVp5p0J1AKjByEioGnm9z6lDFwFRElYe',
    role = 'admin';
