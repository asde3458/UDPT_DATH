<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Prescriptions - Patient Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .prescription-card {
            transition: transform 0.2s;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .prescription-card:hover {
            transform: translateY(-5px);
        }

        .status-pending {
            color: #ffc107;
        }

        .status-dispensed {
            color: #17a2b8;
        }

        .status-completed {
            color: #28a745;
        }
    </style>
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
        <h2>My Prescriptions</h2>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="statusFilter" class="form-label">Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="dispensed">Dispensed</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dateFilter" class="form-label">Date</label>
                            <input type="date" class="form-control" id="dateFilter">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="searchFilter" class="form-label">Search Doctor</label>
                            <input type="text" class="form-control" id="searchFilter" placeholder="Doctor name...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescriptions List -->
        <div class="card">
            <div class="card-body">
                <div id="prescriptionsList">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescription Details Modal -->
        <div class="modal fade" id="prescriptionModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Prescription Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="prescriptionModalBody">
                        <!-- Content will be loaded dynamically -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="updateStatusBtn">Update Status</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const PRESCRIPTION_SERVICE_URL = '<?php echo PRESCRIPTION_SERVICE_URL; ?>';
        const patientId = '<?php echo $_SESSION['user']['id']; ?>';
        let currentFilters = {
            status: '',
            date: '',
            search: ''
        };
        let prescriptionModal;

        // Initialize modal
        document.addEventListener('DOMContentLoaded', function() {
            prescriptionModal = new bootstrap.Modal(document.getElementById('prescriptionModal'));
        });

        // Function to fetch and display prescriptions
        function fetchPrescriptions() {
            const queryParams = new URLSearchParams();
            if (currentFilters.status) queryParams.append('status', currentFilters.status);
            if (currentFilters.date) queryParams.append('date', currentFilters.date);
            if (currentFilters.search) queryParams.append('search', currentFilters.search);

            fetch(`${PRESCRIPTION_SERVICE_URL}/prescriptions/patient/${patientId}?${queryParams.toString()}`)
                .then(response => response.json())
                .then(prescriptions => {
                    const html = prescriptions.length ? prescriptions.map(prescription => `
                        <div class="mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1">Dr. ${prescription.doctor_name}</h5>
                                    <p class="mb-1"><strong>Diagnosis:</strong> ${prescription.diagnosis}</p>
                                    <p class="mb-2"><strong>Medications:</strong> ${prescription.medications.length} items</p>
                                    <div class="mb-2">
                                        <span class="badge bg-${prescription.status === 'completed' ? 'success' : prescription.status === 'dispensed' ? 'warning' : 'primary'}">${prescription.status}</span>
                                        <small class="text-muted ms-2">Prescribed on: ${new Date(prescription.created_at).toLocaleDateString()}</small>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-sm" onclick="viewPrescription('${prescription._id}')">
                                    View Details
                                </button>
                            </div>
                        </div>
                    `).join('') : '<p class="text-muted">No prescriptions found</p>';

                    document.getElementById('prescriptionsList').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('prescriptionsList').innerHTML = '<p class="text-danger">Error loading prescriptions</p>';
                });
        }

        // Function to view prescription details
        function viewPrescription(prescriptionId) {
            fetch(`${PRESCRIPTION_SERVICE_URL}/prescriptions/${prescriptionId}`)
                .then(response => response.json())
                .then(prescription => {
                    const modalBody = document.getElementById('prescriptionModalBody');
                    modalBody.innerHTML = `
                        <div class="mb-4">
                            <h6>Doctor</h6>
                            <p>Dr. ${prescription.doctor_name}</p>
                        </div>
                        <div class="mb-4">
                            <h6>Diagnosis</h6>
                            <p>${prescription.diagnosis}</p>
                        </div>
                        <div class="mb-4">
                            <h6>Status</h6>
                            <p><span class="badge bg-${prescription.status === 'completed' ? 'success' : prescription.status === 'dispensed' ? 'warning' : 'primary'}">${prescription.status}</span></p>
                        </div>
                        <div class="mb-4">
                            <h6>Medications</h6>
                            <div class="table-responsive">
                                <table class="table">
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
                        ${prescription.notes ? `
                            <div class="mb-4">
                                <h6>Notes</h6>
                                <p>${prescription.notes}</p>
                            </div>
                        ` : ''}
                    `;

                    // Update status button handler
                    document.getElementById('updateStatusBtn').onclick = () => updateStatus(prescriptionId, prescription.status);

                    prescriptionModal.show();
                })
                .catch(error => {
                    alert('Error loading prescription details');
                });
        }

        // Function to update prescription status
        function updateStatus(prescriptionId, currentStatus) {
            const newStatus = prompt('Enter new status (pending/dispensed/completed):', currentStatus);
            if (!newStatus) return;

            fetch(`${PRESCRIPTION_SERVICE_URL}/prescriptions/${prescriptionId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Failed to update status');
                    prescriptionModal.hide();
                    fetchPrescriptions();
                })
                .catch(error => {
                    alert('Error updating prescription status');
                });
        }

        // Event listeners for filters
        document.getElementById('statusFilter').addEventListener('change', (e) => {
            currentFilters.status = e.target.value;
            fetchPrescriptions();
        });

        document.getElementById('dateFilter').addEventListener('change', (e) => {
            currentFilters.date = e.target.value;
            fetchPrescriptions();
        });

        document.getElementById('searchFilter').addEventListener('input', (e) => {
            currentFilters.search = e.target.value;
            fetchPrescriptions();
        });

        // Initial fetch
        fetchPrescriptions();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>