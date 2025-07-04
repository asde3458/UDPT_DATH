<?php

class PatientController
{
    public function profile()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        // Check if user is a patient
        if ($_SESSION['user']['role'] !== 'patient') {
            header('Location: /dashboard');
            exit;
        }

        // Load the patient profile view
        require_once __DIR__ . '/../views/patient/patient_profile.php';
    }

    public function dashboard()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        // Check if user is a patient
        if ($_SESSION['user']['role'] !== 'patient') {
            header('Location: /dashboard');
            exit;
        }

        // Load the patient dashboard view
        require_once __DIR__ . '/../views/patient/dashboard.php';
    }
}
