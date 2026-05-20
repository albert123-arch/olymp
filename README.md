# Olympiad Maths MVP

Clean standalone PHP/MySQL/Bootstrap subsite for Olympiad Maths.

## Setup

1. Create a separate MySQL database, for example `olymp`.
2. Import `database/schema.sql`.
3. Copy `config.example.php` to `config.php` and set credentials.
4. Serve the project root so `index.php` is the subdomain entry point.

Images are stored under `uploads/problems/{problem_code}/` and are referenced by path in `problem_media`; images are not stored as base64.
