# Urli - URL Shortener

A modern URL shortener built with PHP backend and React frontend, featuring a clean API and beautiful user interface.

## 🚀 Features

- **URL Shortening**: Transform long URLs into short, memorable links
- **Custom Short Codes**: Create personalized short codes (authenticated users only)
- **URL Management**: Delete your own shortened URLs
- **Click Analytics**: Track clicks and view statistics
- **User Authentication**: Register, login, and logout functionality
- **Account Management**: Update email, password, or delete account
- **Session Management**: Secure session-based authentication
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
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "email": "user@example.com"
  }
}
```

**Validation:**
- Email must be valid format
- Password must be at least 6 characters
- Email must be unique

**Error Response:**
```json
{
  "error": {
    "code": "INVALID_EMAIL",
    "message": "Invalid email format"
  }
}
```

**Error Codes:**
- `MISSING_FIELDS` - Email or password not provided
- `INVALID_EMAIL` - Email format is invalid
- `INVALID_PASSWORD` - Password is too short (minimum 6 characters)
- `EMAIL_EXISTS` - Email is already registered

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
  "message": "Login successful",
  "user": {
    "id": 1,
    "email": "user@example.com"
  }
}
```

**Error Responses:**

Already authenticated:
```json
{
  "error": {
    "code": "ALREADY_AUTHENTICATED",
    "message": "Already logged in"
  }
}
```

Invalid credentials:
```json
{
  "error": {
    "code": "INVALID_CREDENTIALS",
    "message": "Invalid email or password"
  }
}
```

**Error Codes:**
- `ALREADY_AUTHENTICATED` - User is already logged in
- `MISSING_FIELDS` - Email or password not provided
- `INVALID_CREDENTIALS` - Email or password is incorrect

#### Logout
```bash
POST /api/auth/logout
```

**Response:**
```json
{
  "message": "Logout successful"
}
```

#### Get Current User
```bash
GET /api/auth/me
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "email": "user@example.com"
  }
}
```

#### Update Email
```bash
PATCH /api/auth/email
Content-Type: application/json

{
  "email": "newemail@example.com"
}
```

**Response:**
```json
{
  "message": "Email updated successfully",
  "user": {
    "id": 1,
    "email": "newemail@example.com"
  }
}
```

**Notes:**
- Requires authentication
- Email must be valid format
- Email must not already be taken by another user
- Session is automatically updated with new email

**Error Responses:**

Missing email field:
```json
{
  "error": {
    "code": "MISSING_FIELDS",
    "message": "Email is required"
  }
}
```

Not authenticated:
```json
{
  "error": {
    "code": "UNAUTHORIZED",
    "message": "Not authenticated"
  }
}
```

Invalid email format:
```json
{
  "error": {
    "code": "INVALID_EMAIL",
    "message": "Invalid email format"
  }
}
```

Email unchanged:
```json
{
  "error": {
    "code": "EMAIL_UNCHANGED",
    "message": "Email is the same as current email"
  }
}
```

Email already taken:
```json
{
  "error": {
    "code": "EMAIL_TAKEN",
    "message": "Email already taken by another user"
  }
}
```

#### Update Password
```bash
PATCH /api/auth/password
Content-Type: application/json

{
  "current_password": "oldpassword",
  "new_password": "newpassword"
}
```

**Response:**
```json
{
  "message": "Password updated successfully",
  "user": {
    "id": 1,
    "email": "user@example.com"
  }
}
```

**Notes:**
- Requires authentication
- Must provide correct current password
- New password must be at least 6 characters
- New password must be different from current password

**Error Responses:**

Missing fields:
```json
{
  "error": {
    "code": "MISSING_FIELDS",
    "message": "Current password and new password are required"
  }
}
```

Not authenticated:
```json
{
  "error": {
    "code": "UNAUTHORIZED",
    "message": "Not authenticated"
  }
}
```

Incorrect current password:
```json
{
  "error": {
    "code": "INVALID_CURRENT_PASSWORD",
    "message": "Current password is incorrect"
  }
}
```

Invalid new password:
```json
{
  "error": {
    "code": "INVALID_PASSWORD",
    "message": "New password must be at least 6 characters"
  }
}
```

Password unchanged:
```json
{
  "error": {
    "code": "PASSWORD_UNCHANGED",
    "message": "New password must be different from current password"
  }
}
```

#### Delete Account
```bash
DELETE /api/auth/me
```

