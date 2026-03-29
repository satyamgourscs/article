-- =====================================================
-- COMPLETE ARTICLE CONNECT CONVERSION SCRIPT
-- Date: March 15, 2026
-- Project: Article Connect Platform
-- =====================================================
-- This script completes the full conversion from freelance marketplace to Article Connect
-- PHASE 1: Update CMS/Frontend Content
-- PHASE 2: Create Realistic Demo Firms
-- PHASE 3: Create Realistic Demo Students
-- PHASE 4: Create Realistic Opportunities
-- PHASE 5: Create Realistic Applications
-- =====================================================

USE article_base;

-- =====================================================
-- PHASE 1: UPDATE CMS/FRONTEND CONTENT
-- =====================================================

-- Update banner content (already done, but ensuring it's correct)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Find the Right Articleship & Internship Opportunities',
    '$.subheading', 'Article Connect helps students discover articleship, internship, and training opportunities with trusted firms and companies.',
    '$.subtitle', 'Connect with trusted CA firms, companies, and training opportunities designed for students and career starters.'
)
WHERE tempname = 'basic' AND data_keys = 'banner.content';

-- Update account section (signup cards)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.freelancer_title', 'Sign Up as a Student',
    '$.freelancer_content', 'Build your profile, apply for articleship and internship opportunities, and start your professional journey.',
    '$.freelancer_button_name', 'Create Student Account',
    '$.buyer_title', 'Sign Up as a Firm',
    '$.buyer_content', 'Post articleship and internship opportunities, connect with talented students, and build your team.',
    '$.buyer_button_name', 'Create Firm Account'
)
WHERE tempname = 'basic' AND data_keys = 'account.content';

-- Update facility section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'How Article Connect is Different',
    '$.subheading', 'Discover the features and benefits of using Article Connect for articleship, internship, and student opportunity needs.'
)
WHERE tempname = 'basic' AND data_keys = 'facility.content';

-- Update top freelancer section (Top Students)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Top Students',
    '$.subheading', 'Meet talented students who have excelled in their articleship and internship programs'
)
WHERE tempname = 'basic' AND data_keys = 'top_freelancer.content';

-- Update why choose section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Why Choose Article Connect',
    '$.subheading', 'A platform designed for students, CA aspirants, firms, and companies'
)
WHERE tempname = 'basic' AND data_keys = 'why_choose.content';

-- Update how work section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'How Article Connect Works',
    '$.subheading', 'Simple steps to connect students with the right opportunities'
)
WHERE tempname = 'basic' AND data_keys = 'how_work.content';

-- Update find task section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Find Your Perfect Articleship Opportunity',
    '$.subheading', 'Discover articleship and internship opportunities tailored to your skills and career goals'
)
WHERE tempname = 'basic' AND data_keys = 'find_task.content';

-- Update counter section (stats)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Article Connect by the Numbers',
    '$.subheading', 'Join thousands of students and firms connecting through articleship and internship opportunities'
)
WHERE tempname = 'basic' AND data_keys = 'counter.content' AND id IN (SELECT id FROM (SELECT id FROM frontends WHERE tempname = 'basic' AND data_keys = 'counter.content' ORDER BY id DESC LIMIT 1) AS t);

-- Update testimonial section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'What Our Students Say',
    '$.subheading', 'Hear from students who found their perfect articleship and internship opportunities'
)
WHERE tempname = 'basic' AND data_keys = 'testimonial.content';

-- Update about section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'About Article Connect',
    '$.subheading', 'Article Connect is a modern platform built to connect students, firms, and organizations through articleship, internship, and career opportunities.'
)
WHERE tempname = 'basic' AND data_keys = 'about.content';

-- Update contact section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Get in Touch',
    '$.subheading', 'Have questions about articleship or internship opportunities? We are here to help.'
)
WHERE tempname = 'basic' AND data_keys = 'contact_us.content';

-- =====================================================
-- PHASE 2: CREATE REALISTIC DEMO FIRMS (BUYERS)
-- =====================================================

-- Delete existing demo buyers if they exist (optional - comment out if you want to keep existing)
-- DELETE FROM buyers WHERE email IN ('careers@sharmaagrawalca.in', 'hr@nexacorporate.in');

