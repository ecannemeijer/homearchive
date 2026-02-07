# 🚀 SETUP GUIDE - Mijn Abonnementen

Complete stap-voor-stap installatie van de applicatie.

## System Vereisten

- **OS**: Linux, macOS of Windows
- **PHP**: 7.4+ (recommend 8.1+)
- **MySQL**: 5.7+ of MariaDB 10.3+
- **Web Server**: Apache (met mod_rewrite) of Nginx
- **Composer**: Latest versie
- **Git**: Voor cloning

## Installatiestappen

### Option 1: Docker (Aanbevolen voor Development)

```bash
# Clone project
git clone <repository> homearchive
cd homearchive

# Start containers
docker-compose up -d

# Install PHP dependencies
docker-compose exec php composer install

# Done! Daarna http://localhost
```

Zie [DOCKER.md](DOCKER.md) voor meer details.

### Option 2: Manual Installation

#### 1. Project voorbereiding

```bash
# Clone of download
git clone <repository> homearchive
cd homearchive

# Install Composer dependencies
composer install
```

#### 2. MySQL Database

```bash
# Login in MySQL
mysql -u root -p

# Create database
CREATE DATABASE abonnementen;
CREATE USER 'abonnementen'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON abonnementen.* TO 'abonnementen'@'localhost';
FLUSH PRIVILEGES;

# Import schema
USE abonnementen;
SOURCE database/schema.sql;
```

Of via PHP install script:
```bash
php database/install.php
```

#### 3. Environment Configuration

Maak/update `.env` bestand:

```env
# Database connection
DB_HOST=localhost
DB_PORT=3306
DB_NAME=abonnementen
DB_USER=abonnementen
DB_PASSWORD=password

# Encryption (min 32 chars - genereer veilige string)
ENCRYPTION_KEY=your-super-secure-32-character-minimum-key!

# Application
APP_NAME="Mijn Abonnementen"
APP_DEBUG=true
APP_URL=http://localhost

# Sessions
SESSION_NAME=subscription_app

# File uploads
UPLOAD_DIR=uploads
MAX_UPLOAD_SIZE=5242880
```

#### 4. Bestandspermissies (Linux/Mac)

```bash
# Base directories
chmod 755 .

# Uploads directory (must be writable)
chmod 755 uploads
chmod 755 public

# Webserver user (typically www-data on Linux)
sudo chown -R www-data:www-data uploads/
sudo chown -R www-data:www-data public/
```

#### 5. Web Server Configuration

**Apache VirtualHost** (`/etc/apache2/sites-available/abonnementen.conf`):

```apache
<VirtualHost *:80>
    ServerName abonnementen.local
    ServerAlias www.abonnementen.local
    
    DocumentRoot /var/www/homearchive/public
    
    <Directory /var/www/homearchive>
        AllowOverride All
        Require all granted
    </Directory>
    
    <Directory /var/www/homearchive/public>
        AllowOverride All
        Require all granted
        
        # Rewrite rules
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteBase /
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^(.*)$ index.php/$1 [L]
        </IfModule>
    </Directory>
    
    # Logging
    ErrorLog ${APACHE_LOG_DIR}/abonnementen-error.log
    CustomLog ${APACHE_LOG_DIR}/abonnementen-access.log combined
</VirtualHost>
```

Activate:
```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2ensite abonnementen
sudo systemctl restart apache2
```

**Nginx** (`/etc/nginx/sites-available/abonnementen`):

```nginx
server {
    listen 80;
    server_name abonnementen.local;
    
    root /var/www/homearchive/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
}
```

Enable: `sudo ln -s /etc/nginx/sites-available/abonnementen /etc/nginx/sites-enabled/`

#### 6. Hosts File (Local Development)

Add to `/etc/hosts` (Linux/Mac) or `C:\Windows\System32\drivers\etc\hosts` (Windows):

```
127.0.0.1 abonnementen.local
```

#### 7. Test Installation

