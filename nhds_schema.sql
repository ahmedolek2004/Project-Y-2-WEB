-- Active: 1776112105989@@127.0.0.1@3306@nhds
-- ============================================================
--  National Health Database System — Database Schema
--  Course  : Web Development II
--  Stack   : MySQL 5.7+  |  PDO  |  PHP Native OOP
-- ============================================================

CREATE DATABASE IF NOT EXISTS nhds
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE nhds;

-- ============================================================
-- TABLE 1 : users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
  id          INT          NOT NULL AUTO_INCREMENT,
  name        VARCHAR(100) NOT NULL,
  email       VARCHAR(150) NOT NULL UNIQUE,
  password    VARCHAR(255) NOT NULL,          -- bcrypt hash
  role        ENUM('admin','doctor','patient') NOT NULL,
  phone       VARCHAR(20)  DEFAULT NULL,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_email (email),
  INDEX idx_role  (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE 2 : medical_records
-- ============================================================
CREATE TABLE IF NOT EXISTS medical_records (
  id          INT          NOT NULL AUTO_INCREMENT,
  patient_id  INT          NOT NULL,
  doctor_id   INT          NOT NULL,
  diagnosis   TEXT         NOT NULL,
  notes       TEXT         DEFAULT NULL,
  visit_date  DATE         NOT NULL,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_patient (patient_id),
  INDEX idx_doctor  (doctor_id),

  CONSTRAINT fk_record_patient
    FOREIGN KEY (patient_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_record_doctor
    FOREIGN KEY (doctor_id)  REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE 3 : prescriptions
-- ============================================================
CREATE TABLE IF NOT EXISTS prescriptions (
  id               INT          NOT NULL AUTO_INCREMENT,
  record_id        INT          NOT NULL,
  medication_name  VARCHAR(200) NOT NULL,
  dosage           VARCHAR(100) NOT NULL,
  instructions     TEXT         DEFAULT NULL,
  prescribed_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_record (record_id),

  CONSTRAINT fk_prescription_record
    FOREIGN KEY (record_id) REFERENCES medical_records(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SEED DATA — Default Admin Account
-- password = Admin@1234  (bcrypt hashed)
-- ============================================================
INSERT INTO users (name, email, password, role, phone) VALUES
(
  'System Admin',
  'admin@nhds.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'admin',
  '01000000000'
);

-- ============================================================
-- SEED DATA — Sample Doctor
-- password = Doctor@1234
-- ============================================================
INSERT INTO users (name, email, password, role, phone) VALUES
(
  'Dr. Ahmed Hassan',
  'doctor@nhds.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'doctor',
  '01111111111'
);

-- ============================================================
-- SEED DATA — Sample Patient
-- password = Patient@1234
-- ============================================================
INSERT INTO users (name, email, password, role, phone) VALUES
(
  'Mohamed Ali',
  'patient@nhds.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'patient',
  '01222222222'
);

-- ============================================================
-- SEED DATA — Sample Medical Record
-- ============================================================
INSERT INTO medical_records (patient_id, doctor_id, diagnosis, notes, visit_date) VALUES
(
  3,
  2,
  'Acute upper respiratory infection',
  'Patient reports sore throat and mild fever for 3 days.',
  CURDATE()
);

-- ============================================================
-- SEED DATA — Sample Prescription
-- ============================================================
INSERT INTO prescriptions (record_id, medication_name, dosage, instructions) VALUES
(
  1,
  'Amoxicillin',
  '500mg',
  'Take one capsule three times daily for 7 days after meals.'
);

