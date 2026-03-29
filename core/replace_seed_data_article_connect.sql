-- Replace Old Freelance Seed/CMS Data with Article Connect Content
-- This script replaces all old marketplace content with Article Connect-specific content

USE article_base;

-- ============================================
-- UPDATE BANNER/HERO CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Find the Right Articleship & Internship Opportunities',
    '$.subheading', 'Article Connect helps students discover articleship, internship, and training opportunities with trusted firms and companies.',
    '$.subtitle', 'Trusted by Leading Firms & Growing Companies',
    '$.feature_one', 'Verified Openings',
    '$.feature_two', 'Articleship Roles',
    '$.feature_three', 'Internship Opportunities'
)
WHERE data_keys = 'banner.content' 
AND tempname = 'basic';

-- ============================================
-- CREATE/UPDATE CATEGORIES
-- ============================================

-- Delete old categories if they exist and don't match Article Connect
-- Then create new Article Connect categories

-- Articleship
INSERT INTO categories (name, status, created_at, updated_at)
SELECT 'Articleship', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Articleship');

-- Finance Internship
INSERT INTO categories (name, status, created_at, updated_at)
SELECT 'Finance Internship', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Finance Internship');

-- Audit Training
INSERT INTO categories (name, status, created_at, updated_at)
SELECT 'Audit Training', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Audit Training');

-- Taxation Support
INSERT INTO categories (name, status, created_at, updated_at)
SELECT 'Taxation Support', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Taxation Support');

-- Accounts Internship
INSERT INTO categories (name, status, created_at, updated_at)
SELECT 'Accounts Internship', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Accounts Internship');

-- Compliance Internship
INSERT INTO categories (name, status, created_at, updated_at)
SELECT 'Compliance Internship', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Compliance Internship');

-- Get category IDs
SET @cat_articleship_id = (SELECT id FROM categories WHERE name = 'Articleship' LIMIT 1);
SET @cat_finance_id = (SELECT id FROM categories WHERE name = 'Finance Internship' LIMIT 1);
SET @cat_audit_id = (SELECT id FROM categories WHERE name = 'Audit Training' LIMIT 1);
SET @cat_taxation_id = (SELECT id FROM categories WHERE name = 'Taxation Support' LIMIT 1);
SET @cat_accounts_id = (SELECT id FROM categories WHERE name = 'Accounts Internship' LIMIT 1);
SET @cat_compliance_id = (SELECT id FROM categories WHERE name = 'Compliance Internship' LIMIT 1);

-- ============================================
-- CREATE/UPDATE SUBCATEGORIES
-- ============================================

-- CA Articleship (under Articleship)
INSERT INTO subcategories (category_id, name, status, created_at, updated_at)
SELECT @cat_articleship_id, 'CA Articleship', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM subcategories WHERE name = 'CA Articleship' AND category_id = @cat_articleship_id);

-- Finance & Accounts (under Finance Internship)
INSERT INTO subcategories (category_id, name, status, created_at, updated_at)
SELECT @cat_finance_id, 'Finance & Accounts', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM subcategories WHERE name = 'Finance & Accounts' AND category_id = @cat_finance_id);

-- Statutory Audit (under Audit Training)
INSERT INTO subcategories (category_id, name, status, created_at, updated_at)
SELECT @cat_audit_id, 'Statutory Audit', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM subcategories WHERE name = 'Statutory Audit' AND category_id = @cat_audit_id);

-- GST Compliance (under Taxation Support)
INSERT INTO subcategories (category_id, name, status, created_at, updated_at)
SELECT @cat_taxation_id, 'GST Compliance', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM subcategories WHERE name = 'GST Compliance' AND category_id = @cat_taxation_id);

-- Bookkeeping (under Accounts Internship)
INSERT INTO subcategories (category_id, name, status, created_at, updated_at)
SELECT @cat_accounts_id, 'Bookkeeping', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM subcategories WHERE name = 'Bookkeeping' AND category_id = @cat_accounts_id);

-- Regulatory Compliance (under Compliance Internship)
INSERT INTO subcategories (category_id, name, status, created_at, updated_at)
SELECT @cat_compliance_id, 'Regulatory Compliance', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM subcategories WHERE name = 'Regulatory Compliance' AND category_id = @cat_compliance_id);

