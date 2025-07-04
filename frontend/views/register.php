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

        .form-control {
            padding-left: 2.5rem;
        }
    </style>
</head>

<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="col-md-5">
            <div class="card p-4">
                <div class="card-body">
                    <h2 class="text-center mb-4 text-primary fw-bold">Sign Up</h2>
                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger text-center py-2"><?php echo $_SESSION['error'];
                                                                            unset($_SESSION['error']); ?></div>
                    <?php endif; ?>
                    <form action="/register" method="POST" autocomplete="off">
                        <div class="mb-3 position-relative">
                            <span class="form-icon"><i class="fa fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus>
                        </div>
                        <div class="mb-3 position-relative">
                            <span class="form-icon"><i class="fa fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        </div>
                        <div class="mb-3 position-relative">
                            <span class="form-icon"><i class="fa fa-user-tag"></i></span>
                            <select class="form-select" id="role" name="role" required style="padding-left:2.5rem;">
                                <option value="">Select a role</option>
                                <option value="doctor">Doctor</option>
                                <option value="patient">Patient</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Register</button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <span>Already have an account? <a href="/login" class="text-decoration-none text-primary">Login here</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>