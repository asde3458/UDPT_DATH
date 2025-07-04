<?php
require_once __DIR__ . '/../config/config.php';

class AuthController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Call User Service API
            $ch = curl_init(USER_SERVICE_URL . '/login');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'username' => $username,
                'password' => $password
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            file_put_contents(__DIR__ . '/../log_auth.txt', "LOGIN: code=$httpCode, response=$response\n", FILE_APPEND);
            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                $_SESSION['user'] = $data;
                header('Location: /dashboard');
                exit;
            } else {
                $_SESSION['error'] = 'Invalid username or password';
                header('Location: /login');
                exit;
            }
        }

        require_once __DIR__ . '/../views/login.php';
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];
            $fullName = $_POST['full_name'];
            $role = $_POST['role'];

            // Validate password confirmation
            if ($password !== $confirmPassword) {
                $_SESSION['error'] = 'Passwords do not match';
                header('Location: /register');
                exit;
            }

            // Call User Service API
            $ch = curl_init(USER_SERVICE_URL . '/register');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'username' => $username,
                'password' => $password,
                'fullName' => $fullName,
                'role' => $role
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            file_put_contents(__DIR__ . '/../log_auth.txt', "REGISTER: code=$httpCode, response=$response\n", FILE_APPEND);
            curl_close($ch);

            if ($httpCode === 201) {
                $_SESSION['success'] = 'Registration successful. Please login.';
                header('Location: /login');
                exit;
            } else {
                $_SESSION['error'] = 'Registration failed. Please try again.';
                header('Location: /register');
                exit;
            }
        }

        require_once __DIR__ . '/../views/register.php';
    }

    public function logout()
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
}
