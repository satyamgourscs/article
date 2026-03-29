-- SQL Script to Update Remaining CMS Content for Article Connect Platform
-- This updates element content that still contains old marketplace terminology
-- Database: article_base
-- Table: frontends

USE article_base;

-- Update How Work Element: "Hire Freelancers"
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Hire Students',
    '$.content', 'Browse through our extensive list of talented students, review their profiles, and find the perfect match for your articleship or internship opportunity.'
)
WHERE data_keys = 'how_work.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Hire Freelancer%';

-- Update Why Choose Elements
-- "No Cost Until You Hire"
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'No Cost Until You Hire',
    '$.content', 'Enjoy our platform with zero upfront costs. You only pay when you hire a student and the work is completed hassle-free.'
)
WHERE data_keys = 'why_choose.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%No Cost Until You Hire%';

-- "Post Job & Hire a Pro"
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Post Opportunity & Hire Students',
    '$.content', 'Firms can easily post opportunities and hire talented students. Provide detailed requirements and attract applications from qualified candidates.'
)
WHERE data_keys = 'why_choose.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Post Job & Hire%';

-- "Bid to Find Jobs"
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Apply for Opportunities',
    '$.content', 'Students can apply for articleship and internship opportunities. Showcase your skills, submit applications, and secure positions that match your career goals.'
)
WHERE data_keys = 'why_choose.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Bid to Find Jobs%';

-- "Top Rated"
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Top Rated',
    '$.content', 'We host top-rated students who excel in their fields. Browse profiles and review ratings to find the best talent for your firm.'
)
WHERE data_keys = 'why_choose.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Top Rated%'
AND JSON_EXTRACT(data_values, '$.content') LIKE '%freelancer%';

-- Update Counter Elements
-- "Top Rated freelancers"
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', 'Top Rated students, covering 8,766 skills'
)
WHERE data_keys = 'counter.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.content') LIKE '%Top Rated freelancer%';

-- "Every year earned by top freelancers"
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', 'Every year earned by top students earning over $7,000/m'
)
WHERE data_keys = 'counter.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.content') LIKE '%earned by top freelancer%';

-- "Find task a freelancer"
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', 'Find opportunities for students, with 90% of assignments completed in 7 days'
)
WHERE data_keys = 'counter.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.content') LIKE '%Find task a freelancer%';

-- Update About Elements
-- "Find a Freelancer and Hire Top Talent"
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Find Students and Hire Top Talent',
    '$.content', 'To find top talent for your articleship or internship opportunity, explore our extensive student database.'
)
WHERE data_keys = 'about.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Find a Freelancer%';

-- "Find a Job and Top Matches Buyer"
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Find Opportunities and Top Matches',
    '$.content', 'Discover the perfect articleship and internship opportunities tailored to your skills and career goals.'
)
WHERE data_keys = 'about.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Find a Job%';

-- Update Completion Work Element
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.done_step', 'Get matched with talented students in minutes'
)
WHERE data_keys = 'completion_work.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.done_step') LIKE '%freelancer%';

-- Update Testimonial Elements
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.quote', REPLACE(JSON_EXTRACT(data_values, '$.quote'), 'freelancers', 'students')
)
WHERE data_keys = 'testimonial.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.quote') LIKE '%freelancer%';

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.quote', REPLACE(JSON_EXTRACT(data_values, '$.quote'), 'clients', 'firms')
)
WHERE data_keys = 'testimonial.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.quote') LIKE '%client%';

-- Update Switching Button Content
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.freelancer_login_button', 'Login as Student',
    '$.buyer_login_button', 'Login as Firm',
    '$.freelancer_register_button', 'Join as Student',
    '$.buyer_register_button', 'Join as Firm'
)
WHERE data_keys = 'switching_button.content' 
AND tempname = 'basic';

-- Update Pages Table
UPDATE pages 
SET name = 'Find Opportunities'
WHERE slug = 'freelance-jobs' AND name = 'Find Job';

UPDATE pages 
SET name = 'Find Students'
WHERE slug = 'talents' AND name = 'Find Talent';
