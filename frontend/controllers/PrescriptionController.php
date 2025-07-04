<?php

class PrescriptionController
{
    public function doctorPrescriptions()
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

        // Load the doctor prescriptions view
        require_once __DIR__ . '/../views/doctor/prescriptions.php';
    }

    public function patientPrescriptions()
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

        // Load the patient prescriptions view
        require_once __DIR__ . '/../views/patient/prescriptions.php';
    }

    public function createPrescription()
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

        // Load the create prescription view
        require_once __DIR__ . '/../views/doctor/create_prescription.php';
    }

    public function editPrescription()
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

        // Load the edit prescription view
        require_once __DIR__ . '/../views/doctor/edit_prescription.php';
    }
}
