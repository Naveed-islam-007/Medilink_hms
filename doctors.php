<?php
// ============================================================
// doctors.php
// Full CRUD (Create, Read, Update, Delete) for the doctors table.
// Each doctor optionally belongs to a hospital (foreign key).
// ============================================================
require_once 'config/db.php';
require_once 'config/auth.php';
require_login();

$page_title = 'Healthcare Provider Directory';

// ---------- CREATE or UPDATE ----------
if (isset($_POST['save'])) {

    $name           = trim($_POST['name']);
    $specialization = trim($_POST['specialization']);
    $symptoms       = trim($_POST['symptoms']);
    $phone          = trim($_POST['phone']);
    $email          = trim($_POST['email']);
    $hospital_id    = $_POST['hospital_id'] ?: null;

    if (!empty($_POST['doctor_id'])) {
        // UPDATE an existing doctor
        $stmt = $pdo->prepare(
            "UPDATE doctors
             SET name = ?, specialization = ?, symptoms = ?, phone = ?, email = ?, hospital_id = ?
             WHERE id = ?"
        );
        $stmt->execute([$name, $specialization, $symptoms, $phone, $email, $hospital_id, $_POST['doctor_id']]);
    } else {
        // INSERT a new doctor
        $stmt = $pdo->prepare(
            "INSERT INTO doctors (name, specialization, symptoms, phone, email, hospital_id)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$name, $specialization, $symptoms, $phone, $email, $hospital_id]);
    }

    header("Location: doctors.php");
    exit;
}

// ---------- DELETE ----------
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt->execute([$_GET['delete']]);

    header("Location: doctors.php");
    exit;
}

// ---------- Load one doctor when the user clicks "Edit" ----------
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch();
}

// ---------- Hospitals for the dropdown ----------
$hospitals = $pdo->query("SELECT id, name FROM hospitals ORDER BY name")->fetchAll();

// ---------- READ (search + list doctors, joined with hospital name) ----------
$q    = trim($_GET['q'] ?? '');
$like = "%$q%";

$stmt = $pdo->prepare(
    "SELECT d.*, h.name AS hospital
     FROM doctors d
     LEFT JOIN hospitals h ON h.id = d.hospital_id
     WHERE d.name LIKE ? OR d.specialization LIKE ? OR d.symptoms LIKE ?
     ORDER BY d.name"
);
$stmt->execute([$like, $like, $like]);
$rows = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="panel">
    <h2><?= $editing ? 'Edit Doctor' : 'Add Doctor' ?></h2>

    <form method="post" class="grid">

        <?php if ($editing): ?>
            <input type="hidden" name="doctor_id" value="<?= $editing['id'] ?>">
        <?php endif; ?>

        <div class="field">
            <label>Name</label>
            <input name="name" required value="<?= htmlspecialchars($editing['name'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Specialization</label>
            <input name="specialization" required value="<?= htmlspecialchars($editing['specialization'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Symptoms</label>
            <input name="symptoms" value="<?= htmlspecialchars($editing['symptoms'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Phone</label>
            <input name="phone" value="<?= htmlspecialchars($editing['phone'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($editing['email'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Hospital</label>
            <select name="hospital_id">
                <option value="">-- Select --</option>
                <?php foreach ($hospitals as $h): ?>
                    <option value="<?= $h['id'] ?>"
                        <?= (isset($editing['hospital_id']) && $editing['hospital_id'] == $h['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($h['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="actions">
            <button class="btn" name="save"><?= $editing ? 'Update Doctor' : 'Add Doctor' ?></button>
            <?php if ($editing): ?>
                <a class="btn secondary" href="doctors.php">Cancel</a>
            <?php endif; ?>
        </div>

    </form>
</div>

<div class="panel">
    <form class="searchbar">
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search doctor, specialty or symptom">
        <button class="btn">Search</button>
    </form>

    <div class="table-wrap">
        <table>
            <tr>
                <th>Doctor</th>
                <th>Specialization</th>
                <th>Symptoms</th>
                <th>Hospital</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['specialization']) ?></td>
                    <td><?= htmlspecialchars($r['symptoms']) ?></td>
                    <td><?= htmlspecialchars($r['hospital'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['phone']) ?></td>
                    <td class="actions">
                        <a class="btn secondary" href="?edit=<?= $r['id'] ?>">Edit</a>
                        <a class="btn danger" href="?delete=<?= $r['id'] ?>"
                           onclick="return confirm('Delete this doctor?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
