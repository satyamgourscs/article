USE article;

-- ============================================
-- PHASE 3: DATABASE/CMS BRANDING UPDATES
-- Replace legacy vendor branding with "Article Connect"
-- ============================================

-- 1. Update SEO Data (ID: 1)
-- Keywords, description, social_title, social_description
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.keywords', JSON_ARRAY('article connect', 'articleship', 'internship', 'ca firm', 'student opportunity', 'ca aspirant', 'nexa technologies'),
    '$.description', 'Article Connect is a dedicated platform connecting Students and CA Aspirants with CA Firms, Employers, and Corporate Offices for articleship, internship, industrial training, and early-career opportunities. With secure transactions, a user-friendly interface, and advanced opportunity management tools, Article Connect simplifies the connection between students and employers.',
    '$.social_title', 'Article Connect - CA Articleship & Internship Platform',
    '$.social_description', 'Article Connect is a dedicated platform connecting Students and CA Aspirants with CA Firms, Employers, and Corporate Offices for articleship, internship, industrial training, and early-career opportunities.'
)
WHERE id = 1 AND data_keys = 'seo.data';

-- 2. Update FAQ Element (ID: 65)
-- Question and answer
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.question', 'What types of opportunities are available on Article Connect?',
    '$.answer', 'Article Connect offers a wide range of opportunities including articleship positions, internships, industrial training programs, and entry-level positions at CA Firms, corporate offices, and employers across various industries.'
)
WHERE id = 65 AND data_keys = 'faq.element';

-- 3. Update Facility Content (ID: 90)
-- Facility heading/subheading refresh
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'How Article Connect is Different',
    '$.subheading', 'Discover the facilities, or benefits of using Article Connect for your articleship, internship, and hiring needs.'
)
WHERE id = 90 AND data_keys = 'facility.content';

-- 4. Update Facility Element (ID: 92)
-- Facility element body text
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', 'With Article Connect, you access unlimited opportunity search resources. Use advanced filters, personalized opportunity recommendations, and comprehensive listings to find the best match for your skills & goals.'
)
WHERE id = 92 AND data_keys = 'facility.element';

-- Verify updates
SELECT 'SEO Data' as section, id, data_keys, JSON_EXTRACT(data_values, '$.description') as description FROM frontends WHERE id = 1
UNION ALL
SELECT 'FAQ', id, data_keys, JSON_EXTRACT(data_values, '$.question') FROM frontends WHERE id = 65
UNION ALL
SELECT 'Facility Content', id, data_keys, JSON_EXTRACT(data_values, '$.heading') FROM frontends WHERE id = 90
UNION ALL
SELECT 'Facility Element', id, data_keys, JSON_EXTRACT(data_values, '$.content') FROM frontends WHERE id = 92;
