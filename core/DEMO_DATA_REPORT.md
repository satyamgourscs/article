# Demo Data Creation Report
**Date:** March 15, 2026  
**Project:** Article Connect  
**URL:** http://localhost/article/

## Executive Summary

Successfully created realistic demo data for the Article Connect platform including 2 firm accounts, 2 student accounts, 2 opportunities, and 2 applications. All data follows the platform's business logic and database structure while presenting realistic content aligned with the Article Connect concept (articleship, internships, student opportunities).

## Demo Accounts Created

### FIRMS / COMPANIES (Buyers)

#### 1. Sharma & Agrawal Chartered Accountants
- **ID:** 1
- **Type:** CA Firm
- **Username:** `sharmaagrawalca`
- **Email:** `careers@sharmaagrawalca.in`
- **Mobile:** +91 9876543210
- **Location:** Bhopal, Madhya Pradesh
- **Address:** 204, Business Square, MP Nagar Zone 1, Bhopal
- **Password:** `demo123`
- **Status:** Active, Profile Complete, Email Verified
- **About:** Mid-sized CA firm specializing in audit, taxation, GST compliance, and articleship training for commerce students.

#### 2. Nexa Corporate Advisory Services Pvt. Ltd.
- **ID:** 2
- **Type:** Company / Advisory
- **Username:** `nexacorporate`
- **Email:** `hr@nexacorporate.in`
- **Mobile:** +91 9827012345
- **Location:** Indore, Madhya Pradesh
- **Address:** 5th Floor, Horizon Tower, Vijay Nagar, Indore
- **Password:** `demo123`
- **Status:** Active, Profile Complete, Email Verified
- **About:** Advisory and finance solutions company offering internships in accounting, compliance, MIS reporting, and financial analysis.

### STUDENTS (Users)

#### 1. Aarav Jain
- **ID:** 1
- **Username:** `aaravjain21`
- **Email:** `aarav.jain21@gmail.com`
- **Mobile:** +91 9111223344
- **Location:** Bhopal, Madhya Pradesh
- **Password:** `demo123`
- **Status:** Active, Profile Complete, Work Profile Complete, Email Verified
- **Tagline:** CA Intermediate Student | Seeking Articleship in Audit & Taxation
- **About:** Commerce student actively seeking articleship opportunities in audit, taxation, and compliance. Currently pursuing CA Intermediate with strong foundation in GST, Tally Prime, and accounting principles. Completed 6 months internship at local tax consultant.
- **Skills:** GST, Tally Prime, MS Excel, Accounting, Audit, Taxation

#### 2. Priya Mehta
- **ID:** 2
- **Username:** `priyamehta`
- **Email:** `priyamehta.acads@gmail.com`
- **Mobile:** +91 9098665544
- **Location:** Indore, Madhya Pradesh
- **Password:** `demo123`
- **Status:** Active, Profile Complete, Work Profile Complete, Email Verified
- **Tagline:** BBA Finance Student | Seeking Internship in Finance & Accounts
- **About:** Motivated student looking for internship and training opportunities in finance, accounts, and reporting. Strong skills in MS Excel, MIS reporting, and documentation. Eager to learn and contribute to a professional finance team.
- **Skills:** MS Excel, MIS Reporting, Bookkeeping, Accounting

## Opportunities Created (Jobs)

### 1. CA Articleship Trainee - Audit, GST & Taxation
- **ID:** 1
- **Company:** Sharma & Agrawal Chartered Accountants (Buyer ID: 1)
- **Title:** CA Articleship Trainee - Audit, GST & Taxation
- **Slug:** `ca-articleship-trainee-audit-gst-taxation`
- **Category:** Articleship
- **Subcategory:** CA Articleship
- **Stipend:** ₹6,000 per month
- **Openings:** 1 (implied by single application)
- **Duration:** 3 years (as per ICAI guidelines)
- **Location:** Bhopal, MP
- **Status:** Published, Approved
- **Deadline:** 30 days from creation date
- **Scope:** Medium
- **Skill Level:** Intermediate
- **Required Skills:** Audit, GST, Taxation, Tally Prime, MS Excel, Accounting
- **Description:** Comprehensive articleship program covering statutory audit, GST compliance, income tax, bank reconciliation, TDS compliance, and client interaction.

### 2. Finance & Accounts Intern - MIS Reporting & Bookkeeping
- **ID:** 2
- **Company:** Nexa Corporate Advisory Services Pvt. Ltd. (Buyer ID: 2)
- **Title:** Finance & Accounts Intern - MIS Reporting & Bookkeeping
- **Slug:** `finance-accounts-intern-mis-reporting-bookkeeping`
- **Category:** Internship
- **Subcategory:** Finance & Accounts
- **Stipend:** ₹8,000 per month
- **Openings:** 1 (implied by single application)
- **Duration:** 6 months (extendable)
- **Location:** Indore, MP
- **Status:** Published, Approved
- **Deadline:** 30 days from creation date
- **Scope:** Medium
- **Skill Level:** Intermediate
- **Required Skills:** MS Excel, MIS Reporting, Bookkeeping, Accounting
- **Description:** Internship opportunity covering daily bookkeeping, MIS reports, bank reconciliation, documentation, data entry, and financial analysis.

## Applications Created (Bids)

### 1. Aarav Jain → CA Articleship Trainee
- **ID:** 1
- **Job ID:** 1
- **Student:** Aarav Jain (User ID: 1)
- **Company:** Sharma & Agrawal Chartered Accountants (Buyer ID: 1)
- **Expected Stipend:** ₹6,000
- **Duration:** 3 years
- **Status:** Pending
- **Shortlisted:** No
- **Applied:** 2 days ago
- **Application Quote:** Detailed application highlighting CA Intermediate status, 6 months internship experience, proficiency in GST, Tally Prime, MS Excel, and strong communication skills.

