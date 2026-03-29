USE article;

-- Update About Content (ID: 24)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'About Article Connect',
    '$.content', 'Article Connect is a modern platform built to connect students, firms, and organizations through articleship, internship, and career opportunities. We help students discover meaningful training opportunities and assist firms in finding talented young professionals.'
)
WHERE id = 24 AND data_keys = 'about.content';

-- Check if there's a 4th counter element and add/update it
-- First, let's see what counter IDs exist
-- If we need to add a 4th counter, we'll do it separately

-- Update any remaining content that needs Article Connect branding
-- Verify all updates
SELECT 'About' as section, id, JSON_EXTRACT(data_values, '$.heading') as heading FROM frontends WHERE id = 24
UNION ALL
SELECT 'Banner', id, JSON_EXTRACT(data_values, '$.heading') FROM frontends WHERE id = 64
UNION ALL
SELECT 'Why Choose', id, JSON_EXTRACT(data_values, '$.heading') FROM frontends WHERE id = 79
UNION ALL
SELECT 'How Work', id, JSON_EXTRACT(data_values, '$.heading') FROM frontends WHERE id = 73;
