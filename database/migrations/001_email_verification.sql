-- Email verification for new client accounts
-- Run this once in phpMyAdmin on bd_hair_salon before testing registration.

ALTER TABLE users
  ADD COLUMN email_verified TINYINT(1) NOT NULL DEFAULT 1 AFTER role,
  ADD COLUMN email_verification_token CHAR(64) NULL DEFAULT NULL AFTER email_verified,
  ADD COLUMN email_verification_expires DATETIME NULL DEFAULT NULL AFTER email_verification_token;

CREATE INDEX idx_users_email_verification_token
  ON users (email_verification_token);
