-- =====================================================
-- COMPLETE ARTICLE CONNECT CONVERSION SCRIPT (FIXED)
-- Date: March 15, 2026
-- Project: Article Connect Platform
-- =====================================================

USE article_base;

-- =====================================================
-- PHASE 1: UPDATE CMS/FRONTEND CONTENT
-- =====================================================

-- Update banner content
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Find the Right Articleship & Internship Opportunities',
    '$.subheading', 'Article Connect helps students discover articleship, internship, and training opportunities with trusted firms and companies.',
    '$.subtitle', 'Connect with trusted CA firms, companies, and training opportunities designed for students and career starters.'
)
WHERE tempname = 'basic' AND data_keys = 'banner.content';

-- Update account section
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

-- Update top freelancer section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Top Students',
    '$.subheading', 'Meet talented students who have excelled in their articleship and internship programs'
)
WHERE tempname = 'basic' AND data_keys = 'top_freelancer.content';

-- Update counter section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Article Connect by the Numbers',
    '$.subheading', 'Join thousands of students and firms connecting through articleship and internship opportunities'
)
WHERE tempname = 'basic' AND data_keys = 'counter.content' AND id = (SELECT id FROM (SELECT id FROM frontends WHERE tempname = 'basic' AND data_keys = 'counter.content' ORDER BY id DESC LIMIT 1) AS t);

-- =====================================================
-- PHASE 2: CREATE/UPDATE DEMO FIRMS (BUYERS)
-- =====================================================

-- Firm 1: Sharma & Agrawal Chartered Accountants
INSERT INTO buyers (
    firstname, lastname, username, email, dial_code, mobile,
    password, country_name, country_code, city, state, zip, address,
    status, ev, sv, kv, profile_complete, created_at, updated_at
) VALUES (
    'Sharma', '& Agrawal CA', 'sharmaagrawalca', 'careers@sharmaagrawalca.in', '+91', '9876543210',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'India', 'IN', 'Bhopal', 'Madhya Pradesh', '462011',
    '204, Business Square, MP Nagar Zone 1, Bhopal',
    1, 1, 1, 1, 1, NOW(), NOW()
)
ON DUPLICATE KEY UPDATE
    firstname = VALUES(firstname),
    lastname = VALUES(lastname),
    city = VALUES(city),
    state = VALUES(state),
    address = VALUES(address),
    updated_at = NOW();

-- Firm 2: Nexa Corporate Advisory Services Pvt. Ltd.
INSERT INTO buyers (
    firstname, lastname, username, email, dial_code, mobile,
    password, country_name, country_code, city, state, zip, address,
    status, ev, sv, kv, profile_complete, created_at, updated_at
) VALUES (
    'Nexa Corporate', 'Advisory', 'nexacorporate', 'hr@nexacorporate.in', '+91', '9827012345',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'India', 'IN', 'Indore', 'Madhya Pradesh', '452001',
    '5th Floor, Horizon Tower, Vijay Nagar, Indore',
    1, 1, 1, 1, 1, NOW(), NOW()
)
ON DUPLICATE KEY UPDATE
    firstname = VALUES(firstname),
    lastname = VALUES(lastname),
    city = VALUES(city),
    state = VALUES(state),
    address = VALUES(address),
    updated_at = NOW();

-- =====================================================
-- PHASE 3: CREATE/UPDATE DEMO STUDENTS (USERS)
-- =====================================================

-- Student 1: Aarav Jain
INSERT INTO users (
    firstname, lastname, username, email, dial_code, mobile,
    password, country_name, country_code, city, state, zip, address,
    status, ev, sv, kv, profile_complete, about, tagline, created_at, updated_at
) VALUES (
    'Aarav', 'Jain', 'aaravjain', 'aarav.jain21@gmail.com', '+91', '9111223344',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'India', 'IN', 'Bhopal', 'Madhya Pradesh', '462001',
    'Bhopal, Madhya Pradesh',
    1, 1, 1, 1, 1,
    'Commerce student actively seeking articleship opportunities in audit, taxation, and compliance. Completed B.Com Final Year and currently pursuing CA Intermediate.',
    'CA Intermediate | B.Com Final Year | Seeking Articleship',
    NOW(), NOW()
)
ON DUPLICATE KEY UPDATE
    firstname = VALUES(firstname),
    lastname = VALUES(lastname),
    city = VALUES(city),
    state = VALUES(state),
    about = VALUES(about),
    tagline = VALUES(tagline),
    updated_at = NOW();

