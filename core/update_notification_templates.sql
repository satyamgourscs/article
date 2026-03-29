-- Update Notification Templates for Article Connect
-- Update email notification templates to use Article Connect terminology

USE article_base;

-- Update JOB_APPROVED template
UPDATE notification_templates 
SET subject = REPLACE(REPLACE(subject, 'Job', 'Opportunity'), 'job', 'opportunity'),
    email_body = REPLACE(REPLACE(REPLACE(REPLACE(email_body, 'Job', 'Opportunity'), 'job', 'opportunity'), 'Bid', 'Application'), 'bid', 'application')
WHERE name = 'JOB_APPROVED';

-- Update JOB_REJECTED template
UPDATE notification_templates 
SET subject = REPLACE(REPLACE(subject, 'Job', 'Opportunity'), 'job', 'opportunity'),
    email_body = REPLACE(REPLACE(REPLACE(REPLACE(email_body, 'Job', 'Opportunity'), 'job', 'opportunity'), 'Bid', 'Application'), 'bid', 'application')
WHERE name = 'JOB_REJECTED';

-- Update BID_ACCEPTED template
UPDATE notification_templates 
SET subject = REPLACE(REPLACE(REPLACE(REPLACE(subject, 'Bid', 'Application'), 'bid', 'application'), 'Job', 'Opportunity'), 'job', 'opportunity'),
    email_body = REPLACE(REPLACE(REPLACE(REPLACE(email_body, 'Bid', 'Application'), 'bid', 'application'), 'Job', 'Opportunity'), 'job', 'opportunity')
WHERE name = 'BID_ACCEPTED';

-- Update BID_REJECTED template
UPDATE notification_templates 
SET subject = REPLACE(REPLACE(REPLACE(REPLACE(subject, 'Bid', 'Application'), 'bid', 'application'), 'Job', 'Opportunity'), 'job', 'opportunity'),
    email_body = REPLACE(REPLACE(REPLACE(REPLACE(email_body, 'Bid', 'Application'), 'bid', 'application'), 'Job', 'Opportunity'), 'job', 'opportunity')
WHERE name = 'BID_REJECTED';

-- Update PROJECT_COMPLETED template
UPDATE notification_templates 
SET subject = REPLACE(REPLACE(REPLACE(REPLACE(subject, 'Project', 'Assignment'), 'project', 'assignment'), 'Job', 'Opportunity'), 'job', 'opportunity'),
    email_body = REPLACE(REPLACE(REPLACE(REPLACE(email_body, 'Project', 'Assignment'), 'project', 'assignment'), 'Job', 'Opportunity'), 'job', 'opportunity')
WHERE name = 'PROJECT_COMPLETED';

-- Update REPORTED_PROJECT_REJECTED template
UPDATE notification_templates 
SET subject = REPLACE(REPLACE(REPLACE(REPLACE(subject, 'Project', 'Assignment'), 'project', 'assignment'), 'Job', 'Opportunity'), 'job', 'opportunity'),
    email_body = REPLACE(REPLACE(REPLACE(REPLACE(email_body, 'Project', 'Assignment'), 'project', 'assignment'), 'Job', 'Opportunity'), 'job', 'opportunity')
WHERE name = 'REPORTED_PROJECT_REJECTED';

-- Update REPORTED_PROJECT_COMPLETED template
UPDATE notification_templates 
SET subject = REPLACE(REPLACE(REPLACE(REPLACE(subject, 'Project', 'Assignment'), 'project', 'assignment'), 'Job', 'Opportunity'), 'job', 'opportunity'),
    email_body = REPLACE(REPLACE(REPLACE(REPLACE(email_body, 'Project', 'Assignment'), 'project', 'assignment'), 'Job', 'Opportunity'), 'job', 'opportunity')
WHERE name = 'REPORTED_PROJECT_COMPLETED';

SELECT 'Notification templates updated!' as Status;
