<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Details</title>
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
            <h2>Prescription Details</h2>
            <a href="/prescriptions" class="btn btn-secondary">
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
                                <h5 class="mb-0">Prescription </h5>
                                <span class="badge bg-${prescription.status === 'completed' ? 'success' : prescription.status === 'dispensed' ? 'warning' : 'primary'} fs-6">${prescription.status}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Patient Information</h6>
                                    <p><strong>Patient ID:</strong> ${prescription.patient_id}</p>
                                    <p><strong>Patient Name:</strong> ${prescription.patient_name || 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Doctor Information</h6>
                                    <p><strong>Doctor:</strong> Dr. ${prescription.doctor_name}</p>
                                    <p><strong>Created:</strong> ${new Date(prescription.created_at).toLocaleString()}</p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="mb-3">
                                <h6>Diagnosis</h6>
                                <p class="mb-0">${prescription.diagnosis}</p>
                            </div>
                            
                            ${prescription.notes ? `
                                <div class="mb-3">
                                    <h6>Notes</h6>
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
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-warning" onclick="editPrescription()">
                                    <i class="fas fa-edit me-2"></i>Edit
                                </button>
                                <button class="btn btn-danger" onclick="deletePrescription()">
                                    <i class="fas fa-trash me-2"></i>Delete
                                </button>
                            </div>
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
                        <a href="/prescriptions" class="btn btn-primary">Back to Prescriptions</a>
                    </div>
                `;
            });

        function editPrescription() {
            // Tạo modal edit
            const editModal = `
                <div class="modal fade" id="editPrescriptionModal" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Prescription</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editPrescriptionForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="editPatientId" class="form-label">Patient ID</label>
                                                <input type="text" class="form-control" id="editPatientId" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="editPatientName" class="form-label">Patient Name</label>
                                                <input type="text" class="form-control" id="editPatientName" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editDiagnosis" class="form-label">Diagnosis</label>
                                        <input type="text" class="form-control" id="editDiagnosis" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editNotes" class="form-label">Notes</label>
                                        <textarea class="form-control" id="editNotes"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Medications</label>
                                        <div id="editMedicationsList"></div>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addEditMedication()">Add Medication</button>
                                    </div>
                                    <div id="editResultMsg" class="mt-3"></div>
                                    <button type="submit" class="btn btn-primary">Update Prescription</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Thêm modal vào body
            document.body.insertAdjacentHTML('beforeend', editModal);

            // Load current prescription data
            fetch(`${PRESCRIPTION_SERVICE_URL}/prescriptions/${prescriptionId}`)
                .then(response => response.json())
                .then(prescription => {
                    document.getElementById('editPatientId').value = prescription.patient_id;
                    document.getElementById('editPatientName').value = prescription.patient_name || '';
                    document.getElementById('editDiagnosis').value = prescription.diagnosis;
                    document.getElementById('editNotes').value = prescription.notes || '';

                    // Load medications
                    const medicationsList = document.getElementById('editMedicationsList');
                    medicationsList.innerHTML = '';
                    prescription.medications.forEach(med => {
                        const html = `
                            <div class="row g-2 mb-2 medication-item">
                                <div class="col-md-3"><input type="text" class="form-control" placeholder="Name" value="${med.medication_name}" required></div>
                                <div class="col-md-2"><input type="text" class="form-control" placeholder="Dosage" value="${med.dosage}" required></div>
                                <div class="col-md-2"><input type="text" class="form-control" placeholder="Frequency" value="${med.frequency}" required></div>
                                <div class="col-md-2"><input type="text" class="form-control" placeholder="Duration" value="${med.duration}" required></div>
                                <div class="col-md-2"><input type="text" class="form-control" placeholder="Instructions" value="${med.instructions || ''}"></div>
                                <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.medication-item').remove()">&times;</button></div>
                            </div>
                        `;
                        medicationsList.insertAdjacentHTML('beforeend', html);
                    });
                });

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editPrescriptionModal'));
            modal.show();

            // Handle form submission
            document.getElementById('editPrescriptionForm').onsubmit = function(e) {
                e.preventDefault();
                const meds = Array.from(document.querySelectorAll('#editMedicationsList .medication-item')).map(item => {
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
                    patient_id: document.getElementById('editPatientId').value,
                    patient_name: document.getElementById('editPatientName').value,
                    diagnosis: document.getElementById('editDiagnosis').value,
                    notes: document.getElementById('editNotes').value,
                    medications: meds
                };

                fetch(`${PRESCRIPTION_SERVICE_URL}/prescriptions/${prescriptionId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (result.error) {
                            document.getElementById('editResultMsg').innerHTML = `<div class='alert alert-danger'>${result.error}</div>`;
                        } else {
                            document.getElementById('editResultMsg').innerHTML = `<div class='alert alert-success'>Prescription updated!</div>`;
                            setTimeout(() => {
                                modal.hide();
                                location.reload();
                            }, 1000);
                        }
                    })
                    .catch(() => {
                        document.getElementById('editResultMsg').innerHTML = `<div class='alert alert-danger'>Error updating prescription</div>`;
                    });
            };
        }

        function addEditMedication() {
            const container = document.getElementById('editMedicationsList');
            const html = `
                <div class="row g-2 mb-2 medication-item">
                    <div class="col-md-3"><input type="text" class="form-control" placeholder="Name" required></div>
                    <div class="col-md-2"><input type="text" class="form-control" placeholder="Dosage" required></div>
                    <div class="col-md-2"><input type="text" class="form-control" placeholder="Frequency" required></div>
                    <div class="col-md-2"><input type="text" class="form-control" placeholder="Duration" required></div>
                    <div class="col-md-2"><input type="text" class="form-control" placeholder="Instructions"></div>
                    <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.medication-item').remove()">&times;</button></div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function deletePrescription() {
            if (confirm('Are you sure you want to delete this prescription?')) {
                fetch(`${PRESCRIPTION_SERVICE_URL}/prescriptions/${prescriptionId}`, {
                        method: 'DELETE'
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.error) {
                            alert('Error deleting prescription: ' + result.error);
                        } else {
                            alert('Prescription deleted successfully');
                            window.location.href = '/prescriptions';
                        }
                    })
                    .catch(error => {
                        alert('Error deleting prescription');
                    });
            }
        }
    </script>
</body>

</html>