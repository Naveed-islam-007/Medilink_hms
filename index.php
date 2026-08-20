<?php
// ============================================================
// index.php
// The dashboard: shows a quick count of rows in each table.
// ============================================================
require_once 'config/db.php';
require_once 'config/auth.php';
require_login();

$page_title = 'Dashboard';

// Table name => label shown on the card
$tables = [
    'patients'     => 'Patients',
    'doctors'      => 'Doctors',
    'hospitals'    => 'Hospitals',
    'pharmacies'   => 'Pharmacies',
    'medicines'    => 'Medicines',
    'blood_donors' => 'Blood Donors',
    'prescriptions'=> 'Prescriptions',
    'consultations'=> 'Consultations',
];

// Count how many rows are in each table
$stats = [];
foreach ($tables as $table => $label) {
    $stats[$table] = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
}

include 'includes/header.php';
?>

<div class="cards">
    <?php foreach ($tables as $table => $label): ?>
        <div class="card">
            <h3><?= $label ?></h3>
            <div class="number"><?= $stats[$table] ?></div>
        </div>
    <?php endforeach; ?>
</div>

<div class="panel">
    <h2>System Overview</h2>
    <p>MediLink centralizes patient records, healthcare providers, prescriptions,
       pharmacy inventory and blood donor information in one MySQL database.</p>
</div>

<div class="panel">
    <h2>Quick Actions</h2>
    <div class="actions">
        <a class="btn" href="patients.php">Manage Patients</a>
        <a class="btn" href="doctors.php">Find Doctors</a>
        <a class="btn" href="donors.php">Search Donors</a>
        <a class="btn" href="medicines.php">Check Medicines</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
