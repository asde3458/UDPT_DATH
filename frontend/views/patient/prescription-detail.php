<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Prescription Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
                        <a class="nav-link" href="/patient/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/patient/profile">My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/patient/appointments">My Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/patient/prescriptions">My Prescriptions</a>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Prescription Details</h2>
            <a href="/patient/prescriptions" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Prescriptions
            </a>
        </div>

        <div id="prescriptionDetails">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const PRESCRIPTION_SERVICE_URL = '<?php echo PRESCRIPTION_SERVICE_URL; ?>';
        const prescriptionId = '<?php echo $_GET['id']; ?>';

        // Load prescription details
        fetch(`${PRESCRIPTION_SERVICE_URL}/prescriptions/${prescriptionId}`)
            .then(response => response.json())
            .then(prescription => {
                const html = `
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Prescription Details</h5>
                                <span class="badge bg-${prescription.status === 'completed' ? 'success' : prescription.status === 'dispensed' ? 'warning' : 'primary'} fs-6">${prescription.status}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Doctor Information</h6>
                                    <p><strong>Doctor:</strong> Dr. ${prescription.doctor_name}</p>
                                    <p><strong>Prescribed on:</strong> ${new Date(prescription.created_at).toLocaleString()}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Status Information</h6>
                                    <p><strong>Current Status:</strong> <span class="badge bg-${prescription.status === 'completed' ? 'success' : prescription.status === 'dispensed' ? 'warning' : 'primary'}">${prescription.status}</span></p>
                                    <p><strong>Last Updated:</strong> ${new Date(prescription.updated_at).toLocaleString()}</p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="mb-3">
                                <h6>Diagnosis</h6>
                                <p class="mb-0">${prescription.diagnosis}</p>
                            </div>
                            
                            ${prescription.notes ? `
                                <div class="mb-3">
                                    <h6>Doctor's Notes</h6>
                                    <p class="mb-0">${prescription.notes}</p>
                                </div>
                            ` : ''}
                            
                            <div class="mb-3">
                                <h6>Medications</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Medication</th>
                                                <th>Dosage</th>
                                                <th>Frequency</th>
                                                <th>Duration</th>
                                                <th>Instructions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${prescription.medications.map(med => `
                                                <tr>
                                                    <td>${med.medication_name}</td>
                                                    <td>${med.dosage}</td>
                                                    <td>${med.frequency}</td>
                                                    <td>${med.duration}</td>
                                                    <td>${med.instructions || '-'}</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-primary" onclick="updateStatus()">
                                <i class="fas fa-edit me-2"></i>Update Status
                            </button>
                        </div>
                    </div>
                `;
                document.getElementById('prescriptionDetails').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('prescriptionDetails').innerHTML = `
                    <div class="alert alert-danger">
                        <h5>Error Loading Prescription</h5>
                        <p>Could not load prescription details. Please try again.</p>
                        <a href="/patient/prescriptions" class="btn btn-primary">Back to Prescriptions</a>
                    </div>
                `;
            });

        function updateStatus() {
            const newStatus = prompt('Enter new status (pending/dispensed/completed):');
            if (!newStatus) return;

            fetch(`${PRESCRIPTION_SERVICE_URL}/prescriptions/${prescriptionId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.error) {
                        alert('Error updating status: ' + result.error);
                    } else {
                        alert('Status updated successfully');
                        location.reload();
                    }
                })
                .catch(error => {
                    alert('Error updating status');
                });
        }
    </script>
</body>

</html>