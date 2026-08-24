<?php

require_once 'config/db.php';
require_once 'config/auth.php';
require_login();

$page_title = 'Consultations';

if (isset($_POST['save'])) {

    $patient_id = $_POST['patient_id'];
    $doctor_id  = $_POST['doctor_id'];
    $date       = $_POST['consultation_date'];
    $diagnosis  = trim($_POST['diagnosis']);
    $notes      = trim($_POST['notes']);

    if (!empty($_POST['consultation_id'])) {
        $stmt = $pdo->prepare(
            "UPDATE consultations
             SET patient_id = ?, doctor_id = ?, consultation_date = ?, diagnosis = ?, notes = ?
             WHERE id = ?"
        );
        $stmt->execute([$patient_id, $doctor_id, $date, $diagnosis, $notes, $_POST['consultation_id']]);
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO consultations (patient_id, doctor_id, consultation_date, diagnosis, notes)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$patient_id, $doctor_id, $date, $diagnosis, $notes]);
    }

    header("Location: consultations.php");
    exit;
}
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM consultations WHERE id = ?");
    $stmt->execute([$_GET['delete']]);

    header("Location: consultations.php");
    exit;
}

$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM consultations WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch();
}

$patients = $pdo->query("SELECT id, name FROM patients ORDER BY name")->fetchAll();
$doctors  = $pdo->query("SELECT id, name FROM doctors ORDER BY name")->fetchAll();

$rows = $pdo->query(
    "SELECT c.*, p.name AS patient, d.name AS doctor
     FROM consultations c
     JOIN patients p ON p.id = c.patient_id
     JOIN doctors d ON d.id = c.doctor_id
     ORDER BY c.id DESC"
)->fetchAll();

include 'includes/header.php';
?>

<div class="panel">
    <h2><?= $editing ? 'Edit Consultation' : 'Add Consultation' ?></h2>

    <form method="post" class="grid">

        <?php if ($editing): ?>
            <input type="hidden" name="consultation_id" value="<?= $editing['id'] ?>">
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
            <input type="date" name="consultation_date" required
                   value="<?= htmlspecialchars($editing['consultation_date'] ?? date('Y-m-d')) ?>">
        </div>

        <div class="field">
            <label>Diagnosis</label>
            <input name="diagnosis" value="<?= htmlspecialchars($editing['diagnosis'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Notes</label>
            <textarea name="notes"><?= htmlspecialchars($editing['notes'] ?? '') ?></textarea>
        </div>

        <div class="actions">
            <button class="btn" name="save"><?= $editing ? 'Update Consultation' : 'Save Consultation' ?></button>
            <?php if ($editing): ?>
                <a class="btn secondary" href="consultations.php">Cancel</a>
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
            <th>Diagnosis</th>
            <th>Notes</th>
            <th>Action</th>
        </tr>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['consultation_date']) ?></td>
                <td><?= htmlspecialchars($r['patient']) ?></td>
                <td><?= htmlspecialchars($r['doctor']) ?></td>
                <td><?= htmlspecialchars($r['diagnosis']) ?></td>
                <td><?= htmlspecialchars($r['notes']) ?></td>
                <td class="actions">
                    <a class="btn secondary" href="?edit=<?= $r['id'] ?>">Edit</a>
                    <a class="btn danger" href="?delete=<?= $r['id'] ?>"
                       onclick="return confirm('Delete this consultation?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