### 2. Priya Mehta → Finance & Accounts Intern
- **ID:** 2
- **Job ID:** 2
- **Student:** Priya Mehta (User ID: 2)
- **Company:** Nexa Corporate Advisory Services Pvt. Ltd. (Buyer ID: 2)
- **Expected Stipend:** ₹8,000
- **Duration:** 6 months
- **Status:** Pending
- **Shortlisted:** No
- **Applied:** 1 day ago
- **Application Quote:** Application emphasizing BBA Finance background, advanced MS Excel skills, understanding of accounting principles, documentation skills, and eagerness to learn.

## Database Tables Modified

### Primary Tables
1. **`buyers`** - 2 records inserted
2. **`users`** - 2 records inserted
3. **`jobs`** - 2 records inserted
4. **`bids`** - 2 records inserted

### Supporting Tables
5. **`categories`** - 2 records created (if not existed): Articleship, Internship
6. **`subcategories`** - 2 records created (if not existed): CA Articleship, Finance & Accounts
7. **`skills`** - 8 records created (if not existed): GST, Tally Prime, MS Excel, Accounting, Audit, Taxation, MIS Reporting, Bookkeeping
8. **`skill_user`** - 10 records inserted (linking skills to users)
9. **`job_skills`** - 10 records inserted (linking skills to jobs)
10. **`admin_notifications`** - 4 records inserted (2 for buyers, 2 for users)

## Login Credentials Summary

### Firm Accounts
| Company | Email | Username | Password |
|---------|-------|----------|----------|
| Sharma & Agrawal CA | careers@sharmaagrawalca.in | sharmaagrawalca | demo123 |
| Nexa Corporate | hr@nexacorporate.in | nexacorporate | demo123 |

### Student Accounts
| Student | Email | Username | Password |
|---------|-------|----------|----------|
| Aarav Jain | aarav.jain21@gmail.com | aaravjain21 | demo123 |
| Priya Mehta | priyamehta.acads@gmail.com | priyamehta | demo123 |

## Verification Checklist

### ✅ Accounts Created
- [x] 2 firm accounts with complete profiles
- [x] 2 student accounts with complete profiles
- [x] All accounts verified and active
- [x] All profiles marked as complete

### ✅ Opportunities Created
- [x] 2 opportunities published and approved
- [x] Categories and subcategories created
- [x] Skills linked to opportunities
- [x] Realistic descriptions matching Article Connect concept

### ✅ Applications Created
- [x] 2 applications submitted
- [x] Applications linked to correct opportunities
- [x] Realistic application quotes
- [x] Expected stipend matches opportunity budget

### ✅ Supporting Data
- [x] Skills created and linked
- [x] Admin notifications created
- [x] All foreign key relationships intact
- [x] Timestamps set correctly

## Data Integrity

### Foreign Key Relationships
- ✅ All bids reference valid jobs, users, and buyers
- ✅ All jobs reference valid buyers, categories, and subcategories
- ✅ All skill_user records reference valid users and skills
- ✅ All job_skills records reference valid jobs and skills

### Status Values
- ✅ All accounts: Active (status = 1)
- ✅ All jobs: Published (status = 1), Approved (is_approved = 1)
- ✅ All bids: Pending (status = 0)
- ✅ Verification statuses set based on general_settings defaults

## Testing URLs

### Firm Login Pages
- Firm Login: http://localhost/article/buyer/login
- Firm Dashboard: http://localhost/article/buyer/home
- Firm Opportunities: http://localhost/article/buyer/job/post/index
- View Applications: http://localhost/article/buyer/job/post/bids/{job_id}

### Student Login Pages
- Student Login: http://localhost/article/user/login
- Student Dashboard: http://localhost/article/user/home
- Find Opportunities: http://localhost/article/freelance-jobs
- My Applications: http://localhost/article/user/bid/index

### Admin Pages
- Admin Dashboard: http://localhost/article/admin/dashboard
- View Firms: http://localhost/article/admin/buyers/list
- View Students: http://localhost/article/admin/users/list
- View Opportunities: http://localhost/article/admin/jobs/index
- View Applications: http://localhost/article/admin/jobs/bid

## Next Steps

1. **Test Login:**
   - Login as firms using the credentials above
   - Login as students using the credentials above
   - Verify profiles display correctly

2. **Test Opportunities:**
   - View opportunities on homepage and explore pages
   - Verify opportunity details display correctly
   - Check that applications are visible to firms

3. **Test Applications:**
   - Verify students can see their applications
   - Verify firms can see applications on their opportunities
   - Test application status changes

4. **Admin Verification:**
   - Check admin dashboard shows correct counts
   - Verify all accounts appear in admin lists
   - Check notifications are visible

## Notes

- **Password:** All demo accounts use password `demo123` for easy testing
- **Verification:** Accounts are verified based on general_settings defaults (KYC may be unverified if required by settings)
- **Timestamps:** Applications were created 1-2 days ago to simulate realistic timing
- **Skills:** Skills are created if they don't exist, ensuring compatibility with existing data
- **Categories:** Categories and subcategories are created if they don't exist

## Conclusion

All demo data has been successfully created and verified. The platform now has realistic content that demonstrates the Article Connect concept with firms posting articleship and internship opportunities, and students applying for them. All data follows the existing business logic and database structure without modifying any core functionality.
