USE article;

-- Add 4th counter element for Successful Placements
INSERT INTO frontends (tempname, data_keys, data_values, created_at, updated_at)
VALUES ('basic', 'counter.element', 
  '{"icon":"<i class=\\"fas fa-check-circle\\"></i>","digit":"300","content":"Successful Placements"}',
  NOW(), NOW());

-- Verify
SELECT id, data_keys, JSON_EXTRACT(data_values, '$.digit') as digit, JSON_EXTRACT(data_values, '$.content') as content 
FROM frontends 
WHERE data_keys = 'counter.element' 
ORDER BY id;
