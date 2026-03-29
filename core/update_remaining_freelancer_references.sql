USE article;

-- Update remaining testimonial and counter elements with visible "freelancer" text

-- Testimonial Element (ID: 101) - Fix quote with escaped apostrophe
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.quote', 'The quality of students on this platform is exceptional. I''ve hired top-rated professionals who delivered outstanding results.'
)
WHERE id = 101 AND data_keys = 'testimonial.element';

-- Counter Element (ID: 106)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', 'Every year earned by top students earning over $7,000/m'
)
WHERE id = 106 AND data_keys = 'counter.element';

-- Counter Element (ID: 107)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', 'Find task a student, with 90% of assignments completed in 7 days'
)
WHERE id = 107 AND data_keys = 'counter.element';

-- Verify updates
SELECT 'Testimonial 101' as section, id, JSON_EXTRACT(data_values, '$.quote') as content FROM frontends WHERE id = 101
UNION ALL
SELECT 'Counter 106', id, JSON_EXTRACT(data_values, '$.content') FROM frontends WHERE id = 106
UNION ALL
SELECT 'Counter 107', id, JSON_EXTRACT(data_values, '$.content') FROM frontends WHERE id = 107;
