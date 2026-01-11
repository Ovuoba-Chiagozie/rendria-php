-- Rendria PHP API - Seed Data (for testing)

USE rendria_php;

-- Insert demo user
INSERT INTO users (email) VALUES ('test@gmail.com');

-- Insert demo API key
-- API Key: rnd_12345
-- SHA256 hash of "rnd_12345"
INSERT INTO api_keys (user_id, key_hash, credits, rate_limit, is_active) 
VALUES (
    1, 
    SHA2('rnd_12345', 256), 
    100, 
    100, 
    TRUE
);

-- Insert sample render request (optional)
INSERT INTO render_requests (api_key_id, status, credits_used) 
VALUES (1, 'completed', 2);

-- Display success message
SELECT 'Seed data inserted successfully!' AS message;
SELECT CONCAT('Demo API Key: rnd_12345') AS info;