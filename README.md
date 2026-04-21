# National Health Database System (NHDS)
## Web Development II — Option 2

### Tech Stack
- PHP Native OOP (no frameworks)
- MySQL + PDO Prepared Statements
- HTML5 / CSS3 (Responsive)
- PHP Sessions + Role-Based Access Control

---

### Setup Instructions

1. **Import Database**
   - Open phpMyAdmin
   - Create database: `nhds`
   - Import: `nhds_schema.sql`

2. **Configure DB credentials**
   - Edit `config/database.php`
   - Set DB_USER and DB_PASS

3. **Place in server**
   - Copy project to `htdocs/nhds` (XAMPP)
   - OR `www/nhds` (WAMP)

4. **Access the app**
   - http://localhost/nhds

---

### Demo Accounts

| Role    | Email              | Password |
|---------|--------------------|----------|
| Admin   | admin@nhds.com     | password |
| Doctor  | doctor@nhds.com    | password |
| Patient | patient@nhds.com   | password |

---

### Project Structure

```
nhds/
├── config/         → DB credentials
├── classes/        → All OOP classes
├── includes/       → header, footer, autoload
├── assets/         → style.css
├── pages/
│   ├── admin/      → Admin pages
│   ├── doctor/     → Doctor pages
│   └── patient/    → Patient pages
├── index.php       → Redirect entry point
├── login.php
├── register.php
├── logout.php
└── nhds_schema.sql → Database schema
```

---

### Features
#### Core (20 marks)
- [x] Register / Login / Logout with sessions
- [x] Role-based access (Admin / Doctor / Patient)
- [x] Admin: manage all users
- [x] Doctor: add records, update diagnosis, add prescriptions
- [x] Patient: view own records and prescriptions only
- [x] Patient: update profile + change password
- [x] Security: Prepared Statements, password_hash, htmlspecialchars

#### Bonus (10 marks)
- [x] Search patients by name/email — +2
- [x] Dashboard statistics per role — +3
- [x] Medical history timeline — +3
- [x] Middleware role-based access class — +2
