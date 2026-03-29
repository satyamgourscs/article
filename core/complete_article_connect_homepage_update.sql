USE article;

-- ============================================
-- COMPLETE ARTICLE CONNECT HOMEPAGE CONTENT UPDATE
-- ============================================

-- 1. BANNER/HERO SECTION (ID: 64)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Find the Right Articleship & Internship Opportunities',
    '$.subheading', 'Article Connect helps students discover articleship, internship, and training opportunities with trusted firms and companies.',
    '$.subtitle', 'Trusted by Leading Firms & Growing Companies',
    '$.feature_one', '1200+ Students',
    '$.feature_two', '150+ Opportunities',
    '$.feature_three', '60+ Partner Firms'
)
WHERE id = 64 AND data_keys = 'banner.content';

-- 2. WHY CHOOSE CONTENT (ID: 79)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Why Choose Article Connect',
    '$.subheading', 'Article Connect connects students with real articleship and internship opportunities from trusted firms and companies.'
)
WHERE id = 79 AND data_keys = 'why_choose.content';

-- 3. WHY CHOOSE ELEMENTS - Update all 6 feature cards
-- Element 1 (ID: 80) - Verified Opportunities
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Verified Opportunities',
    '$.content', 'Explore genuine articleship and internship openings from trusted firms and companies.'
)
WHERE id = 80 AND data_keys = 'why_choose.element';

-- Element 2 (ID: 81) - Student Profiles
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Student Profiles',
    '$.content', 'Build a professional student profile with education, skills, and career interests.'
)
WHERE id = 81 AND data_keys = 'why_choose.element';

-- Element 3 (ID: 82) - Easy Applications
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Easy Applications',
    '$.content', 'Apply to relevant opportunities quickly with a simple and guided process.'
)
WHERE id = 82 AND data_keys = 'why_choose.element';

-- Element 4 (ID: 83) - Trusted Firms
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Trusted Firms',
    '$.content', 'Connect with CA firms, finance teams, and growing businesses looking for young talent.'
)
WHERE id = 83 AND data_keys = 'why_choose.element';

-- Element 5 (ID: 84) - Career Growth
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Career Growth',
    '$.content', 'Find practical training opportunities that support long-term professional growth.'
)
WHERE id = 84 AND data_keys = 'why_choose.element';

-- Element 6 (ID: 85) - Smart Matching
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Smart Matching',
    '$.content', 'Discover opportunities based on your education, interests, and preferred role.'
)
WHERE id = 85 AND data_keys = 'why_choose.element';

-- 4. HOW IT WORKS CONTENT (ID: 73)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'How Article Connect Works',
    '$.subheading', 'Start your professional journey in three simple steps'
)
WHERE id = 73 AND data_keys = 'how_work.content';

-- 5. HOW IT WORKS ELEMENTS - Update 3 steps
-- Step 1 (ID: 74) - Create Your Student Profile
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Create Your Student Profile',
    '$.content', 'Build your professional profile with education details, skills, and career interests to showcase your potential.'
)
WHERE id = 74 AND data_keys = 'how_work.element';

-- Step 2 (ID: 75) - Explore Opportunities
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Explore Opportunities',
    '$.content', 'Browse articleship, internship, and training opportunities from verified firms and companies.'
)
WHERE id = 75 AND data_keys = 'how_work.element';

-- Step 3 (ID: 76) - Apply and Start Your Career
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Apply and Start Your Career',
    '$.content', 'Submit applications to relevant opportunities and begin your professional journey with trusted firms.'
)
WHERE id = 76 AND data_keys = 'how_work.element';

-- Step 4 (ID: 77) - Remove or update if needed
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Get Matched',
    '$.content', 'Connect with firms that match your profile and start your articleship or internship journey.'
)
WHERE id = 77 AND data_keys = 'how_work.element';

-- 6. COUNTER ELEMENTS - Update to match requirements
-- Counter 1 (ID: 105) - Students Registered
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', 'Students Registered'
)
WHERE id = 105 AND data_keys = 'counter.element';

-- Counter 2 (ID: 106) - Opportunities Posted
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', 'Opportunities Posted'
)
WHERE id = 106 AND data_keys = 'counter.element';

-- Counter 3 (ID: 107) - Partner Firms
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', 'Partner Firms'
)
WHERE id = 107 AND data_keys = 'counter.element';

-- Check if there's a 4th counter element for Successful Placements
-- If exists, update it; if not, we'll note it

-- 7. TOP STUDENTS CONTENT (ID: 104) - Already updated, verify
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Top Students',
    '$.subheading', 'Discover talented students who excel in their fields and deliver exceptional results.'
)
WHERE id = 104 AND data_keys = 'top_freelancer.content';

-- Verify updates
SELECT 'Banner' as section, id, JSON_EXTRACT(data_values, '$.heading') as heading FROM frontends WHERE id = 64
UNION ALL
SELECT 'Why Choose', id, JSON_EXTRACT(data_values, '$.heading') FROM frontends WHERE id = 79
UNION ALL
SELECT 'How Work', id, JSON_EXTRACT(data_values, '$.heading') FROM frontends WHERE id = 73
UNION ALL
SELECT 'Top Students', id, JSON_EXTRACT(data_values, '$.heading') FROM frontends WHERE id = 104;