-- Firm 1: Sharma & Agrawal Chartered Accountants
INSERT INTO buyers (
    firstname, lastname, username, email, dial_code, mobile,
    password, country_name, country_code, city, state, zip, address,
    status, ev, sv, kv, profile_complete, created_at, updated_at
) VALUES (
    'Sharma', '& Agrawal CA', 'sharmaagrawalca', 'careers@sharmaagrawalca.in', '+91', '9876543210',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- demo123
    'India', 'IN', 'Bhopal', 'Madhya Pradesh', '462011',
    '204, Business Square, MP Nagar Zone 1, Bhopal',
    1, 1, 1, 1, 1, NOW(), NOW()
);

-- Firm 2: Nexa Corporate Advisory Services Pvt. Ltd.
INSERT INTO buyers (
    firstname, lastname, username, email, dial_code, mobile,
    password, country_name, country_code, city, state, zip, address,
    status, ev, sv, kv, profile_complete, created_at, updated_at
) VALUES (
    'Nexa Corporate', 'Advisory', 'nexacorporate', 'hr@nexacorporate.in', '+91', '9827012345',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- demo123
    'India', 'IN', 'Indore', 'Madhya Pradesh', '452001',
    '5th Floor, Horizon Tower, Vijay Nagar, Indore',
    1, 1, 1, 1, 1, NOW(), NOW()
);

-- =====================================================
-- PHASE 3: CREATE REALISTIC DEMO STUDENTS (USERS)
-- =====================================================

-- Delete existing demo users if they exist (optional)
-- DELETE FROM users WHERE email IN ('aarav.jain21@gmail.com', 'priyamehta.acads@gmail.com');

-- Student 1: Aarav Jain
INSERT INTO users (
    firstname, lastname, username, email, dial_code, mobile,
    password, country_name, country_code, city, state, zip, address,
    status, ev, sv, kv, profile_complete, about, tagline, created_at, updated_at
) VALUES (
    'Aarav', 'Jain', 'aaravjain', 'aarav.jain21@gmail.com', '+91', '9111223344',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- demo123
    'India', 'IN', 'Bhopal', 'Madhya Pradesh', '462001',
    'Bhopal, Madhya Pradesh',
    1, 1, 1, 1, 1,
    'Commerce student actively seeking articleship opportunities in audit, taxation, and compliance. Completed B.Com Final Year and currently pursuing CA Intermediate.',
    'CA Intermediate | B.Com Final Year | Seeking Articleship',
    NOW(), NOW()
);

-- Student 2: Priya Mehta
INSERT INTO users (
    firstname, lastname, username, email, dial_code, mobile,
    password, country_name, country_code, city, state, zip, address,
    status, ev, sv, kv, profile_complete, about, tagline, created_at, updated_at
) VALUES (
    'Priya', 'Mehta', 'priyamehta', 'priyamehta.acads@gmail.com', '+91', '9098665544',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- demo123
    'India', 'IN', 'Indore', 'Madhya Pradesh', '452001',
    'Indore, Madhya Pradesh',
    1, 1, 1, 1, 1,
    'Motivated student looking for internship and training opportunities in finance, accounts, and reporting. BBA Finance graduate pursuing CMA Inter.',
    'BBA Finance | CMA Inter | Finance Internship Seeker',
    NOW(), NOW()
);

-- =====================================================
-- PHASE 4: CREATE REALISTIC OPPORTUNITIES (JOBS)
-- =====================================================

-- Get buyer IDs (assuming they were just created or exist)
SET @sharma_buyer_id = (SELECT id FROM buyers WHERE email = 'careers@sharmaagrawalca.in' LIMIT 1);
SET @nexa_buyer_id = (SELECT id FROM buyers WHERE email = 'hr@nexacorporate.in' LIMIT 1);

-- Get category IDs
SET @articleship_cat_id = (SELECT id FROM categories WHERE name = 'Articleship' LIMIT 1);
SET @internship_cat_id = (SELECT id FROM categories WHERE name = 'Internship' LIMIT 1);

