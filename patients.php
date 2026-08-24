<?php

require_once 'config/db.php';
require_once 'config/auth.php';
require_login();

$page_title = 'Patient Management';


if (isset($_POST['save'])) {

    $name        = trim($_POST['name']);
    $dob         = $_POST['dob'] ?: null;   
    $gender      = $_POST['gender'];
    $phone       = trim($_POST['phone']);
    $address     = trim($_POST['address']);
    $blood_group = trim($_POST['blood_group']);

    if (!empty($_POST['patient_id'])) {
        $stmt = $pdo->prepare(
            "UPDATE patients
             SET name = ?, dob = ?, gender = ?, phone = ?, address = ?, blood_group = ?
             WHERE id = ?"
        );
        $stmt->execute([$name, $dob, $gender, $phone, $address, $blood_group, $_POST['patient_id']]);
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO patients (name, dob, gender, phone, address, blood_group)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$name, $dob, $gender, $phone, $address, $blood_group]);
    }

    header("Location: patients.php");
    exit;
}


if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->execute([$_GET['delete']]);

    header("Location: patients.php");
    exit;
}


$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch();
}

$q    = trim($_GET['q'] ?? '');
$like = "%$q%";

$stmt = $pdo->prepare(
    "SELECT * FROM patients
     WHERE name LIKE ? OR phone LIKE ? OR blood_group LIKE ?
     ORDER BY id DESC"
);
$stmt->execute([$like, $like, $like]);
$rows = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="panel">
    <h2><?= $editing ? 'Edit Patient' : 'Add Patient' ?></h2>

    <form method="post" class="grid">

        <?php if ($editing): ?>
            <input type="hidden" name="patient_id" value="<?= $editing['id'] ?>">
        <?php endif; ?>

        <div class="field">
            <label>Name</label>
            <input name="name" required value="<?= htmlspecialchars($editing['name'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Date of Birth</label>
            <input type="date" name="dob" value="<?= htmlspecialchars($editing['dob'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Gender</label>
            <?php $gender = $editing['gender'] ?? ''; ?>
            <select name="gender">
                <option <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
                <option <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
                <option <?= $gender === 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="field">
            <label>Phone</label>
            <input name="phone" value="<?= htmlspecialchars($editing['phone'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Blood Group</label>
            <input name="blood_group" placeholder="A+" value="<?= htmlspecialchars($editing['blood_group'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Address</label>
            <input name="address" value="<?= htmlspecialchars($editing['address'] ?? '') ?>">
        </div>

        <div class="actions">
            <button class="btn" name="save"><?= $editing ? 'Update Patient' : 'Add Patient' ?></button>
            <?php if ($editing): ?>
                <a class="btn secondary" href="patients.php">Cancel</a>
            <?php endif; ?>
        </div>

    </form>
</div>

<div class="panel">
    <form class="searchbar">
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search name, phone or blood group">
        <button class="btn">Search</button>
    </form>

    <div class="table-wrap">
        <table>
            <tr>
                <th>Name</th>
                <th>DOB</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Blood</th>
                <th>Action</th>
            </tr>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['dob']) ?></td>
                    <td><?= htmlspecialchars($r['gender']) ?></td>
                    <td><?= htmlspecialchars($r['phone']) ?></td>
                    <td><?= htmlspecialchars($r['blood_group']) ?></td>
                    <td class="actions">
                        <a class="btn secondary" href="?edit=<?= $r['id'] ?>">Edit</a>
                        <a class="btn danger" href="?delete=<?= $r['id'] ?>"
                           onclick="return confirm('Delete this patient?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
