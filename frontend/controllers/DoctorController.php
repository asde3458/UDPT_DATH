cd<?php

    class DoctorController
    {
        public function dashboard()
        {
            // Check if user is logged in
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                exit;
            }

            // Check if user is a doctor
            if ($_SESSION['user']['role'] !== 'doctor') {
                header('Location: /dashboard');
                exit;
            }

            // Load the doctor dashboard view
            require_once __DIR__ . '/../views/doctor/dashboard.php';
        }

        public function patients()
        {
            // Check if user is logged in
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                exit;
            }

            // Check if user is a doctor
            if ($_SESSION['user']['role'] !== 'doctor') {
                header('Location: /dashboard');
                exit;
            }

            // Load the patients management view
            require_once __DIR__ . '/../views/doctor/patients.php';
        }

        public function appointments()
        {
            // Check if user is logged in
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                exit;
            }

            // Check if user is a doctor
            if ($_SESSION['user']['role'] !== 'doctor') {
                header('Location: /dashboard');
                exit;
            }

            // Load the appointments management view
            require_once __DIR__ . '/../views/doctor/appointments.php';
        }
    }
