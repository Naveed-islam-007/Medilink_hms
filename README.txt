MEDILINK - PHP + MYSQL HOSPITAL MANAGEMENT SYSTEM
=================================================

TECHNOLOGY
- Frontend: HTML + CSS
- Backend: PHP (PDO)
- Database: MySQL
- Environment: XAMPP / Apache

FEATURES
1. Admin login (session-based)
2. Dashboard with live row counts
3. Patient management        - full CRUD (Add / Edit / Delete / Search)
4. Doctor/provider directory  - full CRUD (Add / Edit / Delete / Search)
5. Hospital/clinic management - full CRUD (Add / Edit / Delete)
6. Pharmacy management        - full CRUD (Add / Edit / Delete)
7. Medicine inventory         - full CRUD (Add / Edit / Delete / Search)
8. Blood donor database       - full CRUD (Add / Edit / Delete / Search)
9. Prescriptions               - full CRUD (Add / Edit / Delete), joins patients + doctors
10. Specialty/symptom doctor search (read-only, joins doctors + hospitals)
11. Relational normalized database with foreign keys

HOW THE CODE IS ORGANIZED
- config/db.php     -> connects to MySQL with PDO
- config/auth.php   -> require_login() guards every protected page
- includes/header.php, includes/footer.php -> shared page layout (sidebar + top bar)
- index.php         -> dashboard
- login.php / logout.php
- patients.php, doctors.php, hospitals.php, pharmacies.php,
  medicines.php, donors.php, prescriptions.php -> one file per table, each doing:
    1. CREATE or UPDATE (a single form is reused for both -
       if a hidden "<table>_id" field is submitted, the code runs an
       UPDATE query; otherwise it runs an INSERT)
    2. DELETE (?delete=ID in the URL)
    3. Load a row into the form when the user clicks "Edit" (?edit=ID)
    4. READ - list/search rows in a table below the form
- search.php        -> read-only symptom/specialty search across doctors

Every page follows the same simple structure, so once you understand
patients.php you can read the others quickly.

INSTALLATION
1. Install XAMPP.
2. Start Apache and MySQL from XAMPP Control Panel.
3. Copy the MediLink folder into:
   C:\xampp\htdocs\
4. Open http://localhost/phpmyadmin
5. Create/import the database by selecting the "Import" tab and choosing database.sql.
6. Open:
   http://localhost/MediLink/login.php
7. Login:
   Username: admin
   Password: admin123

DATABASE CONFIG
File: config/db.php
Default XAMPP MySQL settings:
host = localhost
user = root
password = empty
database = medilink

IMPORTANT
For a real deployment, use password_hash/password_verify instead of SHA-256,
add CSRF protection, role-based authorization, server-side validation,
audit logging, HTTPS, backups, and stronger access controls.
