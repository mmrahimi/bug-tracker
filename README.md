# PHP bug-tracker + Unit tests

In this project i do basic CRUD operations with the help of a query-builder and then i test it with PHPUnit.

> 🟡 This project introduced me to PHPUnit and testing in general. REBUILT OLD PROJECT

## 🔧 Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/mmrahimi/bug-tracker
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```
   
3. **Configure environment**
  - Copy `.env.example` to `.env`
  - Set your DB credentials (for both regular and testing DBs)
  - Customize `phpunit.xml` to match your preferences
  
4. **Import the database files**
- Use `bug_tracker.sql` and `bug_tracker_testing.sql` to create the needed DBs and tables

5. **Run the tests**
  ```bash
   php vendor/bin/phpunit
   ```

## 📦 Features
- A Flexible query-builder 
- Unit tests covering important parts of the project
