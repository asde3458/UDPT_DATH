<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/PatientController.php';
require_once __DIR__ . '/../controllers/DoctorController.php';

// Simple router
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Create controller instances
$authController = new AuthController();
$patientController = new PatientController();
$doctorController = new DoctorController();

// Define routes
switch ($path) {
    case '/':
    case '/login':
        $authController->login();
        break;

    case '/register':
        $authController->register();
        break;

    case '/logout':
        $authController->logout();
        break;

    case '/dashboard':
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        // Route to appropriate dashboard based on role
        switch ($_SESSION['user']['role']) {
            case 'doctor':
                $doctorController->dashboard();
                break;
            case 'patient':
                $patientController->dashboard();
                break;
            case 'admin':
                // TODO: Add admin dashboard
                echo "Admin dashboard coming soon";
                break;
            default:
                header('Location: /login');
                exit;
        }
        break;

    // Doctor routes
    case '/doctor/dashboard':
    case '/patients':
    case '/appointments':
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'doctor') {
            header('Location: /login');
            exit;
        }

        switch ($path) {
            case '/doctor/dashboard':
                $doctorController->dashboard();
                break;
            case '/patients':
                $doctorController->patients();
                break;
            case '/appointments':
                $doctorController->appointments();
                break;
        }
        break;

    // Patient routes
    case '/patient/dashboard':
    case '/patient/appointments':
    case '/patient/profile':
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'patient') {
            header('Location: /login');
            exit;
        }

        switch ($path) {
            case '/patient/dashboard':
                $patientController->dashboard();
                break;
            case '/patient/appointments':
                $patientController->appointments();
                break;
            case '/patient/profile':
                $patientController->profile();
                break;
        }
        break;

    default:
        // Handle 404
        http_response_code(404);
        echo "404 Not Found";
        break;
}
