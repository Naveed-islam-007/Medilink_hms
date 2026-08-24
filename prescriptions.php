<?php

require_once 'config/db.php';
require_once 'config/auth.php';
require_login();

$page_title = 'Prescriptions & Consultations';


if (isset($_POST['save'])) {

    $patient_id   = $_POST['patient_id'];
    $doctor_id    = $_POST['doctor_id'];
    $date         = $_POST['prescription_date'];
    $medicine     = trim($_POST['medicine_name']);
    $dosage       = trim($_POST['dosage']);
    $instructions = trim($_POST['instructions']);

    if (!empty($_POST['prescription_id'])) {
        $stmt = $pdo->prepare(
            "UPDATE prescriptions
             SET patient_id = ?, doctor_id = ?, prescription_date = ?, medicine_name = ?, dosage = ?, instructions = ?
             WHERE id = ?"
        );
        $stmt->execute([$patient_id, $doctor_id, $date, $medicine, $dosage, $instructions, $_POST['prescription_id']]);
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO prescriptions (patient_id, doctor_id, prescription_date, medicine_name, dosage, instructions)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$patient_id, $doctor_id, $date, $medicine, $dosage, $instructions]);
    }

    header("Location: prescriptions.php");
    exit;
}
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM prescriptions WHERE id = ?");
    $stmt->execute([$_GET['delete']]);

    header("Location: prescriptions.php");
    exit;
}


$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM prescriptions WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch();
}


$patients = $pdo->query("SELECT id, name FROM patients ORDER BY name")->fetchAll();
$doctors  = $pdo->query("SELECT id, name FROM doctors ORDER BY name")->fetchAll();


$rows = $pdo->query(
    "SELECT pr.*, p.name AS patient, d.name AS doctor
     FROM prescriptions pr
     JOIN patients p ON p.id = pr.patient_id
     JOIN doctors d ON d.id = pr.doctor_id
     ORDER BY pr.id DESC"
)->fetchAll();

include 'includes/header.php';
?>

<div class="panel">
    <h2><?= $editing ? 'Edit Prescription' : 'Create Prescription' ?></h2>

    <form method="post" class="grid">

        <?php if ($editing): ?>
            <input type="hidden" name="prescription_id" value="<?= $editing['id'] ?>">
        <?php endif; ?>

        <div class="field">
            <label>Patient</label>
            <select name="patient_id" required>
                <?php foreach ($patients as $p): ?>
                    <option value="<?= $p['id'] ?>"
                        <?= (isset($editing['patient_id']) && $editing['patient_id'] == $p['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label>Doctor</label>
            <select name="doctor_id" required>
                <?php foreach ($doctors as $d): ?>
                    <option value="<?= $d['id'] ?>"
                        <?= (isset($editing['doctor_id']) && $editing['doctor_id'] == $d['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label>Date</label>
            <input type="date" name="prescription_date" required
                   value="<?= htmlspecialchars($editing['prescription_date'] ?? date('Y-m-d')) ?>">
        </div>

        <div class="field">
            <label>Medicine</label>
            <input name="medicine_name" required value="<?= htmlspecialchars($editing['medicine_name'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Dosage</label>
            <input name="dosage" placeholder="1 tablet twice daily" value="<?= htmlspecialchars($editing['dosage'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Instructions</label>
            <textarea name="instructions"><?= htmlspecialchars($editing['instructions'] ?? '') ?></textarea>
        </div>

        <div class="actions">
            <button class="btn" name="save"><?= $editing ? 'Update Prescription' : 'Save Prescription' ?></button>
            <?php if ($editing): ?>
                <a class="btn secondary" href="prescriptions.php">Cancel</a>
            <?php endif; ?>
        </div>

    </form>
</div>

<div class="panel table-wrap">
    <table>
        <tr>
            <th>Date</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Medicine</th>
            <th>Dosage</th>
            <th>Action</th>
        </tr>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['prescription_date']) ?></td>
                <td><?= htmlspecialchars($r['patient']) ?></td>
                <td><?= htmlspecialchars($r['doctor']) ?></td>
                <td><?= htmlspecialchars($r['medicine_name']) ?></td>
                <td><?= htmlspecialchars($r['dosage']) ?></td>
                <td class="actions">
                    <a class="btn secondary" href="?edit=<?= $r['id'] ?>">Edit</a>
                    <a class="btn danger" href="?delete=<?= $r['id'] ?>"
                       onclick="return confirm('Delete this prescription?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
