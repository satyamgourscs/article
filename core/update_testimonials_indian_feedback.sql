USE article;

-- ============================================
-- UPDATE TESTIMONIALS WITH REALISTIC INDIAN USER FEEDBACK
-- ============================================

-- Testimonial 1 (ID: 96) - Student Success Story
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.name', 'Priya Sharma',
    '$.country', 'India',
    '$.quote', 'Article Connect helped me find the perfect articleship opportunity at a leading CA firm in Mumbai. The platform made it so easy to compare different firms and apply directly. I''m now working on real audit assignments and gaining valuable experience.'
)
WHERE id = 96 AND data_keys = 'testimonial.element';

-- Testimonial 2 (ID: 97) - CA Firm Feedback
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.name', 'Rajesh Kumar',
    '$.country', 'India',
    '$.quote', 'As a CA firm owner, finding motivated students for articleship was always challenging. Article Connect has simplified our hiring process. We''ve found three excellent students who are eager to learn and contribute to our practice.'
)
WHERE id = 97 AND data_keys = 'testimonial.element';

-- Testimonial 3 (ID: 98) - Student Career Growth
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.name', 'Anjali Patel',
    '$.country', 'India',
    '$.quote', 'I was struggling to find internship opportunities after completing my CA Intermediate. Article Connect connected me with multiple firms, and I found a great internship at a corporate office in Delhi. The platform is user-friendly and transparent.'
)
WHERE id = 98 AND data_keys = 'testimonial.element';

-- Testimonial 4 (ID: 99) - Firm Hiring Success
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.name', 'Vikram Singh',
    '$.country', 'India',
    '$.quote', 'We needed students for our tax compliance team, and Article Connect delivered. The quality of student profiles is impressive, and we can easily review their education and skills before making a decision. Highly recommended for CA firms!'
)
WHERE id = 99 AND data_keys = 'testimonial.element';

-- Testimonial 5 (ID: 100) - Student Experience
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.name', 'Sneha Reddy',
    '$.country', 'India',
    '$.quote', 'Article Connect made my articleship search so much easier. Instead of relying on references, I could explore verified opportunities from trusted firms. I found my current articleship at a mid-size CA firm in Bangalore, and I''m learning so much!'
)
WHERE id = 100 AND data_keys = 'testimonial.element';

-- Testimonial 6 (ID: 101) - Firm Owner Feedback
UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.name', 'Amit Agarwal',
    '$.country', 'India',
    '$.quote', 'Posting articleship opportunities on Article Connect has been a game-changer for our firm. We receive applications from qualified students across India, and the platform helps us manage everything efficiently. Great platform for both students and firms!'
)
WHERE id = 101 AND data_keys = 'testimonial.element';

-- Verify updates
SELECT id, JSON_EXTRACT(data_values, '$.name') as name, JSON_EXTRACT(data_values, '$.country') as country, LEFT(JSON_EXTRACT(data_values, '$.quote'), 80) as quote_preview 
FROM frontends 
WHERE data_keys = 'testimonial.element' 
ORDER BY id 
LIMIT 6;
