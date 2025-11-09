-- Add UNIQUE constraint to short_code column in urls table
ALTER TABLE urls
ADD UNIQUE KEY unique_short_code (short_code);
