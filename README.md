# Student Registration System

A full-stack student registration system with React frontend and PHP/MySQL backend.

## Prerequisites
- XAMPP installed and running (Apache and MySQL modules started).
- Node.js installed.

## Setup Instructions

### 1. Database Setup
1. Open your browser and go to `http://localhost/phpmyadmin`.
2. Create a new database named `student_reg_sys`.
3. Import the `database/schema.sql` file located in `C:\xampp\htdocs\Student-reg-sys\database\schema.sql`.
   
   **OR** simply run the setup script:
   Open `http://localhost/Student-reg-sys/backend/setup.php` in your browser. This will create the database and tables for you.

### 2. Backend Config
- The backend is located in `backend/`.
- Ensure your XAMPP Apache server is running.
- The API is accessible at `http://localhost/Student-reg-sys/backend/api/`.

### 3. Frontend Setup
1. Open a terminal in the `frontend` directory:
   ```sh
   cd frontend
   ```
2. Install dependencies (if you haven't already):
   ```sh
   npm install
   ```
3. Start the development server:
   ```sh
   npm run dev
   ```
4. Open the link provided (usually `http://localhost:5173`) in your browser.

## Admin Access
- **URL**: Go to `/admin/login` (e.g. `http://localhost:5173/admin/login`).
- **Email**: `admin@example.com`
- **Password**: `admin123`

## Features
- **Student Registration**: Students can register with their details.
- **Admin Dashboard**: Admin can view all registrations, and Approve or Reject them.
- **Security**: Admin panel is protected by login.

## Folder Structure
- `backend/`: PHP API scripts.
- `database/`: SQL schema.
- `frontend/`: React application.
