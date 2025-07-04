<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients - Doctor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <a class="nav-link" href="/appointments">Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/patients">Patients</a>
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
            <h2>Patient Management</h2>
            <button class="btn btn-primary" onclick="showAddForm()">Add Patient</button>
        </div>

        <div id="errorAlert" class="alert alert-danger" style="display:none;"></div>
        <div id="successAlert" class="alert alert-success" style="display:none;"></div>

        <div id="patientForm" style="display:none;"></div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date of Birth</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="patientsTable">
                            <!-- Patient rows here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const doctorId = '<?php echo $_SESSION['user']['id']; ?>';
        const serviceUrl = '<?php echo PATIENT_SERVICE_URL; ?>/patients';

        function showError(message) {
            const alert = document.getElementById('errorAlert');
            alert.textContent = message;
            alert.style.display = 'block';
            setTimeout(() => alert.style.display = 'none', 5000);
        }

        function showSuccess(message) {
            const alert = document.getElementById('successAlert');
            alert.textContent = message;
            alert.style.display = 'block';
            setTimeout(() => alert.style.display = 'none', 5000);
        }

        function fetchPatients() {
            fetch(serviceUrl)
                .then(res => {
                    if (!res.ok) throw new Error('Failed to fetch patients');
                    return res.json();
                })
                .then(data => {
                    const rows = data.map(p => `
                        <tr>
                            <td>${p.name}</td>
                            <td>${new Date(p.dateOfBirth).toLocaleDateString()}</td>
                            <td>${p.gender}</td>
                            <td>${p.phoneNumber}</td>
                            <td>${p.address}</td>
                            <td>
                                <div class="btn-group">
                                    <button class='btn btn-sm btn-warning' onclick='showEditForm(${JSON.stringify(p)})'>Edit</button>
                                    <button class='btn btn-sm btn-info' onclick='showMedicalHistory(${JSON.stringify(p)})'>History</button>
                                    <button class='btn btn-sm btn-danger' onclick='deletePatient("${p._id}")'>Delete</button>
                                </div>
                            </td>
                        </tr>
                    `).join('');
                    document.getElementById('patientsTable').innerHTML = rows;
                })
                .catch(error => showError(error.message));
        }

        function showAddForm() {
            document.getElementById('patientForm').style.display = 'block';
            document.getElementById('patientForm').innerHTML = `
                <div class="card mb-4">
                    <div class="card-body">
                        <h4>Add New Patient</h4>
                        <form onsubmit="addPatient(event)">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">User ID (from user service)</label>
                                        <input name="userId" class="form-control" placeholder="User ID" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input name="name" class="form-control" placeholder="Full Name" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input name="dateOfBirth" type="date" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Gender</label>
                                        <select name="gender" class="form-control" required>
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input name="phoneNumber" class="form-control" placeholder="Phone Number" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea name="address" class="form-control" placeholder="Full Address" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h5>Initial Medical History</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Condition</label>
                                            <input name="condition" class="form-control" placeholder="Medical Condition" />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Diagnosis</label>
                                            <input name="diagnosis" class="form-control" placeholder="Diagnosis" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Treatment</label>
                                            <input name="treatment" class="form-control" placeholder="Treatment Plan" />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Notes</label>
                                            <textarea name="notes" class="form-control" placeholder="Additional Notes"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" onclick="hideForm()">Cancel</button>
                                <button type="submit" class="btn btn-success">Add Patient</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
        }

        function showMedicalHistory(patient) {
            document.getElementById('patientForm').style.display = 'block';
            document.getElementById('patientForm').innerHTML = `
                <div class="card mb-4">
                    <div class="card-body">
                        <h4>Medical History - ${patient.name}</h4>
                        <form onsubmit="addMedicalRecord(event, '${patient._id}')">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Patient Name</label>
                                        <input name="patientName" class="form-control" value="${patient.name}" readonly />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Condition</label>
                                        <input name="condition" class="form-control" placeholder="Medical Condition" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Diagnosis</label>
                                        <input name="diagnosis" class="form-control" placeholder="Diagnosis" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Treatment</label>
                                        <input name="treatment" class="form-control" placeholder="Treatment Plan" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Notes</label>
                                        <textarea name="notes" class="form-control" placeholder="Additional Notes"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Date</label>
                                        <input name="date" type="date" class="form-control" value="${new Date().toISOString().split('T')[0]}" required />
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" onclick="hideForm()">Cancel</button>
                                <button type="submit" class="btn btn-success">Add Record</button>
                            </div>
                        </form>

                        <hr>
                        <h5>Previous Records</h5>
                        <div id="medicalRecords">Loading...</div>
                    </div>
                </div>
            `;

            // Fetch medical records
            fetch(`${serviceUrl}/${patient._id}/medical-history`)
                .then(res => res.json())
                .then(records => {
                    const recordsHtml = records.length ? records.map(record => `
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h6 class="card-subtitle mb-2 text-muted">Date: ${new Date(record.date).toLocaleDateString()}</h6>
                                    <span class="text-muted">Patient: ${patient.name}</span>
                                </div>
                                <p><strong>Condition:</strong> ${record.condition}</p>
                                <p><strong>Diagnosis:</strong> ${record.diagnosis}</p>
                                <p><strong>Treatment:</strong> ${record.treatment}</p>
                                ${record.notes ? `<p><strong>Notes:</strong> ${record.notes}</p>` : ''}
                            </div>
                        </div>
                    `).join('') : '<p class="text-muted">No medical records found.</p>';
                    document.getElementById('medicalRecords').innerHTML = recordsHtml;
                })
                .catch(error => {
                    document.getElementById('medicalRecords').innerHTML = '<div class="alert alert-danger">Failed to load medical records.</div>';
                });
        }

        function showEditForm(p) {
            document.getElementById('patientForm').style.display = 'block';
            document.getElementById('patientForm').innerHTML = `
                <div class="card mb-4">
                    <div class="card-body">
                        <h4>Edit Patient</h4>
                        <form onsubmit="editPatient(event, '${p._id}')">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input name="name" class="form-control" value="${p.name}" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input name="dateOfBirth" type="date" class="form-control" value="${p.dateOfBirth.split('T')[0]}" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Gender</label>
                                        <select name="gender" class="form-control" required>
                                            <option value="male" ${p.gender==='male'?'selected':''}>Male</option>
                                            <option value="female" ${p.gender==='female'?'selected':''}>Female</option>
                                            <option value="other" ${p.gender==='other'?'selected':''}>Other</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input name="phoneNumber" class="form-control" value="${p.phoneNumber}" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea name="address" class="form-control" required>${p.address}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" onclick="hideForm()">Cancel</button>
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
        }

        function hideForm() {
            document.getElementById('patientForm').style.display = 'none';
            document.getElementById('patientForm').innerHTML = '';
        }

        function addPatient(e) {
            e.preventDefault();
            const form = e.target;

            // Prepare medical history if provided
            const medicalHistory = [];
            if (form.condition.value && form.diagnosis.value && form.treatment.value) {
                medicalHistory.push({
                    condition: form.condition.value,
                    diagnosis: form.diagnosis.value,
                    treatment: form.treatment.value,
                    notes: form.notes.value,
                    doctorId: doctorId,
                    doctorName: '<?php echo htmlspecialchars($_SESSION['user']['full_Name']); ?>',
                    date: new Date()
                });
            }

            fetch(serviceUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        userId: form.userId.value,
                        name: form.name.value,
                        dateOfBirth: form.dateOfBirth.value,
                        gender: form.gender.value,
                        phoneNumber: form.phoneNumber.value,
                        address: form.address.value,
                        medicalHistory: medicalHistory
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(() => {
                    showSuccess('Patient added successfully');
                    hideForm();
                    fetchPatients();
                })
                .catch(error => {
                    showError('Error adding patient: ' + (error.message || 'Unknown error'));
                });
        }

        function addMedicalRecord(e, patientId) {
            e.preventDefault();
            const form = e.target;

            fetch(`${serviceUrl}/${patientId}/medical-history`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        condition: form.condition.value,
                        diagnosis: form.diagnosis.value,
                        treatment: form.treatment.value,
                        notes: form.notes.value,
                        doctorId: doctorId,
                        doctorName: '<?php echo htmlspecialchars($_SESSION['user']['full_Name']); ?>',
                        date: new Date()
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(() => {
                    showSuccess('Medical record added successfully');
                    fetchPatients();
                    hideForm();
                })
                .catch(error => {
                    showError('Error adding medical record: ' + (error.message || 'Unknown error'));
                });
        }

        function editPatient(e, id) {
            e.preventDefault();
            const form = e.target;
            fetch(`${serviceUrl}/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: form.name.value,
                        dateOfBirth: form.dateOfBirth.value,
                        gender: form.gender.value,
                        phoneNumber: form.phoneNumber.value,
                        address: form.address.value
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(() => {
                    showSuccess('Patient updated successfully');
                    hideForm();
                    fetchPatients();
                })
                .catch(error => {
                    showError('Error updating patient: ' + (error.message || 'Unknown error'));
                });
        }

        function deletePatient(patientId) {
            if (!confirm('Are you sure you want to delete this patient?')) return;

            fetch(`${serviceUrl}/${patientId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => {
                    if (!res.ok) throw new Error('Failed to delete patient');
                    return res.json();
                })
                .then(data => {
                    showSuccess('Patient deleted successfully');
                    fetchPatients();
                })
                .catch(error => showError(error.message));
        }

        // Load patients when page loads
        fetchPatients();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>