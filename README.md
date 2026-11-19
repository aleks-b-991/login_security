# Secure Login Page

This repository fixes the security problems in a simple PHP login page.

| File | What it does |
|---|---|
| `login.html` | Login form. Sends data with POST, uses `type="password"`, and includes a Content-Security-Policy meta tag. |
| `user_credential_check.php` | Login handler. Uses PDO prepared statements (no SQL injection), `password_verify()`, and a generic error message. |
| `schema.sql` | Creates the `users` table, which stores password hashes instead of plain-text passwords. |
| `Login_Security_Explanation.txt` | Written explanation of the security issues and fixes (Parts A, B, and C). |

## Running it locally

1. Create the table: `mysql -u root -p < schema.sql`
2. Add a user. Generate a hash with
   `php -r "echo password_hash('MyS3cure!Pass', PASSWORD_DEFAULT), PHP_EOL;"`
   and insert it into `users`.
3. Set the database environment variables and start PHP's built-in server:

```bash
export DB_HOST=127.0.0.1 DB_NAME=webapp DB_USER=webuser DB_PASS=yourpassword
php -S localhost:8000
```

4. Open http://localhost:8000/login.html

On a real site, the page must be served over HTTPS.
