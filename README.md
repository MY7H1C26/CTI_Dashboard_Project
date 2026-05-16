CTI-Dashboard
Native PHP + MySQL Cyber Threat Intelligence dashboard for a student demo.
Features
User registration, login, logout, profile page
CTI service catalog and reservation/request workflow
Admin management for users, services/products, uploads, reservations, and statistics
Alerts, incidents, indicators of compromise, reports
Tables for investigations and threat intelligence feeds with demo data
PDO prepared statements, sessions, `password\_hash`, `password\_verify`
Simple Bootstrap cybersecurity dashboard UI with sidebar navigation
Demo Accounts
systemadmin  use the password:
```text
pass1234
```
Role	Email
System Administrator	systemadmin@cti.local
Admin pages are available `System Administrator`.
XAMPP Setup
Copy the `CTI-Dashboard` folder into your XAMPP `htdocs` folder.
Example on Windows:
```text
   C:\\xampp\\htdocs\\CTI-Dashboard
   ```
Start XAMPP and run:
Apache
MySQL
Open phpMyAdmin:
```text
   http://localhost/phpmyadmin
   ```
Import the database:
Click `Import`
Choose `database.sql`
Click `Go`
The SQL file creates the database named `cti\_dashboard`.
Check database credentials in:
```text
   config/database.php
   ```
Default XAMPP values are already configured:
```php
   $host = 'localhost';
   $dbName = 'cti\_dashboard';
   $username = 'root';
   $password = '';
   ```
Open the app:
```text
   http://localhost/CTI-Dashboard/
   ```
Login with:
```text
   systemadmin@cti.local
   pass1234
   ```
Project Structure
```text
CTI-Dashboard/
CTI-Dashboard/
├── admin/
│   ├── reservations.php
│   ├── services.php
│   ├── statistics.php
│   └── users.php
├── assets/
│   ├── css/style.css
│   ├── js/app.js
│   └── uploads/
├── config/database.php
├── includes/
│   ├── auth.php
│   ├── footer.php
│   ├── functions.php
│   ├── header.php
│   └── sidebar.php
├── alerts.php
├── dashboard.php
├── database.sql
├── incidents.php
├── indicators.php
├── login.php
├── logout.php
├── profile.php
├── register.php
├── reports.php
├── reserve.php
└── services.php
```

   ## Notes

* Uploaded files are saved in `assets/uploads/`.
* Allowed upload types: JPG, PNG, GIF, PDF.
* Maximum upload size in the application code: 2 MB.
* If uploads fail in XAMPP, check that `file\_uploads` is enabled in `php.ini`.
* This project is intentionally simple and readable: native PHP pages, no Laravel, no Composer required.
