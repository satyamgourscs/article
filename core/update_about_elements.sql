-- Update About Elements that mention Upwork or old marketplace content

USE article_base;

-- Update about.element that mentions "Upwork" or "Work without breaking the bank"
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Affordable Opportunities',
    '$.content', 'Find quality articleship and internship opportunities that fit your budget and career goals.'
)
WHERE data_keys = 'about.element' 
AND tempname = 'basic' 
AND (JSON_EXTRACT(data_values, '$.content') LIKE '%Upwork%' 
     OR JSON_EXTRACT(data_values, '$.title') LIKE '%breaking the bank%');

-- ============================================
-- VERIFICATION
-- ============================================

SELECT 'About elements updated!' as Status;
