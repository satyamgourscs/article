-- Update Remaining Homepage Content for Article Connect
-- This script updates any remaining old marketplace content

USE article_base;

-- ============================================
-- UPDATE BANNER CONTENT - Ensure all fields are correct
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Find the Right Articleship & Internship Opportunities',
    '$.subheading', 'Article Connect helps students discover articleship, internship, and training opportunities with trusted firms and companies.',
    '$.subtitle', 'Trusted by Leading Firms & Growing Companies',
    '$.feature_one', 'Verified Openings',
    '$.feature_two', 'Articleship & Internship Roles',
    '$.feature_three', 'Trusted Opportunities'
)
WHERE data_keys = 'banner.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE ABOUT CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'About Article Connect',
    '$.content', 'Article Connect is a dedicated platform designed to help students and CA aspirants connect with CA firms, companies, and training organizations for articleship, internship, and early-career opportunities. We simplify the discovery, application, and hiring process with a clean and practical workflow.'
)
WHERE data_keys = 'about.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE WHY CHOOSE CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Why Choose Article Connect',
    '$.subheading', 'A platform designed for students, CA aspirants, firms, and companies'
)
WHERE data_keys = 'why_choose.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE HOW WORK CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'How Article Connect Works',
    '$.subheading', 'Simple steps to connect students with the right opportunities'
)
WHERE data_keys = 'how_work.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE FIND TASK CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Find Your Perfect Articleship Opportunity',
    '$.subheading', 'Discover articleship and internship opportunities tailored to your skills and career goals',
    '$.button_name', 'Explore Opportunities'
)
WHERE data_keys = 'find_task.content' 
AND tempname = 'basic';

-- Update find_task.element if it has old content
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.find_step', 'Access verified articleship and internship opportunities'
)
WHERE data_keys = 'find_task.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.find_step') LIKE '%talent%';

-- ============================================
-- UPDATE FACILITY CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Why Students Choose Article Connect',
    '$.subheading', 'Features designed to help students find the right opportunities'
)
WHERE data_keys = 'facility.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE COMPLETION WORK CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Success Stories',
    '$.subheading', 'Students and firms achieving success through Article Connect'
)
WHERE data_keys = 'completion_work.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE TESTIMONIAL CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'What Our Students Say',
    '$.subheading', 'Hear from students who found their perfect articleship and internship opportunities'
)
WHERE data_keys = 'testimonial.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE TOP FREELANCER CONTENT (Top Students)
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Top Students',
    '$.subheading', 'Meet talented students who have excelled in their articleship and internship programs'
)
WHERE data_keys = 'top_freelancer.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE COUNTER ELEMENTS
-- ============================================

-- Replace "Jobs" with "Opportunities" in counter content
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', REPLACE(JSON_EXTRACT(data_values, '$.content'), 'Jobs', 'Opportunities')
)
WHERE data_keys = 'counter.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.content') LIKE '%Job%';

-- Replace "freelancers" with "students" in counter content
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', REPLACE(JSON_EXTRACT(data_values, '$.content'), 'freelancers', 'students')
)
WHERE data_keys = 'counter.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.content') LIKE '%freelancer%';

-- ============================================
-- UPDATE SUBSCRIBE CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Stay Updated',
    '$.subheading', 'Get notified about new articleship and internship opportunities'
)
WHERE data_keys = 'subscribe.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE SUPPORT CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Need Help?',
    '$.subheading', 'Our support team is here to assist you'
)
WHERE data_keys = 'support.content' 
AND tempname = 'basic';

-- ============================================
-- VERIFICATION
-- ============================================

SELECT 'All homepage content updated!' as Status;