-- Get subcategory IDs
SET @ca_articleship_subcat_id = (SELECT id FROM subcategories WHERE name = 'CA Articleship' LIMIT 1);
SET @finance_subcat_id = (SELECT id FROM subcategories WHERE name LIKE '%Finance%' LIMIT 1);

-- Opportunity 1: CA Articleship Trainee
INSERT INTO jobs (
    buyer_id, title, slug, category_id, subcategory_id,
    budget, custom_budget, description,
    project_scope, job_longevity, skill_level,
    status, is_approved, deadline, created_at, updated_at
) VALUES (
    @sharma_buyer_id,
    'CA Articleship Trainee',
    'ca-articleship-trainee',
    COALESCE(@articleship_cat_id, 1),
    COALESCE(@ca_articleship_subcat_id, 1),
    6000.00, 0,
    'We are looking for dedicated CA Intermediate students to join our articleship program. The role involves hands-on experience in audit, GST compliance, taxation, and statutory compliance work. You will work directly with senior CAs on real client projects, learn accounting software (Tally Prime), and gain practical knowledge in tax filing, audit procedures, and compliance reporting. This is a full-time articleship position with a stipend of ₹6,000 per month. Ideal for students who have completed CA Foundation and are pursuing CA Intermediate.',
    1, -- Large scope
    4, -- 3 to 6 months
    4, -- Entry level
    1, -- Published
    1, -- Approved
    DATE_ADD(CURDATE(), INTERVAL 30 DAY),
    NOW(), NOW()
);

-- Opportunity 2: Finance & Accounts Intern
INSERT INTO jobs (
    buyer_id, title, slug, category_id, subcategory_id,
    budget, custom_budget, description,
    project_scope, job_longevity, skill_level,
    status, is_approved, deadline, created_at, updated_at
) VALUES (
    @nexa_buyer_id,
    'Finance & Accounts Intern',
    'finance-accounts-intern',
    COALESCE(@internship_cat_id, 2),
    COALESCE(@finance_subcat_id, 2),
    8000.00, 0,
    'Join our finance team as an intern and gain practical experience in bookkeeping, MIS reporting, financial documentation, and internal reporting. You will assist in maintaining accounting records, preparing financial reports, handling documentation, and supporting the finance team with day-to-day operations. This internship offers exposure to real-world finance and accounting practices. Stipend: ₹8,000 per month. Perfect for BBA Finance, B.Com, or CMA students looking to build their career in finance and accounts.',
    2, -- Medium scope
    3, -- 1 to 3 months
    3, -- Intermediate level
    1, -- Published
    1, -- Approved
    DATE_ADD(CURDATE(), INTERVAL 30 DAY),
    NOW(), NOW()
);

-- =====================================================
-- PHASE 5: CREATE REALISTIC APPLICATIONS (BIDS)
-- =====================================================

-- Get user IDs
SET @aarav_user_id = (SELECT id FROM users WHERE email = 'aarav.jain21@gmail.com' LIMIT 1);
SET @priya_user_id = (SELECT id FROM users WHERE email = 'priyamehta.acads@gmail.com' LIMIT 1);

-- Get job IDs
SET @ca_articleship_job_id = (SELECT id FROM jobs WHERE slug = 'ca-articleship-trainee' LIMIT 1);
SET @finance_intern_job_id = (SELECT id FROM jobs WHERE slug = 'finance-accounts-intern' LIMIT 1);

-- Application 1: Aarav applies to CA Articleship Trainee
INSERT INTO bids (
    job_id, user_id, buyer_id, project_id,
    bid_quote, bid_amount, estimated_time,
    is_shortlist, status, created_at, updated_at
) VALUES (
    @ca_articleship_job_id,
    @aarav_user_id,
    @sharma_buyer_id,
    0,
    'I am a B.Com Final Year student currently pursuing CA Intermediate. I have completed 6 months of internship at a local tax consultant where I gained experience in GST filing, Tally Prime, Excel, and basic accounting entries. I am eager to learn audit procedures, taxation, and compliance work. I am available to start immediately and can commit to the full articleship duration. I am hardworking, detail-oriented, and ready to contribute to your team.',
    6000.00,
    '6 months',
    0,
    0, -- Pending
    NOW(), NOW()
);

