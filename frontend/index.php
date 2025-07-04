<?php

$router->addRoute('/patient/dashboard', 'PatientController', 'dashboard');
$router->addRoute('/patient/appointments', 'PatientController', 'appointments');

// Doctor routes
$router->addRoute('/doctor/dashboard', 'DoctorController', 'dashboard');
$router->addRoute('/doctor/appointments', 'DoctorController', 'appointments');
$router->addRoute('/doctor/patients', 'DoctorController', 'patients');

// Authentication routes
$router->addRoute('/login', 'AuthController', 'login');
$router->addRoute('/logout', 'AuthController', 'logout');
