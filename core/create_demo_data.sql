-- Demo Data Script for Article Connect Platform
-- Creates 2 firms, 2 students, 2 opportunities, and 2 applications
-- Password for all demo accounts: demo123

USE article_base;

-- Generate password hash for 'demo123'
SET @demo_password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

-- Get default verification settings
SET @kv_default = (SELECT kv FROM general_settings LIMIT 1);
SET @ev_default = (SELECT ev FROM general_settings LIMIT 1);
SET @sv_default = (SELECT sv FROM general_settings LIMIT 1);

-- If verification is required, set to verified for demo accounts
SET @kv_status = IF(@kv_default = 1, 0, 1);
SET @ev_status = IF(@ev_default = 1, 0, 1);
SET @sv_status = IF(@sv_default = 1, 0, 1);

-- ============================================
-- CREATE FIRMS / COMPANIES (BUYERS)
-- ============================================

-- Firm 1: Sharma & Agrawal Chartered Accountants
INSERT INTO buyers (
    firstname, lastname, username, email, dial_code, mobile,
    password, country_name, country_code, city, state, zip, address,
    status, kv, ev, sv, profile_complete, ts, tv,
    created_at, updated_at
) VALUES (
    'Sharma', 'Agrawal CA',
    'sharmaagrawalca',
    'careers@sharmaagrawalca.in',
    '+91', '9876543210',
    @demo_password,
    'India', 'IN',
    'Bhopal', 'Madhya Pradesh', '462001',
    '204, Business Square, MP Nagar Zone 1, Bhopal',
    1, -- Active
    @kv_status, @ev_status, @sv_status,
    1, -- Profile complete
    0, 1, -- 2FA disabled, verified
    NOW(), NOW()
);

SET @buyer1_id = LAST_INSERT_ID();

-- Firm 2: Nexa Corporate Advisory Services Pvt. Ltd.
INSERT INTO buyers (
    firstname, lastname, username, email, dial_code, mobile,
    password, country_name, country_code, city, state, zip, address,
    status, kv, ev, sv, profile_complete, ts, tv,
    created_at, updated_at
) VALUES (
    'Nexa', 'Corporate',
    'nexacorporate',
    'hr@nexacorporate.in',
    '+91', '9827012345',
    @demo_password,
    'India', 'IN',
    'Indore', 'Madhya Pradesh', '452001',
    '5th Floor, Horizon Tower, Vijay Nagar, Indore',
    1, -- Active
    @kv_status, @ev_status, @sv_status,
    1, -- Profile complete
    0, 1, -- 2FA disabled, verified
    NOW(), NOW()
);

SET @buyer2_id = LAST_INSERT_ID();

-- ============================================
-- CREATE STUDENTS (USERS)
-- ============================================

-- Student 1: Aarav Jain
INSERT INTO users (
    firstname, lastname, username, email, dial_code, mobile,
    password, country_name, country_code, city, state, zip, address,
    status, kv, ev, sv, profile_complete, work_profile_complete, step,
    tagline, about, ts, tv,
    created_at, updated_at
) VALUES (
    'Aarav', 'Jain',
    'aaravjain21',
    'aarav.jain21@gmail.com',
    '+91', '9111223344',
    @demo_password,
    'India', 'IN',
    'Bhopal', 'Madhya Pradesh', '462001',
    'Bhopal, MP',
    1, -- Active
    @kv_status, @ev_status, @sv_status,
    1, -- Profile complete
    1, -- Work profile complete
    4, -- Step 4 (completed)
    'CA Intermediate Student | Seeking Articleship in Audit & Taxation',
    'Commerce student actively seeking articleship opportunities in audit, taxation, and compliance. Currently pursuing CA Intermediate with strong foundation in GST, Tally Prime, and accounting principles. Completed 6 months internship at local tax consultant.',
    0, 1, -- 2FA disabled, verified
    NOW(), NOW()
);

SET @user1_id = LAST_INSERT_ID();

