USE article_base;

-- Show current state
SELECT id, tempname, data_keys, JSON_EXTRACT(data_values, '$.freelancer_title') as freelancer_title, JSON_EXTRACT(data_values, '$.buyer_title') as buyer_title FROM frontends WHERE id = 78;

-- Update record ID 78 directly
UPDATE frontends 
SET data_values = JSON_OBJECT(
    'has_image', '1',
    'freelancer_title', 'Sign Up as a Student',
    'freelancer_content', 'Build your profile, apply for articleship and internship opportunities, and start your professional journey.',
    'freelancer_button_name', 'Create Student Account',
    'buyer_title', 'Sign Up as a Firm',
    'buyer_content', 'Post articleship and internship opportunities, connect with talented students, and build your team.',
    'buyer_button_name', 'Create Firm Account',
    'freelancer', '67d929f13124f1742285297.png',
    'buyer', '67d929f13efd31742285297.png'
)
WHERE id = 78 AND tempname = 'basic' AND data_keys = 'account.content';

-- Verify update
SELECT id, tempname, data_keys, JSON_EXTRACT(data_values, '$.freelancer_title') as freelancer_title, JSON_EXTRACT(data_values, '$.buyer_title') as buyer_title FROM frontends WHERE id = 78;
