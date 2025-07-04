<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Patient</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>My Profile</h2>
        <div id="patientProfile" class="card mt-3">
            <div class="card-body">
                <div id="loadingMessage" class="text-center">
                    Loading your profile...
                </div>
                <div id="profileData" style="display:none;">
                    <!-- Profile data will be loaded here -->
                </div>
                <div id="errorMessage" class="alert alert-danger" style="display:none;">
                    <!-- Error messages will be shown here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        const userId = '<?php echo $_SESSION['user']['id']; ?>';
        const serviceUrl = '<?php echo PATIENT_SERVICE_URL; ?>/patients';

        function fetchPatientProfile() {
            // First, try to get patient by user ID
            fetch(`${serviceUrl}/user/${userId}`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Failed to fetch profile');
                    }
                    return res.json();
                })
                .then(patient => {
                    if (!patient) {
                        throw new Error('Patient profile not found');
                    }
                    displayProfile(patient);
                })
                .catch(error => {
                    document.getElementById('loadingMessage').style.display = 'none';
                    document.getElementById('errorMessage').style.display = 'block';
                    document.getElementById('errorMessage').textContent = error.message;
                });
        }

        function displayProfile(patient) {
            const profileHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <h4>${patient.name}</h4>
                        <p><strong>Age:</strong> ${patient.age}</p>
                        <p><strong>Gender:</strong> ${patient.gender}</p>
                        <p><strong>Patient ID:</strong> ${patient._id}</p>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary" onclick="showEditForm()">Edit Profile</button>
                </div>
            `;

            document.getElementById('loadingMessage').style.display = 'none';
            document.getElementById('profileData').style.display = 'block';
            document.getElementById('profileData').innerHTML = profileHtml;
        }

        function showEditForm() {
            fetch(`${serviceUrl}/user/${userId}`)
                .then(res => res.json())
                .then(patient => {
                    const formHtml = `
                        <h4>Edit Profile</h4>
                        <form onsubmit="updateProfile(event, '${patient._id}')">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input name="name" class="form-control" value="${patient.name}" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Age</label>
                                <input name="age" type="number" class="form-control" value="${patient.age}" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-control" required>
                                    <option value="male" ${patient.gender === 'male' ? 'selected' : ''}>Male</option>
                                    <option value="female" ${patient.gender === 'female' ? 'selected' : ''}>Female</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                            <button type="button" class="btn btn-secondary" onclick="cancelEdit()">Cancel</button>
                        </form>
                    `;
                    document.getElementById('profileData').innerHTML = formHtml;
                });
        }

        function updateProfile(e, patientId) {
            e.preventDefault();
            const form = e.target;

            fetch(`${serviceUrl}/${patientId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: form.name.value,
                        age: parseInt(form.age.value),
                        gender: form.gender.value
                    })
                })
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Failed to update profile');
                    }
                    return res.json();
                })
                .then(() => {
                    fetchPatientProfile();
                })
                .catch(error => {
                    document.getElementById('errorMessage').style.display = 'block';
                    document.getElementById('errorMessage').textContent = error.message;
                });
        }

        function cancelEdit() {
            fetchPatientProfile();
        }

        // Load profile when page loads
        fetchPatientProfile();
    </script>
</body>

</html>