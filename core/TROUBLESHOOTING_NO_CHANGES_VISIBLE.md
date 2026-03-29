# Troubleshooting: Changes Not Visible After Cache Clear

## Issue
After restarting Apache and hard refreshing browser, changes are not visible on the website.

## Database Verification ✅

**Database content is CORRECT:**
- `account.content` (ID: 78): Contains "Sign Up as a Student" and "Sign Up as a Firm"
- `banner.content` (ID: 64): Contains "Find the Right Articleship & Internship Opportunities"
- `facility.content` (ID: 90): Contains "How Article Connect is Different"
- `top_freelancer.content` (ID: 104): Contains "Top Students"

**Template Setting:** `basic` (correct)

## Possible Causes

### 1. Browser Cache (Most Likely)
Even with hard refresh, some browsers cache aggressively.

**Solution:**
- Open browser DevTools (F12)
- Go to Network tab
- Check "Disable cache" checkbox
- Keep DevTools open while browsing
- Reload page (Ctrl+Shift+R)

### 2. CDN/Proxy Cache
If using a CDN or proxy, it may cache responses.

**Solution:**
- Clear browser cache completely
- Try incognito/private browsing mode
- Try a different browser

### 3. PHP Opcode Cache (OPcache)
PHP may cache compiled code.

**Solution:**
- Restart Apache/PHP-FPM completely
- Or disable OPcache temporarily in php.ini:
  ```ini
  opcache.enable=0
  ```

### 4. Session Cache
Laravel may cache template name in session.

**Solution:**
- Clear browser cookies for localhost
- Or clear Laravel session:
  ```sql
  DELETE FROM sessions;
  ```

### 5. View Cache Still Present
Some compiled views may still exist.

**Solution:**
- Verify view cache is cleared:
  ```powershell
  Get-ChildItem "storage\framework\views" -Filter "*.php" | Measure-Object
  ```
- Should return 0 files
- If not, delete manually:
  ```powershell
  Remove-Item "storage\framework\views\*.php" -Force
  ```

### 6. Database Query Issue
The `getContent()` function uses `orderBy('id', 'desc')->first()` which should get the latest record.

**Verification:**
- Check if there are multiple records:
  ```sql
  SELECT id, tempname, data_keys FROM frontends 
  WHERE data_keys = 'account.content' 
  ORDER BY id DESC;
  ```
- Should only show ID 78 with tempname 'basic'

### 7. Model Casting Issue
The Frontend model casts `data_values` to 'object', which should work.

**Verification:**
- The cast is correct in `app/Models/Frontend.php`
- Laravel should automatically decode JSON to object

## Immediate Actions Taken

1. ✅ Cleared view cache (0 files remaining)
2. ✅ Cleared database cache
3. ✅ Cleared config cache
4. ✅ Verified database content is correct
5. ✅ Verified template name is 'basic'

## Next Steps

1. **Clear browser cookies for localhost**
   - Chrome: Settings → Privacy → Clear browsing data → Cookies
   - Or use DevTools → Application → Cookies → Delete all

2. **Try incognito/private browsing**
   - This bypasses all browser cache
   - If it works in incognito, it's a browser cache issue

3. **Check browser console for errors**
   - Open DevTools (F12)
   - Check Console tab for JavaScript errors
   - Check Network tab for failed requests

4. **Verify Apache is using correct PHP version**
   - Check phpinfo() output
   - Ensure it matches the project requirements

5. **Check Laravel logs**
   - `storage/logs/laravel.log`
   - Look for any errors related to Frontend model or getContent()

6. **Test direct database query**
   - Access the database directly
   - Verify the JSON structure matches what Laravel expects

## Debugging Commands

```powershell
# Check view cache
Get-ChildItem "storage\framework\views" -Filter "*.php" | Measure-Object

# Check application cache
Get-ChildItem "storage\framework\cache\data" -Recurse | Measure-Object

# Verify database content
mariadb.exe -u root -e "USE article_base; SELECT id, tempname, data_keys, JSON_EXTRACT(data_values, '$.freelancer_title') FROM frontends WHERE data_keys = 'account.content' ORDER BY id DESC LIMIT 1;"
```

## Expected Behavior

After clearing all caches:
- Homepage should show "Sign Up as a Student" / "Sign Up as a Firm"
- Banner should show "Find the Right Articleship & Internship Opportunities"
- Facility section should show "How Article Connect is Different"
- Top Students section should show "Top Students"

## If Still Not Working

1. Check if there's a `.env` file override
2. Verify `APP_ENV` is not set to 'production' (which enables aggressive caching)
3. Check if there's a reverse proxy (nginx, Apache mod_proxy) caching responses
4. Verify the URL you're accessing matches the expected route
5. Check if there are multiple Laravel installations and you're accessing the wrong one
