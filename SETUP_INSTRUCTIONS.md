# Restaurant Project Setup Instructions

## Prerequisites
- XAMPP installed with PHP and MySQL
- Node.js and npm installed
- Composer (installed locally in project)

## Setup Steps Completed

### 1. Environment Configuration
- Created `.env` file from `.env.example`
- Configured MySQL database connection:
  - Database: `restaurant`
  - Username: `root`
  - Password: (empty)
  - Host: `127.0.0.1`
  - Port: `3306`

### 2. Dependencies Installation
```bash
# PHP dependencies (using XAMPP's PHP)
/mnt/c/xampp/php/php.exe composer.phar install

# Node.js dependencies
npm install
```

### 3. Laravel Setup
```bash
# Generate application key
/mnt/c/xampp/php/php.exe artisan key:generate

# Create database
/mnt/c/xampp/mysql/bin/mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS restaurant"

# Run migrations
/mnt/c/xampp/php/php.exe artisan migrate
```

### 4. Frontend Build
```bash
# Build assets for production
npm run build

# Or for development with hot reload
npm run dev
```

## Running the Application

### Start the Laravel server:
```bash
/mnt/c/xampp/php/php.exe artisan serve
```

### Access the application:
- Open your browser and go to: http://127.0.0.1:8000
- You should see the Laravel welcome page with React/Inertia.js

## Available Routes
- `/` - Welcome page
- `/login` - Login page
- `/register` - Registration page
- `/dashboard` - Dashboard (requires authentication)
- `/settings/profile` - Profile settings
- `/settings/password` - Password settings
- `/settings/appearance` - Appearance settings

## Development Commands

### Run development server with hot reload:
```bash
npm run dev
```

### Run tests:
```bash
/mnt/c/xampp/php/php.exe artisan test
```

### Clear cache:
```bash
/mnt/c/xampp/php/php.exe artisan cache:clear
/mnt/c/xampp/php/php.exe artisan config:clear
/mnt/c/xampp/php/php.exe artisan route:clear
```

## Tech Stack
- **Backend**: Laravel 12.x
- **Frontend**: React with TypeScript
- **UI Components**: Custom components with Tailwind CSS
- **State Management**: Inertia.js
- **Database**: MySQL
- **Build Tool**: Vite

## Project Structure
- `/app` - Laravel application logic
- `/resources/js` - React/TypeScript frontend code
- `/resources/js/pages` - Page components
- `/resources/js/components` - Reusable components
- `/resources/js/layouts` - Layout components
- `/database/migrations` - Database migration files
- `/routes` - Application routes
- `/public` - Public assets and entry point

## Notes
- The PHP warning about "openssl module already loaded" can be ignored
- Make sure XAMPP's Apache and MySQL services are running
- The application uses SQLite by default but has been configured for MySQL