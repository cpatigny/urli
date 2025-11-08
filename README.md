# Urli - URL Shortener

A modern URL shortener built with PHP backend and React frontend, featuring a clean API and beautiful user interface.

## 🚀 Features

- **URL Shortening**: Transform long URLs into short, memorable links
- **Custom Short Codes**: Create personalized short codes
- **Click Analytics**: Track clicks and view statistics
- **Docker Support**: Easy local development setup

## 🏗️ Architecture

```
Frontend (React + Vite) ←→ Backend (PHP 8.3) ←→ Database (MySQL 8.0)
```

## 🛠️ Tech Stack

### Backend
- **PHP 8.3** with Apache
- **MySQL 8.0** database

### Frontend
- **React** with Vite

### Docker
- **Docker & Docker Compose** for local development

## 🚀 Quick Start (Development)

### Prerequisites
- Docker & Docker Compose
- Git

### 1. Clone Repository
```bash
git clone https://github.com/cpatigny/urli.git
cd urli
```

### 2. Setup Backend
```bash
# Copy environment file
cp backend/.env.example backend/.env

# Install PHP dependencies
cd backend
composer install
cd ..
```

### 3. Start Development Environment
```bash
# Start all services
docker-compose up -d

# Check if running
docker-compose ps
```

### 4. Setup Frontend
```bash
cd frontend
npm install
npm run dev
```

## 📚 API Documentation

### Authentication

#### Register
```bash
POST /api/auth/register
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "yourpassword"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "User registered successfully",
    "user": {
      "id": 1,
      "email": "user@example.com"
    }
  }
}
```

**Validation:**
- Email must be valid format
- Password must be at least 6 characters
- Email must be unique

#### Login
```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "yourpassword"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Login successful",
    "user": {
      "id": 1,
      "email": "user@example.com"
    }
  }
}
```

#### Logout
```bash
POST /api/auth/logout
```

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Logout successful"
  }
}
```

#### Get Current User
```bash
GET /api/auth/me
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "email": "user@example.com"
    }
  }
}
```

### URL Shortening

#### Shorten URL
```bash
POST /api/shorten
Content-Type: application/json

{
  "url": "https://www.example.com",
  "custom_code": "example"  // optional
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "short_code": "abc123",
    "short_url": "http://localhost:8000/abc123",
    "original_url": "https://www.example.com",
    "created_at": "2025-01-20 10:30:45",
    "clicks": 0,
    "existing": false
  }
}
```

#### Redirect to Original URL
```bash
GET /{shortCode}
```
Returns a `302 redirect` to the original URL.

## 🔧 Configuration

### Environment Variables

Create `backend/.env` from the example:

```bash
# Application
APP_NAME=Urli
APP_ENV=development
APP_URL=http://localhost:8000

# Database (Docker)
DB_HOST=db
DB_NAME=urli_db
DB_USER=urli_user
DB_PASS=urli_password
```

### Database Setup

#### Manual Setup

1. Access phpMyAdmin: http://localhost:8081
2. Login
3. Import: Upload and run backend/docker/schema.sql

### Manual API Testing
```bash
# Test URL shortening
curl -X POST http://localhost:8000/api/shorten \
  -H "Content-Type: application/json" \
  -d '{"url":"https://google.com"}'
```

### Frontend Testing
```bash
cd frontend
npm run build    # Test production build
```