-- Application 2: Priya applies to Finance & Accounts Intern
INSERT INTO bids (
    job_id, user_id, buyer_id, project_id,
    bid_quote, bid_amount, estimated_time,
    is_shortlist, status, created_at, updated_at
) VALUES (
    @finance_intern_job_id,
    @priya_user_id,
    @nexa_buyer_id,
    0,
    'I am a BBA Finance graduate currently pursuing CMA Inter. I have strong skills in MS Excel, MIS reporting, bookkeeping basics, and documentation. Although I am a fresher, I am highly motivated and eager to learn. I am looking for an internship opportunity to gain practical experience in finance, accounts, and reporting. I am detail-oriented, have good communication skills, and can work effectively in a team environment. I am ready to start immediately.',
    8000.00,
    '3 months',
    0,
    0, -- Pending
    NOW(), NOW()
);

-- =====================================================
-- PHASE 6: ATTACH SKILLS TO OPPORTUNITIES
-- =====================================================

-- Get skill IDs
SET @gst_skill_id = (SELECT id FROM skills WHERE name = 'GST' LIMIT 1);
SET @tally_skill_id = (SELECT id FROM skills WHERE name = 'Tally Prime' LIMIT 1);
SET @excel_skill_id = (SELECT id FROM skills WHERE name = 'MS Excel' LIMIT 1);
SET @accounting_skill_id = (SELECT id FROM skills WHERE name = 'Accounting' LIMIT 1);
SET @audit_skill_id = (SELECT id FROM skills WHERE name = 'Audit' LIMIT 1);
SET @taxation_skill_id = (SELECT id FROM skills WHERE name = 'Taxation' LIMIT 1);
SET @mis_skill_id = (SELECT id FROM skills WHERE name = 'MIS Reporting' LIMIT 1);
SET @bookkeeping_skill_id = (SELECT id FROM skills WHERE name = 'Bookkeeping' LIMIT 1);

-- Attach skills to CA Articleship Trainee
INSERT INTO job_skills (job_id, skill_id)
SELECT @ca_articleship_job_id, id FROM skills WHERE name IN ('GST', 'Tally Prime', 'MS Excel', 'Accounting', 'Audit', 'Taxation')
ON DUPLICATE KEY UPDATE job_id = job_id;

-- Attach skills to Finance & Accounts Intern
INSERT INTO job_skills (job_id, skill_id)
SELECT @finance_intern_job_id, id FROM skills WHERE name IN ('MS Excel', 'MIS Reporting', 'Bookkeeping', 'Accounting')
ON DUPLICATE KEY UPDATE job_id = job_id;

-- =====================================================
-- PHASE 7: ATTACH SKILLS TO STUDENTS
-- =====================================================

-- Attach skills to Aarav Jain
INSERT INTO skill_user (user_id, skill_id)
SELECT @aarav_user_id, id FROM skills WHERE name IN ('GST', 'Tally Prime', 'MS Excel', 'Accounting', 'Taxation')
ON DUPLICATE KEY UPDATE user_id = user_id;

-- Attach skills to Priya Mehta
INSERT INTO skill_user (user_id, skill_id)
SELECT @priya_user_id, id FROM skills WHERE name IN ('MS Excel', 'MIS Reporting', 'Bookkeeping', 'Accounting')
ON DUPLICATE KEY UPDATE user_id = user_id;

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================

-- Verify firms created
-- SELECT id, firstname, lastname, email, city, state FROM buyers WHERE email IN ('careers@sharmaagrawalca.in', 'hr@nexacorporate.in');

-- Verify students created
-- SELECT id, firstname, lastname, email, city, state, about FROM users WHERE email IN ('aarav.jain21@gmail.com', 'priyamehta.acads@gmail.com');

-- Verify opportunities created
-- SELECT id, title, buyer_id, budget, status, is_approved FROM jobs WHERE slug IN ('ca-articleship-trainee', 'finance-accounts-intern');

-- Verify applications created
-- SELECT id, job_id, user_id, buyer_id, bid_amount, status FROM bids WHERE job_id IN (SELECT id FROM jobs WHERE slug IN ('ca-articleship-trainee', 'finance-accounts-intern'));

-- =====================================================
-- END OF SCRIPT
-- =====================================================
