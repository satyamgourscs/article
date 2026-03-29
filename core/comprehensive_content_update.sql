-- Comprehensive Content Update for Article Connect
-- Replace all remaining freelance marketplace terminology

USE article_base;

-- ============================================
-- UPDATE FAQ ELEMENTS
-- ============================================

-- Update FAQ that mentions "Olance" or "jobs"
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.question', 'What types of opportunities are available on Article Connect?',
    '$.answer', 'Article Connect offers a wide range of opportunities including CA articleship, finance internships, audit training, taxation support, accounts internships, and compliance training, catering to various student needs and career goals.'
)
WHERE data_keys = 'faq.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.question') LIKE '%Olance%';

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.question', 'How can I increase my chances of getting selected?',
    '$.answer', 'To increase your chances, maintain a complete and professional profile, submit tailored applications for each opportunity, and gather positive reviews from firms to build your reputation.'
)
WHERE data_keys = 'faq.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.question') LIKE '%increase my chances%';

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.question', 'How to apply for opportunities?',
    '$.answer', 'Look for opportunities that match your skills and interests, consider the firm\'s requirements and feedback history, and assess the number of applications already submitted to gauge competition.'
)
WHERE data_keys = 'faq.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.question') LIKE '%bid%';

-- ============================================
-- UPDATE FACILITY ELEMENTS
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Higher Quality Listings',
    '$.content', 'We ensure that our opportunity listings are of the highest quality. Each listing is thoroughly vetted to ensure it meets our standards, providing you with the best opportunities to showcase your skills and expertise.'
)
WHERE data_keys = 'facility.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Higher Quality%';

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Unlimited Opportunity Search Resources',
    '$.content', 'With Article Connect, you access unlimited opportunity search resources. Use advanced filters, personalized opportunity recommendations, and comprehensive listings to find the best match for your skills & goals.'
)
WHERE data_keys = 'facility.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Unlimited Job%';

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Save Time',
    '$.content', 'Our streamlined application process helps you save time. Apply to multiple opportunities quickly and efficiently with our user-friendly platform.'
)
WHERE data_keys = 'facility.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Save Time%';

-- ============================================
-- UPDATE TESTIMONIAL ELEMENTS
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.quote', 'I\'ve saved so much time using this platform. The advanced search filters and personalized opportunity recommendations are fantastic.'
)
WHERE data_keys = 'testimonial.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.quote') LIKE '%job recommendations%';

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.quote', 'The platform is incredibly easy to use. Posting opportunities, communicating with students, and managing assignments has never been simpler.'
)
WHERE data_keys = 'testimonial.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.quote') LIKE '%Posting jobs%';

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.quote', 'The quality of students on this platform is exceptional. I\'ve hired top-rated professionals who delivered outstanding results.'
)
WHERE data_keys = 'testimonial.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.quote') LIKE '%quality of students%';

-- ============================================
-- UPDATE BLOG ELEMENTS (if they mention freelance)
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', REPLACE(REPLACE(JSON_EXTRACT(data_values, '$.title'), 'Freelance', 'Student'), 'freelance', 'student')
)
WHERE data_keys = 'blog.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Freelance%';

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.description', REPLACE(REPLACE(JSON_EXTRACT(data_values, '$.description'), 'freelance', 'student'), 'Freelance', 'Student')
)
WHERE data_keys = 'blog.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.description') LIKE '%freelance%';

-- ============================================
-- VERIFICATION
-- ============================================

SELECT 'CMS content updated!' as Status;