-- Student 2: Priya Mehta
INSERT INTO users (
    firstname, lastname, username, email, dial_code, mobile,
    password, country_name, country_code, city, state, zip, address,
    status, kv, ev, sv, profile_complete, work_profile_complete, step,
    tagline, about, ts, tv,
    created_at, updated_at
) VALUES (
    'Priya', 'Mehta',
    'priyamehta',
    'priyamehta.acads@gmail.com',
    '+91', '9098665544',
    @demo_password,
    'India', 'IN',
    'Indore', 'Madhya Pradesh', '452001',
    'Indore, MP',
    1, -- Active
    @kv_status, @ev_status, @sv_status,
    1, -- Profile complete
    1, -- Work profile complete
    4, -- Step 4 (completed)
    'BBA Finance Student | Seeking Internship in Finance & Accounts',
    'Motivated student looking for internship and training opportunities in finance, accounts, and reporting. Strong skills in MS Excel, MIS reporting, and documentation. Eager to learn and contribute to a professional finance team.',
    0, 1, -- 2FA disabled, verified
    NOW(), NOW()
);

SET @user2_id = LAST_INSERT_ID();

-- ============================================
-- CREATE OR GET CATEGORIES AND SUBCATEGORIES
-- ============================================

-- Check if categories exist, if not create them
INSERT INTO categories (name, status, created_at, updated_at)
SELECT 'Articleship', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Articleship');

INSERT INTO categories (name, status, created_at, updated_at)
SELECT 'Internship', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Internship');

SET @category1_id = (SELECT id FROM categories WHERE name = 'Articleship' LIMIT 1);
SET @category2_id = (SELECT id FROM categories WHERE name = 'Internship' LIMIT 1);

-- Create subcategories
INSERT INTO subcategories (category_id, name, status, created_at, updated_at)
SELECT @category1_id, 'CA Articleship', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM subcategories WHERE name = 'CA Articleship');

INSERT INTO subcategories (category_id, name, status, created_at, updated_at)
SELECT @category2_id, 'Finance & Accounts', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM subcategories WHERE name = 'Finance & Accounts');

SET @subcategory1_id = (SELECT id FROM subcategories WHERE name = 'CA Articleship' LIMIT 1);
SET @subcategory2_id = (SELECT id FROM subcategories WHERE name = 'Finance & Accounts' LIMIT 1);

-- ============================================
-- CREATE OR GET SKILLS
-- ============================================

-- Create skills if they don't exist
INSERT INTO skills (name, status, created_at, updated_at)
SELECT 'GST', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'GST');

INSERT INTO skills (name, status, created_at, updated_at)
SELECT 'Tally Prime', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'Tally Prime');

INSERT INTO skills (name, status, created_at, updated_at)
SELECT 'MS Excel', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'MS Excel');

INSERT INTO skills (name, status, created_at, updated_at)
SELECT 'Accounting', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'Accounting');

INSERT INTO skills (name, status, created_at, updated_at)
SELECT 'Audit', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'Audit');

INSERT INTO skills (name, status, created_at, updated_at)
SELECT 'Taxation', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'Taxation');

INSERT INTO skills (name, status, created_at, updated_at)
SELECT 'MIS Reporting', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'MIS Reporting');

INSERT INTO skills (name, status, created_at, updated_at)
SELECT 'Bookkeeping', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE name = 'Bookkeeping');

-- Get skill IDs
SET @skill_gst_id = (SELECT id FROM skills WHERE name = 'GST' LIMIT 1);
SET @skill_tally_id = (SELECT id FROM skills WHERE name = 'Tally Prime' LIMIT 1);
SET @skill_excel_id = (SELECT id FROM skills WHERE name = 'MS Excel' LIMIT 1);
SET @skill_accounting_id = (SELECT id FROM skills WHERE name = 'Accounting' LIMIT 1);
SET @skill_audit_id = (SELECT id FROM skills WHERE name = 'Audit' LIMIT 1);
SET @skill_taxation_id = (SELECT id FROM skills WHERE name = 'Taxation' LIMIT 1);
SET @skill_mis_id = (SELECT id FROM skills WHERE name = 'MIS Reporting' LIMIT 1);
SET @skill_bookkeeping_id = (SELECT id FROM skills WHERE name = 'Bookkeeping' LIMIT 1);

