<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Hospital Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
        }

        .card {
            border-radius: 1.5rem;
            box-shadow: 0 4px 32px rgba(0, 0, 0, 0.12);
        }

        .form-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6a11cb;
        }

        .form-control,
        .form-select {
            padding-left: 2.5rem;
            border-radius: 0.75rem;
            border: 1px solid #e0e0e0;
            height: 48px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 0 0.2rem rgba(106, 17, 203, 0.25);
        }

        .btn-primary {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            border: none;
            border-radius: 0.75rem;
            height: 48px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #5a0cb1, #1e63d4);
        }

        .form-label {
            font-weight: 500;
            color: #444;
            margin-bottom: 0.5rem;
        }

        .name-group {
            display: flex;
            gap: 1rem;
        }
    </style>
</head>

<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="col-md-6">
            <div class="card p-4">
                <div class="card-body">
                    <h2 class="text-center mb-4 text-primary fw-bold">Sign Up</h2>
                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger text-center py-2"><?php echo $_SESSION['error'];
                                                                            unset($_SESSION['error']); ?></div>
                    <?php endif; ?>
                    <form action="/register" method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <div class="position-relative">
                                <span class="form-icon"><i class="fa fa-user"></i></span>
                                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Enter your full name" required autofocus>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <div class="position-relative">
                                <span class="form-icon"><i class="fa fa-user-circle"></i></span>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="position-relative">
                                <span class="form-icon"><i class="fa fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <div class="position-relative">
                                <span class="form-icon"><i class="fa fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Role</label>
                            <div class="position-relative">
                                <span class="form-icon"><i class="fa fa-user-tag"></i></span>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Select a role</option>
                                    <option value="doctor">Doctor</option>
                                    <option value="patient">Patient</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Register</button>
                        </div>
                    </form>
                    <div class="text-center mt-4">
                        <span>Already have an account? <a href="/login" class="text-decoration-none text-primary">Login here</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>