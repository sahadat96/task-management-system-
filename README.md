# Task Module - Laravel Application

## Overview
A dynamic task management module with repeater fields for adding multiple tasks without page reload.

## Features
-  Dynamic task addition/removal without page reload
-  Real-time form validation
-  Bulk task creation with optimized performance
-  API endpoints for integration

## Installation

### Prerequisites
- PHP 8.1+
- Composer
- MySQL

### Setup Steps
 **Clone and install dependencies**
   ```bash
   composer install
   ```

 ### Environment Configuration
```
cp .env.example .env
php artisan key:generate
```

 ### Update .env with database credentials
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=database_name
DB_USERNAME=username
DB_PASSWORD=password
```

 ### Start Development Server
 ```
   php artisan serve
```
##  API Endpoints

### Products
| Feature          | Endpoint          | Method | Auth Required |
|------------------|-------------------|--------|---------------|
| Create Multiple Tasks  | `/api/tasks`   | POST    | No            
| Get All Tasks  | `/api/get-tasks` | GET | No      |


 ### Architecture
- MVC Pattern: Clean separation of concerns
- Service Layer: Business logic separation
- Repository Pattern: Data access abstraction
- Validation Layer: Dedicated form request validation




   
