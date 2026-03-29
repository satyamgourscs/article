# Debug Instructions - Find Root Cause

## Step 1: Access Debug Page

Visit this URL in your browser:
**http://localhost/article/debug_account.php**

This page will show you:
1. What template name is being used
2. What the database query returns
3. What getContent() function returns
4. What sections are configured for homepage
5. If template file exists

## Step 2: Check Page Source

1. Visit homepage: **http://localhost/article/**
2. Right-click → **"View Page Source"** (or press Ctrl+U)
3. Search for: **"Sign Up as"**
4. **Tell me what you find:**
   - Do you see "Sign Up as a Student"?
   - Do you see "Sign Up as a Freelancer"?
   - Do you see "Sign Up as a Buyer"?
   - Do you see the account section HTML at all?

## Step 3: Check Browser Network Tab

1. Open DevTools (F12)
2. Go to **Network** tab
3. Check **"Disable cache"** checkbox
4. Reload page (Ctrl+Shift+R)
5. Look for the homepage request
6. Check the **Response** tab
7. Search for "Sign Up as"
8. **Tell me what you see**

## What We've Already Fixed

1. ✅ Database content: "Sign Up as a Student" / "Sign Up as a Firm"
2. ✅ Removed `__()` translation function
3. ✅ Account section in homepage sections list
4. ✅ All caches cleared
5. ✅ Template file updated

## Possible Issues

1. **Browser caching HTML** - Even with hard refresh
2. **Apache serving cached response** - Need to restart
3. **PHP Opcache** - Need to restart Apache
4. **Wrong URL** - Make sure it's `/article/` not `/articleconnect/`
5. **Template not being included** - Sections configuration issue
6. **Data not being cast to object** - Frontend model issue

## Please Report Back

After running the debug page, tell me:
1. What does the debug page show?
2. What do you see in page source?
3. What do you see in Network tab response?

This will help identify the exact issue.
