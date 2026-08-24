<?php

require_once 'config/db.php';
require_once 'config/auth.php';
require_login();

$page_title = 'Pharmacy Management';

if (isset($_POST['save'])) {

    $name    = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone   = trim($_POST['phone']);

    if (!empty($_POST['pharmacy_id'])) {
        
        $stmt = $pdo->prepare(
            "UPDATE pharmacies SET name = ?, address = ?, phone = ? WHERE id = ?"
        );
        $stmt->execute([$name, $address, $phone, $_POST['pharmacy_id']]);
    } else {
        
        $stmt = $pdo->prepare(
            "INSERT INTO pharmacies (name, address, phone) VALUES (?, ?, ?)"
        );
        $stmt->execute([$name, $address, $phone]);
    }

    header("Location: pharmacies.php");
    exit;
}


if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM pharmacies WHERE id = ?");
    $stmt->execute([$_GET['delete']]);

    header("Location: pharmacies.php");
    exit;
}


$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM pharmacies WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch();
}

$q    = trim($_GET['q'] ?? '');
$like = "%$q%";

$stmt = $pdo->prepare(
    "SELECT * FROM pharmacies
     WHERE name LIKE ? OR address LIKE ? OR phone LIKE ?
     ORDER BY name"
);
$stmt->execute([$like, $like, $like]);
$rows = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="panel">
    <h2><?= $editing ? 'Edit Pharmacy' : 'Add Pharmacy' ?></h2>

    <form method="post" class="grid">

        <?php if ($editing): ?>
            <input type="hidden" name="pharmacy_id" value="<?= $editing['id'] ?>">
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

        <div class="actions">
            <button class="btn" name="save"><?= $editing ? 'Update Pharmacy' : 'Add Pharmacy' ?></button>
            <?php if ($editing): ?>
                <a class="btn secondary" href="pharmacies.php">Cancel</a>
            <?php endif; ?>
        </div>

    </form>
</div>

<div class="panel">
    <form class="searchbar">
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search by name, address or phone">
        <button class="btn">Search</button>
    </form>

    <div class="table-wrap">
    <table>
        <tr>
            <th>Name</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['address']) ?></td>
                <td><?= htmlspecialchars($r['phone']) ?></td>
                <td class="actions">
                    <a class="btn secondary" href="?edit=<?= $r['id'] ?>">Edit</a>
                    <a class="btn danger" href="?delete=<?= $r['id'] ?>"
                       onclick="return confirm('Delete this pharmacy?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