-- Link skills to users
INSERT INTO skill_user (user_id, skill_id)
VALUES
    (@user1_id, @skill_gst_id),
    (@user1_id, @skill_tally_id),
    (@user1_id, @skill_excel_id),
    (@user1_id, @skill_accounting_id),
    (@user1_id, @skill_audit_id),
    (@user1_id, @skill_taxation_id),
    (@user2_id, @skill_excel_id),
    (@user2_id, @skill_mis_id),
    (@user2_id, @skill_bookkeeping_id),
    (@user2_id, @skill_accounting_id);

-- ============================================
-- CREATE OPPORTUNITIES (JOBS)
-- ============================================

-- Opportunity 1: CA Articleship Trainee
INSERT INTO jobs (
    buyer_id, title, slug,
    category_id, subcategory_id,
    budget, custom_budget,
    description, project_scope, job_longevity, skill_level,
    deadline, status, is_approved,
    created_at, updated_at
) VALUES (
    @buyer1_id,
    'CA Articleship Trainee - Audit, GST & Taxation',
    'ca-articleship-trainee-audit-gst-taxation',
    @category1_id,
    @subcategory1_id,
    6000.00, -- Stipend
    0, -- Fixed budget
    'We are looking for motivated CA Intermediate students to join our articleship program. The trainee will work on:
    
- Statutory audit assignments for various clients
- GST compliance and return filing
- Income tax return preparation and filing
- Bank reconciliation and accounting entries
- TDS compliance and documentation
- Client interaction and communication

Requirements:
- CA Intermediate cleared or appearing
- Basic knowledge of GST, Tally Prime, and MS Excel
- Good communication skills
- Willingness to learn and work in a team environment

Duration: 3 years (as per ICAI guidelines)
Stipend: ₹6,000 per month
Location: Bhopal, MP

This is an excellent opportunity for students seeking practical exposure in audit, taxation, and compliance.',
    2, -- Medium scope
    4, -- 3 to 6 months (actually 3 years, but using available option)
    3, -- Intermediate skill level
    DATE_ADD(NOW(), INTERVAL 30 DAY), -- Deadline 30 days from now
    1, -- Published
    1, -- Approved
    NOW(), NOW()
);

SET @job1_id = LAST_INSERT_ID();

-- Link skills to job 1
INSERT INTO job_skills (job_id, skill_id)
VALUES
    (@job1_id, @skill_audit_id),
    (@job1_id, @skill_gst_id),
    (@job1_id, @skill_taxation_id),
    (@job1_id, @skill_tally_id),
    (@job1_id, @skill_excel_id),
    (@job1_id, @skill_accounting_id);

-- Opportunity 2: Finance & Accounts Intern
INSERT INTO jobs (
    buyer_id, title, slug,
    category_id, subcategory_id,
    budget, custom_budget,
    description, project_scope, job_longevity, skill_level,
    deadline, status, is_approved,
    created_at, updated_at
) VALUES (
    @buyer2_id,
    'Finance & Accounts Intern - MIS Reporting & Bookkeeping',
    'finance-accounts-intern-mis-reporting-bookkeeping',
    @category2_id,
    @subcategory2_id,
    8000.00, -- Stipend
    0, -- Fixed budget
    'Nexa Corporate Advisory Services is offering an internship opportunity for finance and accounts students. The intern will assist in:

- Daily bookkeeping and accounting entries
- Preparation of MIS reports and financial statements
- Bank reconciliation and cash flow management
- Documentation and filing of financial records
- Data entry and maintenance of accounting software
- Assisting in financial analysis and reporting

Requirements:
- BBA/B.Com/BBA Finance or related field
- Proficiency in MS Excel (pivot tables, formulas, charts)
- Basic understanding of accounting principles
- Good documentation and communication skills
- Attention to detail and accuracy

Duration: 6 months (extendable based on performance)
Stipend: ₹8,000 per month
Location: Indore, MP

This internship provides hands-on experience in corporate finance, MIS reporting, and accounting operations.',
    2, -- Medium scope
    4, -- 3 to 6 months
    3, -- Intermediate skill level
    DATE_ADD(NOW(), INTERVAL 30 DAY), -- Deadline 30 days from now
    1, -- Published
    1, -- Approved
    NOW(), NOW()
);

SET @job2_id = LAST_INSERT_ID();

