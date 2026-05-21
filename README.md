# Olympiad Math

Clean PHP 8 + MySQL/MariaDB MVP for a bilingual olympiad mathematics platform. Russian is the default UI language; English is the second active language. Content translations use separate `*_texts` tables with `lang VARCHAR(10)`.

## Setup

1. Copy `includes/config.example.php` to `includes/config.php`.
2. Put real database credentials into `includes/config.php`.
3. Create an empty MySQL/MariaDB database.
4. Import:

```bash
mysql -u USER -p DATABASE < database/schema.sql
mysql -u USER -p DATABASE < database/seed.sql
```

5. Point the subdomain document root to this folder. No `/public` folder is required.

## Admin User

Create the first admin from SQL:

```sql
INSERT INTO users (name, email, password_hash, role, created_at, updated_at)
VALUES ('Admin', 'admin@example.com', '$2y$10$REPLACE_WITH_PASSWORD_HASH', 'admin', NOW(), NOW());
```

Generate a password hash with:

```bash
php -r "echo password_hash('your-password', PASSWORD_DEFAULT), PHP_EOL;"
```

## Hostinger Notes

- Upload all files except `includes/config.php`.
- Create `includes/config.php` directly on the server from the example.
- Ensure `uploads/problems/` is writable by PHP.
- Use PHP 8+ and MySQL/MariaDB with `utf8mb4`.
- Internal links are simple PHP pages, so `https://olymp.maths4u.sbs/` works without rewrite rules.

## Content

- All UI labels come from `includes/lang.php`.
- All course/chapter/problem content comes from text tables.
- Math content supports MathJax 3 syntax: inline `\( ... \)` and display `\[ ... \]`.
- Guest bookmarks and solved states use `localStorage`.
- Logged-in database progress hooks are prepared through `bookmarks` and progress tables.

## Scripts

```bash
php scripts/import_problems.php problems.json
php scripts/export_book.php number-theory ru
php scripts/validate_content.php
```

