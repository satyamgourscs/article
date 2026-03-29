-- Update remaining how_work.element: "Post a Job"
USE article_base;

UPDATE frontends 
SET data_values = JSON_SET(
    data_values,
    '$.title', 'Post an Opportunity',
    '$.content', 'Firms can easily post articleship and internship opportunities. Provide detailed requirements and attract applications from qualified students.'
)
WHERE data_keys = 'how_work.element' 
AND tempname = 'basic' 
AND JSON_EXTRACT(data_values, '$.title') LIKE '%Post a Job%';
