<?php
define('USER_SERVICE_URL', 'http://localhost:5000/api');  // Python User Service
define('PATIENT_SERVICE_URL', 'http://localhost:3000/api');  // Node.js Patient Service
define('APPOINTMENT_SERVICE_URL', 'http://localhost:3001/api');  // Node.js Appointment Service

// Database configuration (if needed for sessions)
define('DB_HOST', 'localhost');
define('DB_NAME', 'hospital_frontend');
define('DB_USER', 'root');
define('DB_PASS', '');

// Session configuration
session_start();
