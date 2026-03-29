-- Final Homepage Fix for Article Connect
-- Fix all remaining marketplace terminology

USE article_base;

-- ============================================
-- UPDATE FACILITY CONTENT (How's Olance)
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'How Article Connect is Different',
    '$.subheading', 'Discover the features and benefits of using Article Connect for articleship, internship, and student opportunity needs.'
)
WHERE data_keys = 'facility.content' 
AND tempname = 'basic';

-- ============================================
-- VERIFY ACCOUNT CONTENT IS CORRECT
-- ============================================

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

-- ============================================
-- VERIFY BANNER CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Find the Right Articleship & Internship Opportunities',
    '$.subheading', 'Article Connect helps students discover articleship, internship, and training opportunities with trusted firms and companies.',
    '$.subtitle', 'Trusted by Leading Firms & Growing Companies'
)
WHERE data_keys = 'banner.content' 
AND tempname = 'basic';

-- ============================================
-- VERIFY TOP FREELANCER CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Top Students',
    '$.subheading', 'Meet talented students who have excelled in their articleship and internship programs'
)
WHERE data_keys = 'top_freelancer.content' 
AND tempname = 'basic';

SELECT 'Homepage content verified and updated!' as Status;
