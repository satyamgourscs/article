-- SQL Script to Update CMS Content for Article Connect Platform
-- Run this script to update database-driven frontend content
-- Database: article_base
-- Table: frontends

USE article_base;

-- Update Banner Content
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Connect Students with the Right Articleship and Internship Opportunities',
    '$.subheading', 'Article Connect helps students discover articleship roles, internships, training opportunities, and career openings with trusted firms, companies, and professional organizations.',
    '$.subtitle', 'Trusted by Leading CA Firms & Companies'
)
WHERE data_keys = 'banner.content' AND tempname = 'basic';

-- Update Account Section (Student Registration)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.freelancer_title', 'Join as a Student',
    '$.freelancer_content', 'Create your student profile and start exploring articleship opportunities, internships, and training programs with top CA firms and companies.',
    '$.freelancer_button_name', 'Register as Student'
)
WHERE data_keys = 'account.content' AND tempname = 'basic';

-- Update Account Section (Firm Registration)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.buyer_title', 'Join as a Firm / Company',
    '$.buyer_content', 'Post articleship opportunities, internships, and training programs. Connect with talented students and build your team.',
    '$.buyer_button_name', 'Register as Firm'
)
WHERE data_keys = 'account.content' AND tempname = 'basic';

-- Update Find Task Section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.subtitle', 'How It Works',
    '$.heading', 'Find Your Perfect Articleship Opportunity',
    '$.subheading', 'Discover articleship roles, internships, and training programs that match your career goals and interests.',
    '$.button_name', 'Explore Opportunities'
)
WHERE data_keys = 'find_task.content' AND tempname = 'basic';

-- Update How Work Section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'How Article Connect Works',
    '$.subheading', 'A simple process to connect students with articleship and internship opportunities'
)
WHERE data_keys = 'how_work.content' AND tempname = 'basic';

-- Update Why Choose Section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Why Choose Article Connect',
    '$.subheading', 'Your trusted platform for articleship, internships, and career growth'
)
WHERE data_keys = 'why_choose.content' AND tempname = 'basic';

-- Update About Section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'About Article Connect'
)
WHERE data_keys = 'about.content' AND tempname = 'basic';

-- Update Top Freelancer Section (Top Students)
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Top Students',
    '$.subheading', 'Meet talented students who have excelled in their articleship and internship programs'
)
WHERE data_keys = 'top_freelancer.content' AND tempname = 'basic';

-- Update Facility Section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Why Students Choose Article Connect',
    '$.subheading', 'Features designed to help you succeed in your articleship journey'
)
WHERE data_keys = 'facility.content' AND tempname = 'basic';

-- Update Completion Work Section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Success Stories',
    '$.subheading', 'Students who completed their articleship and internship programs successfully'
)
WHERE data_keys = 'completion_work.content' AND tempname = 'basic';

-- Update Testimonial Section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'What Our Students Say',
    '$.subheading', 'Real experiences from students who found their perfect articleship opportunities'
)
WHERE data_keys = 'testimonial.content' AND tempname = 'basic';

-- Update Subscribe Section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Stay Updated',
    '$.subheading', 'Get notified about new articleship opportunities and career insights'
)
WHERE data_keys = 'subscribe.content' AND tempname = 'basic';

-- Update Support Section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Need Help?'
)
WHERE data_keys = 'support.content' AND tempname = 'basic';

-- Update Brand Section
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.heading', 'Trusted by Leading Firms'
)
WHERE data_keys = 'brand.content' AND tempname = 'basic';

-- Note: Individual element content (like find_task.element, how_work.element, etc.)
-- should be updated through the admin panel's frontend management interface
-- or by updating the JSON data_values for each element record
