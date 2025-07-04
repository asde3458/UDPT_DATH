<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - Doctor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Appointment Management</h2>
        <button class="btn btn-primary mb-3" onclick="showAddForm()">Add Appointment</button>
        <div id="appointmentForm" style="display:none;"></div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="appointmentsTable">
                <!-- Appointment rows here -->
            </tbody>
        </table>
    </div>
    <script>
        const doctorId = '<?php echo $_SESSION['user']['id']; ?>';
        const serviceUrl = '<?php echo APPOINTMENT_SERVICE_URL; ?>/appointments';

        function fetchAppointments() {
            fetch(serviceUrl + '?doctorId=' + doctorId)
                .then(res => res.json())
                .then(data => {
                    const rows = data.map(a => `
                        <tr>
                            <td>${a.patientId}</td>
                            <td>${new Date(a.date).toLocaleDateString()}</td>
                            <td>${a.time}</td>
                            <td>${a.reason}</td>
                            <td>${a.status}</td>
                            <td>
                                <button class='btn btn-sm btn-warning' onclick='showEditForm(${JSON.stringify(a)})'>Edit</button>
                                <button class='btn btn-sm btn-danger' onclick='deleteAppointment("${a._id}")'>Delete</button>
                            </td>
                        </tr>
                    `).join('');
                    document.getElementById('appointmentsTable').innerHTML = rows;
                });
        }

        function showAddForm() {
            document.getElementById('appointmentForm').style.display = 'block';
            document.getElementById('appointmentForm').innerHTML = `
                <h4>Add Appointment</h4>
                <form onsubmit="addAppointment(event)">
                    <input name="patientId" class="form-control mb-2" placeholder="Patient ID" required />
                    <input name="date" type="date" class="form-control mb-2" required />
                    <input name="time" type="time" class="form-control mb-2" required />
                    <input name="reason" class="form-control mb-2" placeholder="Reason" required />
                    <select name="status" class="form-control mb-2" required>
                        <option value="scheduled">Scheduled</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <button class="btn btn-success" type="submit">Add</button>
                    <button class="btn btn-secondary" type="button" onclick="hideForm()">Cancel</button>
                </form>
            `;
        }

        function showEditForm(a) {
            document.getElementById('appointmentForm').style.display = 'block';
            document.getElementById('appointmentForm').innerHTML = `
                <h4>Edit Appointment</h4>
                <form onsubmit="editAppointment(event, '${a._id}')">
                    <input name="patientId" class="form-control mb-2" value="${a.patientId}" required />
                    <input name="date" type="date" class="form-control mb-2" value="${a.date.split('T')[0]}" required />
                    <input name="time" type="time" class="form-control mb-2" value="${a.time}" required />
                    <input name="reason" class="form-control mb-2" value="${a.reason}" required />
                    <select name="status" class="form-control mb-2" required>
                        <option value="scheduled" ${a.status==='scheduled'?'selected':''}>Scheduled</option>
                        <option value="completed" ${a.status==='completed'?'selected':''}>Completed</option>
                        <option value="cancelled" ${a.status==='cancelled'?'selected':''}>Cancelled</option>
                    </select>
                    <button class="btn btn-success" type="submit">Save</button>
                    <button class="btn btn-secondary" type="button" onclick="hideForm()">Cancel</button>
                </form>
            `;
        }

        function hideForm() {
            document.getElementById('appointmentForm').style.display = 'none';
            document.getElementById('appointmentForm').innerHTML = '';
        }

        function addAppointment(e) {
            e.preventDefault();
            const form = e.target;
            fetch(serviceUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    patientId: form.patientId.value,
                    doctorId: doctorId,
                    date: form.date.value,
                    time: form.time.value,
                    reason: form.reason.value,
                    status: form.status.value
                })
            }).then(() => {
                hideForm();
                fetchAppointments();
            });
        }

        function editAppointment(e, id) {
            e.preventDefault();
            const form = e.target;
            fetch(serviceUrl + '/' + id, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    patientId: form.patientId.value,
                    doctorId: doctorId,
                    date: form.date.value,
                    time: form.time.value,
                    reason: form.reason.value,
                    status: form.status.value
                })
            }).then(() => {
                hideForm();
                fetchAppointments();
            });
        }

        function deleteAppointment(id) {
            if (confirm('Delete this appointment?')) {
                fetch(serviceUrl + '/' + id, {
                        method: 'DELETE'
                    })
                    .then(() => fetchAppointments());
            }
        }
        fetchAppointments();
    </script>
</body>

</html>