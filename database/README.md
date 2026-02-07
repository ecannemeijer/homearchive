# Database Setup

## 📁 Bestanden

- **schema.sql** - Complete database schema met alle tabellen, users, categories EN voorbeeld offers
- **add_price_comparison.sql** - Alleen de nieuwe price comparison tabellen + voorbeeld data (voor bestaande databases)

## 🚀 Nieuw Project Setup

Voor een **nieuwe installatie** gebruik je `schema.sql`:

```bash
# Via command line
mysql -u root -p abonnementen < database/schema.sql

# Of via MySQL client
mysql -u root -p
CREATE DATABASE IF NOT EXISTS abonnementen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE abonnementen;
SOURCE /path/to/database/schema.sql;
```

Dit creëert:
- ✅ Alle tabellen (users, subscriptions, passwords, documents, categories, monthly_costs, offers, savings_recommendations)
- ✅ System user (id=6)
- ✅ Admin user (admin@example.com / admin123)
- ✅ 6 standaard categorieën
- ✅ 18 voorbeeld aanbiedingen (Netflix, Ziggo, Basic-Fit, etc.)

## 🔄 Bestaande Database Updaten

Voor **bestaande installatie** die de price comparison feature wil toevoegen:

```bash
# Via command line
mysql -u root -p abonnementen < database/add_price_comparison.sql

# Of via MySQL client
USE abonnementen;
SOURCE /path/to/database/add_price_comparison.sql;
```

Dit voegt toe:
- ✅ `offers` tabel
- ✅ `savings_recommendations` tabel
- ✅ 18 voorbeeld aanbiedingen

## 📊 Database Schema Overzicht

### Core Tables
- **users** - Login accounts (admin / regular users)
- **subscriptions** - Abonnementen en verzekeringen
- **passwords** - Password vault (encrypted)
- **documents** - Uploaded documenten
- **categories** - Categorieën voor organisatie
- **monthly_costs** - Maandelijkse kosten tracking

### Price Comparison Tables (NEW)
- **offers** - Aanbiedingen van providers voor prijsvergelijking
- **savings_recommendations** - Besparingsaanbevelingen per abonnement

## 🔐 Default Login

Na het uitvoeren van `schema.sql`:

```
Email: admin@example.com
Password: admin123
```

⚠️ **Belangrijk:** Wijzig het admin wachtwoord na eerste login!

## 📝 Voorbeeld Offers Data

De schema bevat 18 voorbeeldaanbiedingen verdeeld over:
- 🎬 Streaming (Netflix, Disney+, Amazon Prime, Videoland)
- 💻 Software (Microsoft 365, Adobe, Dropbox)
- 🏥 Verzekering (OHRA, Zilveren Kruis, VGZ)
- 💪 Sport (Basic-Fit, TrainMore, Fit For Free)
- 📡 Internet/Telecom (Ziggo, KPN, T-Mobile)

Deze kunnen via het admin panel worden aangepast of verwijderd.

## 🛠️ Troubleshooting

### Foreign Key Errors
Als je een FK error krijgt bij importeren:
```sql
SET FOREIGN_KEY_CHECKS=0;
-- Run your SQL
SET FOREIGN_KEY_CHECKS=1;
```
(Dit staat al in de schema.sql)

### Tabellen bestaan al
Alle CREATE TABLE statements gebruiken `IF NOT EXISTS`, dus het is veilig om opnieuw uit te voeren.

### Duplicate Entry bij INSERT
Alle INSERT statements gebruiken `ON DUPLICATE KEY UPDATE`, dus het is veilig om opnieuw uit te voeren.

## 📖 Meer Info

Voor meer informatie over de database structuur, zie de comments in `schema.sql`.
