-- Update Account Section Content for Article Connect
-- Update homepage registration cards to match exact requirements

USE article_base;

-- Update account.content with exact text requested
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.freelancer_title', 'Sign Up as a Student',
    '$.freelancer_content', 'Build your profile, apply for articleship and internship opportunities, and start your professional journey.',
    '$.freelancer_button_name', 'Create Student Account',
    '$.buyer_title', 'Sign Up as a Firm',
    '$.buyer_content', 'Post articleship and internship opportunities, connect with talented students, and build your team.',
    '$.buyer_button_name', 'Create Firm Account'
)
WHERE data_keys = 'account.content' 
AND tempname = 'basic';

SELECT 'Account section content updated!' as Status;
