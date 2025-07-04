<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Doctor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .appointment-card {
            transition: transform 0.2s;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .appointment-card:hover {
            transform: translateY(-5px);
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
                        <a class="nav-link active" href="/appointments">Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/patients">Patients</a>
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
        <div class="row mb-4">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-calendar-alt me-2"></i>Appointments</h2>
                    <button class="btn btn-primary" onclick="showAddForm()">
                        <i class="fas fa-plus me-2"></i>New Appointment
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" id="filterStartDate" class="form-control" onchange="filterAppointments()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" id="filterEndDate" class="form-control" onchange="filterAppointments()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select id="filterStatus" class="form-select" onchange="filterAppointments()">
                            <option value="">All Status</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments List -->
        <div class="row" id="appointmentsContainer">
            <!-- Appointments will be loaded here -->
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="appointmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Form will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const doctorId = '<?php echo $_SESSION['user']['id']; ?>';
        const doctorName = '<?php echo $_SESSION['user']['name']; ?>';
        const serviceUrl = '<?php echo APPOINTMENT_SERVICE_URL; ?>/appointments';
        let appointmentModal;

        document.addEventListener('DOMContentLoaded', function() {
            appointmentModal = new bootstrap.Modal(document.getElementById('appointmentModal'));
            fetchAppointments();
        });

        function fetchAppointments() {
            const startDate = document.getElementById('filterStartDate').value;
            const endDate = document.getElementById('filterEndDate').value;
            const status = document.getElementById('filterStatus').value;

            let url = `${serviceUrl}?doctorId=${doctorId}`;
            if (startDate) url += `&startDate=${startDate}`;
            if (endDate) url += `&endDate=${endDate}`;
            if (status) url += `&status=${status}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('appointmentsContainer');
                    container.innerHTML = data.map(a => `
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card appointment-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-title mb-0">Patient: ${a.patientName}</h5>
                                        <span class="badge bg-${getStatusColor(a.status)}">${a.status}</span>
                                    </div>
                                    <div class="mb-2">
                                        <i class="far fa-calendar me-2"></i>${new Date(a.date).toLocaleDateString()}
                                    </div>
                                    <div class="mb-2">
                                        <i class="far fa-clock me-2"></i>${formatTime(a.time)}
                                    </div>
                                    <div class="mb-3">
                                        <i class="far fa-comment me-2"></i>${a.reason}
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        ${getActionButtons(a)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('') || '<div class="col-12"><p class="text-center text-muted">No appointments found</p></div>';
                });
        }

        function getStatusColor(status) {
            switch (status) {
                case 'scheduled':
                    return 'primary';
                case 'confirmed':
                    return 'info';
                case 'completed':
                    return 'success';
                case 'cancelled':
                    return 'danger';
                default:
                    return 'secondary';
            }
        }

        function getActionButtons(appointment) {
            let buttons = [];

            if (appointment.status === 'scheduled') {
                buttons.push(`
                    <button class="btn btn-sm btn-info" onclick='updateStatus("${appointment._id}", "confirmed")'>
                        <i class="fas fa-check me-1"></i>Confirm
                    </button>
                `);
            }

            if (appointment.status === 'confirmed') {
                buttons.push(`
                    <button class="btn btn-sm btn-success" onclick='updateStatus("${appointment._id}", "completed")'>
                        <i class="fas fa-check-double me-1"></i>Complete
                    </button>
                `);
            }

            if (['scheduled', 'confirmed'].includes(appointment.status)) {
                buttons.push(`
                    <button class="btn btn-sm btn-danger" onclick='updateStatus("${appointment._id}", "cancelled")'>
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                `);
            }

            buttons.push(`
                <button class="btn btn-sm btn-primary" onclick='showEditForm("${appointment._id}")'>
                    <i class="fas fa-edit me-1"></i>Edit
                </button>
            `);

            return buttons.join('');
        }

        function formatTime(time) {
            const [hours, minutes] = time.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const hour12 = hour % 12 || 12;
            return `${hour12}:${minutes} ${ampm}`;
        }

        function getMinuteOptions(selectedMinute = null) {
            return [0, 15, 30, 45].map(m => {
                const value = m.toString().padStart(2, '0');
                const selected = selectedMinute !== null && m === parseInt(selectedMinute) ? 'selected' : '';
                return `<option value="${m}" ${selected}>${value}</option>`;
            }).join('');
        }

        function getHourOptions(selectedHour = null) {
            return Array.from({
                length: 12
            }, (_, i) => i + 1).map(h => {
                const value = h.toString().padStart(2, '0');
                const selected = selectedHour !== null && h === selectedHour ? 'selected' : '';
                return `<option value="${h}" ${selected}>${value}</option>`;
            }).join('');
        }

        function showAddForm() {
            document.getElementById('modalTitle').textContent = 'Add Appointment';
            document.getElementById('modalContent').innerHTML = `
                <form onsubmit="addAppointment(event)" id="appointmentForm">
                    <div class="mb-3">
                        <label class="form-label">Patient ID <span class="text-danger">*</span></label>
                        <input type="text" name="patientId" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                        <input type="text" name="patientName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Doctor Name <span class="text-danger">*</span></label>
                        <input type="text" name="doctorName" class="form-control" value="${doctorName}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" required min="${new Date().toISOString().split('T')[0]}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Time <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-6">
                                <select name="hour" class="form-select" required>
                                    <option value="">Hour</option>
                                    ${getHourOptions()}
                                </select>
                            </div>
                            <div class="col-3">
                                <select name="minute" class="form-select" required>
                                    <option value="">Min</option>
                                    ${getMinuteOptions()}
                                </select>
                            </div>
                            <div class="col-3">
                                <select name="ampm" class="form-select" required>
                                    <option value="AM">AM</option>
                                    <option value="PM">PM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="scheduled">Scheduled</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Appointment</button>
                    </div>
                </form>
            `;
            appointmentModal.show();
        }

        function showEditForm(id) {
            fetch(`${serviceUrl}/${id}`)
                .then(res => res.json())
                .then(appointment => {
                    const [hours, minutes] = appointment.time.split(':');
                    const hour = parseInt(hours);
                    const hour12 = hour % 12 || 12;
                    const ampm = hour >= 12 ? 'PM' : 'AM';

                    document.getElementById('modalTitle').textContent = 'Edit Appointment';
                    document.getElementById('modalContent').innerHTML = `
                        <form onsubmit="editAppointment(event, '${id}')">
                            <div class="mb-3">
                                <label class="form-label">Patient ID <span class="text-danger">*</span></label>
                                <input type="text" name="patientId" class="form-control" value="${appointment.patientId}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                                <input type="text" name="patientName" class="form-control" value="${appointment.patientName}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Doctor Name <span class="text-danger">*</span></label>
                                <input type="text" name="doctorName" class="form-control" value="${appointment.doctorName}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" value="${appointment.date.split('T')[0]}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Time <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-6">
                                        <select name="hour" class="form-select" required>
                                            <option value="">Hour</option>
                                            ${getHourOptions(hour12)}
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <select name="minute" class="form-select" required>
                                            <option value="">Min</option>
                                            ${getMinuteOptions(parseInt(minutes))}
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <select name="ampm" class="form-select" required>
                                            <option value="AM" ${ampm === 'AM' ? 'selected' : ''}>AM</option>
                                            <option value="PM" ${ampm === 'PM' ? 'selected' : ''}>PM</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Reason <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="3" required>${appointment.reason}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="scheduled" ${appointment.status === 'scheduled' ? 'selected' : ''}>Scheduled</option>
                                    <option value="confirmed" ${appointment.status === 'confirmed' ? 'selected' : ''}>Confirmed</option>
                                    <option value="completed" ${appointment.status === 'completed' ? 'selected' : ''}>Completed</option>
                                    <option value="cancelled" ${appointment.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                                </select>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    `;
                    appointmentModal.show();
                });
        }

        function addAppointment(e) {
            e.preventDefault();
            const form = e.target;

            // Convert 12-hour time to 24-hour format
            const hour = parseInt(form.hour.value);
            const minute = parseInt(form.minute.value);
            const ampm = form.ampm.value;
            let hour24 = hour;
            if (ampm === 'PM' && hour !== 12) hour24 += 12;
            if (ampm === 'AM' && hour === 12) hour24 = 0;
            const time = `${hour24.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;

            const appointmentData = {
                patientId: form.patientId.value,
                patientName: form.patientName.value,
                doctorId: doctorId,
                doctorName: form.doctorName.value,
                date: form.date.value,
                time: time,
                reason: form.reason.value,
                status: form.status.value
            };

            console.log('Sending appointment data:', appointmentData);

            fetch(serviceUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(appointmentData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    appointmentModal.hide();
                    fetchAppointments();
                    form.reset();
                    alert('Appointment added successfully!');
                })
                .catch(error => {
                    console.error('Error adding appointment:', error);
                    alert(error.error || 'Failed to add appointment');
                });
        }

        function editAppointment(e, id) {
            e.preventDefault();
            const form = e.target;

            // Convert 12-hour time to 24-hour format
            const hour = parseInt(form.hour.value);
            const minute = parseInt(form.minute.value);
            const ampm = form.ampm.value;
            let hour24 = hour;
            if (ampm === 'PM' && hour !== 12) hour24 += 12;
            if (ampm === 'AM' && hour === 12) hour24 = 0;
            const time = `${hour24.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;

            const appointmentData = {
                patientId: form.patientId.value,
                patientName: form.patientName.value,
                doctorId: doctorId,
                doctorName: form.doctorName.value,
                date: form.date.value,
                time: time,
                reason: form.reason.value,
                status: form.status.value
            };

            console.log('Updating appointment data:', appointmentData);

            fetch(`${serviceUrl}/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(appointmentData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    appointmentModal.hide();
                    fetchAppointments();
                    alert('Appointment updated successfully!');
                })
                .catch(error => {
                    console.error('Error updating appointment:', error);
                    alert(error.error || 'Failed to update appointment');
                });
        }

        function updateStatus(id, status) {
            fetch(`${serviceUrl}/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    fetchAppointments();
                    alert(`Appointment ${status} successfully!`);
                })
                .catch(error => {
                    alert(error.error || `Failed to update appointment status`);
                });
        }

        function filterAppointments() {
            fetchAppointments();
        }
    </script>
</body>

</html>