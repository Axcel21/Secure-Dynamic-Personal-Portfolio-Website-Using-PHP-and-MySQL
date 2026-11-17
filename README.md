# Secure Dynamic Personal Portfolio (PHP + MySQL)

Dynamic version of the static portfolio: content lives in MySQL and is editable from a password-protected admin panel.

## Requirements
PHP 8.0+ (with PDO MySQL and fileinfo), MySQL/MariaDB. Easiest: XAMPP.

## Setup (XAMPP)
1. Copy this folder to `C:\xampp\htdocs\portfolio-php`.
2. Copy your original `images/` files (facebook.jpg, rise brew.jpg, cisco.jpg, Power Point Project.jpg, adobe illustrator.jpg) into `images/`. Optional: put your résumé in `assets/resume.pdf`.
3. Start Apache + MySQL. In phpMyAdmin → Import → choose `database/schema.sql`.
4. Edit `config/config.php` (DB user/password, and `base_url` = your folder name).
5. Create the admin account in a terminal inside the project folder:
   `php scripts/create_admin.php admin "axcel123456789"`
6. Visit http://localhost/portfolio-php/ and the admin at http://localhost/portfolio-php/admin/login.php

## Security features
- PDO prepared statements everywhere (SQL injection)
- Output escaping with `e()` on every value (XSS) + Content-Security-Policy header
- CSRF tokens on every POST form
- `password_hash` / `password_verify`, session ID regenerated on login, HttpOnly + SameSite=Strict cookies
- Login throttling (5 tries / 15 min / IP) and contact rate limit (5 / hour / IP), honeypot field against bots
- Server-side validation (the JS validation is only convenience)
- Image uploads: MIME sniffing with `finfo`, `getimagesize`, size limit, random filenames, PHP execution disabled in `uploads/`
- `config/` and `includes/` blocked from direct web access; DB credentials never in HTML
- IPs stored only as SHA-256 hashes

## Before deploying publicly
Use HTTPS, set a strong DB password and a non-root DB user, and note that GitHub Pages cannot run PHP — use a PHP host (e.g. InfinityFree, Hostinger).
