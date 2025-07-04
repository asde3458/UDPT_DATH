# Add prescription routes for doctor
$router->get('/prescriptions', function() {
requireLogin();
requireRole('doctor');
require 'views/doctor/prescriptions.php';
});

$router->get('/prescriptions/new', function() {
requireLogin();
requireRole('doctor');
require 'views/doctor/prescription-form.php';
});

$router->get('/prescriptions/:id', function($params) {
requireLogin();
requireRole('doctor');
$_GET['id'] = $params['id'];
require 'views/doctor/prescription-detail.php';
});

# Add prescription routes for patient
$router->get('/patient/prescriptions', function() {
requireLogin();
requireRole('patient');
require 'views/patient/prescriptions.php';
});

$router->get('/patient/prescriptions/:id', function($params) {
requireLogin();
requireRole('patient');
$_GET['id'] = $params['id'];
require 'views/patient/prescription-detail.php';
});