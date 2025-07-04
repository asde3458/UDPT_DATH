<?php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hospital');

// Service URLs
define('APPOINTMENT_SERVICE_URL', 'http://localhost:3001/api');
define('USER_SERVICE_URL', 'http://localhost:5000/api');
define('PATIENT_SERVICE_URL', 'http://localhost:3000/api');
define('PRESCRIPTION_SERVICE_URL', 'http://localhost:3002/api');

// Session configuration
session_start();