-- Student 2: Priya Mehta
INSERT INTO users (
    firstname, lastname, username, email, dial_code, mobile,
    password, country_name, country_code, city, state, zip, address,
    status, ev, sv, kv, profile_complete, about, tagline, created_at, updated_at
) VALUES (
    'Priya', 'Mehta', 'priyamehta', 'priyamehta.acads@gmail.com', '+91', '9098665544',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'India', 'IN', 'Indore', 'Madhya Pradesh', '452001',
    'Indore, Madhya Pradesh',
    1, 1, 1, 1, 1,
    'Motivated student looking for internship and training opportunities in finance, accounts, and reporting. BBA Finance graduate pursuing CMA Inter.',
    'BBA Finance | CMA Inter | Finance Internship Seeker',
    NOW(), NOW()
)
ON DUPLICATE KEY UPDATE
    firstname = VALUES(firstname),
    lastname = VALUES(lastname),
    city = VALUES(city),
    state = VALUES(state),
    about = VALUES(about),
    tagline = VALUES(tagline),
    updated_at = NOW();

-- =====================================================
-- PHASE 4: CREATE OPPORTUNITIES (JOBS)
-- =====================================================

-- Get buyer IDs
SET @sharma_buyer_id = (SELECT id FROM buyers WHERE email = 'careers@sharmaagrawalca.in' ORDER BY id DESC LIMIT 1);
SET @nexa_buyer_id = (SELECT id FROM buyers WHERE email = 'hr@nexacorporate.in' ORDER BY id DESC LIMIT 1);

-- Get category IDs
SET @articleship_cat_id = (SELECT id FROM categories WHERE name = 'Articleship' LIMIT 1);
SET @internship_cat_id = (SELECT id FROM categories WHERE name = 'Internship' LIMIT 1);

-- Get subcategory IDs
SET @ca_articleship_subcat_id = (SELECT id FROM subcategories WHERE name = 'CA Articleship' LIMIT 1);
SET @finance_subcat_id = (SELECT id FROM subcategories WHERE name LIKE '%Finance%' LIMIT 1);

-- Delete existing opportunities with these slugs
DELETE FROM jobs WHERE slug IN ('ca-articleship-trainee', 'finance-accounts-intern');

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
    1, 4, 4, 1, 1, DATE_ADD(CURDATE(), INTERVAL 30 DAY), NOW(), NOW()
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
    2, 3, 3, 1, 1, DATE_ADD(CURDATE(), INTERVAL 30 DAY), NOW(), NOW()
);

-- =====================================================
-- PHASE 5: CREATE APPLICATIONS (BIDS)
-- =====================================================

-- Get user IDs
SET @aarav_user_id = (SELECT id FROM users WHERE email = 'aarav.jain21@gmail.com' ORDER BY id DESC LIMIT 1);
SET @priya_user_id = (SELECT id FROM users WHERE email = 'priyamehta.acads@gmail.com' ORDER BY id DESC LIMIT 1);

-- Get job IDs
SET @ca_articleship_job_id = (SELECT id FROM jobs WHERE slug = 'ca-articleship-trainee' LIMIT 1);
SET @finance_intern_job_id = (SELECT id FROM jobs WHERE slug = 'finance-accounts-intern' LIMIT 1);

-- Delete existing bids for these jobs
DELETE FROM bids WHERE job_id IN (@ca_articleship_job_id, @finance_intern_job_id);

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
    0, 0, NOW(), NOW()
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
    0, 0, NOW(), NOW()
);

-- =====================================================
-- PHASE 6: ATTACH SKILLS TO OPPORTUNITIES
-- =====================================================

-- Attach skills to CA Articleship Trainee
DELETE FROM job_skills WHERE job_id = @ca_articleship_job_id;
INSERT INTO job_skills (job_id, skill_id)
SELECT @ca_articleship_job_id, id FROM skills WHERE name IN ('GST', 'Tally Prime', 'MS Excel', 'Accounting', 'Audit', 'Taxation');

-- Attach skills to Finance & Accounts Intern
DELETE FROM job_skills WHERE job_id = @finance_intern_job_id;
INSERT INTO job_skills (job_id, skill_id)
SELECT @finance_intern_job_id, id FROM skills WHERE name IN ('MS Excel', 'MIS Reporting', 'Bookkeeping', 'Accounting');

-- =====================================================
-- PHASE 7: ATTACH SKILLS TO STUDENTS
-- =====================================================

-- Attach skills to Aarav Jain
DELETE FROM skill_user WHERE user_id = @aarav_user_id;
INSERT INTO skill_user (user_id, skill_id)
SELECT @aarav_user_id, id FROM skills WHERE name IN ('GST', 'Tally Prime', 'MS Excel', 'Accounting', 'Taxation');

-- Attach skills to Priya Mehta
DELETE FROM skill_user WHERE user_id = @priya_user_id;
INSERT INTO skill_user (user_id, skill_id)
SELECT @priya_user_id, id FROM skills WHERE name IN ('MS Excel', 'MIS Reporting', 'Bookkeeping', 'Accounting');
