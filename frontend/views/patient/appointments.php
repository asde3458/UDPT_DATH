<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Patient</title>
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

        .status-scheduled {
            color: #007bff;
        }

        .status-completed {
            color: #28a745;
        }

        .status-cancelled {
            color: #dc3545;
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
                        <a class="nav-link active" href="/patient/appointments">My Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/patient/profile">My Profile</a>
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
            <h2><i class="fas fa-calendar-alt me-2"></i>My Appointments</h2>
            <button class="btn btn-primary" onclick="showRequestForm()">
                <i class="fas fa-plus me-2"></i>Request Appointment
            </button>
        </div>

        <div id="errorAlert" class="alert alert-danger" style="display:none;"></div>
        <div id="successAlert" class="alert alert-success" style="display:none;"></div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">From Date</label>
                        <input type="date" id="filterStartDate" class="form-control" onchange="filterAppointments()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">To Date</label>
                        <input type="date" id="filterEndDate" class="form-control" onchange="filterAppointments()">
                    </div>
                    <div class="col-md-4">
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Request Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalContent" style="max-height: 70vh; overflow-y: auto;">
                    <!-- Form will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const patientId = '<?php echo $_SESSION['user']['id']; ?>';
        const patientName = '<?php echo $_SESSION['user']['name']; ?>';
        const serviceUrl = '<?php echo APPOINTMENT_SERVICE_URL; ?>/appointments';
        let appointmentModal;

        document.addEventListener('DOMContentLoaded', function() {
            appointmentModal = new bootstrap.Modal(document.getElementById('appointmentModal'));
            fetchAppointments();
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

        function fetchAppointments() {
            const startDate = document.getElementById('filterStartDate').value;
            const endDate = document.getElementById('filterEndDate').value;
            const status = document.getElementById('filterStatus').value;

            let url = `${serviceUrl}?patientId=${patientId}`;
            if (startDate) url += `&startDate=${startDate}`;
            if (endDate) url += `&endDate=${endDate}`;
            if (status) url += `&status=${status}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('appointmentsContainer');
                    if (!data || data.length === 0) {
                        container.innerHTML = '<div class="col-12"><p class="text-center text-muted">No appointments found</p></div>';
                        return;
                    }

                    container.innerHTML = data.map(a => `
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card appointment-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-title mb-0">Dr. ${a.doctorName}</h5>
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
                    `).join('');
                })
                .catch(error => showError('Failed to load appointments'));
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

        function formatTime(time) {
            const [hours, minutes] = time.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const hour12 = hour % 12 || 12;
            return `${hour12}:${minutes} ${ampm}`;
        }

        function getActionButtons(appointment) {
            if (appointment.status === 'scheduled' || appointment.status === 'confirmed') {
                return `
                    <button class="btn btn-sm btn-danger" onclick='cancelAppointment("${appointment._id}")'>
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                `;
            }
            return '';
        }

        function showRequestForm() {
            document.getElementById('modalTitle').textContent = 'Request Appointment';
            document.getElementById('modalContent').innerHTML = `
                <form onsubmit="requestAppointment(event)" id="appointmentForm">
                    <div class="mb-3">
                        <label class="form-label">Patient ID <span class="text-danger">*</span></label>
                        <input type="text" name="patientId" class="form-control" value="${patientId}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                        <input type="text" name="patientName" class="form-control" value="${patientName}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Doctor ID <span class="text-danger">*</span></label>
                        <input type="text" name="doctorId" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Doctor Name <span class="text-danger">*</span></label>
                        <input type="text" name="doctorName" class="form-control" required>
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
                                    ${Array.from({length: 12}, (_, i) => i + 1).map(h =>
                                        ` < option value = "${h}" > $ {
                h.toString().padStart(2, '0')
            } < /option>`
        ).join('')
        } <
        /select> < /
        div > <
            div class = "col-3" >
            <
            select name = "minute"
        class = "form-select"
        required >
            <
            option value = "" > Min < /option> <
        option value = "00" > 00 < /option> <
        option value = "30" > 30 < /option> < /
            select > <
            /div> <
        div class = "col-3" >
        <
        select name = "ampm"
        class = "form-select"
        required >
            <
            option value = "AM" > AM < /option> <
        option value = "PM" > PM < /option> < /
            select > <
            /div> < /
            div > <
            /div> <
        div class = "mb-3" >
        <
        label class = "form-label" > Reason < span class = "text-danger" > * < /span></label >
            <
            textarea name = "reason"
        class = "form-control"
        rows = "3"
        required > < /textarea> < /
            div > <
            div class = "text-end" >
            <
            button type = "button"
        class = "btn btn-secondary"
        data - bs - dismiss = "modal" > Cancel < /button> <
        button type = "submit"
        class = "btn btn-primary" > Request Appointment < /button> < /
            div > <
            /form>
        `;
            appointmentModal.show();
        }

        function requestAppointment(e) {
            e.preventDefault();
            const form = e.target;

            // Convert 12-hour time to 24-hour format
            const hour = parseInt(form.hour.value);
            const minute = parseInt(form.minute.value);
            const ampm = form.ampm.value;
            let hour24 = hour;
            if (ampm === 'PM' && hour !== 12) hour24 += 12;
            if (ampm === 'AM' && hour === 12) hour24 = 0;
            const time = `
        $ {
            hour24.toString().padStart(2, '0')
        }: $ {
            minute.toString().padStart(2, '0')
        }
        `;

            fetch(serviceUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    patientId: patientId,
                    patientName: patientName,
                    doctorId: form.doctorId.value,
                    doctorName: form.doctorName.value,
                    date: form.date.value,
                    time: time,
                    reason: form.reason.value,
                    status: 'scheduled'
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(() => {
                appointmentModal.hide();
                showSuccess('Appointment requested successfully');
                fetchAppointments();
            })
            .catch(error => {
                showError(error.error || 'Failed to request appointment');
            });
        }

        function cancelAppointment(id) {
            if (!confirm('Are you sure you want to cancel this appointment?')) return;

            fetch(`
        $ {
            serviceUrl
        }
        /${id}`, {
        method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                status: 'cancelled'
            })
        })
        .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(() => {
                showSuccess('Appointment cancelled successfully');
                fetchAppointments();
            })
            .catch(error => {
                showError(error.error || 'Failed to cancel appointment');
            });
        }

        function filterAppointments() {
            fetchAppointments();
        }
    </script>
</body>

</html>