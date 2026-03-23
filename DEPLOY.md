# Deployment Guide — CAPD ASBL

## Architecture

```
GitHub (main branch)
    │
    │  push → GitHub Actions
    ▼
cPanel / public_html/   ← your domain root (e.g. capd-asbl.org)
    ├── index.php
    ├── config.php        ← NOT from git, created manually on server
    ├── .env              ← NOT from git, created manually on server
    ├── .htaccess
    ├── uploads/          ← NOT from git, managed on server
    └── ...
```

---

## 1. First-time cPanel setup (Namecheap)

### A. Create the MySQL database

1. cPanel → **MySQL Databases**
2. Create database: `capd_db`
3. Create user: `capd_root` + strong password
4. Add user to database → grant **All Privileges**
5. Go to **phpMyAdmin** → select the database → **Import** → upload `sql/capd.sql`
6. Then import `sql/update_v2.sql`, then `sql/update_v3.sql` in order

### B. Upload config.php manually (once)

Create `public_html/config.php` via cPanel File Manager with production values:

```php
<?php
// Production config — DO NOT commit this file
$_ENV['DB_HOST']     = 'localhost';
$_ENV['DB_USER']     = 'yourusername_capduser';
$_ENV['DB_PASS']     = 'your_strong_db_password';
$_ENV['DB_NAME']     = 'yourusername_capd';
$_ENV['BASE_URL']    = 'https://yourdomain.com';
$_ENV['APP_ENV']     = 'production';
$_ENV['DEFAULT_LANG']= 'fr';
$_ENV['CSRF_SECRET'] = 'generate_64_random_chars_here';
```

> Tip: generate CSRF_SECRET with: `openssl rand -hex 32`

### C. Create uploads/ folders on server

Via cPanel File Manager, create these folders inside `public_html/uploads/`:
```
activities/   centres/   hero/   members/   partners/   posts/   favicon/
```
Upload the existing `uploads/.htaccess` to `public_html/uploads/.htaccess`.

### D. Set folder permissions

In cPanel File Manager, set `uploads/` and all subfolders to **755**.

---

## 2. Connect GitHub → cPanel via FTP (CI/CD)

### A. Get FTP credentials from Namecheap cPanel

cPanel → **FTP Accounts** → note or create:
- Host: `ftp.capd-rdc.org`
- Username: `capd@capd-rdc.org`
- Password: your FTP password

### B. Add GitHub Secrets

In your GitHub repo → **Settings → Secrets and variables → Actions → New repository secret**:

| Secret name | Value |
|-------------|-------|
| `FTP_HOST`  | `ftp.yourdomain.com` |
| `FTP_USER`  | `yourusername@yourdomain.com` |
| `FTP_PASS`  | your FTP password |

### C. How it works

Every `git push` to `main` triggers `.github/workflows/deploy.yml` which:
- Checks out the code
- FTPs all files to `public_html/` **except** `.env`, `config.php`, and `uploads/`

---

## 3. .htaccess differences: local vs production

| Setting | Local (XAMPP) | Production (cPanel) |
|---------|--------------|---------------------|
| `ErrorDocument 404` | `/capd/404.php` | `/404.php` |
| `RewriteBase` | `/capd/` | `/` |
| `BASE_URL` in .env | `http://localhost/capd` | `https://yourdomain.com` |

The `.htaccess` in this repo is set for **production**. For local dev, your `.env` handles `BASE_URL`.

---

## 4. Workflow for ongoing development

```bash
# Work locally on XAMPP
git add .
git commit -m "feat: add new activity section"
git push origin main
# → GitHub Actions auto-deploys to cPanel within ~1 minute
```

### Branches strategy (recommended)

```
main        → production (auto-deploy)
develop     → staging / testing
feature/*   → individual features
```

Change the workflow trigger to `develop` while building, switch to `main` when ready to go live.

---

## 5. Database migrations on production

SQL migration files are in `sql/`. When you add new columns or tables:

1. Write the migration in `sql/update_vX.sql`
2. Run it manually in **phpMyAdmin** on the production database
3. Commit the file to git for history

> Never auto-run migrations from the deploy workflow on shared hosting — too risky.

---

## 6. Admin first login on production

After importing `sql/capd.sql`, the default credentials are:
- Username: `admin`
- Password: `password`

**Change the password immediately** via Admin → Utilisateurs → reset.

---

## 7. SSL (HTTPS)

Namecheap cPanel includes **AutoSSL** (free Let's Encrypt).
- cPanel → **SSL/TLS** → **AutoSSL** → Run
- Once active, update `BASE_URL` in your production `config.php` to `https://`

---

## 8. Checklist before going live

- [ ] `config.php` created on server with production values
- [ ] `APP_ENV=production` in config
- [ ] Database imported (capd.sql + update_v2.sql + update_v3.sql)
- [ ] `uploads/` folders created with correct permissions
- [ ] Admin password changed from default
- [ ] SSL certificate active
- [ ] `BASE_URL` uses `https://`
- [ ] GitHub Secrets set (FTP_HOST, FTP_USER, FTP_PASS)
- [ ] Test a push to verify auto-deploy works