-- Link skills to job 2
INSERT INTO job_skills (job_id, skill_id)
VALUES
    (@job2_id, @skill_excel_id),
    (@job2_id, @skill_mis_id),
    (@job2_id, @skill_bookkeeping_id),
    (@job2_id, @skill_accounting_id);

-- ============================================
-- CREATE APPLICATIONS (BIDS)
-- ============================================

-- Application 1: Aarav Jain applies to CA Articleship Trainee
INSERT INTO bids (
    job_id, user_id, buyer_id,
    bid_quote, bid_amount, estimated_time,
    status, is_shortlist,
    created_at, updated_at
) VALUES (
    @job1_id,
    @user1_id,
    @buyer1_id,
    'I am a CA Intermediate student with strong interest in audit, GST, and taxation. I have completed 6 months of internship at a local tax consultant where I gained hands-on experience in GST return filing, TDS compliance, and accounting entries using Tally Prime.

My key strengths:
- Proficient in GST compliance and return filing
- Experience with Tally Prime for accounting entries
- Strong MS Excel skills for data analysis
- Good understanding of audit procedures
- Excellent communication and documentation skills

I am eager to learn and contribute to your firm while completing my articleship. I believe this opportunity will help me gain comprehensive exposure in audit, taxation, and compliance, which aligns perfectly with my career goals.',
    6000.00, -- Expected stipend matches the opportunity
    '3 years', -- Articleship duration
    0, -- Pending status
    0, -- Not shortlisted yet
    DATE_SUB(NOW(), INTERVAL 2 DAY), -- Applied 2 days ago
    NOW()
);

-- Application 2: Priya Mehta applies to Finance & Accounts Intern
INSERT INTO bids (
    job_id, user_id, buyer_id,
    bid_quote, bid_amount, estimated_time,
    status, is_shortlist,
    created_at, updated_at
) VALUES (
    @job2_id,
    @user2_id,
    @buyer2_id,
    'I am a BBA Finance student seeking an internship opportunity to gain practical experience in finance and accounts. Although I am a fresher, I have strong theoretical knowledge and excellent skills in MS Excel, including pivot tables, VLOOKUP, and data analysis.

My key strengths:
- Advanced MS Excel skills for MIS reporting and data analysis
- Strong understanding of accounting principles and bookkeeping
- Excellent documentation and organizational skills
- Quick learner with attention to detail
- Good communication skills

I am highly motivated and eager to contribute to your team while learning from experienced professionals. This internship will provide me with the practical exposure I need to build a successful career in finance and accounts.',
    8000.00, -- Expected stipend matches the opportunity
    '6 months', -- Internship duration
    0, -- Pending status
    0, -- Not shortlisted yet
    DATE_SUB(NOW(), INTERVAL 1 DAY), -- Applied 1 day ago
    NOW()
);

-- ============================================
-- CREATE ADMIN NOTIFICATIONS
-- ============================================

-- Notification for new buyers
INSERT INTO admin_notifications (buyer_id, title, click_url, created_at, updated_at)
VALUES
    (@buyer1_id, 'New buyer registered', CONCAT('/admin/buyers/detail/', @buyer1_id), NOW(), NOW()),
    (@buyer2_id, 'New buyer registered', CONCAT('/admin/buyers/detail/', @buyer2_id), NOW(), NOW());

-- Notification for new users
INSERT INTO admin_notifications (user_id, title, click_url, created_at, updated_at)
VALUES
    (@user1_id, 'New Freelancer registered', CONCAT('/admin/users/detail/', @user1_id), NOW(), NOW()),
    (@user2_id, 'New Freelancer registered', CONCAT('/admin/users/detail/', @user2_id), NOW(), NOW());

-- ============================================
-- SUMMARY
-- ============================================

SELECT 'Demo Data Created Successfully!' as Status;
SELECT CONCAT('Buyer 1 ID: ', @buyer1_id) as Buyer1;
SELECT CONCAT('Buyer 2 ID: ', @buyer2_id) as Buyer2;
SELECT CONCAT('User 1 ID: ', @user1_id) as User1;
SELECT CONCAT('User 2 ID: ', @user2_id) as User2;
SELECT CONCAT('Job 1 ID: ', @job1_id) as Job1;
SELECT CONCAT('Job 2 ID: ', @job2_id) as Job2;
