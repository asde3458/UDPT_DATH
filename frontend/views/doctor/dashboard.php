<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/doctor/dashboard">Hospital System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="/doctor/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/patients">Patients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/appointments">Appointments</a>
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
        <div class="row mb-4">
            <div class="col">
                <h2>Welcome, Dr. <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</h2>
            </div>
        </div>

        <div class="row">
            <!-- Today's Appointments Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-calendar-day text-primary me-2"></i>
                            Today's Appointments
                        </h5>
                        <div id="todayAppointments">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="/appointments" class="btn btn-primary btn-sm">View All Appointments</a>
                    </div>
                </div>
            </div>

            <!-- Recent Patients Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-user-injured text-success me-2"></i>
                            Recent Patients
                        </h5>
                        <div id="recentPatients">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="/patients" class="btn btn-success btn-sm">Manage Patients</a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-bolt text-warning me-2"></i>
                            Quick Actions
                        </h5>
                        <div class="list-group">
                            <a href="/patients" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-plus me-2"></i>
                                Add New Patient
                            </a>
                            <a href="/appointments" class="list-group-item list-group-item-action">
                                <i class="fas fa-calendar-plus me-2"></i>
                                Schedule Appointment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const doctorId = '<?php echo $_SESSION['user']['id']; ?>';

        // Fetch today's appointments
        fetch(`<?php echo APPOINTMENT_SERVICE_URL; ?>/appointments?doctorId=${doctorId}&date=${new Date().toISOString().split('T')[0]}`)
            .then(response => response.json())
            .then(appointments => {
                const html = appointments.length ? appointments.map(appointment => `
                    <div class="mb-2 p-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${appointment.patientName}</strong>
                                <br>
                                <small class="text-muted">${appointment.time}</small>
                            </div>
                            <span class="badge bg-${appointment.status === 'scheduled' ? 'primary' : 'success'}">${appointment.status}</span>
                        </div>
                    </div>
                `).join('') : '<p class="text-muted">No appointments for today</p>';

                document.getElementById('todayAppointments').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('todayAppointments').innerHTML = '<p class="text-danger">Error loading appointments</p>';
            });

        // Fetch recent patients
        fetch(`<?php echo PATIENT_SERVICE_URL; ?>/patients?limit=5`)
            .then(response => response.json())
            .then(patients => {
                const html = patients.length ? patients.map(patient => `
                    <div class="mb-2 p-2 border-bottom">
                        <strong>${patient.name}</strong>
                        <br>
                        <small class="text-muted">Last visit: ${new Date(patient.updatedAt).toLocaleDateString()}</small>
                    </div>
                `).join('') : '<p class="text-muted">No recent patients</p>';

                document.getElementById('recentPatients').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('recentPatients').innerHTML = '<p class="text-danger">Error loading patients</p>';
            });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>