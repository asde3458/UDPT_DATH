<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Patient</title>
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
                        <a class="nav-link" href="/patient/appointments">My Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/patient/profile">My Profile</a>
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
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-user-circle me-2"></i>My Profile</h4>
                    </div>
                    <div class="card-body">
                        <div id="errorAlert" class="alert alert-danger" style="display:none;"></div>
                        <div id="successAlert" class="alert alert-success" style="display:none;"></div>

                        <form id="profileForm" onsubmit="updateProfile(event)">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="dateOfBirth" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phoneNumber" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Emergency Contact</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" name="emergencyContactName" class="form-control" placeholder="Contact Name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="tel" name="emergencyContactPhone" class="form-control" placeholder="Contact Phone" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Medical History</label>
                                <textarea name="medicalHistory" class="form-control" rows="3" placeholder="Any pre-existing conditions, allergies, or important medical information"></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const patientId = '<?php echo $_SESSION['user']['id']; ?>';
        const serviceUrl = '<?php echo PATIENT_SERVICE_URL; ?>/patients';

        document.addEventListener('DOMContentLoaded', function() {
            fetchProfile();
        });

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

        function fetchProfile() {
            fetch(`${serviceUrl}/${patientId}`)
                .then(res => res.json())
                .then(data => {
                    const form = document.getElementById('profileForm');
                    form.name.value = data.name || '';
                    form.dateOfBirth.value = data.dateOfBirth ? data.dateOfBirth.split('T')[0] : '';
                    form.gender.value = data.gender || '';
                    form.phoneNumber.value = data.phoneNumber || '';
                    form.email.value = data.email || '';
                    form.address.value = data.address || '';
                    form.emergencyContactName.value = data.emergencyContact?.name || '';
                    form.emergencyContactPhone.value = data.emergencyContact?.phone || '';
                    form.medicalHistory.value = data.medicalHistory || '';
                })
                .catch(error => showError('Failed to load profile data'));
        }

        function updateProfile(e) {
            e.preventDefault();
            const form = e.target;

            const profileData = {
                name: form.name.value,
                dateOfBirth: form.dateOfBirth.value,
                gender: form.gender.value,
                phoneNumber: form.phoneNumber.value,
                email: form.email.value,
                address: form.address.value,
                emergencyContact: {
                    name: form.emergencyContactName.value,
                    phone: form.emergencyContactPhone.value
                },
                medicalHistory: form.medicalHistory.value
            };

            fetch(`${serviceUrl}/${patientId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(profileData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(() => {
                    showSuccess('Profile updated successfully');
                })
                .catch(error => {
                    showError(error.error || 'Failed to update profile');
                });
        }

        function resetForm() {
            fetchProfile();
        }
    </script>
</body>

</html>