USE article;

-- ============================================
-- UPDATE 6 BLOG POSTS FOR ARTICLE CONNECT
-- ============================================

-- BLOG 1: How to Find the Right Articleship Opportunity as a CA Student
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'How to Find the Right Articleship Opportunity as a CA Student',
    '$.description', 'Choosing the right articleship opportunity is one of the most important steps in a CA student''s career. Articleship provides practical exposure to auditing, taxation, compliance, and financial advisory work.',
    '$.content', '<div><p>Choosing the right articleship opportunity is one of the most important steps in a CA student''s career. Articleship provides practical exposure to auditing, taxation, compliance, and financial advisory work.</p><p>Through Article Connect, students can explore verified opportunities posted by trusted CA firms and companies. The platform allows students to compare firms, understand job roles, and apply directly to opportunities that match their skills and career interests.</p><p>Students should focus on gaining exposure to real client work, tax filings, audit assignments, and financial reporting processes. These experiences help build a strong professional foundation.</p><p>Article Connect simplifies the process by bringing firms and students onto one platform where opportunities are transparent and applications are easy.</p></div>'
)
WHERE id = (SELECT id FROM (SELECT id FROM frontends WHERE data_keys = 'blog.element' ORDER BY id LIMIT 1 OFFSET 0) AS t);

-- BLOG 2: Why Internships and Articleship Matter for Career Growth
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Why Internships and Articleship Matter for Career Growth',
    '$.description', 'Practical training plays a crucial role in shaping a student''s professional future. Internships and articleship opportunities allow students to apply theoretical knowledge in real-world situations.',
    '$.content', '<div><p>Practical training plays a crucial role in shaping a student''s professional future. Internships and articleship opportunities allow students to apply theoretical knowledge in real-world situations.</p><p>Firms benefit by hiring enthusiastic students who are eager to learn and contribute. Students gain hands-on experience with accounting systems, compliance processes, and client communication.</p><p>Article Connect bridges this gap by helping firms find talented students while helping students discover meaningful training opportunities.</p></div>'
)
WHERE id = (SELECT id FROM (SELECT id FROM frontends WHERE data_keys = 'blog.element' ORDER BY id LIMIT 1 OFFSET 1) AS t);

-- BLOG 3: How Firms Can Find the Right Students for Training
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'How Firms Can Find the Right Students for Training',
    '$.description', 'Firms often struggle to find motivated students who are ready to learn and contribute. Article Connect provides firms with a streamlined way to connect with students who are actively seeking articleship and internship opportunities.',
    '$.content', '<div><p>Firms often struggle to find motivated students who are ready to learn and contribute. Article Connect provides firms with a streamlined way to connect with students who are actively seeking articleship and internship opportunities.</p><p>Firms can post opportunities, review student profiles, and select candidates based on skills, education, and interests.</p><p>This ensures that both firms and students benefit from a transparent and efficient recruitment process.</p></div>'
)
WHERE id = (SELECT id FROM (SELECT id FROM frontends WHERE data_keys = 'blog.element' ORDER BY id LIMIT 1 OFFSET 2) AS t);

-- BLOG 4: Top Skills Every CA Articleship Student Should Develop
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Top Skills Every CA Articleship Student Should Develop',
    '$.description', 'While academic knowledge is important, certain practical skills can make a huge difference during articleship training.',
    '$.content', '<div><p>While academic knowledge is important, certain practical skills can make a huge difference during articleship training.</p><p>Students should focus on developing:</p><ul><li>Accounting software knowledge</li><li>Tax compliance understanding</li><li>Communication skills</li><li>Analytical thinking</li><li>Client interaction skills</li></ul><p>Platforms like Article Connect help students showcase these skills through their profiles and applications.</p></div>'
)
WHERE id = (SELECT id FROM (SELECT id FROM frontends WHERE data_keys = 'blog.element' ORDER BY id LIMIT 1 OFFSET 3) AS t);

-- BLOG 5: How Article Connect Helps Students Discover Career Opportunities
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'How Article Connect Helps Students Discover Career Opportunities',
    '$.description', 'Article Connect is designed to make it easier for students to find real opportunities with trusted firms and organizations.',
    '$.content', '<div><p>Article Connect is designed to make it easier for students to find real opportunities with trusted firms and organizations.</p><p>Instead of relying on informal networks, students can explore multiple opportunities, compare roles, and apply directly.</p><p>The platform improves transparency and helps students take control of their career journey.</p></div>'
)
WHERE id = (SELECT id FROM (SELECT id FROM frontends WHERE data_keys = 'blog.element' ORDER BY id LIMIT 1 OFFSET 4) AS t);

-- BLOG 6: The Future of Student-Firm Collaboration
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'The Future of Student-Firm Collaboration',
    '$.description', 'Digital platforms are transforming the way students and firms connect.',
    '$.content', '<div><p>Digital platforms are transforming the way students and firms connect.</p><p>Article Connect represents a new generation of professional networking platforms where students can build profiles, firms can post opportunities, and both sides can collaborate effectively.</p><p>By simplifying discovery and communication, Article Connect supports long-term professional growth for students and helps firms find the right talent.</p></div>'
)
WHERE id = (SELECT id FROM (SELECT id FROM frontends WHERE data_keys = 'blog.element' ORDER BY id LIMIT 1 OFFSET 5) AS t);

-- Verify updates
SELECT id, JSON_EXTRACT(data_values, '$.title') as title FROM frontends WHERE data_keys = 'blog.element' ORDER BY id LIMIT 6;