-- ============================================
-- UPDATE ACCOUNT CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.freelancer_title', 'Join as a Student',
    '$.freelancer_content', 'Create your student profile and start exploring articleship opportunities, internships, and training programs with top CA firms and companies.',
    '$.freelancer_button_name', 'Register as Student',
    '$.buyer_title', 'Join as a Firm / Company',
    '$.buyer_content', 'Post articleship opportunities, internships, and training programs. Connect with talented students and build your team.',
    '$.buyer_button_name', 'Register as Firm'
)
WHERE data_keys = 'account.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE FIND TASK CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Find Your Perfect Articleship Opportunity',
    '$.subheading', 'Discover articleship and internship opportunities tailored to your skills and career goals',
    '$.button_name', 'Explore Opportunities'
)
WHERE data_keys = 'find_task.content' 
AND tempname = 'basic';

-- Update find_task.element
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.find_step', 'Access verified articleship and internship opportunities'
)
WHERE data_keys = 'find_task.element' 
AND tempname = 'basic';

-- ============================================
-- UPDATE HOW WORK CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'How Article Connect Works',
    '$.subheading', 'Simple steps to connect students with the right opportunities'
)
WHERE data_keys = 'how_work.content' 
AND tempname = 'basic';

-- Update how_work.element with new feature cards
-- Feature 1: Verified Opportunities
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Verified Opportunities',
    '$.content', 'Browse through verified articleship and internship opportunities from trusted firms and companies.'
)
WHERE data_keys = 'how_work.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Post%'
LIMIT 1;

-- Feature 2: Student Profiles
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Student Profiles',
    '$.content', 'Students create detailed profiles showcasing their skills, qualifications, and career goals.'
)
WHERE data_keys = 'how_work.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Hire%'
LIMIT 1;

-- Feature 3: Easy Applications
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Easy Applications',
    '$.content', 'Students can easily apply for opportunities with a simple application process.'
)
WHERE data_keys = 'how_work.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Get Work%'
LIMIT 1;

-- Feature 4: Trusted Firms
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Trusted Firms',
    '$.content', 'Connect with verified CA firms and companies offering quality training and career opportunities.'
)
WHERE data_keys = 'how_work.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Make Secure%'
LIMIT 1;

-- ============================================
-- UPDATE WHY CHOOSE CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Why Choose Article Connect',
    '$.subheading', 'A platform designed for students, CA aspirants, firms, and companies'
)
WHERE data_keys = 'why_choose.content' 
AND tempname = 'basic';

-- Update why_choose.element with new features
-- Feature 1: Verified Opportunities
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Verified Opportunities',
    '$.content', 'All opportunities are verified to ensure quality and authenticity for students.'
)
WHERE data_keys = 'why_choose.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Proof%'
LIMIT 1;

-- Feature 2: No Cost Until You Hire
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'No Cost Until You Hire',
    '$.content', 'Enjoy our platform with zero upfront costs. You only pay when you hire a student and the work is completed hassle-free.'
)
WHERE data_keys = 'why_choose.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%No Cost%'
LIMIT 1;

-- Feature 3: Post Opportunity & Hire Students
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Post Opportunity & Hire Students',
    '$.content', 'Firms can easily post opportunities and hire talented students. Provide detailed requirements and attract applications from qualified candidates.'
)
WHERE data_keys = 'why_choose.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Post Opportunity%'
LIMIT 1;

-- Feature 4: Apply for Opportunities
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Apply for Opportunities',
    '$.content', 'Students can apply for articleship and internship opportunities. Showcase your skills, submit applications, and secure positions that match your career goals.'
)
WHERE data_keys = 'why_choose.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Apply%'
LIMIT 1;

-- Feature 5: Career Growth
INSERT INTO frontends (tempname, data_keys, data_values, created_at, updated_at)
SELECT 'basic', 'why_choose.element', 
       JSON_OBJECT(
           'has_image', '1',
           'title', 'Career Growth',
           'content', 'Build your career with structured articleship and internship programs designed for professional development.',
           'image', '67d92d169e8d61742286102.png'
       ),
       NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM frontends 
    WHERE tempname = 'basic' 
    AND data_keys = 'why_choose.element' 
    AND JSON_EXTRACT(data_values, '$.title') = 'Career Growth'
)
LIMIT 1;

-- Feature 6: Smart Matching
INSERT INTO frontends (tempname, data_keys, data_values, created_at, updated_at)
SELECT 'basic', 'why_choose.element',
       JSON_OBJECT(
           'has_image', '1',
           'title', 'Smart Matching',
           'content', 'Our platform matches students with opportunities based on skills, location, and career goals.',
           'image', '67d92d210a8a91742286113.png'
       ),
       NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM frontends 
    WHERE tempname = 'basic' 
    AND data_keys = 'why_choose.element' 
    AND JSON_EXTRACT(data_values, '$.title') = 'Smart Matching'
)
LIMIT 1;

