-- Active: 1774468603335@@127.0.0.1@3306@medical_system
-- Run this file to set up the database

CREATE DATABASE IF NOT EXISTS medical_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE medical_system;

-- Users table (doctors, patients, admins)
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    email       VARCHAR(180) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('admin','doctor','patient') NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Medical records table
CREATE TABLE IF NOT EXISTS medical_records (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    patient_id  INT NOT NULL,
    doctor_id   INT NOT NULL,
    diagnosis   TEXT NOT NULL,
    notes       TEXT,
    visit_date  DATE NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id)  REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Prescriptions table
CREATE TABLE IF NOT EXISTS prescriptions (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    record_id       INT NOT NULL,
    medication_name VARCHAR(200) NOT NULL,
    dosage          VARCHAR(100) NOT NULL,
    instructions    TEXT NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (record_id) REFERENCES medical_records(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed: default admin account (password: Admin@1234)
INSERT INTO users (name, email, password, role) VALUES
('System Admin', 'admin@medical.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBP7nmBfFGznKm', 'admin')
ON DUPLICATE KEY UPDATE id=id;

UPDATE users 
SET password = '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE email = 'admin@medical.com';