**Response:**
```json
{
  "message": "Account deleted"
}
```

**Notes:**
- Requires authentication
- Permanently deletes the user account
- Automatically logs out the user after deletion
- All URLs created by the user will have their `user_id` set to NULL (due to foreign key constraint ON DELETE SET NULL)

**Error Response:**
```json
{
  "error": {
    "code": "UNAUTHORIZED",
    "message": "Not authenticated"
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
  "custom_code": "example"  // optional (requires authentication)
}
```

**Response:**
```json
{
  "short_code": "abc123",
  "short_url": "http://localhost:8000/abc123",
  "original_url": "https://www.example.com",
  "created_at": "2025-01-20 10:30:45",
  "clicks": 0,
  "existing": false
}
```

**Notes:**
- **Rate Limiting**:
  - Anonymous users: 10 URLs per 24 hours (tracked by IP address)
  - Authenticated users: 30 URLs per 24 hours (tracked by user ID)
- Anonymous users: Always create a new short URL with random code
- Authenticated users without custom code: Return existing short URL if they already shortened this URL, otherwise create new one
- Authenticated users with custom code: Always create a new short URL (allows multiple custom codes for same URL)
- Only authenticated users can provide a custom short code

**Error Responses:**

Rate limit exceeded (anonymous users):
```json
{
  "error": {
    "code": "RATE_LIMIT_EXCEEDED",
    "message": "Rate limit exceeded. Anonymous users can create 10 URLs per day. Please register for 30 URLs per day."
  }
}
```

Rate limit exceeded (authenticated users):
```json
{
  "error": {
    "code": "RATE_LIMIT_EXCEEDED",
    "message": "Rate limit exceeded. You have reached your limit of 30 URLs per day."
  }
}
```

When custom code is provided without authentication:
```json
{
  "error": {
    "code": "AUTHENTICATION_REQUIRED",
    "message": "You must be logged in to use custom short codes"
  }
}
```

When custom code already exists:
```json
{
  "error": {
    "code": "SHORT_CODE_EXISTS",
    "message": "Short code already exists"
  }
}
```

#### Get User URLs
```bash
GET /api/urls
```

**Response:**
```json
{
  "urls": [
    {
      "short_code": "abc123",
      "short_url": "http://localhost:8000/abc123",
      "original_url": "https://www.example.com",
      "created_at": "2025-01-20 10:30:45",
      "clicks": 42
    },
    {
      "short_code": "custom",
      "short_url": "http://localhost:8000/custom",
      "original_url": "https://www.google.com",
      "created_at": "2025-01-19 15:22:10",
      "clicks": 15
    }
  ]
}
```

**Notes:**
- Requires authentication
- Returns all URLs created by the authenticated user
- URLs are ordered by creation date (most recent first)
- Returns empty array if user has no URLs

**Error Response:**

Not authenticated:
```json
{
  "error": {
    "code": "UNAUTHORIZED",
    "message": "Authentication required"
  }
}
```

#### Delete URL
```bash
DELETE /api/urls/{shortCode}
```

**Response:**
```json
{
  "message": "URL deleted successfully"
}
```

**Notes:**
- Requires authentication
- Users can only delete their own URLs
- Anonymous URLs (created without authentication) cannot be deleted

**Error Responses:**

Not authenticated:
```json
{
  "error": {
    "code": "UNAUTHORIZED",
    "message": "Authentication required"
  }
}
```

URL not found or forbidden (same response for security):
```json
{
  "error": {
    "code": "URL_NOT_FOUND",
    "message": "URL not found"
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

### Production Deployment

#### Document Root Configuration

**If you can set the document root** (recommended):
- Point your web server's document root to `backend/src/public/`
- No additional configuration needed

**If you cannot set the document root** (e.g., Hostinger, shared hosting):
1. Rename `backend/.htaccess.dev` to `backend/.htaccess`
2. Upload to your server
3. This `.htaccess` will redirect all requests to `src/public/`

The `.htaccess.dev` file is kept separate for local development where Docker already sets the correct document root.

```apache
# backend/.htaccess (for production without document root control)
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Only rewrite if the request is NOT an actual file or directory
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d

    # Forward all other requests to src/public, preserving path and query strings
    RewriteRule ^(.*)$ src/public/$1 [QSA,L]

    # Set the default file to src/public/index.php
    DirectoryIndex src/public/index.php
</IfModule>
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
