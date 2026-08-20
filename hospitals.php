<?php
// ============================================================
// hospitals.php
// Full CRUD (Create, Read, Update, Delete) for the hospitals table.
// ============================================================
require_once 'config/db.php';
require_once 'config/auth.php';
require_login();

$page_title = 'Hospitals & Clinics';

// ---------- CREATE or UPDATE ----------
// The same form is used for adding a new hospital and editing an
// existing one. If a hidden "hospital_id" field is present, we UPDATE.
// Otherwise we INSERT a new row.
if (isset($_POST['save'])) {

    $name    = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone   = trim($_POST['phone']);
    $email   = trim($_POST['email']);

    if (!empty($_POST['hospital_id'])) {
        // UPDATE an existing hospital
        $stmt = $pdo->prepare(
            "UPDATE hospitals SET name = ?, address = ?, phone = ?, email = ? WHERE id = ?"
        );
        $stmt->execute([$name, $address, $phone, $email, $_POST['hospital_id']]);
    } else {
        // INSERT a new hospital
        $stmt = $pdo->prepare(
            "INSERT INTO hospitals (name, address, phone, email) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$name, $address, $phone, $email]);
    }

    header("Location: hospitals.php");
    exit;
}

// ---------- DELETE ----------
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM hospitals WHERE id = ?");
    $stmt->execute([$_GET['delete']]);

    header("Location: hospitals.php");
    exit;
}

// ---------- Load one hospital when the user clicks "Edit" ----------
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM hospitals WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch();
}

// ---------- READ (list all hospitals) ----------
$rows = $pdo->query("SELECT * FROM hospitals ORDER BY name")->fetchAll();

include 'includes/header.php';
?>

<div class="panel">
    <h2><?= $editing ? 'Edit Hospital / Clinic' : 'Add Hospital / Clinic' ?></h2>

    <form method="post" class="grid">

        <?php if ($editing): ?>
            <!-- Hidden field so the PHP code above knows this is an UPDATE -->
            <input type="hidden" name="hospital_id" value="<?= $editing['id'] ?>">
        <?php endif; ?>

        <div class="field">
            <label>Name</label>
            <input name="name" required value="<?= htmlspecialchars($editing['name'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Address</label>
            <input name="address" required value="<?= htmlspecialchars($editing['address'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Phone</label>
            <input name="phone" value="<?= htmlspecialchars($editing['phone'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Email</label>
            <input name="email" value="<?= htmlspecialchars($editing['email'] ?? '') ?>">
        </div>

        <div class="actions">
            <button class="btn" name="save"><?= $editing ? 'Update Hospital' : 'Add Hospital' ?></button>
            <?php if ($editing): ?>
                <a class="btn secondary" href="hospitals.php">Cancel</a>
            <?php endif; ?>
        </div>

    </form>
</div>

<div class="panel table-wrap">
    <table>
        <tr>
            <th>Name</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['address']) ?></td>
                <td><?= htmlspecialchars($r['phone']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
                <td class="actions">
                    <a class="btn secondary" href="?edit=<?= $r['id'] ?>">Edit</a>
                    <a class="btn danger" href="?delete=<?= $r['id'] ?>"
                       onclick="return confirm('Delete this hospital?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
