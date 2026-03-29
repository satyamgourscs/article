-- Update Homepage Content for Article Connect
-- This script updates banner features, client/brand elements, and other homepage content

USE article_base;

-- ============================================
-- UPDATE BANNER CONTENT - Feature Badges
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.feature_one', 'Verified Openings',
    '$.feature_two', 'Articleship & Internship Roles',
    '$.feature_three', 'Trusted Opportunities'
)
WHERE data_keys = 'banner.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE BANNER CONTENT - Heading & Subheading (if needed)
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
-- UPDATE CLIENT/BRAND ELEMENTS - Company Names
-- ============================================

-- Note: The brand.element records only contain image references.
-- Company names would typically be in the image alt text or a separate field.
-- Since we can't change images, we'll update any text fields if they exist.
-- The actual company logos/images will remain unchanged as per requirements.

-- Check if there's a client.content or similar that has company names
-- If client.element has title/name fields, update them

-- For now, we'll create/update client.content if it exists, or note that
-- company names might be hardcoded in Blade templates or image filenames

-- ============================================
-- UPDATE FIND TASK ELEMENT (if it has old content)
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.find_step', 'Access verified articleship and internship opportunities'
)
WHERE data_keys = 'find_task.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.find_step') LIKE '%talent%';

-- ============================================
-- UPDATE COUNTER ELEMENTS (if they show old job counts)
-- ============================================

-- Update counter elements that mention "Jobs" to "Opportunities"
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', REPLACE(JSON_EXTRACT(data_values, '$.content'), 'Jobs', 'Opportunities')
)
WHERE data_keys = 'counter.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.content') LIKE '%Job%';

-- ============================================
-- VERIFICATION QUERY
-- ============================================

SELECT 'Homepage content updated successfully!' as Status;
SELECT data_keys, JSON_EXTRACT(data_values, '$.heading') as heading, 
       JSON_EXTRACT(data_values, '$.feature_one') as feature_one,
       JSON_EXTRACT(data_values, '$.feature_two') as feature_two,
       JSON_EXTRACT(data_values, '$.feature_three') as feature_three
FROM frontends 
WHERE data_keys = 'banner.content' AND tempname = 'basic';
