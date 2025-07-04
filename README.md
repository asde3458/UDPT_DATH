# Hospital Management System

A microservices-based hospital management system with patient management, appointment scheduling, and user authentication.

## System Architecture

The system consists of the following components:

1. Frontend (PHP)
   - MVC architecture
   - User interface for all functionalities
   - Communicates with microservices via REST APIs

2. User Service (Python + MySQL)
   - Handles user authentication and authorization
   - Manages user roles (Admin, Doctor, Patient)
   - Port: 5000

3. Patient Service (Node.js + MongoDB)
   - Manages patient information
   - Stores medical history
   - Port: 3000

4. Appointment Service (Node.js + MongoDB)
   - Handles appointment scheduling
   - Manages appointment status
   - Port: 3001

## Prerequisites

- PHP 7.4 or higher
- Python 3.8 or higher
- Node.js 14 or higher
- MySQL
- MongoDB
- Web server (Apache/Nginx)

## Setup Instructions

### 1. Database Setup

```sql
-- Create MySQL database for User Service
CREATE DATABASE hospital_user_service;
```

### 2. User Service (Python)

```bash
cd user-service
pip install -r requirements.txt
python app.py
```

### 3. Patient Service (Node.js)

```bash
cd patient-service
npm install
npm start
```

### 4. Appointment Service (Node.js)

```bash
cd appointment-service
npm install
npm start
```

### 5. Frontend (PHP)

1. Configure your web server to point to the `frontend/public` directory
2. Ensure the web server has PHP support enabled
3. Update the service URLs in `frontend/config/config.php` if needed

## Features

### Patient Management
- Register new patients
- Update patient information
- View medical history
- Search patient records

### Appointment Management
- Schedule new appointments
- Update appointment status
- View appointment calendar
- Filter appointments by doctor or patient

### User Management
- User registration with role selection (Doctor/Patient)
- User authentication
- Role-based access control

## Access Control

1. Patients can:
   - View their own appointments
   - Schedule new appointments
   - Update their profile

2. Doctors can:
   - View patient information
   - Manage appointments
   - Update medical records

3. Admins can:
   - Access all functionalities
   - Manage user roles
   - View system-wide data

## API Documentation

### User Service (Port 5000)
- POST /login - User authentication
- POST /register - User registration

### Patient Service (Port 3000)
- GET /patients - List all patients
- POST /patients - Create new patient
- GET /patients/:id - Get patient details
- PUT /patients/:id - Update patient
- DELETE /patients/:id - Delete patient
- POST /patients/:id/medical-history - Add medical history

### Appointment Service (Port 3001)
- GET /appointments - List appointments
- POST /appointments - Create appointment
- GET /appointments/:id - Get appointment details
- PUT /appointments/:id - Update appointment
- DELETE /appointments/:id - Delete appointment
- PATCH /appointments/:id/status - Update appointment status 