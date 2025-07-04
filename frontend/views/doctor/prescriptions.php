<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Prescriptions - Doctor Dashboard</title>
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
            <a class="navbar-brand" href="/doctor/dashboard">Hospital System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/doctor/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/patients">Patients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/appointments">Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/prescriptions">Prescriptions</a>
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
            <h2>Manage Prescriptions</h2>
            <button type="button" class="btn btn-primary" id="openCreatePrescriptionModal">
                <i class="fas fa-plus me-2"></i>Create New Prescription
            </button>
        </div>

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
                            <label for="searchFilter" class="form-label">Search Patient</label>
                            <input type="text" class="form-control" id="searchFilter" placeholder="Patient name...">
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
    </div>

    <!-- Create Prescription Modal -->
    <div class="modal fade" id="createPrescriptionModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Prescription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="prescriptionForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="patientId" class="form-label">Patient ID</label>
                                    <input type="text" class="form-control" id="patientId" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="patientName" class="form-label">Patient Name</label>
                                    <input type="text" class="form-control" id="patientName" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="diagnosis" class="form-label">Diagnosis</label>
                            <input type="text" class="form-control" id="diagnosis" required>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Medications</label>
                            <div id="medicationsList"></div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addMedication()">Add Medication</button>
                        </div>
                        <div id="resultMsg" class="mt-3"></div>
                        <button type="submit" class="btn btn-primary">Create Prescription</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Prescription Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Prescription Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning" id="editBtn" onclick="editPrescription()">Edit</button>
                    <button type="button" class="btn btn-danger" id="deleteBtn" onclick="deletePrescription()">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const PRESCRIPTION_SERVICE_URL = '<?php echo PRESCRIPTION_SERVICE_URL; ?>';
        const doctorId = '<?php echo $_SESSION['user']['id']; ?>';
        const doctorName = '<?php echo $_SESSION['user']['fullName']; ?>';
        let currentFilters = {
            status: '',
            date: '',
            search: ''
        };

        // Khởi tạo modal sau khi Bootstrap đã load
        let createPrescriptionModal;
        document.addEventListener('DOMContentLoaded', function() {
            createPrescriptionModal = new bootstrap.Modal(document.getElementById('createPrescriptionModal'));

            // Gắn sự kiện mở modal
            document.getElementById('openCreatePrescriptionModal').addEventListener('click', function(e) {
                document.getElementById('prescriptionForm').reset();
                document.getElementById('medicationsList').innerHTML = '';
                document.getElementById('resultMsg').innerHTML = '';
                createPrescriptionModal.show();
            });
        });

        // Function to fetch and display prescriptions
        function fetchPrescriptions() {
            const queryParams = new URLSearchParams();
            if (currentFilters.status) queryParams.append('status', currentFilters.status);
            if (currentFilters.date) queryParams.append('date', currentFilters.date);
            if (currentFilters.search) queryParams.append('search', currentFilters.search);

            fetch(`${PRESCRIPTION_SERVICE_URL}/prescriptions/doctor/${doctorId}?${queryParams.toString()}`)
                .then(response => response.json())
                .then(prescriptions => {
                    const html = prescriptions.length ? prescriptions.map(prescription => `
                        <div class="mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1">${prescription.patient_name}</h5>
                                    <p class="mb-1"><strong>Diagnosis:</strong> ${prescription.diagnosis}</p>
                                    <p class="mb-2"><strong>Medications:</strong> ${prescription.medications.length} items</p>
                                    <div class="mb-2">
                                        <span class="badge bg-${prescription.status === 'completed' ? 'success' : prescription.status === 'dispensed' ? 'warning' : 'primary'}">${prescription.status}</span>
                                        <small class="text-muted ms-2">Created: ${new Date(prescription.created_at).toLocaleDateString()}</small>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <a href="/prescriptions/${prescription._id}" class="btn btn-sm btn-outline-primary">View</a>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="updateStatus('${prescription._id}')">Update Status</button>
                                </div>
                            </div>
                        </div>
                    `).join('') : '<p class="text-muted">No prescriptions found</p>';

                    document.getElementById('prescriptionsList').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('prescriptionsList').innerHTML = '<p class="text-danger">Error loading prescriptions</p>';
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

        // Function to update prescription status
        function updateStatus(prescriptionId) {
            const newStatus = prompt('Enter new status (pending/dispensed/completed):');
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
                    fetchPrescriptions();
                })
                .catch(error => {
                    alert('Error updating prescription status');
                });
        }

        // Form logic
        function addMedication() {
            const container = document.getElementById('medicationsList');
            const html = `<div class="row g-2 mb-2 medication-item">
                <div class="col-md-3"><input type="text" class="form-control" placeholder="Name" required></div>
                <div class="col-md-2"><input type="text" class="form-control" placeholder="Dosage" required></div>
                <div class="col-md-2"><input type="text" class="form-control" placeholder="Frequency" required></div>
                <div class="col-md-2"><input type="text" class="form-control" placeholder="Duration" required></div>
                <div class="col-md-2"><input type="text" class="form-control" placeholder="Instructions"></div>
                <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.medication-item').remove()">&times;</button></div>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }

        document.getElementById('prescriptionForm').onsubmit = function(e) {
            e.preventDefault();
            const meds = Array.from(document.querySelectorAll('.medication-item')).map(item => {
                const inputs = item.querySelectorAll('input');
                return {
                    medication_name: inputs[0].value,
                    dosage: inputs[1].value,
                    frequency: inputs[2].value,
                    duration: inputs[3].value,
                    instructions: inputs[4].value
                };
            });
            const data = {
                patient_id: document.getElementById('patientId').value,
                patient_name: document.getElementById('patientName').value,
                doctor_id: doctorId,
                doctor_name: doctorName,
                diagnosis: document.getElementById('diagnosis').value,
                notes: document.getElementById('notes').value,
                medications: meds
            };
            fetch(`${PRESCRIPTION_SERVICE_URL}/prescriptions`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(result => {
                    if (result.error) {
                        document.getElementById('resultMsg').innerHTML = `<div class='alert alert-danger'>${result.error}</div>`;
                    } else {
                        document.getElementById('resultMsg').innerHTML = `<div class='alert alert-success'>Prescription created!</div>`;
                        setTimeout(() => {
                            createPrescriptionModal.hide();
                            fetchPrescriptions();
                        }, 1000);
                    }
                })
                .catch(() => {
                    document.getElementById('resultMsg').innerHTML = `<div class='alert alert-danger'>Error creating prescription</div>`;
                });
        }

        // Initial fetch
        fetchPrescriptions();
    </script>
</body>

</html>