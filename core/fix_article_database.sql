USE article;

-- Show current state
SELECT id, tempname, data_keys, JSON_EXTRACT(data_values, '$.freelancer_title') as freelancer_title, JSON_EXTRACT(data_values, '$.buyer_title') as buyer_title FROM frontends WHERE data_keys = 'account.content' ORDER BY id DESC;

-- Update ALL records with account.content (in case there are multiple)
UPDATE frontends 
SET data_values = JSON_SET(
    COALESCE(data_values, '{}'),
    '$.has_image', '1',
    '$.freelancer_title', 'Sign Up as a Student',
    '$.freelancer_content', 'Build your profile, apply for articleship and internship opportunities, and start your professional journey.',
    '$.freelancer_button_name', 'Create Student Account',
    '$.buyer_title', 'Sign Up as a Firm',
    '$.buyer_content', 'Post articleship and internship opportunities, connect with talented students, and build your team.',
    '$.buyer_button_name', 'Create Firm Account'
)
WHERE tempname = 'basic' AND data_keys = 'account.content';

-- Verify update
SELECT id, tempname, data_keys, JSON_EXTRACT(data_values, '$.freelancer_title') as freelancer_title, JSON_EXTRACT(data_values, '$.buyer_title') as buyer_title FROM frontends WHERE data_keys = 'account.content' ORDER BY id DESC;