```bash
# Check PHP version
php --version

# Check database connection
php -r "
require 'config/config.php';
try {
    get_db();
    echo 'Database connection OK';
} catch (Exception $e) {
    echo 'Error: ' . \$e->getMessage();
}
"

# Navigate to browser
http://localhost/  (if Apache root)
http://abonnementen.local/  (if VirtualHost configured)
```

## First Login

Default test account (created via install):
- **Email**: test@example.com
- **Password**: password

*Change these immediately after first login!*

## Production Deployment

### Security Checklist

- [ ] `APP_DEBUG=false` in .env
- [ ] Update `ENCRYPTION_KEY` met veilige random string
- [ ] HTTPS/SSL enabled
- [ ] Update database password in .env
- [ ] Restrict file permissions (uploads: 700)
- [ ] Backup database regularly
- [ ] Monitor error logs
- [ ] Update PHP to latest secure version
- [ ] Disable debug mode
- [ ] Use strong database user password

### Nginx Production Config

```nginx
server {
    listen 443 ssl http2;
    server_name abonnementen.example.com;
    
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/key.key;
    
    root /var/www/abonnementen/public;
    
    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    
    # PHP pooling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm.sock;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}

# HTTP redirect
server {
    listen 80;
    server_name abonnementen.example.com;
    return 301 https://$server_name$request_uri;
}
```

## Troubleshooting

### Database Errors

```
"SQLSTATE[HY000]: General error: 2006 MySQL server has gone away"
```
**Solution**: Controleer MySQL connection, restart service

```
"SQLSTATE[42S02]: Table or view not found"
```
**Solution**: Run database install script: `php database/install.php`

### Permission Errors

```bash
# Fix uploads folder
sudo chown www-data:www-data uploads/
sudo chmod 755 uploads/

# Fix public folder
sudo chown www-data:www-data public/
sudo chmod 755 public/
```

### Apache Rewrite Not Working

```bash
# Enable mod_rewrite
sudo a2enmod rewrite

# Check .htaccess is present
ls -la public/.htaccess

# Restart Apache
sudo systemctl restart apache2
```

### Encryption Key Issues

```
"openssl_decrypt(): Authentication tag verification failed"
```
**Solution**: Zorg dat ENCRYPTION_KEY correct en consistent is

### Uploads Not Working

- Check `uploads/` permissions (755)
- Verify `MAX_UPLOAD_SIZE` in .env
- Check webserver max upload in php.ini: `upload_max_filesize`
- Check `post_max_size`

## Performance Tuning

### PHP Configuration (`php.ini`)

```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 60
memory_limit = 256M
max_input_vars = 5000
```

### MySQL Optimization

```sql
-- Add indexes if not present
CREATE INDEX idx_user_subscription ON subscriptions(user_id);
CREATE INDEX idx_user_password ON passwords(user_id);
CREATE INDEX idx_sub_end_date ON subscriptions(end_date);
```

### Caching (Optional)

Implementeer Redis/Memcached voor:
- Session storage
- Query result caching
- User data caching

## Backup Strategy

```bash
# Daily database backup
mysqldump -u abonnementen -p abonnementen > backup_$(date +%Y%m%d).sql

# Backup uploads
tar -czf uploads_backup_$(date +%Y%m%d).tar.gz uploads/

# Full backup script
#!/bin/bash
BACKUP_DIR="/backups"
DATE=$(date +%Y%m%d_%H%M%S)

mysqldump -u abonnementen -p abonnementen > $BACKUP_DIR/db_$DATE.sql
tar -czf $BACKUP_DIR/uploads_$DATE.tar.gz uploads/

# Keep last 30 days
find $BACKUP_DIR -mtime +30 -delete
```

## Support & Debugging

### Enable Debug Mode (Development Only)

In `.env`:
```env
APP_DEBUG=true
```

### Check Application Health

```
http://localhost/api/health
```

### View Apache Logs

```bash
tail -f /var/log/apache2/abonnementen-error.log
tail -f /var/log/apache2/abonnementen-access.log
```

### View PHP Errors

```bash
tail -f /var/log/php-fpm.log
```

---

**Setup voltooid! 🎉**

Veel plezier met het beheren van uw abonnementen!

Voor vragen: Check README.md of contacteer support.
