USE article;

-- ============================================
-- PHASE 3: DATABASE/CMS CONTENT UPDATES
-- Remove all visible Freelancer/Buyer/Olance from CMS content
-- ============================================

-- 1. About Content (ID: 24)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Simple and Easy to Find Students or Great Opportunities'
)
WHERE id = 24 AND data_keys = 'about.content';

-- 2. How Work Content (ID: 73)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'It\'s Easy to Get Work Done',
    '$.subheading', 'Our platform connects firms with students to get work done efficiently and securely.'
)
WHERE id = 73 AND data_keys = 'how_work.content';

-- 3. How Work Element (ID: 75)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Hire Students',
    '$.content', 'Browse through our extensive list of students, review their profiles, and find the perfect match for your opportunity.'
)
WHERE id = 75 AND data_keys = 'how_work.element';

-- 4. Why Choose Element (ID: 81)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', 'Enjoy our platform with zero upfront costs. You only pay when you hire a student and paid if hassle free done your assignment.'
)
WHERE id = 81 AND data_keys = 'why_choose.element';

-- 5. Why Choose Element (ID: 83)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Post Opportunity & Hire a Pro',
    '$.content', 'Firms can easily post opportunities and hire professionals. Provide detailed opportunity requirements and attract applications from qualified students.'
)
WHERE id = 83 AND data_keys = 'why_choose.element';

-- 6. Why Choose Element (ID: 84)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Apply to Find Opportunities',
    '$.content', 'Students can apply for opportunities. Showcase your skills, submit applications, and secure assignments that match your expertise.'
)
WHERE id = 84 AND data_keys = 'why_choose.element';

-- 7. Why Choose Element (ID: 85)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', 'We host top-rated students who are experts in their fields. Browse profiles & review ratings to find the best talents.'
)
WHERE id = 85 AND data_keys = 'why_choose.element';

-- 8. Find Task Content (ID: 86)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.subheading', 'Unlock your potential and find work that matches your skills and expertise. Our platform is designed to connect talented students with firms who need their services.'
)
WHERE id = 86 AND data_keys = 'find_task.content';

-- 9. Completion Work Element (ID: 95)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.done_step', 'Get matched with expert students in minutes'
)
WHERE id = 95 AND data_keys = 'completion_work.element';

-- Verify updates
SELECT 'About' as section, id, JSON_EXTRACT(data_values, '$.heading') as content FROM frontends WHERE id = 24
UNION ALL
SELECT 'How Work Content', id, JSON_EXTRACT(data_values, '$.subheading') FROM frontends WHERE id = 73
UNION ALL
SELECT 'How Work Element', id, JSON_EXTRACT(data_values, '$.title') FROM frontends WHERE id = 75
UNION ALL
SELECT 'Why Choose 81', id, JSON_EXTRACT(data_values, '$.content') FROM frontends WHERE id = 81
UNION ALL
SELECT 'Why Choose 83', id, JSON_EXTRACT(data_values, '$.title') FROM frontends WHERE id = 83
UNION ALL
SELECT 'Why Choose 84', id, JSON_EXTRACT(data_values, '$.title') FROM frontends WHERE id = 84
UNION ALL
SELECT 'Find Task', id, JSON_EXTRACT(data_values, '$.subheading') FROM frontends WHERE id = 86
UNION ALL
SELECT 'Completion Work', id, JSON_EXTRACT(data_values, '$.done_step') FROM frontends WHERE id = 95;
