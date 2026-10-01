-- Stores the last successful verification-email send.
-- The application enforces a 60-second cooldown per account, with no daily send limit.

ALTER TABLE users
  ADD COLUMN email_verification_last_sent_at DATETIME NULL DEFAULT NULL
  AFTER email_verification_expires;
