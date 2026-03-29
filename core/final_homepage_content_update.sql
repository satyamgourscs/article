-- Final Homepage Content Update for Article Connect
-- Update all remaining marketplace terminology to CA articleship/internship platform

USE article_base;

-- ============================================
-- UPDATE WHY_CHOOSE ELEMENTS
-- ============================================

-- Update "No Cost Until You Hire" to be more articleship-focused
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'No Upfront Costs',
    '$.content', 'Start exploring articleship and internship opportunities with zero upfront costs. Connect with firms and apply to positions that match your career goals.'
)
WHERE data_keys = 'why_choose.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') = 'No Cost Until You Hire';

-- Update "Top Rated" to be more student-focused
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Top Rated Students',
    '$.content', 'We showcase top-rated students who excel in their fields. Browse profiles and review ratings to find the best candidates for your articleship or internship program.'
)
WHERE data_keys = 'why_choose.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') = 'Top Rated';

-- ============================================
-- UPDATE FIND_TASK ELEMENTS
-- ============================================

-- Ensure find_task elements are articleship-focused
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.find_step', 'Access verified articleship and internship opportunities'
)
WHERE data_keys = 'find_task.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.find_step') NOT LIKE '%articleship%'
AND JSON_EXTRACT(data_values, '$.find_step') NOT LIKE '%internship%'
LIMIT 1;

-- ============================================
-- UPDATE GENERAL SETTINGS
-- ============================================

-- Update site name if it still has old branding
UPDATE general_settings 
SET site_name = 'Article Connect'
WHERE site_name LIKE '%Olance%' OR site_name LIKE '%Freelance%' OR site_name LIKE '%Marketplace%';

-- ============================================
-- VERIFICATION
-- ============================================

SELECT 'Homepage content updated!' as Status;
