<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0098ef 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .card {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 2rem;
        }

        .btn-edit {
            position: absolute;
            top: 1rem;
            right: 1rem;
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
                        <a class="nav-link" href="/patient/appointments">Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/patient/profile">Profile</a>
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

    <div class="profile-header">
        <div class="container text-center">
            <h2>My Profile</h2>
        </div>
    </div>

    <div class="container">
        <div id="errorAlert" class="alert alert-danger" style="display:none;"></div>
        <div id="successAlert" class="alert alert-success" style="display:none;"></div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Personal Information Card -->
                <div class="card">
                    <div class="card-body position-relative">
                        <button class="btn btn-primary btn-edit" onclick="toggleEditMode()">
                            <i class="fas fa-edit"></i> Edit Profile
                        </button>

                        <!-- View Mode -->
                        <div id="viewMode">
                            <h4 class="card-title mb-4">Personal Information</h4>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Full Name:</div>
                                <div class="col-md-8" id="viewName">Loading...</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Date of Birth:</div>
                                <div class="col-md-8" id="viewDob">Loading...</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Gender:</div>
                                <div class="col-md-8" id="viewGender">Loading...</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Phone Number:</div>
                                <div class="col-md-8" id="viewPhone">Loading...</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Address:</div>
                                <div class="col-md-8" id="viewAddress">Loading...</div>
                            </div>
                        </div>

                        <!-- Edit Mode -->
                        <div id="editMode" style="display:none;">
                            <h4 class="card-title mb-4">Edit Profile</h4>
                            <form onsubmit="updateProfile(event)">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="editName" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="editDob" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Gender</label>
                                    <select class="form-control" id="editGender" required>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="editPhone" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" id="editAddress" rows="3" required></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary me-2" onclick="toggleEditMode()">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Medical History Card -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Medical History</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Condition</th>
                                        <th>Diagnosis</th>
                                        <th>Treatment</th>
                                        <th>Doctor</th>
                                    </tr>
                                </thead>
                                <tbody id="medicalHistoryTable">
                                    <tr>
                                        <td colspan="5" class="text-center">Loading medical history...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const userId = '<?php echo $_SESSION['user']['id']; ?>';
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

        function toggleEditMode() {
            const viewMode = document.getElementById('viewMode');
            const editMode = document.getElementById('editMode');
            if (viewMode.style.display !== 'none') {
                viewMode.style.display = 'none';
                editMode.style.display = 'block';
            } else {
                viewMode.style.display = 'block';
                editMode.style.display = 'none';
            }
        }

        function loadProfile() {
            fetch(`${serviceUrl}/user/${userId}`)
                .then(res => {
                    if (!res.ok) throw new Error('Failed to fetch profile');
                    return res.json();
                })
                .then(data => {
                    // Update view mode
                    document.getElementById('viewName').textContent = data.name;
                    document.getElementById('viewDob').textContent = new Date(data.dateOfBirth).toLocaleDateString();
                    document.getElementById('viewGender').textContent = data.gender.charAt(0).toUpperCase() + data.gender.slice(1);
                    document.getElementById('viewPhone').textContent = data.phoneNumber;
                    document.getElementById('viewAddress').textContent = data.address;

                    // Update edit mode
                    document.getElementById('editName').value = data.name;
                    document.getElementById('editDob').value = data.dateOfBirth.split('T')[0];
                    document.getElementById('editGender').value = data.gender;
                    document.getElementById('editPhone').value = data.phoneNumber;
                    document.getElementById('editAddress').value = data.address;

                    // Load medical history
                    if (data.medicalHistory && data.medicalHistory.length > 0) {
                        const historyHtml = data.medicalHistory.map(record => `
                            <tr>
                                <td>${new Date(record.date).toLocaleDateString()}</td>
                                <td>${record.condition}</td>
                                <td>${record.diagnosis}</td>
                                <td>${record.treatment}</td>
                                <td>Dr. ${record.doctorName || 'Unknown'}</td>
                            </tr>
                        `).join('');
                        document.getElementById('medicalHistoryTable').innerHTML = historyHtml;
                    } else {
                        document.getElementById('medicalHistoryTable').innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center">No medical history available</td>
                            </tr>
                        `;
                    }
                })
                .catch(error => showError(error.message));
        }

        function updateProfile(e) {
            e.preventDefault();

            const updatedData = {
                name: document.getElementById('editName').value,
                dateOfBirth: document.getElementById('editDob').value,
                gender: document.getElementById('editGender').value,
                phoneNumber: document.getElementById('editPhone').value,
                address: document.getElementById('editAddress').value
            };

            fetch(`${serviceUrl}/user/${userId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(updatedData)
                })
                .then(res => {
                    if (!res.ok) throw new Error('Failed to update profile');
                    return res.json();
                })
                .then(data => {
                    showSuccess('Profile updated successfully');
                    toggleEditMode();
                    loadProfile();
                })
                .catch(error => showError(error.message));
        }

        // Load profile when page loads
        loadProfile();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>

</html>