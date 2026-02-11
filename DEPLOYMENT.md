# Deployment Guide for EL-THOMP Polytechnic Portal

This application has two parts that need to be hosted separately:
1.  **Backend**: PHP API & MySQL Database (Hosted on 000webhost, InfinityFree, or any PHP host).
2.  **Frontend**: React Student Portal & Admin Dashboard (Hosted on Vercel).

---

## Part 1: Host the Backend (PHP + MySQL)

You need a server that supports PHP and MySQL. Free options: **000webhost.com** or **InfinityFree.com**.

1.  **Sign Up** for a free hosting account.
2.  **Create a Database**:
    *   Go to the "MySQL Databases" section of your hosting panel.
    *   Create a new database (e.g., `id12345_student_reg_sys`).
    *   Create a user and password for it.
    *   **Note down** the: `Database Name`, `Database User`, `Database Password`, which are usually different from your login.
3.  **Import Database Schema**:
    *   Open **phpMyAdmin** from your hosting panel.
    *   Select your new database.
    *   Click **Import** tab.
    *   Upload the file `database/schema.sql` from this project.
    *   Click **Go**.
4.  **Upload Backend Files**:
    *   Open "File Manager" from your hosting panel.
    *   Go to `public_html`.
    *   Upload the entire `backend` folder.
5.  **Configure Database Connection**:
    *   In File Manager, edit `backend/config.php` (right-click -> Edit).
    *   Updates these lines with your NEW database details from Step 2:
        ```php
        $host = 'localhost'; // Usually remains localhost
        $db_name = 'id12345_student_reg_sys'; // Your new DB name
        $username = 'id12345_root'; // Your new DB user
        $password = 'your_password'; // Your new DB password
        ```
    *   Save the file.
6.  **Test the Backend**:
    *   Visit your new site URL, e.g., `https://my-poly-app.000webhostapp.com/backend/api/students.php`.
    *   If you see `[]` or JSON data, it works! Copy this URL (excluding `students.php`).
    *   Example Base URL: `https://my-poly-app.000webhostapp.com/backend/api`

---

## Part 2: Host the Frontend (Vercel)

1.  **Push to GitHub**:
    *   Ensure your latest code is on GitHub.
2.  **Log in to Vercel**:
    *   Go to [vercel.com](https://vercel.com) and login with GitHub.
3.  **Import Project**:
    *   Click "Add New..." -> "Project".
    *   Select your `Student-reg-sys` repository.
4.  **Configure Environment Variables**:
    *   In the "Environment Variables" section, add a new variable:
        *   **Name**: `VITE_API_BASE_URL`
        *   **Value**: The Backend URL you blindly copied in Part 1 (e.g., `https://my-poly-app.000webhostapp.com/backend/api`).
5.  **Deploy**:
    *   Click **Deploy**.
    *   Wait for a minute, and your site will be live!

---

## Option B: The Ngrok Method (Your Current Setup)

Since you are using **ngrok** to expose your local XAMPP server, follow these steps:

1.  **Start your Backend**:
    *   Make sure XAMPP Apache is running (Port 8080).

2.  **Start Ngrok**:
    *   Open your terminal and run:
        ```bash
        ngrok http 8080
        ```
    *   Copy the `Forwarding` URL (e.g., `https://a1b2-c3d4.ngrok-free.app`).

3.  **Prepare the API URL**:
    *   Your full API URL will be: `YOUR_NGROK_URL` + `/Student-reg-sys/backend/api`
    *   Example: `https://a1b2-c3d4.ngrok-free.app/Student-reg-sys/backend/api`

4.  **Deploy Frontend to Vercel**:
    *   Go to Vercel dashboard.
    *   Import your project from GitHub.
    *   **CRITICAL STEP**: In **Environment Variables**, add:
        *   **Name**: `VITE_API_BASE_URL`
        *   **Value**: The URL you created in Step 3.
    *   Deploy!

**Important**: If you restart ngrok, the URL changes. You will need to update the Environment Variable in Vercel and redeploy (or just update the variable in Vercel settings and it might pick it up on next visit, but usually requires a redeploy/promote).

---
