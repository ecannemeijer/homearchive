# Production Database Setup

## Problem
In production, when saving a subscription, you get:
```
SQLSTATE[23000]: Integrity constraint violation: 1452
Cannot add or update a child row: a foreign key constraint fails
```

This happens because the **system user (id=6)** doesn't exist in production, but the application tries to assign all data to this user.

## Solution

### Step 1: Run Setup Script
Execute this on your production server:

```bash
php setup_production.php
```

This will:
- ✓ Create system user (id=6) if it doesn't exist
- ✓ Create admin user if it doesn't exist
- ✓ Verify database is configured correctly

### Step 2: Login Credentials
After setup, you can login with:
- **Email:** `admin@example.com`
- **Password:** `admin123`

### Step 3: If Using Environment Variables
Make sure your `.env` file (or server env vars) has:
```
DB_HOST=localhost
DB_PORT=3306
DB_NAME=abonnementen
DB_USER=root
DB_PASSWORD=your_password
```

The script reads these automatically.

## What is the System User?

This application uses a **shared data model** where ALL users see and edit the same data. The system user (id=6) is a internal account that owns all shared data:
- All subscriptions
- All passwords
- All documents
- All categories

Login users are separate and don't own individual data - they all access data through the system user.

## Manual Alternative

If you can't run the script, login to MySQL directly:

```sql
-- Disable FK checks
SET FOREIGN_KEY_CHECKS=0;

-- Create system user
INSERT INTO users (id, name, email, password, is_admin) 
VALUES (6, 'System', 'system@example.com', '', 0) 
ON DUPLICATE KEY UPDATE id=6;

-- Create admin user  
INSERT INTO users (name, email, password, is_admin) 
VALUES ('Administrator', 'admin@example.com', '$2y$10$SHWiFzOIWMfkgfgJcXz93eptOE5e648shifWZrHHR94FC.JvUJQJy', 1) 
ON DUPLICATE KEY UPDATE email='admin@example.com';

-- Re-enable FK checks
SET FOREIGN_KEY_CHECKS=1;
```

## Troubleshooting

If you still get the FK error after running setup:

1. Verify system user was created:
```sql
SELECT * FROM users WHERE id = 6;
```

2. Check subscriptions table:
```sql
SELECT * FROM subscriptions LIMIT 1;
```

3. View all users:
```sql
SELECT id, name, email, is_admin FROM users;
```

## Support
If the issue persists, check that:
- Database connection is correct in `.env`
- MySQL user has `INSERT` permission on users table
- Foreign key constraints are enabled
