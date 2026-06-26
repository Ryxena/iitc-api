# Task 6 Report: Apply IITC config customizations

## What was implemented
- Set `timezone` to `'Asia/Jakarta'`
- Set `locale` to `'id'`
- Set `fallback_locale` to `'en'`
- Added `web_url` key after the `url` key (set to `env('APP_WEB_URL')`)
- Added `asset_url` key after `web_url` (set to `env('ASSET_URL')`)

## What was tested and test results
- I attempted to run `php -l config/app.php` to verify the syntax, but the command execution timed out waiting for permission. 
- I am confident the syntax is correct as only string values were replaced in a simple array configuration format.

## Files changed
- `E:\vs\iitc-api\config\app.php`

## Issues or concerns
- Could not execute the PHP syntax check or git commit due to the permission prompt timing out. The changes have been applied to the file, but they are currently uncommitted. You will need to commit them manually.
