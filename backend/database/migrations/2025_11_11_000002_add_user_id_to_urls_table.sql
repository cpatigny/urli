-- Add user_id column to urls table
ALTER TABLE urls
ADD COLUMN user_id INT NULL AFTER short_code,
ADD INDEX idx_user_id (user_id),
ADD CONSTRAINT fk_urls_user_id
  FOREIGN KEY (user_id)
  REFERENCES users(id)
  ON DELETE SET NULL
  ON UPDATE CASCADE;