-- ============================================
-- UPDATE ABOUT CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'About Article Connect',
    '$.content', 'Article Connect is a modern platform built to connect students, firms, and organizations through articleship, internship, and career opportunities.'
)
WHERE data_keys = 'about.content' 
AND tempname = 'basic';

-- Update about.element
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Find Students and Hire Top Talent',
    '$.content', 'To find top talent for your articleship or internship opportunity, explore our extensive student database.'
)
WHERE data_keys = 'about.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Find Students%'
LIMIT 1;

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Find Opportunities and Top Matches',
    '$.content', 'Discover the perfect articleship and internship opportunities tailored to your skills and career goals.'
)
WHERE data_keys = 'about.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Find a Job%'
LIMIT 1;

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Affordable Opportunities',
    '$.content', 'Find quality articleship and internship opportunities that fit your budget and career goals.'
)
WHERE data_keys = 'about.element' 
AND tempname = 'basic' 
AND (JSON_EXTRACT(data_values, '$.title') LIKE '%breaking%' OR JSON_EXTRACT(data_values, '$.content') LIKE '%Upwork%')
LIMIT 1;

-- ============================================
-- UPDATE FACILITY CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Why Students Choose Article Connect',
    '$.subheading', 'Features designed to help students find the right opportunities'
)
WHERE data_keys = 'facility.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE COMPLETION WORK CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Success Stories',
    '$.subheading', 'Students and firms achieving success through Article Connect'
)
WHERE data_keys = 'completion_work.content' 
AND tempname = 'basic';

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.done_step', 'Get matched with talented students in minutes'
)
WHERE data_keys = 'completion_work.element' 
AND tempname = 'basic';

-- ============================================
-- UPDATE TESTIMONIAL CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'What Our Students Say',
    '$.subheading', 'Hear from students who found their perfect articleship and internship opportunities'
)
WHERE data_keys = 'testimonial.content' 
AND tempname = 'basic';

-- Update testimonial.element to replace "freelancers" with "students" and "clients" with "firms"
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.quote', REPLACE(REPLACE(JSON_EXTRACT(data_values, '$.quote'), 'freelancers', 'students'), 'clients', 'firms')
)
WHERE data_keys = 'testimonial.element' 
AND tempname = 'basic' 
AND (JSON_EXTRACT(data_values, '$.quote') LIKE '%freelancer%' OR JSON_EXTRACT(data_values, '$.quote') LIKE '%client%');

-- ============================================
-- UPDATE TOP FREELANCER CONTENT (Top Students)
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Top Students',
    '$.subheading', 'Meet talented students who have excelled in their articleship and internship programs'
)
WHERE data_keys = 'top_freelancer.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE COUNTER ELEMENTS
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.content', REPLACE(REPLACE(JSON_EXTRACT(data_values, '$.content'), 'Jobs', 'Opportunities'), 'freelancers', 'students')
)
WHERE data_keys = 'counter.element' 
AND tempname = 'basic' 
AND (JSON_EXTRACT(data_values, '$.content') LIKE '%Job%' OR JSON_EXTRACT(data_values, '$.content') LIKE '%freelancer%');

-- ============================================
-- UPDATE SUBSCRIBE CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Stay Updated',
    '$.subheading', 'Get notified about new articleship and internship opportunities'
)
WHERE data_keys = 'subscribe.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE SUPPORT CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Need Help?',
    '$.subheading', 'Our support team is here to assist you'
)
WHERE data_keys = 'support.content' 
AND tempname = 'basic';

-- ============================================
-- UPDATE BRAND CONTENT
-- ============================================

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Trusted by Leading Firms'
)
WHERE data_keys = 'brand.content' 
AND tempname = 'basic';

-- ============================================
-- VERIFICATION
-- ============================================

SELECT 'Seed data replacement completed!' as Status;
SELECT COUNT(*) as categories_created FROM categories WHERE name IN ('Articleship', 'Finance Internship', 'Audit Training', 'Taxation Support', 'Accounts Internship', 'Compliance Internship');
SELECT COUNT(*) as cms_sections_updated FROM frontends WHERE tempname = 'basic' AND data_keys IN ('banner.content', 'account.content', 'find_task.content', 'how_work.content', 'why_choose.content', 'about.content', 'facility.content', 'completion_work.content', 'testimonial.content', 'top_freelancer.content', 'subscribe.content', 'support.content', 'brand.content');
