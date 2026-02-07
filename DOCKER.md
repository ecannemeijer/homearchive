# Mijn Abonnementen - Docker Setup

Gemakkelijke lokale development setup met Docker.

## Vereisten

- [Docker](https://www.docker.com/products/docker-desktop)
- [Docker Compose](https://docs.docker.com/compose/)

## Quick Start

### 1. Start de containers

```bash
docker-compose up -d
```

Dit zal:
- MySQL database starten en schema importeren
- PHP Apache server starten
- Database connectie testen

### 2. Install PHP dependencies

```bash
docker-compose exec php composer install
```

### 3. Configure .env

```bash
# In je .env bestand (al voorgeconfigureerd via docker-compose):
DB_HOST=mysql
DB_USER=abonnementen
DB_PASSWORD=password
DB_NAME=abonnementen
```

### 4. Toegang tot applicatie

Open in browser: http://localhost

## Commando's

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f php
docker-compose logs -f mysql

# Execute PHP command
docker-compose exec php php database/install.php

# MySQL access
docker-compose exec mysql mysql -u abonnementen -ppassword -D abonnementen

# Bash in PHP container
docker-compose exec php bash
```

## Volumes

- MySQL data: `mysql_data` (persistent)
- Application files: Mounted van lokale directory

## Ports

- **HTTP**: localhost:80
- **MySQL**: localhost:3306

## Reset Database

```bash
docker-compose down -v  # Verwijdert volumes
docker-compose up -d    # Recreate everything
```

## Troubleshooting

### Port 80 already in use
```bash
docker-compose down
# Of gebruik ander port in docker-compose.yml
```

### MySQL wachtwoord problemen
```bash
docker-compose exec mysql mysql -u root -proot
```

### PHP modules ontbreken
```bash
docker-compose exec php docker-php-ext-install [extensie]
```

---

Veel plezier met development!
