<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/patient/dashboard">Hospital System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="/patient/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/patient/profile">My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/patient/appointments">My Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/patient/prescriptions">My Prescriptions</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Welcome <?php echo htmlspecialchars($_SESSION['user']['fullName']); ?> </h2>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">My Profile</h5>
                        <p class="card-text">View and update your personal information</p>
                        <a href="/patient/profile" class="btn btn-primary">View Profile</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">My Appointments</h5>
                        <p class="card-text">View your upcoming and past appointments</p>
                        <a href="/patient/appointments" class="btn btn-primary">View Appointments</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Medical History</h5>
                        <p class="card-text">Access your medical records and history</p>
                        <a href="/patient/medical-history" class="btn btn-primary">View History</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">My Prescriptions</h5>
                        <p class="card-text">View and manage your prescriptions</p>
                        <a href="/patient/prescriptions" class="btn btn-primary">View Prescriptions</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Prescriptions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Prescriptions</h5>
                        <div id="recentPrescriptions">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const patientId = '<?php echo $_SESSION['user']['id']; ?>';

        // Fetch recent prescriptions
        fetch(`<?php echo PRESCRIPTION_SERVICE_URL; ?>/prescriptions/patient/${patientId}`)
            .then(response => response.json())
            .then(prescriptions => {
                const html = prescriptions.length ? prescriptions.map(prescription => `
                    <div class="mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Dr. ${prescription.doctor_name}</h6>
                                <p class="mb-1"><strong>Diagnosis:</strong> ${prescription.diagnosis}</p>
                                <p class="mb-1"><strong>Status:</strong> <span class="badge bg-${prescription.status === 'completed' ? 'success' : 'primary'}">${prescription.status}</span></p>
                                <small class="text-muted">Prescribed on: ${new Date(prescription.created_at).toLocaleDateString()}</small>
                            </div>
                            <a href="/patient/prescriptions/${prescription._id}" class="btn btn-sm btn-outline-primary">View Details</a>
                        </div>
                    </div>
                `).join('') : '<p class="text-muted">No prescriptions found</p>';

                document.getElementById('recentPrescriptions').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('recentPrescriptions').innerHTML = '<p class="text-danger">Error loading prescriptions</p>';
            });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>