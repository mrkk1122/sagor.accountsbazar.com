# sagor.accountsbazar.com

Database schema files:
- SQLite: db/schema.sql
- MySQL/MariaDB (phpMyAdmin): db/schema.mysql.sql

## OpenRouter API Key Setup (XAMPP/Apache)

Use one of the following methods.

### Method A: .htaccess (quick)

1. Open `.htaccess` in project root.
2. Replace this value:

```apache
SetEnv OPENROUTER_API_KEY "YOUR_OPENROUTER_API_KEY"
```

3. Restart Apache from XAMPP Control Panel.

### Method B: Apache vhost (recommended)

1. Open your Apache vhost config (example: `C:/xampp/apache/conf/extra/httpd-vhosts.conf`).
2. Inside your `<VirtualHost ...>` block add:

```apache
SetEnv OPENROUTER_API_KEY "YOUR_OPENROUTER_API_KEY"
```

3. Ensure vhost has `AllowOverride All` for this site and restart Apache.

### Windows command (system env, optional)

Run PowerShell as Administrator:

```powershell
setx OPENROUTER_API_KEY "YOUR_OPENROUTER_API_KEY" /M
```

Then restart Apache.

### Method C: Local secret file (shared hosting friendly)

1. Copy `includes/secret.local.example.php` to `includes/secret.local.php`.
2. Edit `includes/secret.local.php` and set your real OpenRouter key.
3. Keep this file private (it is git-ignored and includes path is blocked in `.htaccess`).
4. Open `/health-check.php` to verify:
	- `openrouter_key_loaded: true`
	- `openrouter_key_format_valid: true`