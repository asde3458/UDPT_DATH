<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Prescription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Create New Prescription</h2>
        <form id="prescriptionForm">
            <div class="mb-3">
                <label for="patientId" class="form-label">Patient ID</label>
                <input type="text" class="form-control" id="patientId" required>
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
            <button type="submit" class="btn btn-primary">Create Prescription</button>
            <a href="/prescriptions" class="btn btn-secondary">Cancel</a>
        </form>
        <div id="resultMsg" class="mt-3"></div>
    </div>
    <script>
        const doctorId = '<?php echo $_SESSION['user']['id']; ?>';
        const doctorName = '<?php echo $_SESSION['user']['fullName']; ?>';
        const PRESCRIPTION_SERVICE_URL = '<?php echo PRESCRIPTION_SERVICE_URL; ?>';

        function addMedication() {
            const container = document.getElementById('medicationsList');
            const idx = container.children.length;
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
                        setTimeout(() => window.location.href = '/prescriptions', 1000);
                    }
                })
                .catch(() => {
                    document.getElementById('resultMsg').innerHTML = `<div class='alert alert-danger'>Error creating prescription</div>`;
                });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>