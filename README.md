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
1. **Clone and install dependencies**
   ```bash
   composer install
   npm install

3. ### Environment Configuration
cp .env.example .env
php artisan key:generate


4. ### Update .env with database credentials

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_module
DB_USERNAME=your_username
DB_PASSWORD=your_password


5. ### Start Development Server
   php artisan serve

API Endpoints
Base URL: http://localhost:8000/api
Method	Endpoint	Description	 Parameters
POST	/tasks	Create multiple tasks	Array of task objects
GET	/tasks	Get all tasks	None




   
