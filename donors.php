<?php
// ============================================================
// donors.php
// Full CRUD (Create, Read, Update, Delete) for the blood_donors table.
// ============================================================
require_once 'config/db.php';
require_once 'config/auth.php';
require_login();

$page_title = 'Blood Donor Database';

// ---------- CREATE or UPDATE ----------
if (isset($_POST['save'])) {

    $name        = trim($_POST['name']);
    $blood_group = trim($_POST['blood_group']);
    $phone       = trim($_POST['phone']);
    $location    = trim($_POST['location']);
    $last_date   = $_POST['last_donation_date'] ?: null;
    $available   = isset($_POST['available']) ? 1 : 0;

    if (!empty($_POST['donor_id'])) {
        // UPDATE an existing donor
        $stmt = $pdo->prepare(
            "UPDATE blood_donors
             SET name = ?, blood_group = ?, phone = ?, location = ?, last_donation_date = ?, available = ?
             WHERE id = ?"
        );
        $stmt->execute([$name, $blood_group, $phone, $location, $last_date, $available, $_POST['donor_id']]);
    } else {
        // INSERT a new donor
        $stmt = $pdo->prepare(
            "INSERT INTO blood_donors (name, blood_group, phone, location, last_donation_date, available)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$name, $blood_group, $phone, $location, $last_date, $available]);
    }

    header("Location: donors.php");
    exit;
}

// ---------- DELETE ----------
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM blood_donors WHERE id = ?");
    $stmt->execute([$_GET['delete']]);

    header("Location: donors.php");
    exit;
}

// ---------- Load one donor when the user clicks "Edit" ----------
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM blood_donors WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch();
}

// ---------- READ (search + list donors) ----------
$q    = trim($_GET['q'] ?? '');
$like = "%$q%";

$stmt = $pdo->prepare(
    "SELECT * FROM blood_donors
     WHERE blood_group LIKE ? OR location LIKE ? OR name LIKE ?
     ORDER BY available DESC, name"
);
$stmt->execute([$like, $like, $like]);
$rows = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="panel">
    <h2><?= $editing ? 'Edit Blood Donor' : 'Add Blood Donor' ?></h2>

    <form method="post" class="grid">

        <?php if ($editing): ?>
            <input type="hidden" name="donor_id" value="<?= $editing['id'] ?>">
        <?php endif; ?>

        <div class="field">
            <label>Name</label>
            <input name="name" required value="<?= htmlspecialchars($editing['name'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Blood Group</label>
            <input name="blood_group" required placeholder="O+" value="<?= htmlspecialchars($editing['blood_group'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Phone</label>
            <input name="phone" required value="<?= htmlspecialchars($editing['phone'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Location</label>
            <input name="location" required value="<?= htmlspecialchars($editing['location'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Last Donation</label>
            <input type="date" name="last_donation_date" value="<?= htmlspecialchars($editing['last_donation_date'] ?? '') ?>">
        </div>

        <div class="field">
            <?php $available = $editing['available'] ?? 1; ?>
            <label>
                <input type="checkbox" name="available" <?= $available ? 'checked' : '' ?>> Available
            </label>
        </div>

        <div class="actions">
            <button class="btn" name="save"><?= $editing ? 'Update Donor' : 'Add Donor' ?></button>
            <?php if ($editing): ?>
                <a class="btn secondary" href="donors.php">Cancel</a>
            <?php endif; ?>
        </div>

    </form>
</div>

<div class="panel">
    <form class="searchbar">
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search by blood group, location or name">
        <button class="btn">Search</button>
    </form>

    <div class="table-wrap">
        <table>
            <tr>
                <th>Name</th>
                <th>Blood</th>
                <th>Phone</th>
                <th>Location</th>
                <th>Available</th>
                <th>Action</th>
            </tr>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><b><?= htmlspecialchars($r['blood_group']) ?></b></td>
                    <td><?= htmlspecialchars($r['phone']) ?></td>
                    <td><?= htmlspecialchars($r['location']) ?></td>
                    <td><?= $r['available'] ? 'Yes' : 'No' ?></td>
                    <td class="actions">
                        <a class="btn secondary" href="?edit=<?= $r['id'] ?>">Edit</a>
                        <a class="btn danger" href="?delete=<?= $r['id'] ?>"
                           onclick="return confirm('Delete this donor?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
